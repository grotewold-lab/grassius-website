<?php
namespace App\Controllers;

/**
 * handles the /pdicollection page
 *
 * also inherited by ProteininforController
 * to show interaction tables on /proteininfor page
 */
class PdicollectionController extends DatatableController
{
    
    // implement DatatableController
    protected function get_column_config()
    {
        return [
            
            // [ query-key, result-key, view-label ]
           ["gi.protein_name", "reg_protein", "Regulator Protein"],
           ["reg_dmn.name_sort_order", "reg_protein_order", "Regulator Protein"],
           ["gi.gene_id", "reg_gene", "Regulator Gene"],
           ["gi.target_name", "tar_protein", "Target Protein"],
           ["tar_dmn.name_sort_order", "tar_protein_order", "Target Protein"],
           ["gi.target_id", "tar_gene", "Target Gene"],
           ["gi.pubmed_id", "pubmed", "Publication"],
           ["gi.interaction_type", "type", "Type of Interaction"],
           ["gi.experiment", "exp", "Experiment"]
        ];
    }
    
    // implement DatatableController
    protected function is_column_searchable( $query_key )
    {
        // disable searching of the hidden numerical values
        if( in_array($query_key, ["reg_dmn.name_sort_order","tar_dmn.name_sort_order","gi.pubmed_id"]) ){
            return false;
        }
           
        return true;
    }
    
    // OVERRIDE DatatableController
    // hide certain columns
    // set default sort order
    protected function get_extra_datatable_options(){
        return '
              "columnDefs": [ 
                { "targets": [0,3],"visible": false }
              ],
              "order": [[ 4, "asc" ]]
            ';   
    }
    
    // implement DatatableController
    protected function get_base_query_builder()
    {
        $result = $this->db->table('public.gene_interaction gi')
            ->select("
                    gi.gene_id AS reg_gene, 
                    gi.protein_name AS reg_protein,
                    reg_dmn.name_sort_order AS reg_protein_order,
                    gi.target_id AS tar_gene, 
                    gi.target_name AS tar_protein,
                    tar_dmn.name_sort_order AS tar_protein_order,
                    gi.pubmed_id as pubmed, 
                    gi.interaction_type as type, 
                    gi.experiment as exp")
            ->join("public.default_maize_names reg_dmn", "reg_dmn.name = gi.protein_name", 'left')
            ->join("public.default_maize_names tar_dmn", "tar_dmn.name = gi.target_name", 'left');
        
    
        // special cases to support tables on proteininfor page
        if( isset($this->regulator_name) ){
            $result = $result->where('gi.protein_name',$this->regulator_name);
        }
        if( isset($this->target_name) ) {
            $result = $result->where('gi.target_name',$this->target_name);
        }
        
        return $result;
    }
    
    // implement DatatableController
    protected function prepare_results( $row ) {
        if ($row['reg_protein'] === $row['reg_gene']) {
            $protein_name_1 = "";
        } else {
            $protein_name_1 = $row['reg_protein'];
        }

        if ($row['tar_protein'] === $row['tar_gene']) {
            $protein_name_2 = "";
        } else {
            $protein_name_2 = $row['tar_protein'];
        }

        // assume the species is always maize
        $species = 'Maize';
        
        return [
           "reg_protein" => "", # hidden placeholder for searching
           "reg_protein_order" => get_proteininfor_link($species, $protein_name_1), # visible column
           "reg_gene" => get_external_db_link($species, $row['reg_gene']),
           "tar_protein" => "", # hidden placeholder for searching
           "tar_protein_order" => get_proteininfor_link($species, $protein_name_2), # visible column
           "tar_gene" => get_external_db_link($species, $row['tar_gene']),
           "pubmed" => get_pubmed_link($row['pubmed']),
           "type" => $row['type'],
           "exp" => $row['exp']
        ];
    }
    
    
    // render view for route: /pdicollection
    public function pdicollection_page()
    {                
        $db=$this->db;

        $data['distinct_bases']=$db->query("
            SELECT gene_id,COUNT(gene_id) 
            FROM public.gene_interaction 
            GROUP BY gene_id 
            ORDER BY COUNT(gene_id) DESC
            ")->getResult();

        $data['distinct_types']=$db->query("
            SELECT interaction_type,COUNT(interaction_type) 
            FROM public.gene_interaction 
            GROUP BY interaction_type 
            ORDER BY COUNT(interaction_type) DESC
            ")->getResult();

        $data['distinct_exps']=$db->query("
            SELECT experiment,COUNT(experiment) 
            FROM public.gene_interaction 
            GROUP BY experiment 
            ORDER BY COUNT(experiment) DESC
            ")->getResult();

        
        $n_total= $this->get_base_query_builder()->countAllResults();
        
        
        $data['title'] ="PDI Collection";
        $data['n_total'] = $n_total;
        $data['datatable'] = $this->get_datatable_html("pdi_table","/pdicollection/datatable");
        
        return view('pdicollection', $data);
    }
}
