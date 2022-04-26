<?php

namespace App\Controllers;

class HomeController extends BaseController
{

    
    public function index()
    {
        $db = $this->db;
        
        //count numbre of TF
        $query = $db->query("
            SELECT base.feature_id FROM feature base 
            JOIN featureprop taxrank__class 
                ON base.feature_id = taxrank__class.feature_id 
                AND (taxrank__class.type_id = '13') 
            WHERE (taxrank__class.value = 'TF')");
        $data['no_of_tfs'] = $query->getNumRows();

        //count number of coregs
        $query = $db->query("
            SELECT base.feature_id FROM feature base 
            JOIN featureprop taxrank__class 
                ON base.feature_id = taxrank__class.feature_id 
                AND (taxrank__class.type_id = '13') 
            WHERE (taxrank__class.value = 'Coreg')");
        $data['no_of_coregs'] = $query->getNumRows();

        //count number of tfome
        $query = $db->query("SELECT * FROM featureprop clone WHERE (clone.type_id = '1368')");
        $data['no_of_tfome'] = $query->getNumRows();
        
        
        $data['title'] = "Grassius";
        return view('home', $data);
    }
}
