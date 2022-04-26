<?php
namespace App\Controllers;


class TfomeinforController extends BaseController
{

    
    public function index( $clone_name )
    {                
        $sql= get_tfominfor_query();
        
        $query=$this->db->query($sql,[
            'clone_name' => $clone_name
        ]);
        
        $results=$query->getRowArray();
        
        
        // in case the query failed, stop here (do not attempt to show the view)
        if( is_null($results) ){
            return "unrecognized clone name '$clone_name'";
        }
            
        $data['results']=$results;
        $data['species'] = $results['speciesname'];
        $data['template'] = $results['template'];
        $data['title'] ="Tfome Clone Information";
        $data['clone_name'] = $clone_name;
        $data['proteinsequence'] = $results['translation'];
        
        
        return view('tfomeinfor', $data);
    }
}
