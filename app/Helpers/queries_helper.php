<?php
    
/**
 * build a general query with placeholders, to retrieve sequences
 *
 * used in FastaDownloadController.php
 *
 * parameters:
 *    protein - true if querying for amino acids sequences, otherwise DNA
 *    class_filter - true if the query should support filtering by class
 *    family_filter - true if the query should support filtering by family
 */
function build_fasta_query( $protein, $class_filter, $family_filter )
{
    if( $protein ) {
        $sql =  "SELECT
                aa_seq.uniquename as tid,
                aa_seq.residues as seq";
    } else {
        $sql =  "SELECT
                base.uniquename as tid,
                base.residues as seq";
    }

    $sql .= "
        FROM feature base

        LEFT JOIN feature_relationship aa_rel
            ON (aa_rel.object_id = base.feature_id)
            AND (aa_rel.type_id = 327)

        LEFT JOIN feature aa_seq
            ON (aa_seq.feature_id = aa_rel.subject_id) 
            AND (aa_seq.type_id = 534)

        JOIN organism org 
            ON base.organism_id = org.organism_id
    ";
    
    if( $class_filter ){
        $sql .= "
            JOIN featureprop taxrank__class
                ON (base.feature_id = taxrank__class.feature_id)
                AND (taxrank__class.type_id = 13)
                AND taxrank__class.value = :class:
        ";
    }
    if( $family_filter ){
        $sql .= "
            JOIN featureprop taxrank__family
                ON (base.feature_id = taxrank__family.feature_id)
                AND (taxrank__family.type_id = 1362)
                AND taxrank__family.value = :family:
        ";
    }
    $sql .= "
        WHERE org.common_name = :species:
        AND org.infraspecific_name = :species_version:
        AND base.type_id = 844
    ";

    if( $protein ) {
        $sql .= "ORDER BY aa_seq.uniquename";
    } else {
        $sql .= "ORDER BY base.uniquename";
    }

    return $sql; 
}

/**
 * get the main query used for tfomeinfor.php
 * return a string
 */
function get_tfominfor_query()
{
    return "SELECT
                base.residues as sequence,
                aa_seq.residues as translation,
                CONCAT(obi__organism.genus, ' ', obi__organism.species) as speciesname,
                tm.insert_gene_bank_id,
                tm.vector,
                tm.notes,
                tm.five_prime_name,
                tm.five_prime_seq,
                tm.five_prime_temp,
                tm.three_prime_name,
                tm.three_prime_seq,
                tm.three_prime_temp,
                tm.pcr_condition,
                tm.transcript_number,
                tm.request_info,
                tm.template,
                dmn.name as gene_name,
                gc.v3_id as gene_id,
                dmn.family as family,
                sg.subgenome as subgenome

            FROM feature base

            JOIN organism obi__organism
                ON base.organism_id = obi__organism.organism_id

            JOIN feature_relationship aa_rel
                ON aa_rel.object_id = base.feature_id

            JOIN feature aa_seq
                ON (aa_seq.feature_id = aa_rel.subject_id)
                AND (aa_seq.type_id = 534)

            JOIN tfome_metadata tm
                ON tm.utname = base.uniquename
                
            JOIN gene_clone gc
                ON gc.clone_name = base.uniquename
            
            LEFT JOIN default_maize_names dmn
                ON dmn.v3_id = gc.v3_id
                and dmn.v3_id != ''
                
            LEFT JOIN subgenome sg
                ON sg.geneid = gc.v3_id
                
            WHERE base.uniquename=:clone_name:";
}


function get_pdi_distance_bin_indices( $db, $search_term)
{
    $query = $db->table('public.gene_interaction gi')
        ->select("(distance+2)*10 as bin_index");
    
    if( isset($search_term) and (trim($search_term)!='') ){
        $term = trim(strtolower($search_term));
        if( ($term!='null') and ($term!='') ){
            $query = $query
                ->groupStart()
                ->Like("LOWER(gi.gene_id)", $term )
                ->orLike("LOWER(gi.protein_name)", $term )
                ->orLike("LOWER(gi.target_id)", $term )
                ->orLike("LOWER(gi.target_name)", $term )
                ->orLike("LOWER(gi.experiment)", $term )
                ->groupEnd();
        }
    }
    
    $qr = $query->get()->getResultArray();
    $result = [];
    foreach( $qr as $row ){
       $result[] = intval($row['bin_index']);
    }
    return $result;
}


// used in
function get_subgenome($db,$base_fid){
 
    $sql = "
        SELECT sg.subgenome 
            FROM featureprop fp

        JOIN public.subgenome sg
            ON sg.geneid = fp.value 
            
        WHERE fp.feature_id = :fid:
            AND fp.type_id = 496
            
        LIMIT 1;
    ";
    $query=$db->query($sql,[
        'fid' => $base_fid
    ]);

    $results=$query->getResultArray();

    if( count($results) == 0 ){
        return null;   
    }
    
    return $results[0]['subgenome'];
}

/**
 * get the main query used for the following pages:
 * proteininfor.php, interactions.php
 * return a string
 */
function get_proteininfor_query()
{
    return  "SELECT
                base.feature_id as base_fid,
                base.residues as nucleotidesequence,
                base.uniquename as id_name,
                taxrank__family.value as family,
                taxrank__class.value as class,
                gene_name.synonym as synonym,
                aa_seq.residues as proteinsequence,
                sf.secondary_structures as secondary_structures,
                uniprot.uniprot_id as uniprot_id,
                obi__organism.infraspecific_name as species_version,
                clone.names as clone_names,
                domain.json as domains
                
            FROM feature base
            
            JOIN featureprop taxrank__family
                ON (base.feature_id = taxrank__family.feature_id)
                AND (taxrank__family.type_id = 1362)
            
            JOIN featureprop taxrank__class
                ON (base.feature_id = taxrank__class.feature_id)
                AND (taxrank__class.type_id = 13)
                
            LEFT JOIN public.gene_name
                ON gene_name.grassius_name = base.name
                
            LEFT JOIN public.uniprot_ids uniprot
                ON uniprot.gene_name = base.name

            LEFT JOIN feature_relationship aa_rel
                ON (aa_rel.object_id = base.feature_id)
                AND (aa_rel.type_id = 327)

            LEFT JOIN feature aa_seq
                ON (aa_seq.feature_id = aa_rel.subject_id) 
                AND (aa_seq.type_id = 534)

            LEFT JOIN (
                SELECT 
                    clone_rel.object_id as base_id,
                    string_agg(clone.name, ' ') as names
                
                FROM feature_relationship clone_rel

                JOIN feature clone
                    ON (clone.feature_id = clone_rel.subject_id) 
                    AND (clone.type_id = 844)
                    
                WHERE (clone_rel.type_id = 435)
                    
                GROUP BY clone_rel.object_id
            ) as clone
                ON clone.base_id = base.feature_id

            LEFT JOIN (
                SELECT 
                    domain.feature_id as feature_id,
                    string_agg(domain.value, ',') as json
                
                FROM featureprop domain
                    
                WHERE (domain.type_id = 61467)
                    
                GROUP BY domain.feature_id
            ) as domain
                ON domain.feature_id = base.feature_id
                
            LEFT JOIN public.seq_features sf
                ON (sf.feature_id = aa_seq.feature_id)
                
            JOIN organism obi__organism 
                ON base.organism_id = obi__organism.organism_id
            
            WHERE (base.name=:genename:) AND (base.name!='NaN') AND base.type_id=844
            
            ORDER BY obi__organism.infraspecific_name, base.uniquename";
}

/**
 * get the main query used for browsefamily.php
 */
function get_browsefamily_query( $species )
{
    if( $species == 'Maize' ){
        return "SELECT cf.family as familyname,total
            FROM class_family cf

            LEFT JOIN (
                SELECT family, COUNT(*) as total

                FROM default_maize_names

                GROUP BY family
            ) as dmn
                ON dmn.family = cf.family

            WHERE (class = :class:) OR (class = 'Orphans')

            ORDER BY cf.family;";
        
    } else { // not maize
        
        return "
            SELECT fp.value as familyname, COUNT(*) as total

            FROM feature base

            JOIN featureprop fp
                ON fp.feature_id = base.feature_id
                AND fp.type_id = 1362
                
            JOIN featureprop cp
                ON cp.feature_id = base.feature_id
                AND cp.type_id = 13

            JOIN organism org
                ON org.organism_id = base.organism_id

            WHERE org.common_name = '$species'
            AND base.type_id = 844
            AND ((cp.value = :class:) OR (cp.value = 'Orphans'))

            GROUP BY fp.value

            ORDER BY fp.value;";
    }
}

/**
 * get the main query used for family.php and search_results.php
 * return a string
 */
function get_gene_query( $paging=FALSE, $searching_family=FALSE, $searching_gene=FALSE )
{

    $result = "SELECT
        base.feature_id AS feature_id,
        base.name AS grassius_name,
        base.uniquename AS id_name,
        base.residues as nucleotidesequence,
        array_to_string(array(SELECT clone.value FROM featureprop clone WHERE clone.feature_id = base.feature_id AND (clone.type_id = '1368')),', ') AS clones,
        gene_name.synonym as othername,
        gene_name.accepted as accepted,
        CONCAT(obi__organism.genus, ' ', obi__organism.species) as speciesname,
        taxrank__class.value as class

        FROM feature base
        INNER JOIN organism obi__organism ON base.organism_id = obi__organism.organism_id
        LEFT JOIN public.gene_name ON gene_name.grassius_name = base.name

        JOIN featureprop taxrank__class
            ON (base.feature_id = taxrank__class.feature_id)
            AND (taxrank__class.type_id = 13)

        JOIN featureprop taxrank__family
            ON (base.feature_id = taxrank__family.feature_id)
            AND (taxrank__family.type_id = 1362)
    ";

    if ($searching_family) {
        $result .= "
            WHERE  (CONCAT(obi__organism.genus, ' ', obi__organism.species) = :species:)
            AND (taxrank__family.value = :family:)";
    }

    if ($searching_gene) {
        $result .= " WHERE (base.uniquename=:gene:) OR (gene_name.synonym=:synonym:)";
    }

    $result .= " ORDER BY grassius_name ASC";

    if ($paging) {
        throw new Exception('paging is not yet supported');
    }

    return $result;
}

/**
 * count the total number of tfomes for the given species
 * used to setup paging in tfomecollection.php and RiceTFome.php
 * return an integer
 */
function get_tfome_count( $db, $species ) {
    return $db->query(
        "SELECT *
        FROM feature base
        JOIN organism obi__organism 
            ON base.organism_id = obi__organism.organism_id
        LEFT JOIN featureprop clone 
            ON (base.feature_id = clone.feature_id) 
            AND (clone.type_id = '1368')
        WHERE (CONCAT(obi__organism.genus, ' ', obi__organism.species) = '$species')"
    )->getNumRows();
}
