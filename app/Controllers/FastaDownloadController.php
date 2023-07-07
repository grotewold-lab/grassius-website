<?php
namespace App\Controllers;

/**
 * Provides endpoints for users to download fasta files
 * (or csv files with the same data as a fasta)
 *
 * provides endpoint for routes 
 *      /download_sequences_csv/...
 *      /download_sequences_fasta/...
 * 
 * used for species portal page (download whole species)
 * used for family page         (download one family)
 * used for browsefamily page   (download all tfs or all coregs)
 */
class FastaDownloadController extends BaseController
{    
    
    
    /**
     * download a fasta or csv file with sequences
     *
     * provides endpoint for routes 
     *      /download_sequences_csv/...
     *      /download_sequences_fasta/...
     *
     * parameters:
     *    csv_format                - true if the results should be in csv format, otherwise fasta
     *    species (string)          - the name of the species
     *    species_version (string)  - optional version of the species genome (infraspecific name)
     *    sclazz (string)            - optional class 'TF' or 'Coreg'
     *    family_part1 (string) and family_part2 (string) 
     *                              - optional family name, which may become split in routing
     */
    public function download_seq_fasta( $csv_format, $species, $species_version=null, $clazz=null, $family_part1=null, $family_part2=null )
    {        
        
        //debug
        //return json_encode([$csv_format, $species, $species_version, $clazz, $family_part1, $family_part2]);
        
        // set member variables based on arguments
        $this->parse_params($csv_format, $species, $species_version, $clazz, $family_part1, $family_part2);
        
        // send a fasta or csv file to the user 
        return $this->download_fasta();
    }
    
    // helper to interpret arguments for download_seq_fasta()
    private function parse_params($csv_format, $species, $species_version, $clazz, $family_part1, $family_part2){
        $this->csv_format = $csv_format;
        $this->species = $species;
        
        // handle placeholder for species with only one version
        if( ($species_version == "_") or is_null($species_version) ) {
            $species_version = "";   
        }
        $this->species_version = $species_version;
        
        $this->clazz = $clazz;
        
        // handle family name that may have been split
        $this->family = $this->get_family_name($family_part1, $family_part2);
    }
    
    // helper to recognize family names passed through urls including up to one slash
    private function get_family_name($family_part1, $family_part2=null)
    {
        if( is_null($family_part1) ){
            return null;   
        }
        if( is_null($family_part2) or (trim($family_part2) == '') ) {
            return $family_part1;
        } else {
            return $family_part1."/".$family_part2;
        }
    }
    
    
    // used in download_seq_csv() and download_seq_fasta()
    private function download_fasta()
    {                
        //debug
        //return "PLACEHOLDER fasta file for {$species} {$species_version} {$family}";
        
        
        define('ROOT_DIR', dirname(__FILE__));
        ignore_user_abort(true);
        set_time_limit(0); // disable the time limit for this script
        
        
        
        
        // get all relevant transcripts 
        $sql = build_fasta_query( true, !is_null($this->clazz), !is_null($this->family) );
        $query=$this->db->query($sql,[
            'species' => $this->species,
            'species_version' => $this->species_version,
            'class' => $this->clazz,
            'family' => $this->family
        ]);            
        $results=$query->getResultArray();
        

        // write to local file
        $fullPath = WRITEPATH.microtime().".fasta";
        $myfile = fopen($fullPath, "w");
        $this->write_fasta($myfile, $results);
        fclose($myfile);

        // send to client
        $client_filename = ($this->csv_format ? 'seq.csv' : 'seq.fa');
        if ($fd = fopen($fullPath, "r")) {
            $fsize = filesize($fullPath);
            header("Content-type: application/octet-stream");
            header("Content-Disposition: filename=\"$client_filename\"");
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
    
    // write the contents of a fasta/csv file
    private function write_fasta( $stream, $query_results ){
        
        if( $this->csv_format ){
            fwrite($stream,"transcript ID,sequence\n");
        }
        foreach( $query_results as $row ){
            $tid = $row['tid'];
            $seq = $row['seq'];
            
            if( $this->csv_format ){
                fwrite($stream, "$tid,$seq\n");
            } else {
                fwrite($stream, ">$tid\n");
                while( strlen($seq) > 60 ) {
                    fwrite($stream, substr($seq,0,60)."\n");
                    $seq = substr($seq,60);
                }
                fwrite($stream, $seq."\n");
            }
        }
    }
}
