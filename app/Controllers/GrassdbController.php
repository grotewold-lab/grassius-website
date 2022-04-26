<?php

namespace App\Controllers;

class GrassdbController extends BaseController
{
    
    public function index( $class )
    {
        
        $sql = "SELECT family as famname 
            FROM class_family 
            WHERE class=:class: ORDER BY famname";
        $query = $this->db->query($sql,[
            'class' => $class
        ]);
        
        $result = $query->getResult();
        
        $data['family_names']=array_map(function($x) {return $x->famname;}, $result);
            
        if( $class==='Coreg' ) {
            $data['title'] ="Coregulator Database";
            $view_name = 'grasscoregdb';
        } else {
            $data['title'] ="Transcription Factor Databases";
            $view_name = 'grasstfdb';
        }
        
        return view($view_name, $data);
    }
}
