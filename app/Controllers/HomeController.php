<?php

namespace App\Controllers;

class HomeController extends BaseController
{

    
    public function index()
    {
        $db = $this->db;
        
        // count numbre of TF
        $query = $db->query("
            SELECT base.feature_id FROM feature base 
            JOIN featureprop taxrank__class 
                ON base.feature_id = taxrank__class.feature_id 
                AND (taxrank__class.type_id = '13') 
            WHERE (taxrank__class.value = 'TF')");
        $data['no_of_tfs'] = $query->getNumRows();

        // count number of coregs
        $query = $db->query("
            SELECT base.feature_id FROM feature base 
            JOIN featureprop taxrank__class 
                ON base.feature_id = taxrank__class.feature_id 
                AND (taxrank__class.type_id = '13') 
            WHERE (taxrank__class.value = 'Coreg')");
        $data['no_of_coregs'] = $query->getNumRows();

        // count number of tfome
        $query = $db->query("SELECT * FROM featureprop clone WHERE (clone.type_id = '1368')");
        $data['no_of_tfome'] = $query->getNumRows();
        
        
        $data['news_html'] = $this->load_news_content();
        $data['title'] = "Grassius";
        return view('home', $data);
    }
    
    /**
     * Read news content from local folder public/news
     * return html string containing one <li> tag for each news entry
     */
    private function load_news_content()
    {
        
        $dir = ROOTPATH."/public/news/";
        $news_arr = array();
        foreach( scandir($dir) as $fname ){
            if( strlen($fname)<=3 ){
                continue;
            }
            $contents = file_get_contents($dir.$fname);
            $rows = explode( "\n", $contents );
            if( count($rows)<3 ){
                continue;
            }
            
            $news_arr[$rows[1]] = $rows;
        }
        
        krsort($news_arr);
        
        $news_html = "";
        foreach($news_arr as $key=>$rows){
            $news_html .= "<li>";
            $news_html .= "<span>$rows[1]</span>";
            $news_html .= "<br />";
            $news_html .= "<strong>$rows[0]</strong>";
            $news_html .= "<br />";
            $news_html .= $rows[2];
            $news_html .= "</li>";
        }
        return $news_html;
    }
}
