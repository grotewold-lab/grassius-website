<?php
namespace App\Controllers;


class FamilyController extends CsvDatatableController
{    
    
    // implement DatatableController
    protected function get_column_config()
    {
        if( $this->species == 'Maize' ){
            return [

               // [ query-key, result-key, view-label ]
               ["dmn.name_sort_order", "name_sort_order", "Protein Name <br><font color=#ce6301>accepted</font>&#x2F;<font color=#808B96>suggested</font>"],
               ["dmn.name", "grassius_name", "Protein Name"],
               ["dmn.v3_id", "v3_id", "Maize v3 ID"],
               ["dmn.v4_id", "v4_id", "Maize v4 ID"],
               ["dmn.v5_id", "v5_id", "Maize v5 ID"],
               ["default_domains.domains","domains","Domains"],
               ["gene_name.synonym", "othername", "Synonym/<br>Gene Name"],
               ["searchable_clones.clone_list", "clones", "Clone in TFome"],
               ["dmn.all_ids", "all_ids", "All Gene IDs"],
               ["dmn.all_ids", "raw_ids", "All Gene IDs"]
            ];
            
        }else { // not maize
            return [

               // [ query-key, result-key, view-label ]
               ["no.sortorder", "name_sort_order", "Protein Name"],
               ["base.name", "grassius_name", "Protein Name"],
               ["base.uniquename", "v3_id", "Gene ID"],
            ];
            
        }
    }
    
    // implement DatatableController
    protected function is_column_searchable( $query_key )
    {
        // disable searching of certain columns
        if( in_array( $query_key, ["dmn.name_sort_order","no.sortorder"]) ){
            return false;
        }
        
        return true;
    }
    
    // implement DatatableController
    protected function get_base_query_builder()
    {
        
        if( $this->species == 'Maize' ){
            return $this->db->table('public.default_maize_names dmn')
                ->select("dmn.name AS grassius_name,
                    dmn.name_sort_order AS name_sort_order,
                    gene_name.synonym AS othername,
                    dmn.v3_id AS v3_id,
                    dmn.v4_id AS v4_id,
                    dmn.v5_id AS v5_id,
                    default_domains.domains AS domains,
                    dmn.all_ids AS all_ids,
                    dmn.all_ids AS raw_ids,
                    searchable_clones.clone_list AS clones,
                    gene_name.accepted as accepted,
                    'Zea mays' AS speciesname")
                ->join('public.searchable_clones', 'searchable_clones.name = dmn.name', 'left')
                ->join('public.gene_name', 'gene_name.grassius_name = dmn.name', 'left')
                ->join('default_domains', 'default_domains.protein_name = dmn.name', 'left')
                ->where('dmn.family', $this->family);
            
        } else { //not maize
            return $this->db->table('feature base')
                ->select("base.name AS grassius_name,
                    no.sortorder AS name_sort_order,
                    '' AS othername,
                    base.uniquename AS v3_id,
                    ' AS v4_id,
                    ' AS v5_id,
                    '' AS domains,
                    '' AS all_ids,
                    '' AS raw_ids,
                    '' AS clones,
                    '' as accepted,
                    CONCAT(org.genus,' ',org.species) AS speciesname")
                ->join('featureprop fp', 'fp.feature_id = base.feature_id AND fp.type_id = 1362')
                ->join('organism org', 'org.organism_id = base.organism_id')
                ->join('name_orders no', 'no.name = base.name')
                ->where('fp.value', $this->family)
                ->where('base.type_id', 844)
                ->where('org.common_name', $this->species);
        }
            
        return $result;
        
    }
    
    // implement DatatableController
    protected function prepare_results( $row ) {
        if ($row['grassius_name'] === $row['v3_id']) {
            $protein_link = "";
        } else {
            $basic_species = get_basic_species_name($row['speciesname']);
            $protein_link = get_proteininfor_link($basic_species, $row['grassius_name']);
        }
        
        if ($row['accepted'] === "no"){
            $protein_class = "sugg";
        }else {
            $protein_class = "accpt";
        }

        
        if( $this->species == 'Maize' ){
            

            return [
               "name_sort_order" => "<div class=$protein_class>$protein_link</div>", # visible column
               "grassius_name" => "", # hidden placeholder for searching
               "v3_id" => get_external_db_link($row['speciesname'], $row['v3_id']),
               "v4_id" => get_external_db_link($row['speciesname'], $row['v4_id']),
               "v5_id" => get_external_db_link($row['speciesname'], $row['v5_id']),
               "domains" => get_domain_image($row['grassius_name'],$row['domains']),
               "othername" => $row['othername'],
               "clones" => get_tfomeinfor_link($row['clones']),
               "all_ids" => get_agids_hover_element($row['all_ids']), #visible column
               "raw_ids" => $row['raw_ids'] # hidden placeholder for downloading fasta
            ];
            
            
        } else { //not maize

            return [
               "name_sort_order" => "<div class=$protein_class>$protein_link</div>",
               "grassius_name" => "<div class=$protein_class>$protein_link</div>",
               "v3_id" => get_external_db_link($row['speciesname'], $row['v3_id']),
            ];
        }
    }
    
    // OVERRIDE DatatableController
    // prevent sorting of certain columns
    // hide certain columns
    // set width of visible columns
    protected function get_extra_datatable_options(){
        if( $this->species == 'Maize' ){
            return '
                  "columnDefs": [ 
                    { "targets": [5,8],"orderable": false },
                    { "targets": [1,9],"visible": false },
                    { "targets": [0,2,3,4,7],"width": "10%" },
                    { "targets": [6,8],"width": "15%" },
                  ],
                ';   
        } else {
            return '
                  "columnDefs": [ 
                    { "targets": [1],"visible": false },
                  ],
                ';  
        }
    }
    
    public function index( $species, $family_part1, $family_part2=null )
    {
        $this->parse_params($species, '', $family_part1, $family_part2);
        $species = $this->species;
        $family = $this->family;
        
        // get class and description
        $famsql=  "
            SELECT 
                class_family.class, 
                family.description,
                csu.url as comments_url
            
            FROM family 
            
            JOIN class_family 
                ON class_family.family=family.familyname
                
            JOIN comment_system_urls csu
                ON csu.name = family.familyname
                AND csu.type = 'family'
                
            WHERE familyname = :familyname:
        ";
        $query=$this->db->query($famsql,[
            'familyname'   => $this->family
        ]);          
        $famresult = $query->getRowArray();
        if( $famresult == null ){
            $famresult = array(
                "comments_url" => "",
                "class" => "TF"
            );   
        }
        
        $data['datatable'] = $this->get_datatable_html("gene_table","/family_datatable/".$species."/$family");
        $data['famresult'] = $famresult;
        $data['species'] = $species;
        $data['familyname'] = $family;
        $data['comments_url'] = $famresult['comments_url'];
        $data['title'] = "GrassTFDB :: Browse Family::$species";
        
        
        # prepare "required domains" info with color coding
        $sql=  "
            SELECT domain,color
            FROM family_domain_colors 
            WHERE family = :familyname:
        ";
        $query=$this->db->query($sql,[
            'familyname'   => $family
        ]);
        $data['domain_colors'] = $query->getResultArray();
        
        
        # if necessary, set the initial state of the maize version radio buttons
        if( $this->species == 'Maize' ){
            $data['species_version'] = $this->get_session_var('Maize_version');
        } else {
            $data['species_version'] = "_";
        }
        
        
        return view('family', $data);
    }
    
    // helper to parse common request params
    private function parse_params( $species, $species_version, $family_part1, $family_part2=null )
    {
        // handle placeholder for species with only one version
        if( $species_version == "_") {
            $species_version = "";   
        }
        $this->species_version = $species_version;
        
        // get species names in two forms
        list($species,$new_species) = parse_species($species);
        
        // accept family names including up to one slash
        $family = $this->get_family_name($family_part1, $family_part2);
    
        $this->family = $family;
        $this->species = $species;
        $this->new_species = $new_species;
    }
    
    
    
    // helper to recognize family names in url, including up to one slash
    private function get_family_name($family_part1, $family_part2=null)
    {
        if( is_null($family_part2) or (trim($family_part2) == '') ) {
            return $family_part1;
        } else {
            return $family_part1."/".$family_part2;
        }
    }
        
    
    public function family_datatable( $species, $family_part1, $family_part2=null )
    {
        $this->parse_params($species, '', $family_part1, $family_part2);
        return $this->datatable();
    }
    
    // implement CsvDatatableController
    protected function get_csv_download_filename(){
        return "data.csv";   
    }
    
    // implement CsvDatatableController
    protected function get_csv_column_headers(){
        
        if( $this->species == 'Maize' ){
            
            return [
                "number for sorting purposes",
                "protein name",
                "gene ID",
                "synonym",
                "clone",
                "all gene IDs",
            ];            
            
                
        } else { //not maize

            return [
                "number for sorting purposes",
                "protein name",
                "gene ID"
            ];
        }
    }
    
    // implement CsvDatatableController
    protected function prepare_results_for_csv( $row ){
        if ($row['grassius_name'] === $row['v3_id']) {
            $protein_link = "";
        } else {
            $basic_species = get_basic_species_name($row['speciesname']);
            $protein_link = get_proteininfor_link($basic_species, $row['grassius_name']);
        }
        
        if ($row['accepted'] === "no"){
            $protein_class = "sugg";
        }else {
            $protein_class = "accpt";
        }

        
        if( $this->species == 'Maize' ){
            

            $version = $this->get_session_var('Maize_version');
            $gid_col = $version."_id";
            
            return [
               "name_sort_order" => $row['name_sort_order'], 
               "grassius_name" => $row['grassius_name'], 
               "gene_id" => $row[$gid_col],
               "othername" => $row['othername'],
               "clones" => $row['clones'],
               "all_ids" => $row['all_ids'],
            ];
            
            
        } else { //not maize

            return [
               "name_sort_order" => $row['name_sort_order'], 
               "grassius_name" => $row['grassius_name'], 
               "gene_id" => $row['v3_id']
            ];
        }
    }
    
    /**
     * download csv like on-screen table
     *
     * wrap CsvDatatableController->download_csv
     *
     * parameters:
     *    species (string) - the name of the species
     *    species_version (string) - the version of the species genome (infraspecific name)
     *    family_part1 (string) and family_part2 (string) - the family name, 
     *          which may become split in routing
     */
    public function download_family_csv( $species, $species_version, $family_part1, $family_part2=null )
    {
        
        //debug
        //return json_encode([$species, $species_version, $family_part1, $family_part2]);
        
        $this->parse_params($species, $species_version, $family_part1, $family_part2);
        return $this->download_csv();
    }
    
    
    /**
     * download a csv file with sequences
     *
     * parameters:
     *    species (string) - the name of the species
     *    species_version (string) - the version of the species genome (infraspecific name)
     *    family_part1 (string) and family_part2 (string) - the family name, 
     *          which may become split in routing
     */
    public function download_seq_csv( $species, $species_version, $family_part1, $family_part2=null )
    {        
        
        //debug
        //return json_encode([$species, $species_version, $family_part1, $family_part2]);
        
        $this->parse_params($species, $species_version, $family_part1, $family_part2);
        return $this->_download_fasta(true);
    }
    
    
    /**
     * download a fasta file with sequences
     *
     * parameters:
     *    species (string) - the name of the species
     *    species_version (string) - the version of the species genome (infraspecific name)
     *    family_part1 (string) and family_part2 (string) - the family name, 
     *          which may become split in routing
     */
    public function download_seq_fasta( $species, $species_version, $family_part1, $family_part2=null )
    {        
        
        //debug
        //return json_encode([$species, $species_version, $family_part1, $family_part2]);
        
        
        $this->parse_params($species, $species_version, $family_part1, $family_part2);
        return $this->_download_fasta(false);
    }
    
    private function _download_fasta($csv_format)
    {                
        //debug
        //return "PLACEHOLDER fasta file for {$species} {$species_version} {$family}";
        
        
        define('ROOT_DIR', dirname(__FILE__));
        ignore_user_abort(true);
        set_time_limit(0); // disable the time limit for this script
        
        
        // get all relevant transcripts 
        $sql = build_fasta_query( true );
        $query=$this->db->query($sql,[
            'species' => $this->species,
            'species_version' => $this->species_version,
            'family' => $this->family
        ]);            
        $results=$query->getResultArray();
        
        
        //debug
        //return json_encode( $results );
        

        // write to local file
        $fullPath = WRITEPATH.microtime().".fasta";
        $myfile = fopen($fullPath, "w");
        if( $csv_format ){
            fwrite($myfile,"transcript ID,sequence\n");
        }
        foreach( $results as $row ){
            $tid = $row['tid'];
            $seq = $row['seq'];
            
            if( $csv_format ){
                fwrite($myfile, "$tid,$seq\n");
            } else {
                fwrite($myfile, ">$tid\n");
                while( strlen($seq) > 60 ) {
                    fwrite($myfile, substr($seq,0,60)."\n");
                    $seq = substr($seq,60);
                }
                fwrite($myfile, $seq."\n");
            }
        }
        fclose($myfile);

        // send to client
        $client_filename = ($csv_format ? 'seq.csv' : 'seq.fa');
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
}
