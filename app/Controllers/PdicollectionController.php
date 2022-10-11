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
           ["reg_dmn.name_sort_order", "reg_protein_order", "Regulator Protein"],
           ["gi.gene_id", "reg_gene", "Regulator Gene"],
           ["gi.target_name", "tar_protein", "Target Protein"],
           ["tar_dmn.name_sort_order", "tar_protein_order", "Target Protein"],
           ["gi.target_id", "tar_gene", "Target Gene"],
           //["gi.pubmed_id", "pubmed", "Publication"],
           //["gi.interaction_type", "type", "Type of Interaction"],
           ["gi.experiment", "exp", "Experiment"],
           ["gi.distance", "dist", "Distance <br>(+ or -) (kb)"],
           ["ABS(gi.distance)", "abs_dist", "Absolute <br>Distance (kb)"]
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
                { "targets": [0,1,2,3,4,5,6,7,8],"orderable": false },
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
                    reg_dmn.name_sort_order AS reg_protein_order,
                    gi.target_id AS tar_gene, 
                    gi.target_name AS tar_protein,
                    tar_dmn.name_sort_order AS tar_protein_order,
                    gi.pubmed_id as pubmed, 
                    gi.interaction_type as type, 
                    gi.experiment as exp,
                    gi.distance as dist,
                    ABS(gi.distance) as abs_dist")
            ->join("public.default_maize_names reg_dmn", "reg_dmn.name = gi.protein_name", 'left')
            ->join("public.default_maize_names tar_dmn", "tar_dmn.name = gi.target_name", 'left');
        
    
        // special cases to support tables on proteininfor page
        if( isset($this->regulator_name) ){
            $result = $result->where('gi.protein_name',$this->regulator_name);
        }
        if( isset($this->target_name) ) {
            $result = $result->where('gi.target_name',$this->target_name);
        }
        
        // support search form on pdicollection page
        if( isset($this->min_dist) ) {
            $result = $result->where('gi.distance >', $this->min_dist);
        }
        if( isset($this->max_dist) ) {
            $result = $result->where('gi.distance <', $this->max_dist);
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
           //"pubmed" => get_pubmed_link($row['pubmed']),
           //"type" => $row['type'],
           "exp" => $row['exp'],
           "dist" => $row['dist'],
           "abs_dist" => $row['abs_dist']
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
        
        // use pre-computed distance histogram
        $data['distance_hist'] = [25684,27161,28781,29605,31217,32876,34066,36533,40070,42699,45029,50522,53622,60583,70214,86623,111147,147615,180585,195155,213999,173141,123612,92430,81538,77591,73343,67632,66844,62203,59230,56499,54032,51138,48734,46096,44060,42895,39610,37693];
            
        /*
        SELECT floor(distance*10)/10 as bin_floor, count(*)
        FROM gene_interaction
        GROUP BY 1
        ORDER BY 1;
        */
            
        
        return view('pdicollection', $data);
    }
    
    // wrap inherited function: datatable()
    // endpoint for route: /pdicollection/filtered_datatable
    // used by search form on pdicollection page
    public function filtered_datatable( $sort_col_index, $sort_dir, $min_dist, $max_dist, $search_term ){        
        $this->sort_col_index = $sort_col_index;
        $this->sort_dir = $sort_dir;
        $this->min_dist = $min_dist;
        $this->max_dist = $max_dist;
        $this->search_term = $search_term;
        return $this->datatable();   
    }
    
    // endpoint for route: /pdicollection/filtered_histogram
    // used to show histogram on pdicolection page
    public function filtered_histogram( $search_term ){  
        $result = [];
        foreach( range(-2,1.95,.1) as $min_dist ) {
            $max_dist = $min_dist + .1;
            $count = get_pdi_distance_histogram_bin( $this->db, $min_dist, $max_dist, $search_term );
            $result[] = $count;
        }
        return json_encode($result);
    }
    
    // wrap inherited function: datatable()
    // endpoint for route: /pdicollection/datatable
    public function default_datatable(){        
        $this->sort_col_index = 8;
        return $this->datatable(); 
    }
    
    // endpoints to download excel sheets with interactions
    // wrap private function "download_pdi_table"
    public function download_table( $sort_col_index, $sort_dir, $min_dist, $max_dist, $search_term ){
        $this->sort_col_index = $sort_col_index;
        $this->sort_dir = $sort_dir;
        $this->min_dist = $min_dist;
        $this->max_dist = $max_dist;
        $this->search_term = $search_term;
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
}
