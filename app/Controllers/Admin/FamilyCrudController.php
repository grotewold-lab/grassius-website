<?php
namespace App\Controllers\Admin;
use App\Controllers\DatatableController;


class FamilyCrudController extends DatatableController
{
    
    // implement DatatableController
    protected function get_column_config()
    {
        return [
            
            // [ query-key, result-key, view-label ]
           ["taxrank__family.value", "family", "Family"],
           ["taxrank__class.value", "class", "Class"],
           ["taxrank__family.value", "family_edit", "edit"],
           ["taxrank__family.value", "family_delete", "delete"]
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
            ->select("
                taxrank__family.value as family,
                taxrank__class.value as class")
            ->join('featureprop taxrank__class', 'base.feature_id = taxrank__class.feature_id')
            ->join('featureprop taxrank__family', 'base.feature_id = taxrank__family.feature_id')
            ->join('organism obi__organism', 'base.organism_id = obi__organism.organism_id')
            ->where('taxrank__class.type_id', 13)
            ->where('taxrank__family.type_id',1362)
            ->distinct();
    }
    
    // implement DatatableController
    protected function prepare_results( $row ) {        
        return [
            "family" => $row["family"],
            "class" => $row["class"],
            "family_edit" => '<a href="/family_admin/edit/'.$row["family"].'">edit</a>',
            "family_delete" => '<a href="#" style="color:red">delete</a>',
        ];
    }
    
    // OVERRIDE DatatableController
    // prevent sorting of certain columns
    protected function get_extra_datatable_options(){
        return '"columnDefs": [ { "targets": [2,3],"orderable": false } ]';   
    }

    
    public function index()
    {           
        if( !user_is_admin() ){
            return redirect("/");
        }
        
        $data["title"] = "Manage Families";
        $data['datatable'] = $this->get_datatable_html("family_table","/family_admin/datatable");
        return view("admin/admin_family_collection", $data);
    }
    
    public function edit($family)
    {           
        if( !user_is_admin() ){
            return redirect("/");
        }
        
        $query = $this->db->table('feature base')
            ->select("
                taxrank__family.value as family,
                taxrank__class.value as class")
            ->join('featureprop taxrank__class', 'base.feature_id = taxrank__class.feature_id')
            ->join('featureprop taxrank__family', 'base.feature_id = taxrank__family.feature_id')
            ->join('organism obi__organism', 'base.organism_id = obi__organism.organism_id')
            ->where('taxrank__class.type_id', 13)
            ->where('taxrank__family.type_id',1362)
            ->where('taxrank__family.value', $family)
            ->get();
                
        $results=$query->getRowArray();
        $data['results']=$results;
        $data['title'] = "Edit Family";
        
        return view('admin/admin_family_edit', $data);
    }
    
}
