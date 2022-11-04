<?php
namespace App\Controllers;


class TfomecollectionController extends DatatableController
{
    
    // implement DatatableController
    protected function get_column_config()
    {
        if( $this->crop == "Maize" ){            
            return [

                // [ query-key, result-key, view-label ]
               ["gc.clone_name", "clone", "Clone Name"],
               ["dmn.name_sort_order", "name_sort_order", "Protein Name <br><font color=#ce6301>accepted</font>&#x2F;<font color=#808B96>suggested</font>"],
               ["dmn.name", "grassius_name", "Protein Name"],
               ["dmn.v3_id", "v3_id", "Maize v3 ID"],
               ["dmn.v4_id", "v4_id", "Maize v4 ID"],
               ["dmn.v5_id", "v5_id", "Maize v5 ID"],
            ];
            
        } else { // not maize
            return [

                // [ query-key, result-key, view-label ]
               ["base.name", "clone", "Clone Name"]
            ];
            
        }
    }
    
    // implement DatatableController
    protected function is_column_searchable( $query_key )
    {
        // disable searching of the numerical values used for sorting by protein name
        if( $query_key == "dmn.name_sort_order" ){
            return false;
        }
        
        return true;
    }
    
    // implement DatatableController
    protected function get_base_query_builder()
    {
                
        if( $this->crop == "Maize" ){
            return $this->db->table('feature base')
                ->select("gc.clone_name as clone,
                    dmn.name AS grassius_name,
                    dmn.name_sort_order AS name_sort_order,
                    dmn.v3_id AS v3_id,
                    dmn.v4_id AS v4_id,
                    dmn.v5_id AS v5_id,
                    dmn.all_ids AS all_ids,
                    gene_name.accepted as accepted")
                ->join('public.gene_clone gc', 'gc.clone_name = base.uniquename')
                ->join('public.default_maize_names dmn', 'gc.v3_id = dmn.v3_id')
                ->join('public.gene_name', 'gene_name.grassius_name = dmn.name')
                ->join('organism org', 'org.organism_id = base.organism_id' )
                ->where( 'org.common_name', $this->crop );
            
            
        } else { // not maize
            return $this->db->table('feature base')
                ->select("base.name as clone")
                ->join('feature_relationship fr', 'fr.subject_id = base.feature_id AND fr.type_id = 435')
                ->join('organism org', 'org.organism_id = base.organism_id' )
                ->where('org.common_name', $this->crop );
        }
    }
    
    // implement DatatableController
    protected function prepare_results( $row ) {
        
        $crop = $this->crop;
        
        if( $this->crop == "Maize" ){
            $protein_link = get_proteininfor_link($crop, $row['grassius_name']);

            if ($row['accepted'] === "no"){
                $protein_class = "sugg";
            }else {
                $protein_class = "accpt";
            }
            return [
               "clone" => get_tfomeinfor_link($row['clone']),
               "name_sort_order" => "<div class=$protein_class>$protein_link</div>", # visible column
               "grassius_name" => "", # hidden placeholder for searching
               "v3_id" => get_external_db_link($this->species, $row['v3_id']),
               "v4_id" => get_external_db_link($this->species, $row['v4_id']),
               "v5_id" => get_external_db_link($this->species, $row['v5_id']),
            ];
            
        }else{ //not maize
            return [
               "clone" => get_tfomeinfor_link($row['clone'])
            ];
        }
    }
    
    // OVERRIDE DatatableController
    // hide certain columns
    // set width of visible columns
    protected function get_extra_datatable_options(){
        if( $this->crop == "Maize" ){
            return '
                  "columnDefs": [ 
                    { "targets": [2],"visible": false },
                    { "targets": [0,1,3,4,5],"width": "33.3333%" },
                  ],
                ';   
        }else{ //not maize
            return '';   
        }
    }

    
    public function index( $crop )
    {             
        $db = $this->db;
        
        // get species name in two forms 
        $crop = get_basic_species_name($crop);
        $new_species = get_chado_species($crop);
        
        // pass values to inherited functions
        $this->crop = $crop;  
        $this->species = $new_species;
        
        // pass values to view
        if( $crop === "Rice" ) {
            $view_name = 'rice_tfome';
        } else {
            $view_name = 'tfomecollection';
        }
       
        $data['datatable'] = $this->get_datatable_html("tfome_table","/$view_name/datatable");
        $data['crop'] = $crop;
        $data['new_species'] = $new_species;
        $data['title'] = $crop." TFOME Database";
        
        
        # set the initial state of the maize version radio buttons
        $data['species_version'] = $this->get_session_var('Maize_version');
        
        return view($view_name, $data);
    }
    
    
    public function crop_datatable( $crop )
    {        
        // get species name in two forms 
        $this->crop = get_basic_species_name($crop);
        $this->species = get_chado_species($this->crop);
        
        return $this->datatable();
    }
}
