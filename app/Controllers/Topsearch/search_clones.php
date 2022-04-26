<?php
    
function search_clones( $db, $searchterm )
{
        
    $query = $db->table('public.gene_clone gc')
            ->select("gc.clone_name AS clone")
            ->like('LOWER(gc.clone_name)', $searchterm)
            ->orderBy('gc.clone_name')
            ->limit(10)->get();
    
    //collect results
    $result = $query->getResultArray();
    $searchresults = [];
    foreach ($result as $row) {
        $clone = $row["clone"];
        $searchresults[] = [
            "category" => "Clone",
            "label" => $clone,
            "value" => "/tfomeinfor/$clone"
        ];
    }
    return $searchresults;
}