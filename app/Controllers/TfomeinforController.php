<?php
namespace App\Controllers;


class TfomeinforController extends BaseController
{

    // support old links to tfome information page e.g. http://grassius.org/tfomeinfor.php?clone=pUT1103
    public function legacy_endpoint()
    {
        $query = $_SERVER['QUERY_STRING']; // e.g. clone=pUT1103
        $clone_name = explode('=',$query)[1];
        return $this->index($clone_name);
    }
    
    // tfome information page e.g. http://grassius.org/tfomeinfor/pUT1103
    public function index( $clone_name )
    {                
        $sql= get_tfominfor_query();
        
        $query=$this->db->query($sql,[
            'clone_name' => $clone_name
        ]);
        
        $results=$query->getRowArray();
        
        
        // in case the query failed, stop here (do not attempt to show the view)
        if( is_null($results) ){
            return "unrecognized clone name '$clone_name'";
        }
            
        // prepare link to search abrc using clone name without p (pUT1103 -> UT1103)
        $utname = substr( $clone_name, 1 );
        $data['abrc_url'] = 'https://abrc.osu.edu/stocks?search%5Btaxon%5D=Maize&search%5Bsearch_text%5D='.$utname.'&search%5Bsearch_fields%5D=All';
            
        $data['results']=$results;
        $data['species'] = $results['speciesname'];
        $data['template'] = $results['template'];
        $data['title'] ="Tfome Clone Information";
        $data['clone_name'] = $clone_name;
        $data['proteinsequence'] = $results['translation'];
        
        
        return view('tfomeinfor', $data);
    }
}
