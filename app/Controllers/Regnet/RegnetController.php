<?php
namespace App\Controllers\Regnet;
use App\Controllers\DatatableController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * handles the /regnet page
 *
 * database-specific function implementations are loaded from 
 * sibling files: *_regnet.php
 *
 * Required columns for all databases:
 *
 * "udp_hash" (undirected pair hash),
 * with values produced by case-insensitive symetric hash function 
 * taking the two gene IDs as input, so f(tf,target) == f(target,tf)
 *
 * "udp_matches"
 * integer, number of entries with matching udp_hash
 *
 * Queries to rebuild required columns should be contained in
 * comments in database-specific implementation files
 */
class RegnetController extends DatatableController
{

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        
        // load database-specific functions from another file
        require_once('grassius_regnet.php');
        
        $this->column_config = $this->get_column_config();
    }
    
    // implement DatatableController
    protected function get_column_config()
    {
        return get_column_config();
    }
    
    // implement DatatableController
    protected function is_column_searchable( $query_key )
    {
        return is_column_searchable( $query_key );
    }
    
    // OVERRIDE DatatableController
    // hide certain columns
    // set default sort order
    protected function get_extra_datatable_options()
    {
        return get_extra_datatable_options();
    }
    
    // implement DatatableController
    protected function get_base_query_builder()
    {
        return get_base_query_builder($this);
    }
    
    // get the total number of PDIs involving the given gene ID
    private function get_pdi_count($gene_id)
    {
        return get_pdi_count($this,$gene_id);
    }
    
    public function is_valid($param_value){
        return ($param_value!='null') and ($param_value!='');
    }
    
    // implement DatatableController
    protected function prepare_results( $row ) {
        return prepare_results($row);
    }

    
    // render view for route: /pdinetwork
    public function index()
    {        
        $data['title'] = ":: GRASSIUS PDI Network ::";
        
        //$data['exp_types'] = $this->get_distinct_gi_vals('experiment');
        $data['default_tf_name'] = get_default_tf_name(); 
        
        $data['db_specific_js_funcs'] = get_db_specific_js_funcs();
        
        return view('regnet', $data);
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

    // autocomplete for TF name text field
    public function autocomplete()
    {
        return autocomplete($this,$this->request);
    }
    
    private function parse_filter_options( $filter_options ){
        $fo_parts = explode(',',$filter_options);       
        $this->sort_col_index = trim($fo_parts[0]);
        $this->sort_dir = trim($fo_parts[1]);
        $this->gene_id = trim($fo_parts[2]);
        $this->protein_name = trim($fo_parts[3]);
        $this->type = trim($fo_parts[4]);
        $this->conf = trim($fo_parts[5]);
    }
    
    // wrap inherited function: datatable()
    // endpoint for route: /regcollection/filtered_datatable
    // used by search form on regnet page
    public function filtered_datatable( $filter_options ){ 
        $this->parse_filter_options($filter_options);
        return $this->datatable();   
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
    
    // endpoint for route: /regnet/get_vis_json
    public function get_vis_json($filter_options, $draw_num){
        $this->parse_filter_options($filter_options);
        $query = $this->get_base_query_builder()->orderBy('udp_matches desc');
        
        //debug
        file_put_contents(WRITEPATH.'/debug.txt', "\n\nnet-vis query:\n".$query->getCompiledSelect(false)."\n\n", FILE_APPEND);
        
        $result = $query->get(10,intval($draw_num)*10)->getResultArray();
        
        $all_gids = [];
        $nodes = [];
        $edges = [];
        foreach( $result as $row ){
            
            // append new nodes if necessary
            foreach( ['reg_','tar_'] as $prefix ){
                $gid_col = $prefix."gene";
                $new_node = null;
                if( !in_array($row[$gid_col],$all_gids) ){
                    $gid = $row[$gid_col];
                    $all_gids[] = $gid;
                    $new_node = [
                        "gene_id"=>$gid,
                        "pdi_count"=>$this->get_pdi_count($gid)
                    ];
                }
                if( !is_null($new_node) ){
                    $name = $row[$prefix."protein"];
                    if( strlen($name)>0 and (strtolower($name) != strtolower($gid)) ){
                        $new_node['protein_name'] = $name;
                    }
                    $nodes[] = $new_node;
                }
            }
            
            // append new edge
            $edges[] = prepare_vis_edge($row);
            
        }
        
        return json_encode(["nodes"=>$nodes,"edges"=>$edges]);
    }
}
