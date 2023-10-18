<?php

namespace App\Controllers;

class SpeciesportalController extends CsvDatatableController
{

    
    public function index( $species)
    {
        // get species name in two forms 
        list($species,$new_species) = parse_species($species);
        
        // get the second part of the species name
        $cies = explode(" ", $new_species)[1];
        
        // lookup organism details
        $org_details = $this->db->table("organism org")
            ->where('org.species', $cies)->get()->getRowArray();
        
        // get a link to an eternal species-specific database
        [$esd_url, $esd_label] = get_external_species_db($species);
        $data["esd_url"] = $esd_url;
        $data["esd_label"] = $esd_label;
        
        # if necessary, set the initial state of the maize version radio buttons
        if( $species == 'Maize' ){
            $data['species_version'] = $this->get_session_var('Maize_version');
        } else {
            $data['species_version'] = "_";
        }
        
        $data["ncbi_id"] = get_ncbi_taxonomy_id($species);
        $data["org_details"] = $org_details;
        $data["full_name"] = $org_details['genus'].' '.$org_details['species'];
        $data["species"] = $species;
        $data["title"] = $species." Portal";
        return View("species_portal", $data);
    }
    
    // wrap CsvDatatableController->download_csv
    public function download_species_csv($species,$version){
        $this->species = $species;
        $this->species_version = $version;
        return $this->download_csv();
    }
    
    
    // implement DatatableController
    protected function get_column_config()
    {
        if( $this->species == 'Maize' ){
            return [

               // [ query-key, result-key, view-label ]
               ["dmn.name_sort_order", "name_sort_order", "Protein Name <br><font color=#ce6301>accepted</font>&#x2F;<font color=#808B96>suggested</font>"],
               ["dmn.name", "grassius_name", "Protein Name"],
               ["dmn.family", "family", "Family"],
               ["dmn.v3_id", "v3_id", "Maize v3 ID"],
               ["dmn.v4_id", "v4_id", "Maize v4 ID"],
               ["dmn.v5_id", "v5_id", "Maize v5 ID"],
               ["gene_name.synonym", "othername", "Synonym/<br>Gene Name"],
               ["searchable_clones.clone_list", "clones", "Clone in TFome"],
               ["dmn.all_ids", "all_ids", "All Gene IDs"],
               ["sg.subgenome", "subgenome", "Subgenome"],
            ];
            
        }else { // not maize
            return [

               // [ query-key, result-key, view-label ]
               ["no.sortorder", "name_sort_order", "Protein Name"],
               ["base.name", "grassius_name", "Protein Name"],
               ["fp.value", "family", "Family"],
               ["base.uniquename", "v3_id", "Gene ID"],
            ];
            
        }
    }
    
    
    
    // implement DatatableController
    protected function is_column_searchable( $query_key )
    {
        return true;
    }
    
    // implement DatatableController
    protected function get_base_query_builder()
    {
        
        if( $this->species == 'Maize' ){
            return $this->db->table('public.default_maize_names dmn')
                ->select("dmn.name AS grassius_name,
                    dmn.name_sort_order AS name_sort_order,
                    gene_name.synonym AS othername,
                    dmn.family as family,
                    dmn.v3_id AS v3_id,
                    dmn.v4_id AS v4_id,
                    dmn.v5_id AS v5_id,
                    default_domains.domains AS domains,
                    dmn.all_ids AS all_ids,
                    dmn.all_ids AS raw_ids,
                    searchable_clones.clone_list AS clones,
                    gene_name.accepted as accepted,
                    'Zea mays' AS speciesname,
                    sg.subgenome as subgenome")
                ->join('public.searchable_clones', 'searchable_clones.name = dmn.name', 'left')
                ->join('public.gene_name', 'gene_name.grassius_name = dmn.name', 'left')
                ->join('default_domains', 'default_domains.protein_name = dmn.name', 'left')
                ->join('subgenome sg', 'dmn.v3_id = sg.geneid', 'left');
            
        } else { //not maize
            return $this->db->table('feature base')
                ->select("base.name AS grassius_name,
                    no.sortorder AS name_sort_order,
                    '' AS othername,
                    fp.value as family,
                    base.uniquename AS v3_id,
                    ' AS v4_id,
                    ' AS v5_id,
                    '' AS domains,
                    '' AS all_ids,
                    '' AS raw_ids,
                    '' AS clones,
                    '' as accepted,
                    CONCAT(org.genus,' ',org.species) AS speciesname")
                ->join('featureprop fp', 'fp.feature_id = base.feature_id AND fp.type_id = 1362')
                ->join('organism org', 'org.organism_id = base.organism_id')
                ->join('name_orders no', 'no.name = base.name')
                ->where('base.type_id', 844)
                ->where('org.common_name', $this->species);
        }
            
        return $result;
        
    }
    
    // implement DatatableController
    protected function prepare_results( $row ) {
        return $row;
    }
    
    // implement CsvDatatableController
    protected function get_csv_download_filename(){
        return "data.csv";
    }
    
    // implement CsvDatatableController
    protected function get_csv_column_headers(){        
        if( $this->species == 'Maize' ){
            
            return [
                "number for sorting purposes",
                "protein name",
                "family",
                "gene ID",
                "synonym",
                "clone",
                "all gene IDs",
                "subgenome",
            ];            
            
                
        } else { //not maize

            return [
                "number for sorting purposes",
                "protein name",
                "family",
                "gene ID"
            ];
        }
    }
    
    // implement CsvDatatableController
    protected function prepare_results_for_csv( $row ){        
        if( $this->species == 'Maize' ){

            $version = $this->species_version;
            $gid_col = $version."_id";
            
            return [
               "name_sort_order" => $row['name_sort_order'], 
               "grassius_name" => $row['grassius_name'], 
               "family" => $row['family'],
               "gene_id" => $row[$gid_col],
               "othername" => $row['othername'],
               "clones" => $row['clones'],
               "all_ids" => $row['all_ids'],
               "subgenome" => $row['subgenome'],
            ];
            
            
        } else { //not maize

            return [
               "name_sort_order" => $row['name_sort_order'], 
               "grassius_name" => $row['grassius_name'], 
               "family" => $row['family'],
               "gene_id" => $row['v3_id']
            ];
        }
    }
}
