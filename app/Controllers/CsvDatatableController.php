<?php
namespace App\Controllers;

/**
 * base-class for controllers that support an on-screen table 
 * with a corresponding csv download button.
 *
 * To develop a new controller, start by extending DatatableController. 
 * (See DatatableController.php)
 *
 * then switch to extending CsvDatatableController, 
 * implement the 3 additional abstract methods below,
 * and add a route to the inherited function download_csv()
 */
abstract class CsvDatatableController extends DatatableController
{    
    
    /**
     * default filename for the user's download
     *
     * return a string ending in ".csv"
     */
    abstract protected function get_csv_download_filename();
    
    
    /**
     * column headers for downloadable csv
     *
     * for n columns, return an array containing n strings
     *
     * output should correspond with the output of 
     * prepare_results_for_csv()
     */
    abstract protected function get_csv_column_headers();
    
    
    /**
     * prepare one row of results for csv download
     *
     * requirements are the same as prepare_results()
     * (See DatatableController.php)
     */
    abstract protected function prepare_results_for_csv( $row );
    
    
    /**
     * Generate a csv file and send it to the user
     */
    public function download_csv()
    {      
        // get config options
        $download_filename = $this->get_csv_download_filename();
        $column_headers = $this->get_csv_column_headers();
        
        // get raw data
        $query = $this->get_base_query_builder();
        $sortcol = $this->get_column_config()[0][0];
        $results=$query->orderBy($sortcol)->get()->getResultArray();
        
        //debug
        //return json_encode( $results );
        
        define('ROOT_DIR', dirname(__FILE__));
        ignore_user_abort(true);
        set_time_limit(0); // disable the time limit for this script

        // write to local file
        $fullPath = WRITEPATH.microtime().".csv";
        $myfile = fopen($fullPath, "w");
        fwrite($myfile, implode(",",$column_headers)."\n" );
        foreach( $results as $row ){
            $prepped_row = $this->prepare_results_for_csv($row);
            fwrite($myfile, implode(",",$prepped_row)."\n");
        }
        fclose($myfile);

        // send to client
        if ($fd = fopen($fullPath, "r")) {
            $fsize = filesize($fullPath);
            $path_parts = pathinfo($fullPath);
            $ext = strtolower($path_parts["extension"]);
            header("Content-type: application/octet-stream");
            header("Content-Disposition: filename=\"$download_filename\"");
            header("Content-length: $fsize");
            header("Cache-control: private"); //use this to open files directly
            while (!feof($fd)) {
                $buffer = fread($fd, 2048);
                echo $buffer;
            }
        }
        fclose($fd);

        // delete local file
        unlink( $fullPath );

        exit;
    }
    
}
