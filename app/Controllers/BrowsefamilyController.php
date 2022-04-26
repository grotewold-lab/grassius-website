<?php
namespace App\Controllers;


class BrowsefamilyController extends BaseController
{

    public function index( $species, $class )
    {                
        // get species name in two forms 
        list($species,$new_species) = parse_species($species);
        
        $sql = get_browsefamily_query();
        
        $query=$this->db->query($sql,[
            'class' => $class
        ]);          
        $results = $query->getResultArray();

        $results=$query->getResultArray();
        
        
        //build input for word cloud generator
        $word_cloud_input = "";
        foreach ($results as $entry)
        {
            $family = $entry['familyname'];
            $count = $entry['total'];
            for($i=0; $i<$count;$i++)
            {
               $word_cloud_input .= $family."\n";
            } 
        }
        $data["word_cloud_input"] = $word_cloud_input;
        
        
        //to format for display in six columns
        $data['rows'] = array_chunk($results, 6);
        
        
        // quick-fix: tell the view how to link to the same page but with different maize genome versions
        $data['mgvc_url_format'] = "/browsefamily/$species"."_%s/$class";
 
        
        $data['results'] = $results;
        $data['species'] = $species;
        $data['class'] = $class;
        $data['title'] ="Browse Families :: $species";
        
        return view("browsefamily",$data);
    }
}
