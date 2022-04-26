<?php
namespace App\Controllers\Admin;
use App\Controllers\DatatableController;


class GeneCrudController extends DatatableController
{
    
    // implement DatatableController
    protected function get_column_config()
    {
        return [
            
           // [ query-key, result-key, view-label ]
           ["base.name", "grassius_name", "Protein Name <br><font color=#ce6301>accepted</font>&#x2F;<font color=#808B96>suggested</font>"],
           ["base.uniquename", "id_name", "Gene Locus"],
           ["base.organism_id", "species", "Species"],
           ["taxrank__family.value", "family", "Family"],
           ["gene_name.synonym", "othername", "Synonym/<br>Gene Name"],
           ["clones", "clones", "Clone in TFome"],
           ["base.feature_id", "fid_edit", "edit"],
           ["base.feature_id", "fid_delete", "delete"],
        ];
    }
    
    // implement DatatableController
    protected function is_column_searchable( $query_key )
    {
        return in_array( $query_key, ["base.name","base.uniquename","gene_name.synonym","taxrank__family.value"] );
    }
    
    // implement DatatableController
    protected function get_base_query_builder()
    {        
        
        return $this->db->table('feature base')
            ->select("base.feature_id AS fid_edit,
                base.feature_id AS fid_delete,
                base.name AS grassius_name,
                base.uniquename AS id_name,
                array_to_string(array(SELECT clone.value FROM featureprop clone WHERE clone.feature_id = base.feature_id AND (clone.type_id = '1368')),', ') AS clones,
                gene_name.synonym as othername,
                gene_name.accepted as accepted,
                CONCAT(obi__organism.genus, ' ', obi__organism.species) as species,
                taxrank__family.value as family")
            ->join('organism obi__organism', 'base.organism_id = obi__organism.organism_id')
            ->join('public.gene_name', 'gene_name.grassius_name = base.name', 'left')
            ->join('featureprop taxrank__class', 'base.feature_id = taxrank__class.feature_id')
            ->join('featureprop taxrank__family', 'base.feature_id = taxrank__family.feature_id')
            ->where('taxrank__class.type_id', 13)
            ->where('taxrank__family.type_id', 1362);
    }
    
    // implement DatatableController
    protected function prepare_results( $row ) {
        if ($row['grassius_name'] === $row['id_name']) {
            $protein_link = "";
        } else {
            $protein_link = get_proteininfor_link($row['species'], $row['grassius_name']);
        }
        
        if ($row['accepted'] === "no"){
            $protein_class = "sugg";
        }else {
            $protein_class = "accpt";
        }
        
       

        return [
           "grassius_name" => "<div class=$protein_class>$protein_link</div>",
           "id_name" => get_external_db_link($row['species'], $row['id_name']),
           "species" => $row['species'],
           "family" => $row["family"],
           "othername" => $row['othername'],
           "clones" => $row['clones'],
           "fid_edit" => '<a href="/gene_admin/edit/'.$row['id_name'].'">edit</a>',
           "fid_delete" => '<a href="#" style="color:red">delete</a>',
        ];
    }
    
    // OVERRIDE DatatableController
    // prevent sorting of certain columns
    protected function get_extra_datatable_options(){
        return '"columnDefs": [ { "targets": [5,6,7], "orderable": false } ]';   
    }

    
    public function index()
    {           
        if( !user_is_admin() ){
            return redirect("/");
        }
        
        $data["title"] = "Manage Genes";
        $data['datatable'] = $this->get_datatable_html("gene_table","/gene_admin/datatable");
        return view("admin/admin_gene_collection", $data);
    }
    
    public function edit($id_name)
    {
        if( !user_is_admin() ){
            return redirect("/");
        }
        
        
        $query = $this->db->table('feature base')
            ->select("base.residues as nucleotidesequence,
                base.uniquename as id_name,
                base.name as protein_name,
                taxrank__family.value as family,
                CONCAT(obi__organism.genus, ' ', obi__organism.species) as species")
            ->join('organism obi__organism', 'base.organism_id = obi__organism.organism_id', 'left')
            ->join("featureprop taxrank__family", "base.feature_id = taxrank__family.feature_id", 'left')
            ->where("taxrank__family.type_id",1362)
            ->where("base.uniquename",$id_name)
            ->get();
                
        $results=$query->getRowArray();
        $data['results']=$results;
        $data['title'] = "Edit Gene";
        
        return view('admin/admin_gene_edit', $data);
    }
}
