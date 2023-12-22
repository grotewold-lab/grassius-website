<?php
namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
           ["reg_no.sortorder", "reg_protein_order", "Regulator Protein"],
           ["gi.gene_id", "reg_gene", "Regulator Gene"],
           ["gi.target_name", "tar_protein", "Target Protein"],
           ["tar_no.sortorder", "tar_protein_order", "Target Protein"],
           ["gi.target_id", "tar_gene", "Target Gene"],
           //["gi.interaction_type", "type", "Type of Interaction"],
           ["gi.experiment", "exp", "Experiment"],
           ["gi.distance", "dist", "Distance <br>(+ or -) (kb)"],
           ["ABS(gi.distance)", "abs_dist", "Absolute <br>Distance (kb)"],
           ["gi.pubmed_id", "pubmed", "Reference"],
           ["gi.note","note","Note"]
        ];
    }
    
    // implement DatatableController
    protected function is_column_searchable( $query_key )
    {
        return false;
    }
    
    // OVERRIDE DatatableController
    // hide certain columns
    // set default sort order
    protected function get_extra_datatable_options(){
        return '
              "columnDefs": [ 
                { "targets": [0,3],"visible": false },
                { "targets": [0,1,2,3,4,5,6,7,8,9,10],"orderable": false },
              ],
        "processing": true,
        "language": {
            processing: \'<span>Loading...</span> \'
            },
 
            ';   
    }
    
    // implement DatatableController
    protected function get_base_query_builder()
    {
        $result = $this->db->table('public.gene_interaction gi')
            ->select("
                    gi.gene_id AS reg_gene, 
                    gi.protein_name AS reg_protein,
                    reg_no.sortorder AS reg_protein_order,
                    gi.target_id AS tar_gene, 
                    gi.target_name AS tar_protein,
                    tar_no.sortorder AS tar_protein_order,
                    gi.pubmed_id as pubmed, 
                    gi.interaction_type as type, 
                    gi.experiment as exp,
                    gi.distance as dist,
                    ABS(gi.distance) as abs_dist,
                    gi.note as note")
            ->join("public.default_maize_names reg_dmn", "reg_dmn.name = gi.protein_name", 'left')
            ->join("public.default_maize_names tar_dmn", "tar_dmn.name = gi.target_name", 'left')
            ->join('name_orders reg_no', 'reg_no.name = reg_dmn.name')
            ->join('name_orders tar_no', 'tar_no.name = tar_dmn.name');
        
    
        // special cases to support tables on proteininfor page
        if( isset($this->regulator_name) and $this->is_valid($this->regulator_name) ){
            $result = $result->where('gi.protein_name',$this->regulator_name);
        }
        if( isset($this->target_name) and $this->is_valid($this->target_name) ) {
            $result = $result->where('gi.target_name',$this->target_name);
        }
        
        // support search form on pdicollection page
        if( isset($this->min_dist) ) {
            $result = $result->groupStart()
                ->where('gi.distance >', $this->min_dist)
                ->orWhere('gi.distance IS NULL', null, false)
                ->groupEnd();
        }
        if( isset($this->max_dist) ) {
            $result = $result->groupStart()
                ->where('gi.distance <', $this->max_dist)
                ->orWhere('gi.distance IS NULL', null, false)
                ->groupEnd();
        }
        if( isset($this->sort_col_index) ){
            $sort_col = $this->get_column_config()[$this->sort_col_index][0];
            if( isset($this->sort_dir) ){
                $sort_dir = ($this->sort_dir === 'DESC' ? 'DESC' : 'ASC');
            } else {
                $sort_dir = "ASC";   
            }
            $result = $result->orderBy($sort_col, $sort_dir);
        }
        /*
        if( isset($this->search_term) and (trim($this->search_term)!='') ){
            $term = trim(strtolower($this->search_term));
            if( ($term!='null') and ($term!='') ){
                $result = $result
                    ->groupStart()
                    ->Like("LOWER(gi.gene_id)", $term )
                    ->orLike("LOWER(gi.protein_name)", $term )
                    ->orLike("LOWER(gi.target_id)", $term )
                    ->orLike("LOWER(gi.target_name)", $term )
                    ->orLike("LOWER(gi.experiment)", $term )
                    ->groupEnd();
            }
        }
        */
        if( isset($this->gene_id) and $this->is_valid($this->gene_id) ){
            $result = $result->where('gi.gene_id',$this->gene_id);
        }
        if( isset($this->protein_name) and $this->is_valid($this->protein_name) ) {
            $result = $result->where('gi.protein_name',$this->protein_name);
        }
        if( isset($this->target_id) and $this->is_valid($this->target_id) ) {
            $result = $result->where('gi.target_id',$this->target_id);
        }
        if( isset($this->target_name) and $this->is_valid($this->target_name) ) {
            $result = $result->where('gi.target_name',$this->target_name);
        }
        if( isset($this->exps) and $this->is_valid($this->exps) ) {
            $result = $result->whereIn('gi.experiment',$this->exps);
        }
        
        return $result;
    }
    
    private function is_valid($param_value){
        return ($param_value!='null') and ($param_value!='');
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
           "pubmed" => get_pubmed_link($row['pubmed'],true),
           //"type" => $row['type'],
           "exp" => $row['exp'],
           "dist" => $row['dist'],
           "abs_dist" => $row['abs_dist'],
           "note" => $row['note'],
        ];
    }
    
    
    // render view for route: /pdicollection
    public function pdicollection_page()
    {                
        $db=$this->db;
        
        $data['title'] ="PDI Collection";
        $data['datatable'] = $this->get_datatable_html("pdi_table","/pdicollection/datatable");
        
        // prepare options for search form
        $data['sort_options'] = array_map(function($x) {return $x[2];}, $this->get_column_config());
        
        $data['exp_types'] = $this->get_distinct_gi_vals('experiment');
        
        // use pre-computed distance histogram
        //$hist = [25684,27411,28215,29921,30898,32876,34066,36145,40061,42683,45029,50011,54133,59924,70057,85703,110757,147295,180330,191293,213999,173141,123612,92430,80754,77591,72626,68349,66195,62221,59230,56499,53501,51191,48760,46096,44060,42509,39635,37715];
        //$data['distance_hist'] = $hist;
        //$data['distance_hist_n'] = $this->total($hist);
        /*
        SELECT floor((distance+1)*20) as bin_floor, count(*)
        FROM gene_interaction
        GROUP BY 1
        ORDER BY 1;
        */
        
        
        
        return view('pdicollection', $data);
    }
    
    private function get_distinct_gi_vals( $field_name ){
        
        $query = $this->db->table('gene_interaction')
            ->select("distinct($field_name)")
            ->get();
        
        $result = [];
        foreach( $query->getResultArray() as $row ){
            $result[] = $row[$field_name];
        }
        return $result;
    }

    // autocomplete for search form
    public function autocomplete($field_name)
    {
        $r = $this->request;
        $term = $r->getVar('term');
        $term = strtolower($term);
        
        $query = $this->db->table('gene_interaction')
                ->select("DISTINCT($field_name) AS name")
                ->like("LOWER($field_name)", $term )
                ->orderBy($field_name)
                ->limit(10)
                ->get();
        
        $result = [];
        foreach( $query->getResultArray() as $row ){
            $name = $row["name"];
            $result[] = ["id" => $name, "label" => $name, "value" => $name];
        }

        return json_encode($result);
    }
    
    private function parse_filter_options( $filter_options ){
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
    
    // wrap inherited function: datatable()
    // endpoint for route: /pdicollection/filtered_datatable
    // used by search form on pdicollection page
    public function filtered_datatable( $filter_options ){ 
        $this->parse_filter_options($filter_options);
        return $this->datatable();   
    }
    
    // endpoint for route: /pdicollection/filtered_histogram
    // used to show histogram on pdicolection page
    public function filtered_histogram( $filter_options ){  
        $this->parse_filter_options($filter_options);

        $result = [];
        
        if( isset($this->gene_id) and $this->is_valid($this->gene_id) ){
            $hist = $this->get_sub_hist( 'gene_id', $this->gene_id );
            $n = $this->total($hist);
            $result[] = [ "Reg Gene = ".$this->gene_id." (n=$n)", $hist ];
        }
        if( isset($this->protein_name) and $this->is_valid($this->protein_name) ) {
            $hist = $this->get_sub_hist( 'protein_name', $this->protein_name );
            $n = $this->total($hist);
            $result[] = [ "Reg Protein = ".$this->protein_name." (n=$n)",$hist ];
        }
        if( isset($this->target_id) and $this->is_valid($this->target_id) ) {
            $hist = $this->get_sub_hist( 'target_id', $this->target_id );
            $n = $this->total($hist);
            $result[] = [ "Trg Gene = ".$this->target_id." (n=$n)",$hist ];
        }
        if( isset($this->target_name) and $this->is_valid($this->target_name) ) {
            $hist = $this->get_sub_hist( 'target_name', $this->target_name );
            $n = $this->total($hist);
            $result[] = [ "Trg Protein = ".$this->target_name." (n=$n)",$hist ];
        }
        
        if( isset($this->exps) and $this->is_valid($this->exps) ) {
            $exp_hists = [];
            foreach( $this->exps as $exp ){
                $exp_hists[] = $this->get_sub_hist( 'experiment', $exp );
            }
            $hist = $this->sum_hists( $exp_hists );
            $n = $this->total($hist);
            $result[] = [ "Experiment = ".implode(',',$this->exps)." (n=$n)",$hist  ];
        }
        
        return json_encode($result);
    }
    
    private function total( $hist ){
        $result = 0;
        foreach( $hist as $freq ){
            $result += $freq;
        }
        return number_format($result);
    }
    
    private function sum_hists( $hists ){ 
        $result = null;
        foreach( $hists as $h ){
            if( is_null($result) ){
                $result = $h;
            } else {
                for( $i = 0 ; $i < count($result) ; $i++ ){
                    $result[$i] += $h[$i];   
                }
            }
        }
        return $result;
    }
                   
    private function get_sub_hist( $field, $value ){
        $query = $this->db->table('pdi_distance_histograms')
            ->select("histogram")
            ->where("field", $field)
            ->where("value", $value)
            ->get();
        $result = $query->getResultArray();
        if( count($result) == 0 ){
            return [];
        } else {
            return json_decode($result[0]['histogram']);
        }
    }
    
    // wrap inherited function: datatable()
    // endpoint for route: /pdicollection/datatable
    public function default_datatable(){        
        $this->sort_col_index = 8;
        return $this->datatable(); 
    }
    
    // endpoints to download excel sheets with interactions
    // wrap private function "download_pdi_table"
    public function download_table( $filter_options ){
        $this->parse_filter_options($filter_options);
        return $this->download_pdi_table();
    }
    
    // serve an excel sheet containining interactions
    private function download_pdi_table()
    {
        define('ROOT_DIR', dirname(__FILE__));
        ignore_user_abort(true);
        set_time_limit(0); // disable the time limit for this script

        // do query
        $query = $this->get_base_query_builder()->get();
        $result = $query->getResultArray();

        // start writing local excel sheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // insert data
        $sheet->fromArray($result,NULL,'A2');

        // add human-readable formatting
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        
        // insert headers corresponding with the order of query results
        // PdicollectionController->get_base_query_builder
        $headers = array("Regulator Gene","Regulator Protein","Regulator sort order","Target Gene","Target Protein","Target sort order","PubMed ID","Type","Experiment","Distance","Abs Distance");
        $sheet->fromArray($headers,NULL,'A1');
        $spreadsheet->getActiveSheet()->getStyle('A1:K1')->applyFromArray($styleArray);
        foreach (str_split("ABCDEFGHIJK") as $char) {
            $spreadsheet->getActiveSheet()->getColumnDimension($char)->setWidth(20);
        }
        
        // remove columns that are not meaningful to users
        $spreadsheet->getActiveSheet()->removeColumn("F");
        $spreadsheet->getActiveSheet()->removeColumn("C");
            
        // write to local file
        $fullPath = WRITEPATH."interactions_".microtime().".xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save($fullPath);


        // send to client
        if ($fd = fopen($fullPath, "r")) {
            $fsize = filesize($fullPath);
            $path_parts = pathinfo($fullPath);
            $ext = strtolower($path_parts["extension"]);
            switch ($ext) {
            case "pdf":
                header("Content-type: application/pdf");
                header("Content-Disposition: attachment; filename=\"interactions.xlsx\""); // use 'attachment' to force a file download
                break;
                // add more headers for other content types here
            default;
                header("Content-type: application/octet-stream");
                header("Content-Disposition: filename=\"interactions.xlsx\"");
                break;
            }
            header("Content-length: $fsize");
            header("Cache-control: private"); //use this to open files directly
            while (!feof($fd)) {
                $buffer = fread($fd, 2048);
                echo $buffer;
            }
        }
        fclose($fd);

        // delete local excel sheet
        unlink( $fullPath );

        exit;
    }
    
    // endpoint for route: /pdicollection/get_vis_json
    public function get_vis_json($filter_options){
        $this->parse_filter_options($filter_options);
        $query = $this->get_base_query_builder()->orderBy('gi.protein_name')->limit(10)->get();
        $result = $query->getResultArray();
        
        $all_gids = [];
        $nodes = [];
        $edges = [];
        foreach( $result as $row ){
            
            // append new nodes if necessary
            foreach( ['reg_','tar_'] as $prefix ){
                $gid_col = $prefix."gene";
                $new_node = null;
                if( !in_array($row[$gid_col],$all_gids) ){
                    $all_gids[] = $row[$gid_col];
                    $new_node = [
                        "gene_id"=>$row[$gid_col]
                    ];
                }
                if( !is_null($new_node) ){
                    $name = $row[$prefix."protein"];
                    if( strlen($name)>0 ){
                        $new_node['protein_name'] = $name;
                    }
                    $nodes[] = $new_node;
                }
            }
            
            // append new edge
            $edges[] = [
                "gene_id"=> $row['reg_gene'],
                "target_id"=> $row['tar_gene'],
                "distance"=> $row['dist'],
                "experiment"=> $row['exp'],
                "support" => 1
            ];
            
        }
        
        return json_encode(["nodes"=>$nodes,"edges"=>$edges]);
    }
    
    // endpoint for route: /pdicollection/visual
    public function visual(){
        $data['title'] = 'Test';
        return view('pdivisual', $data);
    }
}
