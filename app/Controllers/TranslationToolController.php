<?php
namespace App\Controllers;

/*
 * Controller for view: translation_tool.php
 */
class TranslationToolController extends BaseController
{
    
    public function redirect_to_translation_tool()
    {
        return redirect()->to('/translation_tool'); 
    }
    
    /**
     * render the translation_tool page
     *
     * route: /translation_tool
     */
    public function translation_tool( $error_message=NULL )
    {               
        $data['title'] = "Translation Tool";
        
        $data['input_example'] = $this->get_input_example();
        
        if( !is_null($error_message) ){
            $data["error_message"] = $error_message;   
        }
        
        return view("translation_tool",$data);
    }
    
    /**
     * recieve post from translation_tool_page
     *
     * route: /translation_tool/translation
     */
    public function submit()
    {      
        
        $r = $this->request;
        
        # get the list of gene ids that were submitted
        $input_gene_ids = $this->parse_input_gene_ids($r);
        
        # check if input is valid
        $n = count($input_gene_ids);
        if($n == 0){
            return $this->translation_tool( "must input at least one gene ID" );
        }elseif($n > 1000){
            return $this->translation_tool( "too many gene IDs" );
        }
        
        # get target genome version
        # quietly default to "all" in case of any abnormalities
        $target_version = trim($r->getVar('trans_to'));
        $allowed = array_merge( get_maize_genome_versions(), ["all"] );
        if( (strlen($target_version)==0) or (!in_array($target_version,$allowed)) ){
            $target_version = "all";   
        }
            
        # do translation and show results
        return $this->render_translations($input_gene_ids, $target_version);
    }
    
    /*
     * get the protein name associated with the given gene ID
     * return a string
     */
    private function get_protein_name( $gene_id )
    {
        $sql =  "
            SELECT base.name

            FROM feature base

            JOIN featureprop gp
                ON gp.feature_id = base.feature_id
                AND gp.type_id = 496

            WHERE gp.value = :gene_id:
        ";
        $query=$this->db->query($sql,[
            'gene_id' => $gene_id
        ]);          
        $result = $query->getResultArray();
        
        if( count($result) == 0 ){
            return NULL;
        }
        return $result[0]["name"];
    }
    
    /**
     * get gene ids associated with the given protein name
     * return a list of strings
     */
    private function get_gene_ids( $protein_name, $target_version )
    {        
        $sql =  "
            SELECT DISTINCT(gp.value)

            FROM feature base

            JOIN featureprop gp
                ON gp.feature_id = base.feature_id
                AND gp.type_id = 496
                
            JOIN organism org
                ON org.organism_id = base.organism_id

            WHERE base.name = :protein_name:
                AND org.infraspecific_name = :target_version:
        ";
        $query=$this->db->query($sql,[
            'protein_name' => $protein_name,
            'target_version' => $target_version
        ]); 
        
        $all_gene_ids = [];
        foreach ($query->getResultArray() as $row) {
            $all_gene_ids[] = $row['value'];
        }
        return $all_gene_ids;
    }
    
    /**
     * render translation results in tab-delimited form
     */
    private function render_translations( $input_gene_ids, $target_version )
    {
        
        // start writing to local file
        define('ROOT_DIR', dirname(__FILE__));
        ignore_user_abort(true);
        set_time_limit(0); 
        $fullPath = WRITEPATH.microtime().".txt";
        $myfile = fopen($fullPath, "w");
        
        # write header
        if( $target_version == "all" ){
            $all_versions = get_maize_genome_versions();
            fwrite($myfile, "input\t".join("\t",$all_versions)."\n");
        }else{
            fwrite($myfile, "input\t".$target_version."\n");
        }
        
        # iterate through input gene IDs
        foreach( $input_gene_ids as $gene_id ){
           
            # get corresponding protein name
            $protein_name = $this->get_protein_name($gene_id);
            if( is_null($protein_name) ){
                continue;   
            }
                
            if( $target_version == "all" ){
                
                # special case for output with all versions
                # write exactly one line to output
                $result_line = $gene_id;
                foreach( $all_versions as $v ){
                    $all_gene_ids = $this->get_gene_ids($protein_name,$v);
                    $result_line .= "\t".join(",",$all_gene_ids);
                }
                fwrite($myfile, $result_line."\n");
                
            } else {
                
                # get all gene IDs from the target version, with that protein name
                $all_gene_ids = $this->get_gene_ids($protein_name,$target_version);

                # write to output
                foreach( $all_gene_ids as $output_gene_id ){
                    fwrite($myfile, $gene_id."\t".$output_gene_id."\n");
                }
            }
        }

        // stop writing and send to client
        fclose($myfile);
        if ($fd = fopen($fullPath, "r")) {
            $fsize = filesize($fullPath);
            header("Content-type: text/plain");
            header("Content-Disposition: filename=\"translations.txt\"");
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
    
    /**
     * get a list of input gene IDs,
     * which were either entered into a webpage, 
     * or uploaded in a text file
     *
     * used in submit()
     */
    private function parse_input_gene_ids($r)
    {
        # check if gene IDs were entered into the webpage
        # get input from webpage or uploaded file
        $gm_list = $r->getVar('gm_list');
        if( strlen(trim($gm_list)) == 0 ){
            $upload = $r->getFile('filename');
            if( !$upload->isValid() ){
                return [];
            }
            $filepath = WRITEPATH . 'uploads/' . $upload->store();
            $gm_list = file_get_contents( $filepath );
            unlink( $filepath );
        }
        
        # parse list of gene IDs
        $input_gene_ids = [];
        foreach( explode("\n", $gm_list ) as $gid ){
            $input_gene_ids[] = trim($gid);
        }
        
        return $input_gene_ids;
    }
    
    private function get_input_example()
    {
        return join(PHP_EOL, [
            'Zm00001eb143690',
            'Zm00001eb032370',
            'GRMZM2G025002',
            'Zm00001d002790',
            'Zm00001eb171070'
        ]);
    }
}
