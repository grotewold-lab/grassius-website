<?php
namespace App\Controllers\Topsearch;
use App\Controllers\BaseController;

require_once('search_species.php');
require_once('search_families.php');
require_once('search_genes.php');
require_once('search_clones.php');

class TopsearchController extends BaseController
{
    
    public function search( $searchterm )
    {                
        $searchterm = strtolower( $searchterm );
        $callback = $this->request->getVar('callback');
        $db = $this->db;
        $results = [];
        
        // search for species
        //$results = array_merge($results, search_chado_species( $db, $searchterm) );
        //$results = array_merge($results, search_basic_species( $db, $searchterm) );
        $results = array_merge($results, search_families(       $db, $searchterm) );
        $results = array_merge($results, search_maize_genes(    $db, $searchterm) );
        $results = array_merge($results, search_maizegdb_pages( $db, $searchterm) );
        $results = array_merge($results, search_nonmaize_genes( $db, $searchterm) );
        $results = array_merge($results, search_clones(         $db, $searchterm) );
        
        return $callback."(".json_encode($results).");";
    }
}
