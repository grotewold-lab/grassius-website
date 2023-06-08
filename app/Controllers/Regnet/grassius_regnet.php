<?php


/**
 * GRASSIUS regnet implementation
 *
 * This file contains functions used by RegnetController 
 * when the grassius database is selected
 */

/*
 * sql script to build/update required columns
 * (see RegnetController.php) 
 *

DROP FUNCTION IF EXISTS udp_hash_func(text,text);
CREATE FUNCTION udp_hash_func(text,text) RETURNS bigint AS $$
 SELECT hashtext(upper($1))::bigint+hashtext(upper($2))::bigint;
$$ LANGUAGE SQL;

ALTER TABLE gene_interaction ADD COLUMN IF NOT EXISTS udp_hash bigint;
UPDATE gene_interaction SET udp_hash = udp_hash_func(target_id,gene_id);
CREATE INDEX IF NOT EXISTS udp_hash_idx ON gene_interaction(udp_hash);


ALTER TABLE gene_interaction ADD COLUMN IF NOT EXISTS udp_matches integer;
UPDATE gene_interaction ou SET udp_matches = (
    SELECT count(*) FROM gene_interaction inr
    WHERE inr.udp_hash = ou.udp_hash
);
CREATE INDEX IF NOT EXISTS udp_matches_idx ON gene_interaction(udp_matches);

*/

    
// autocomplete for TF name text field
function autocomplete($context,$request){
    $term = $request->getVar('term');
    $term = strtolower($term);

    $query = $context->db->table('pdi_distance_histograms')
        ->select("value")
        ->where("field", "protein_name")
        ->like("lower(value)", $term )
        ->limit(10)
        ->get();

    $result = [];
    foreach( $query->getResult() as $row ){
        $val = $row->value;
        $result[] = ["id" => $val, "label" => $val, "value" => $val];
    }

    return json_encode($result);
}

// implement DatatableController
function get_column_config()
{
    return [

        // [ query-key, result-key, view-label ]
       ["gi.protein_name", "reg_protein", "Regulator Protein"],
       ["reg_dmn.name_sort_order", "reg_protein_order", "Regulator Protein"],
       ["gi.gene_id", "reg_gene", "Regulator Gene"],
       ["gi.target_name", "tar_protein", "Target Protein"],
       ["tar_dmn.name_sort_order", "tar_protein_order", "Target Protein"],
       ["gi.target_id", "tar_gene", "Target Gene"],
       //["gi.pubmed_id", "pubmed", "Publication"],
       //["gi.interaction_type", "type", "Type of Interaction"],
       ["gi.experiment", "exp", "Experiment"],
       ["gi.distance", "dist", "Distance <br>(+ or -) (kb)"],
       ["ABS(gi.distance)", "abs_dist", "Absolute <br>Distance (kb)"],
       ["gi.gi_id", "edge_id", "edge_id"],
       ["gi.note", "note", "Note"]
    ];
}
    
// implement DatatableController
function is_column_searchable( $query_key )
{
    return in_array( $query_key, 
                    ['gi.protein_name','gi.gene_id',
                     'gi.target_name','gi.target_id'] );
}

// OVERRIDE DatatableController
// hide certain columns
// set default sort order
function get_extra_datatable_options(){
    return '
          "columnDefs": [ 
            { "targets": [0,3],"visible": false },
            { "targets": [0,1,2,3,4,5,6,7,8],"orderable": false },
          ],
    "processing": true,
    "language": {
        processing: \'<span>Loading...</span> \'
        },

        ';   
}
    
// implement DatatableController
function get_base_query_builder($context)
{
    $result = $context->db->table('public.gene_interaction gi')
        ->select("
                gi.gene_id AS reg_gene, 
                gi.protein_name AS reg_protein,
                reg_dmn.name_sort_order AS reg_protein_order,
                gi.target_id AS tar_gene, 
                gi.target_name AS tar_protein,
                tar_dmn.name_sort_order AS tar_protein_order,
                gi.pubmed_id as pubmed, 
                gi.interaction_type as type, 
                gi.experiment as exp,
                gi.distance as dist,
                ABS(gi.distance) as abs_dist,
                gi.udp_hash as udp_hash,
                gi.udp_matches as udp_matches,
                gi.gi_id as edge_id,
                gi.note as note")
        ->join("public.default_maize_names reg_dmn", "reg_dmn.name = gi.protein_name", 'left')
        ->join("public.default_maize_names tar_dmn", "tar_dmn.name = gi.target_name", 'left');


    if( isset($context->protein_name) && ($context->protein_name!='') ){
        $result = $result->groupStart()
            ->where('gi.protein_name',$context->protein_name)
            ->orWhere('gi.target_name', $context->protein_name)
            ->groupEnd();
    }
    if( isset($context->gene_id) && ($context->gene_id!='') ){
        $result = $result->groupStart()
            ->where('gi.gene_id',$context->gene_id)
            ->orWhere('gi.target_id', $context->gene_id)
            ->groupEnd();
    }

    return $result;
}

// get total number of interactions involving the given gene
function get_pdi_count( $context, $gene_id ){
    return $context->db->table('public.gene_interaction gi')
        ->where("gi.gene_id",$gene_id)
        ->orWhere("gi.target_id",$gene_id)
        ->countAllResults();
}


function prepare_results( $row ) {
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

    $edge_id = $row['edge_id'];
    
    return [
       "reg_protein" => "", # hidden placeholder for searching
       "reg_protein_order" => "<div class='edge_id_$edge_id'>$protein_name_1</div>", # visible column
       "reg_gene" => "<div class='edge_id_$edge_id'>".$row['reg_gene']."</div>",
       "tar_protein" => "", # hidden placeholder for searching
       "tar_protein_order" => "<div class='edge_id_$edge_id'>$protein_name_2</div>", # visible column
       "tar_gene" => "<div class='edge_id_$edge_id'>".$row['tar_gene']."</div>",
       //"pubmed" => get_pubmed_link($row['pubmed']),
       //"type" => $row['type'],
       "exp" => $row['exp'],
       "dist" => $row['dist'],
       "abs_dist" => $row['abs_dist'],
        "udp_hash" => $row['udp_hash'],
        "edge_id" => "<div class='edge_id_$edge_id'>$edge_id</div>",
        "note" => $row['note']
    ];
}

// output dictionary to pass to net-vis frontend
function prepare_vis_edge( $row )
{
    $exp = $row['exp'];
    if( $exp == 'DAP' ){
        $color = '#BCD249';
    } elseif( $exp == 'ChIP' ){
        $color = '#41B1D1';
    } elseif( $exp == 'pChIP' ){
        $color = '#D238D1';
    } elseif( $exp == 'Chip-Seq' ){
        $color = '#FD4741';
    } else {
        $color = '#37C241';
    }  
    
    return [
        "gene_id"=> $row['reg_gene'],
        "target_id"=> $row['tar_gene'],
        "distance"=> $row['dist'],
        "experiment"=> $exp,
        "type" => 'NA',
        "conf" => 'NA',
        "udp_hash"=> $row['udp_hash'],
        "edge_id" => $row['edge_id'],
        "color" => $color
    ];
}

// output javascript string
function get_db_specific_js_funcs()
{
    return "
    function draw_edge_legend(ctx,x,y){
        var specs = [
            ['DAP','#BCD249'],
            ['ChIP','#41B1D1'],
            ['pChIP','#D238D1'],
            ['Chip-Seq','#FD4741'],
            ['Other','#37C241'],
        ]
    
        for( var i = 0 ; i < specs.length ; i++ ){
            drawEdge( ctx, {
                a: [x+120,y],
                b: [x+200,y],
                data: [{color:specs[i][1]}]
            }, 1);
            
            ctx.fillStyle = 'black'
            ctx.textAlign = 'left'
            ctx.fillText(specs[i][0]+' Interaction', x, y);
            
            y += 20
        }
    }
    
    function get_node_link_text( node_data ){
        if( isTF(node_data) ){
            return 'View on GRASSIUS 🔗';
        } else {
            return 'View on MaizeGDB 🔗';
        }
    }

    function get_url_for_node( node_data ){
        if( isTF(node_data) ){
            return 'https://grassius.eglab-dev.com/proteininfor/Maize/' + node_data.protein_name
        } else {
            return 'http://maizegdb.org/gene_center/gene/' + node_data.gene_id;   
        }
    }   
    ";
}

    
function is_valid($param_value){
    return ($param_value!='null') and ($param_value!='');
}

function get_distinct_gi_vals( $field_name ){

    $query = $this->db->table('gene_interaction')
        ->select("distinct($field_name)")
        ->get();

    $result = [];
    foreach( $query->getResultArray() as $row ){
        $result[] = $row[$field_name];
    }
    return $result;
}

function get_default_tf_name(){
    return "ZmARF34";   
}

function parse_filter_options( $filter_options ){
    $fo_parts = explode(',',$filter_options);       
    $this->sort_col_index = trim($fo_parts[0]);
    $this->sort_dir = trim($fo_parts[1]);
    $this->min_dist = trim($fo_parts[2]);
    $this->max_dist = trim($fo_parts[3]);
    $this->gene_id = trim($fo_parts[4]);
    $this->target_id = trim($fo_parts[5]);
    $this->protein_name = trim($fo_parts[6]);
    $this->target_name = trim($fo_parts[7]);
    $this->exps = trim($fo_parts[8]);
    if( $this->is_valid($this->exps) ){
        $this->exps = explode( ';', $this->exps);
    }
}
