<?php
    
function search_clones( $db, $searchterm )
{
    
    $query = $db->table('feature base')
                ->select("base.name as clone")
                ->join('feature_relationship fr', 'fr.subject_id = base.feature_id AND fr.type_id = 435')
                ->like('LOWER(base.name)', $searchterm)
                ->orderBy('base.name');
    
    /*    
    $query = $db->table('public.gene_clone gc')
            ->join("default_maize_names dmn", "dmn.v3_id = gc.v3_id")
            ->join("gene_name gn", "gn.grassius_name = dmn.name")
            ->select("gc.clone_name AS clone")
            ->where("gc.v3_id !=", "")
            ->groupStart()
                ->like('LOWER(gc.clone_name)', $searchterm)
                ->orLike('LOWER(dmn.name)', $searchterm)
                ->orLike('LOWER(gn.hidden_synonym)', $searchterm)
            ->groupEnd()
            ->orderBy('gc.clone_name');
    */
        
    //debug
    //file_put_contents(WRITEPATH.'/debug.txt', "\n\nsearch_clones query:\n".$query->getCompiledSelect(false)."\n\n", FILE_APPEND);
    
    $query = $query->limit(10)->get();
    
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