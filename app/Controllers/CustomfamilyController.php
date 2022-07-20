<?php
namespace App\Controllers;


class CustomfamilyController extends DatatableController
{    
    
    // implement DatatableController
    protected function get_column_config()
    {
        // WARNING
        // the view customfamily.php must 
        // have matching config (around line 128)
        
        return [
            
           // [ query-key, result-key, view-label ]
           ["f.uniquename", "tid", "Transcript ID"],
           ["fp_gene.value", "gid", "Gene ID"],
           ["td.domains", "domains", "Domains"],
           ["f.name", "protein_name", "Protein Name"],
           ["fp_family.value", "family", "Family"],
           ["fp_class.value", "class", "Class"]
        ];
    }
    
    // implement DatatableController
    protected function is_column_searchable( $query_key )
    {
        return true;
    }
    
    // implement DatatableController
    protected function get_base_query_builder()
    {        
        $result = $this->db->table('feature f')
            ->select("f.uniquename AS tid, fp_gene.value AS gid, td.domains AS domains, f.name as protein_name, fp_class.value as class, fp_family.value as family")
            ->join('organism o','o.organism_id = f.organism_id')
            ->join('transcript_domains td', 'td.tid = f.uniquename')
            ->join('feature_relationship fr', 'fr.subject_id = f.feature_id', 'LEFT')
            ->join('feature f_dna', 'fr.object_id = f_dna.feature_id', 'LEFT')
            ->join('featureprop fp_class', 'fp_class.feature_id = f_dna.feature_id AND fp_class.type_id = 13', 'LEFT')
            ->join('featureprop fp_family', 'fp_family.feature_id = f_dna.feature_id AND fp_family.type_id = 1362','LEFT')
            ->join('featureprop fp_gene', 'fp_gene.feature_id = f.feature_id AND fp_gene.type_id = 496', 'LEFT')
            ->where("f.type_id", 534 )
            ->where("o.infraspecific_name", "v5");
        
        foreach( $this->required_doms as $dom ) {
            $result = $result->like('td.domains', $dom );
        }
        
        foreach( $this->forbidden_doms as $dom ) {
            $result = $result->notLike('td.domains', $dom );
        }
        
        return $result;
    }
    
    // implement DatatableController
    protected function prepare_results( $row ) {
        
        $species = 'Maize';
        $fam = $row['family'];
        $pname = ($row['tid'] == $row['protein_name']) ? '' : $row['protein_name'];
        
        return [
           "tid" => $row['tid'],
           "gid" => get_external_db_link($species,$row['gid']),
           "domains" => get_domain_image($row['tid'],$row['domains'],$this->required_doms),
            "protein_name" => get_proteininfor_link($species,$pname),
            "family" => "<a href='/family/$species/$fam'>$fam</a>",
            "class" => $row['class'],
        ];
    }
        
    
    public function index( $species )
    {
        // get species name in two forms 
        list($species,$new_species) = parse_species($species);
        
        // put most recent species in session vars for convenience
        $this->session->set("species",$species);
        
        //attempt to parse custom family rules from request var
        $r = $this->request;
        $q = $r->getVar('q');
        if( !is_null($q) ){
            $parts = explode(';',$q);
            if( count($parts) == 3 ){
                $data['family_name'] = $parts[0];
                if( $parts[1] != 'None' ){
                    $data['required'] = explode(',',$parts[1]);
                }
                if( $parts[2] != 'None' ){
                    $data['forbidden'] = explode(',',$parts[2]);
                }
            }
        }
        
        $data['species'] = $species;
        $data['title'] = "Custom Family";
        
        // prepare datatable html code, but remove the script tag
        $datatable = $this->get_datatable_html("gene_table","/customfamily_datatable/$species");
        $i = strpos($datatable, "<script>");
        $datatable = substr( $datatable, 0, $i );
        $data['datatable'] = $datatable;
        
        return view('customfamily', $data);
    }
    
    
    public function customfamily_datatable( $species, $required, $forbidden )
    {
        list($species,$new_species) = parse_species($species);
    
        $this->species = $species;
        $this->new_species = $new_species;
        
        # Zm00001eb000020_P001
        if( $required == 'None' ){
            $this->required_doms = [];
        } else {
            $this->required_doms = explode( ',', $required );
        }
        
        if( $forbidden == 'None' ){
            $this->forbidden_doms = [];
        } else {
            $this->forbidden_doms = explode( ',', $forbidden );
        }
        
        return $this->datatable();
    }
    
    public function customfamily_autocomplete()
    {
        $r = $this->request;
        $term = $r->getVar('term');
        $term = strtolower($term);
        $query = $this->db->table('acc_list al')
            ->select("al.accession as acc")
            ->like("LOWER(al.accession)", '%'.$term.'%' )
            ->limit(10)
            ->get();
        
        $result = [];
        foreach( $query->getResult() as $row ){
            $acc = $row->acc;
            $result[] = ["id" => $acc, "label" => $acc, "value" => $acc];
        }
        
        return json_encode($result);
    }
}
