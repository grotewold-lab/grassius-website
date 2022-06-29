<?php
namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProteininforController extends PdicollectionController
{    
    
    // API endpoints to support front-end tables
    // wrap inherited function "datatable"
    public function datatable_filter_by_regulator( $regulator_name ){
        $this->regulator_name = $regulator_name;
        $this->target_name = NULL;
        return $this->datatable();
    }
    public function datatable_filter_by_target( $target_name ){
        $this->regulator_name = NULL;
        $this->target_name = $target_name;
        return $this->datatable();
    }
    
    // endpoints to download excel sheets with interactions
    // wrap private function "download_pdi_table"
    public function download_table_filter_by_regulator( $regulator_name ){
        $this->regulator_name = $regulator_name;
        $this->target_name = NULL;
        return $this->download_pdi_table();
    }
    public function download_table_filter_by_target( $target_name ){
        $this->regulator_name = NULL;
        $this->target_name = $target_name;
        return $this->download_pdi_table();
    }
    
    // use inherited query to get a list of distinct pubmed IDs
    // depends on member variables "regulator_name" and "target_name"
    private function get_pubmed_ids(){
        $results = $this->get_base_query_builder()->get()->getResultArray();
        $pubmed_ids = array();
        
        for($i =0; $i<count($results);$i++)
        {            
            $pmid = $results[$i]['pubmed'];
            if( !in_array($pmid,$pubmed_ids) )
            {
                $pubmed_ids[] = $pmid;
            }
        }
        
        return $pubmed_ids;
    }
    
    // render view for route: /proteininfor/.../...
    public function proteininfor_page( $species, $genename )
    {        
        // make sure species name is in traditional short form
        list($species,$new_species) = parse_species($species);
        
        $sql= get_proteininfor_query();
        $query=$this->db->query($sql,[
            'genename' => $genename
        ]);

        $results=$query->getResultArray();
        
        //debug
        //return json_encode($results[13]['domains']);
        
        $data['species'] = $species;
        $data['genename'] = $genename;
        
        
        //special case, make the page work even if data is missing
        if( count($results) == 0 ) {
            $results = [[
                "id_name" => "",
                "uniprot_id" => "",
                "class" => "",
                "family" => "",
                "synonym" => "",
                "nucleotidesequence" => "",
                "proteinsequence" => "",
                "secondary_structures" => "",
                "species_version" => "",
                "clone_names" => "",
                "domains" => ""
            ]];
        }
        
        
        // assume all results have the same uniprot_id, class, family, and synonym
        foreach( ["uniprot_id", "class", "family", "synonym"] as $key ) {
            $data[$key] = $results[0][$key];
        }
        
        // prepare amino acid sequences for display
        // there should be one transcript with secondary structure data
        $default_transcript_index = 0;
        for($i =0; $i<count($results);$i++)
        {            
            if( empty( $results[$i]['secondary_structures'] ) ) {
                $results[$i]['proteinsequence_ss'] = make_up_color_by_secondary_structure($results[$i]['proteinsequence']);
            } else {
                $default_transcript_index = $i;
                $results[$i]['proteinsequence_ss'] = $results[$i]['secondary_structures'];
            }
            $results[$i]['proteinsequence_dom'] = make_up_color_by_domain($results[$i]['proteinsequence']);
            $results[$i]['proteinsequence_none'] = get_sequence_with_breaks($results[$i]['proteinsequence']);
        } 
        
        
        // parse all relevant domain annotations.
        // find the longest transcript with domain annotations, 
        // extract annotations and mark it as the transcript of interest
        $best_seq_len = 0;
        for($i =0; $i<count($results);$i++)
        {            
            if( $results[$i]['domains'] !== null ) {
                $domains = json_decode( '['.$results[$i]['domains'].']' );
                $results[$i]['domains'] = $domains;
                $aa_seq = $results[$i]['proteinsequence'];
                
                if( strlen($aa_seq) < $best_seq_len ){
                    continue;
                }
                
                $best_seq_len = strlen($aa_seq);
                $data['seq_len'] = $best_seq_len;
                $default_transcript_index = $i;   
                
                //lookup domain descriptions
                foreach( $domains as $dom ) { 
                    $acc = $dom->{'accession'};
                    $dom_info = lookup_dom_info( $acc );
                    $dom->{'title'} = $dom_info[0];
                    $dom->{'desc'} = $dom_info[1];
                }
                
            
                // sort domains based on start position
                usort($domains, function ($a, $b) {
                    return $a->{'start'} - $b->{'start'};
                });
                    
                // pick numbers to use for applying colors to domains
                // make sure domains with the same accession have the same color
                $acc_color_indices = [];
                $j = 0;
                foreach( $domains as $dom )
                {
                    $acc = $dom->{'accession'};
                    if( !array_key_exists( $acc, $acc_color_indices ) )
                    {
                        $acc_color_indices[$acc] = $j;
                        $j += 1;
                    }
                }
                
                $data['domains'] = $domains;
                $data['acc_color_indices'] = $acc_color_indices;
                $results[$i]['proteinsequence_dom'] = build_color_by_domain($aa_seq, $domains, $acc_color_indices );
            }
        } 
        
        // move the transcript of interest to the top of the list
        if( $default_transcript_index > 0 ){
            $results = array_merge( 
                [$results[$default_transcript_index]],
                array_slice($results,0,$default_transcript_index),
                array_slice($results,$default_transcript_index+1)
            );
        }
        
        // build a table showing the differences between transcripts,
        // in terms of their domain annotations
        $all_accessions = [];
        foreach( $results as $r ){
            if( $r['domains'] !== null ) {
                foreach( $r['domains'] as $dom ){
                    $acc = $dom->{'accession'};
                    if( !in_array( $acc, $all_accessions ) ){
                        $all_accessions[] = $acc;   
                    }
                }
            }
        }
        sort($all_accessions);
        $domain_table = [array_merge( [''], $all_accessions )];
        foreach( $results as $r ){
            if( $r['domains'] !== null ) {
                $row_data  = [ $r['id_name'] ];
                foreach( $all_accessions as $target_acc ){
                    $row_data[] = $this->accession_present( $target_acc, $r['domains'] );
                }
                $domain_table[] = $row_data;
            }
        }
        $data['domain_table'] = $domain_table;
        
        
        // get statistics about interactions
        $this->regulator_name = $genename;
        $this->target_name = NULL;
        $data['pubmed_ids_regulator'] = $this->get_pubmed_ids();
        $data['pdi_count_regulator'] = $this->get_base_query_builder()->countAllResults();
        
        $this->regulator_name = NULL;
        $this->target_name = $genename;
        $data['pubmed_ids_target'] = $this->get_pubmed_ids();
        $data['pdi_count_target'] = $this->get_base_query_builder()->countAllResults();
        
        
        // get html for pdi tables
        $data['pdi_table_regulator'] = $this->get_datatable_html(
            "pdi_table_regulator","/proteininfor/datatable_filter_by_regulator/".$genename);
        $data['pdi_table_target'] = $this->get_datatable_html(
            "pdi_table_target","/proteininfor/datatable_filter_by_target/".$genename);
        
        
        $data['results'] = $results;
        $data['title'] ="Protein Information";  
        return view('proteininfor', $data);
    }
    
    # return True if the given accession is present in 
    # the given domain annotations
    private function accession_present( $target_acc, $domains )
    {
        if( $domains == NULL ){
            return false;
        }
        
        foreach( $domains as $dom ){
            if( $dom->{'accession'} == $target_acc ){
                return true;   
            }
        }
        
        return false;
    }
    
    // serve an excel sheet containining interactions
    // depends on member variables "regulator_name" and "target_name"
    private function download_pdi_table()
    {
        define('ROOT_DIR', dirname(__FILE__));
        ignore_user_abort(true);
        set_time_limit(0); // disable the time limit for this script

        // do query
        $query = $this->get_base_query_builder()
            ->orderBy('gi.gi_id')->get();
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
        $headers = array("Regulator Gene","Regulator Protein","Regulator sort order","Target Gene","Target Protein","Target sort order","PubMed ID","Type","Experiment");
        $sheet->fromArray($headers,NULL,'A1');
        $spreadsheet->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleArray);
        foreach (str_split("ABCDEFGH") as $char) {
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
