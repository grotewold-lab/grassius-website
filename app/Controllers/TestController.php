<?php
namespace App\Controllers;


class TestController extends HomeController
{
    
    public function test()
    {                
        
        // Dec 21: get a list of gene ids that do not have uniprot ids
        $query = $this->db->table('feature base')
            ->select("base.uniquename, uniprot.uniprot_id")
            ->join('organism obi__organism', 'base.organism_id = obi__organism.organism_id')
            ->join('public.gene_name', 'gene_name.grassius_name = base.name', 'left')
            ->join('featureprop taxrank__class', 'base.feature_id = taxrank__class.feature_id')
            ->join('featureprop taxrank__family', 'base.feature_id = taxrank__family.feature_id')
            ->join('public.uniprot_ids uniprot', 'base.uniquename = uniprot.gene_id', "left")
            ->where('taxrank__class.type_id', 13)
            ->where('taxrank__family.type_id', 1362)
            ->where('uniprot.uniprot_id IS NULL')
            ->get();
        
        //$result = $query->getResultArray();
        //$result = [];
        
        foreach( $query->getResultArray() as $row ) {
            $name = $row['uniquename'];
            //if( strpos($name, 'Bradi') === 0 ){
                echo $name.'<br>';
            //}
            //$result[] = $row['uniquename'];
        }
        
        //return json_encode($result);
        
        
        
        
        
        // Dec 22: for each gene, get comma-separated list of clones
        /*
        $query = $this->db->table('feature base')
            ->select("base.uniquename as feature_uniquename,
                array_to_string(array(SELECT clone.value FROM featureprop clone WHERE clone.feature_id = base.feature_id AND (clone.type_id = '1368')),', ') AS clone_list")
            ->join('featureprop taxrank__class', 'base.feature_id = taxrank__class.feature_id')
            ->join('featureprop taxrank__family', 'base.feature_id = taxrank__family.feature_id')
            ->where('taxrank__class.type_id', 13)
            ->where('taxrank__family.type_id', 1362)
            ->get();
        
        //$result = $query->getResultArray();
        $result = "";
        $i = 1;
        foreach( $query->getResultArray() as $row ) {
            $result .= "(".$i.",'".$row['feature_uniquename']."','".$row['clone_list']."'),";
            $i += 1;
        }
        return json_encode($result);
        */
        
        
        
        
        
        // return View("test");
        
        //$report = $this->_test_pdi_query();
        
        // show home page with test report
        //$this->session->setFlashdata('message', $report);
        //return $this->index();
    }
    
    
 
    /**
     * get or validate the hash of the result of the given query
     * equivalent queries will always get the same hash
     */
    private function _check_result_hash( $query, $expected_hash=null )
    {
        $result = $query->getResultArray();
        
        if( count($result) == 0 ){
            $query = (string)($this->db->getLastQuery());
            throw new \Exception("invalid test! result is empty for query: ".$query);
        }
        
        $hash = md5(serialize($result));
        
        if( !is_null($expected_hash) ) {
            if($hash !== $expected_hash){
                throw new \Exception("wrong hash");
            }
        }
        
        return $hash;
    }
    
    private function _test_pdi_query()
    {
        $db = $this->db;
        
        //$sql = get_pdi_query( $paging=FALSE, $searching=FALSE );
        //$query=$db->query($sql);
        $query = get_pdi_query_builder($db)->orderBy('gi.gi_id')->get();
        //return json_encode($query->getResultArray());
        //return (string)($db->getLastQuery());
        $this->_check_result_hash($query, "b9223365fe9239c78402e644e962bade");
        
        
        
        //$sql = get_pdi_query( $paging=TRUE,  $searching=FALSE );
        //$query=$db->query($sql,[
        //    'start' => 10,
        //    'end' => 20
        //]);
        $query = get_pdi_query_builder($db)
            ->orderBy('gi.gi_id')->get(20,10);
        //return json_encode($query->getResultArray());
        //return (string)($db->getLastQuery());
        $this->_check_result_hash($query, "98cc61576dada405ff4a77b84b1f3de3");
        
        
        
        //$sql = get_pdi_query( $paging=FALSE, $searching=TRUE  );
        //$query=$db->query($sql,[
        //    'searchterm' => 'GRMZM2G419239'
        //]);
        $query = get_pdi_query_builder($db)
            ->where('gi.gene_id','GRMZM2G419239')->orWhere('gi.target_id','GRMZM2G419239')
            ->orderBy('gi.gi_id')->get();
        //return json_encode($query->getResultArray());
        //return (string)($db->getLastQuery());
        $this->_check_result_hash($query, "b6e2cd060ef991b2b34485f73362ac41");
        
        
        
        //$sql = get_pdi_query( $paging=TRUE, $searching=TRUE );
        //$query=$db->query($sql,[
        //    'start' => 10,
        //    'end' => 20,
        //    'searchterm' => 'GRMZM2G419239'
        //]);
        $query = get_pdi_query_builder($db)
            ->where('gi.gene_id','GRMZM2G419239')->orWhere('gi.target_id','GRMZM2G419239')
            ->orderBy('gi.gi_id')->get(20,10);
        $this->_check_result_hash($query, "d2aee9aec11bacebd4fcb005cf1f497a");
        
        
        
        return "test passed!";
    }
}
