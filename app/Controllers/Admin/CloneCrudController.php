<?php
namespace App\Controllers\Admin;
use App\Controllers\DatatableController;


class CloneCrudController extends DatatableController
{
    
    // implement DatatableController
    protected function get_column_config()
    {
        return [
            
            // [ query-key, result-key, view-label ]
           ["clone.value", "clone", "Clone Name"],
           ["base.name", "grassius_name", "TFome Name"],
           ["base.uniquename", "id_name", "Gene Id[MaizeGDB]"],
           ["clone.value", "clone_edit", "edit"],
           ["clone.value", "clone_delete", "delete"]
        ];
    }
    
    // implement DatatableController
    protected function is_column_searchable( $query_key )
    {
        return true;
    }
    
    // implement DatatableController
    protected function get_base_query_builder()
    {        
        return $this->db->table('feature base')
            ->select("base.feature_id AS feature_id, base.name AS grassius_name, 
                base.uniquename AS id_name, 
                clone.value AS clone, 
                clone.value AS clone_edit, 
                clone.value AS clone_delete,
                obi__organism.genus as genus,
                obi__organism.species as species")
            ->join('featureprop clone', 'base.feature_id = clone.feature_id', 'left')
            ->join('organism obi__organism', 'base.organism_id = obi__organism.organism_id')
            ->where('clone.type_id', 1368);
    }
    
    // implement DatatableController
    protected function prepare_results( $row ) {
        
        $crop = $row["genus"].' '.$row["species"];
        
        return [
            "clone" => get_tfomeinfor_link($row['clone']),
            "grassius_name" => get_proteininfor_link($crop, $row['grassius_name']),
            "id_name" => '<a target = "_blank" href="http://maizegdb.org/gene_center/gene/'.$row['id_name'].'">'.$row['id_name'].'</a>',
            "id_name_gbrowse" => '<a href="Jbrowser/index.html?data=data/'.$crop.'/json&loc=">View in Browser</a>',
            "clone_edit" => '<a href="/clone_admin/edit/'.$row['clone'].'">edit</a>',
            "clone_delete" => '<a href="#" style="color:red">delete</a>',
        ];
    }
    
    // OVERRIDE DatatableController
    // prevent sorting of certain columns
    protected function get_extra_datatable_options(){
        return '"columnDefs": [ { "targets": [3,4],"orderable": false } ]';   
    }

    
    public function index()
    {           
        if( !user_is_admin() ){
            return redirect("/");
        }
        
        $data["title"] = "Manage Clones";
        $data['datatable'] = $this->get_datatable_html("clone_table","/clone_admin/datatable");
        return view("admin/admin_clone_collection", $data);
    }
    
    public function edit( $clone_name )
    {             
        if( !user_is_admin() ){
            return redirect("/");
        }
        
        $sql= get_tfominfor_query();
        
        $query=$this->db->query($sql,[
            'clone_name' => $clone_name
        ]);
        
        $results=$query->getRowArray();
        
        $data['results']=$results;
        $data['template']=$results['template'];
        $data['clone_name'] = $clone_name;
        
        $data['title'] = "Edit clone ".$clone_name;
        return View("admin/admin_clone_edit",$data);
    }
}
