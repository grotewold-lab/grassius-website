<?php
    
function search_genes( $db, $searchterm )
{
    // base query (like FamilyController)
    $query = $db->table('feature base')
            ->select("base.name AS grassius_name,
                CONCAT(obi__organism.genus, ' ', obi__organism.species) as speciesname,
                dmn.name_sort_order as sort_order")
            ->distinct()
            ->join('organism obi__organism', 'base.organism_id = obi__organism.organism_id')
            ->join('public.gene_name', 'gene_name.grassius_name = base.name', 'left')
            ->join('public.searchable_clones', 'searchable_clones.name = base.name', 'left')
            ->join('featureprop taxrank__class', 'base.feature_id = taxrank__class.feature_id')
            ->join('featureprop taxrank__family', 'base.feature_id = taxrank__family.feature_id')
            ->join("public.default_maize_names dmn", "dmn.name = base.name")
            ->where('taxrank__class.type_id', 13)
            ->where('taxrank__family.type_id', 1362);
        
        
    // search multiple fields in query (like DatatableController)
    $searchable_keys = ["base.name","base.uniquename","searchable_clones.clone_list",
                        "gene_name.synonym","gene_name.hidden_synonym"];
    $query = $query->groupStart();
    $first = true;
    foreach( $searchable_keys as $col ) {
        if( $first ) {
            $first = false;
            $query = $query->like("LOWER($col)",$searchterm);
        } else {
            $query = $query->orLike("LOWER($col)",$searchterm);
        }
    }
    $query = $query->groupEnd();
    
    // finish query
    $query = $query->orderBy("sort_order");
    
    //debug
    //file_put_contents(WRITEPATH.'/debug.txt', "\n\nsearch_genes query:\n".$query->getCompiledSelect(false)."\n\n", FILE_APPEND);
    
    $query = $query->limit(10)->get();
    
    
    //collect results
    $result = $query->getResultArray();
    $searchresults = [];
    foreach ($result as $row) {
        $searchresults[] = [
            "category" => "Gene",
            "label" => $row['grassius_name'], 
            "value" => '/proteininfor/'.$row['speciesname'].'/'.$row['grassius_name']
        ];
    }
    return $searchresults;
}