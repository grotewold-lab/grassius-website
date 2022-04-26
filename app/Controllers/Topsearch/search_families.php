<?php
    
function search_families( $db, $searchterm )
{
    $query = $db->table("featureprop family")
        ->select("family.value name")
        ->where("family.type_id", 1362)
        ->like("LOWER(family.value)", $searchterm )
        ->distinct()->limit(10)->get();

    $result = $query->getResultArray();

    $searchresults = [];
    foreach ($result as $row) {
        $name = $row['name'];
        $searchresults[] = [
            "category" => "Family",
            "label" => $name, 
            "value" => "/family/Maize/".$name
        ];
    }
    return $searchresults;
}