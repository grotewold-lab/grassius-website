<?php
namespace App\Controllers;

/**
 * base-class for controllers that involve displaying a table
 *
 * controllers should implement the 4 abstract methods
 * then add a route to the inherited function datatable()
 * then use the inherited function get_datatable_html() to build html code
 * 
 * https://datatables.net/manual/server-side
 */
abstract class DatatableController extends BaseController
{    
    
    /**
     * column configuration for datatable
     * for n columns, return an array containing n arrays of length 3
     * 
     * each column requires 3 strings
     *      key for sorting/searching (column name in database)
     *      key for query results (column name in result of query)
     *      view label (may contain html)
     */
    abstract protected function get_column_config();
    
    /**
     * return true if the given query-key should be considered when searching
     */
    abstract protected function is_column_searchable( $query_key );

    /**
     * get the base query (select and join statements)
     *
     * return a query builder
     */
    abstract protected function get_base_query_builder();
    
    /**
     * prepare one row of results for display
     *
     * return an array where
     *      keys are query-result headers (column name in result of query)
     *      values are strings to be rendered inside td tags
     * 
     * return the parameter for simple display
     */
    abstract protected function prepare_results( $row );
    
    /**
     * optional. 
     *
     * used in FamilyController.php to disable sorting of certain columns
     */
    protected function get_extra_datatable_options(){
        return "";   
    }
    
    /**
     * ajax handler for server-side processing
     * https://datatables.net/manual/server-side
     */
    public function datatable()
    {
        $r = $this->request;
        $db=$this->db;
        $query_keys = array_map(function($x) {return $x[0];}, $this->get_column_config());

        // get request params
        $draw = $r->getVar('draw');
        $start = intval($r->getVar('start'));
        $length = intval($r->getVar('length'));
        $sort_col_index=intval($r->getVar('order[0][column]'));
        $sort_dir=$r->getVar('order[0][dir]');
        $searchterm = $r->getVar('search[value]');
        
        // translate request params
        $sort_col = $query_keys[$sort_col_index];   
        $sort_dir = ($sort_dir === 'desc' ? 'DESC' : 'ASC');
        
        // query data from database
        $n_total=$this->get_base_query_builder()->countAllResults();
        $query = $this->get_base_query_builder();
        $first=true;
        if( trim($searchterm) !== '' ) {
            $searchterm = strtolower( $searchterm );
            $query = $query->groupStart();
            foreach( $query_keys as $col ) {
                if( $this->is_column_searchable($col) ){
                    if( $first ) {
                        $first = false;
                        $query = $query->like('LOWER('.$col.')',$searchterm);
                    } else {
                        $query = $query->orLike('LOWER('.$col.')',$searchterm);
                    }
                }
            }
            $query = $query->groupEnd();
        }
        $result = $query->orderBy($sort_col, $sort_dir)->get($length,$start)->getResultArray();
        $n_filtered = count($result);
        
        
        // prepare results for display
        $view_result = array();
        foreach ($result as $row) {
            array_push( $view_result, $this->prepare_results($row) );
        }
        
        return json_encode([
            "draw" => $draw,
            "recordsTotal" => $n_total,
            "recordsFiltered" => $n_total,
            "data" => $view_result
        ]);
    }
    
    /**
     * get html code for a DataTable with server-side processing
     *
     * this should be used through controllers that extend DatatableController.
     *
     * @param string $dom_id any valid id for the resulting html table 
     * @param string $ajax_uri should route to the controller's inherited method datatable()
     * 
     * return a string containing an html table and script tag
     */
    function get_datatable_html( $dom_id, $ajax_uri )
    {
        $column_config = $this->get_column_config();
        
        $columns_data = array();
        foreach( $column_config as $row ){
            array_push( $columns_data, [
                "data" => $row[1],
                "title" => $row[2]
            ]);
        }

        $extra_options = $this->get_extra_datatable_options();


        return "
        <table align='center' class='wikitable' style='border-collapse:collapse;' id='$dom_id'>
            <thead></thead>
            <tbody><tbody>
        </table>
        <script>
            var $ = jQuery.noConflict();
            $(document).ready(function(){
                $dom_id = $('#$dom_id').DataTable( {
                    serverSide: true,
                    ajax: '$ajax_uri',
                    columns: ".json_encode($columns_data).",
                    $extra_options
                } );
            })
        </script>";

    }
}
