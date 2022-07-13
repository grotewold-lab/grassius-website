<?php
namespace App\Controllers;


class CustomfamilyController extends DatatableController
{    
    
    // implement DatatableController
    protected function get_column_config()
    {
        return [
            
           // [ query-key, result-key, view-label ]
           ["f.uniquename", "tid", "Transcript ID"],
           ["td.domains", "domains", "Domains"]
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
            ->select("f.uniquename AS tid, td.domains AS domains")
            ->join('organism o','o.organism_id = f.organism_id')
            ->join('transcript_domains td', 'td.tid = f.uniquename')
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
        return [
           "tid" => $row['tid'],
           "domains" => get_domain_image($row['tid'],$row['domains'],$this->required_doms)
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
