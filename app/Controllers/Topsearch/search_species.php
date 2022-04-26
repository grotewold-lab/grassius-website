<?php
    
function search_basic_species( $db, $searchterm )
{
    $searchresults = [];
    foreach( list_basic_species() as $bs ) {
        if (strpos(strtolower($bs), $searchterm) !== false) {
            $searchresults[] = [
                "category" => "Species",
                "label" => $bs, 
                "value" => "/species/".$bs
            ];
        }
    }
    return $searchresults;
}

function search_chado_species( $db, $searchterm ) 
{        
    $query = $db->table('organism org')
        ->select("CONCAT(org.genus, ' ', org.species) as name")
        ->like("LOWER(CONCAT(org.genus, ' ', org.species))",$searchterm)
        ->get();

    $result = $query->getResultArray();

    $searchresults = [];
    foreach ($result as $row) {
        $name = $row['name'];
        $searchresults[] = [
            "category" => "Species",
            "label" => $name, 
            "value" => "/species/".$name
        ];
    }
    return $searchresults;
}