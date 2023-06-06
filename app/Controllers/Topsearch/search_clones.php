<?php
    
function search_clones( $db, $searchterm )
{
        
    $query = $db->table('public.gene_clone gc')
            ->join("default_maize_names dmn", "dmn.v3_id = gc.v3_id")
            ->join("gene_name gn", "gn.grassius_name = dmn.name")
            ->select("gc.clone_name AS clone")
            ->like('LOWER(gc.clone_name)', $searchterm)
            ->orLike('LOWER(dmn.name)', $searchterm)
            ->orLike('LOWER(gn.hidden_synonym)', $searchterm)
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