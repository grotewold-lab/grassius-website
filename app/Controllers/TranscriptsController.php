<?php
namespace App\Controllers;


class TranscriptsController extends DatatableController
{    
    
    // implement DatatableController
    protected function get_column_config()
    {
        return [
            
           // [ query-key, result-key, view-label ]
           ["f.uniquename", "tid", "Transcript ID"],
           ["td.domains", "domains", "Domains"],
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
            ->where("f.type_id", 534 );
            
        return $result;
    }
    
    // implement DatatableController
    protected function prepare_results( $row ) {
        return [
           "tid" => $row['tid'],
           "domains" => get_domain_image($row['tid'],$row['domains'])
        ];
    }
        
    
    public function index()
    {        
        $data['datatable'] = $this->get_datatable_html("transcripts_table","/transcripts_datatable/");
        
        $data['title'] = 'Transcripts';
        
        return view('transcripts', $data);
    }
}
