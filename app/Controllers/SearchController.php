<?php
namespace App\Controllers;


class SearchController extends BaseController
{
    
    public function search_results( $searchterm )
    {                
        $db = $this->db;
        
        //seperate gene from synonym
        $splitterms = explode(',', $searchterm);
        $gene=$splitterms[0];
        if (empty($splitterms[1])) {  //if user inputs own search term wthout autocomplete
            //A bit of cleaning
            $gene=strip_tags($gene);
            $gene=trim($gene);
            $synonym=$gene;
        } else {
            $synonym=$splitterms[1];
        }


        $sql= get_gene_query($paging=FALSE, $searching_family=FALSE, $searching_gene=TRUE);    
        $query=$db->query($sql,[
            'gene' => $gene,
            'synonym' => $synonym
        ]);
        $results = $query->getRowArray();

        //get species for image banner
        $species=$results['speciesname'];
        $ans= $results['accepted'];

        //for GRGx input
        if (strcmp($species, "Zea mays") == 0) {
            $abbr='Zm';
        } else {
            $abbr='Os';
        }
        
        $data['searchterm'] = $searchterm;
        $data['gene'] = $gene;
        $data['results'] = $results;
        $data['species'] = $species;
        $data['title'] ="Search Results";
        
        return view('search_results', $data);
    }
    
    public function autocomplete( $class, $key ) 
    {        
        
        $db = $this->db;
        
        // prevent sql code from being injected
        $class = $db->escapeString($class);
        $key = $db->escapeLikeString($key);
        
        // build query by concatenating strings
        $query=$this->db->query(
            "
        SELECT
            base.uniquename as id_name,
            gene_name.synonym as synonym

        FROM feature base

                JOIN featureprop taxrank__family
                    ON (base.feature_id = taxrank__family.feature_id)
                    AND (taxrank__family.type_id = 1362)

                JOIN featureprop taxrank__class
                    ON (base.feature_id = taxrank__class.feature_id)
                    AND (taxrank__class.type_id = 13)
                    AND (taxrank__class.value='".$class."')

                JOIN public.gene_name 
                    ON gene_name.grassius_name = base.name

        AND ((base.uniquename like '%".$key."%') OR (gene_name.synonym like '%".$key."%'))
        ORDER BY CASE WHEN gene_name.synonym LIKE '%".$key."%' THEN 0 ELSE 1 END,base.uniquename;

        ")->getResultArray();

        $array = array();
        foreach ($query as $row) {
            $array[] =$row['id_name'] . ', ' . $row['synonym'];
        }
        echo json_encode($array);
        
    }
}
