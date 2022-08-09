<?php

/**
 * get a list of short names for each available maize genome version
 *
 * the resulting names should match values in the chado 
 * table "organism", column "infraspecific_name"
 */
function get_maize_genome_versions()
{
    return ["v3","v4","v5"];
}


/**
 * Get the title and description of the given domain/accession
 */
function lookup_dom_info( $acc )
{
    $result = \Config\Database::connect()->table('domain_descriptions dd')
        ->select("dd.dom_title as dom_title,
            dd.dom_desc AS dom_desc")
        ->where( 'dd.accession', $acc )
        ->get()->getResultArray(); 
    
    if( count($result) > 0 ){
        return [ $result[0]["dom_title"], strip_tags($result[0]["dom_desc"]) ];
    }
    return [$acc,"No description available"];
}

/**
 * get a long name for the given maize genome version
 * 
 * the argument must be one of the values returned by
 * get_maize_genome_versions()
 */
function describe_maize_genome_version( $version )
{
    $version_map = array(
        "v3" => "B73 RefGen_v3",
        "v4" => "Zm-B73-REFERENCE-GRAMENE-4.0",
        "v5" => "Zm-B73-REFERENCE-NAM-5.0"
    );

    if (array_key_exists($version, $version_map)) {
        return $version_map[$version];
    } else {
        return null;
    }
}

 /**
  * get a string similar to the given amino acid sequence (string)
  * html tags will be added to limit the length of each line
  *
  * if $domain (json) is given, a section of the sequence will be highlighted.
  */
function get_sequence_with_breaks($aa_seq, $domain=NULL, $color='black')
{
    if( empty($aa_seq) ){
        return "";
    }
    
    $max_line_length = 80;
    $i = 0;
    $result = "";
    
    // if necessary, prepare to insert extra tags to annotate one domain
    if( $domain !== NULL ){
        $dstart = $domain->{'start'};
        $dend = $domain->{'end'};
        $dacc = $domain->{'accession'};
        $dseq = substr( $aa_seq, $dstart, ($dend-$dstart) );
        $ssi_suffix = str_replace('.','_',$dacc);
        $dtag = "<span data-seq='$dseq' data-acc='$dacc' class='hl ssi_$ssi_suffix' style='background-color:$color'>";
    }
    
    // start building $result line-by-line
    while( strlen($aa_seq) > 0 ){
        
        $part = substr( $aa_seq, 0, $max_line_length );
        $part_end = $i + min( $max_line_length, strlen($part) );
        
        // part contained in domain
        if( ($domain !== NULL) and ($dstart < $i) and ($dend >= $part_end) ){
            $part = $dtag.$part.'</span>';
        }
        
        // domain contained in part
        if( ($domain !== NULL) and ($dstart >= $i) and ($dend < $part_end) ){
            $j = $dstart - $i;
            $k = $dend - $i;
            $part = '<span>'.substr($part, 0, $j).'</span>'.$dtag.substr($part, $j, ($k-$j)).'</span><span>'.substr($part, $k).'</span>';
        }

        // domain starts in part
        elseif( ($domain !== NULL) and ($dstart >= $i) and ($dstart < $part_end) ){
            $j = $dstart - $i;
            $part = '<span>'.substr($part, 0, $j).'</span>'.$dtag.substr($part, $j).'</span>';
        }

        // domain ends in part
        elseif( ($domain !== NULL) and ($dend >= $i) and ($dend < $part_end) ){
            $k = $dend - $i;
            $part = $dtag.substr($part, 0, $k).'</span><span>'.substr($part, $k).'</span>';
        }
        
        // domain not involved in part
        else {
            $part = "<span>".$part."</span>";
        }
        
        $result .= $part."<br>";
        $aa_seq = substr( $aa_seq, $max_line_length );
        
        $i += $max_line_length;
    }
    
    $result .= "<span>".$aa_seq."</span>";
    return $result;  
}

/**
 * PLACEHOLDER
 * Get a color-coded version of the given amino acid sequence (string)
 * return a string containing html tags
 */
function make_up_color_by_secondary_structure($aa_seq)
{
    if( empty($aa_seq) ){
        return "";
    }
    
    $max_line_length = 80;
    $result = "";
    
    while( strlen($aa_seq) > $max_line_length ){
        $part = substr( $aa_seq, 0, $max_line_length );
        $result .= "<span class='ssi_0 ss_UNDETERMINED'>".$part."</span><br>";
        $aa_seq = substr( $aa_seq, $max_line_length );
    }
    
    $result .= "<span class='ssi_0 ss_UNDETERMINED'>".$aa_seq."</span>";
    return $result;
}

/**
 * Get a color-coded version of the given amino acid sequence (string)
 * 
 * return a string containing html tags
 */
function build_color_by_domain( $aa_seq, $domains, $domain_colors=[] )
{   
    $result = '';
    
    $regular_seq = get_sequence_with_breaks($aa_seq);
    $result .= '<p class="sequence aa aa_dom dom_background">'.$regular_seq.'</p>';
    
            
    $default_colors = [
        '#FFA','#FAF','#AFF'
    ];
    $dc_index = 0;
    
    for($i =0; $i<count($domains);$i++)
    {            
        $acc = $domains[$i]->{'accession'};
        $acc = explode('.',$acc)[0];
                
        if( array_key_exists( $acc, $domain_colors ) ){
            $color = $domain_colors[$acc];
        } else {
            $color = $default_colors[ $dc_index % count($default_colors) ];
            $domain_colors[$acc] = $color;
            $dc_index += 1;
        }
        
        $highlighted_seq = get_sequence_with_breaks($aa_seq, $domains[$i], $color);
        $result .= '<p class="sequence aa aa_dom dom_'.$i.'">'.$highlighted_seq.'</p>';
    }
    
    $result .= '<p class="sequence aa aa_dom dom_foreground">'.$regular_seq.'</p>';
    
    return $result;
}

/**
 * PLACEHOLDER
 * Get a color-coded version of the given amino acid sequence (string)
 * return a string containing html tags
 */
function make_up_color_by_domain($aa_seq)
{
    if( empty($aa_seq) ){
        return "";
    }
    
    $max_line_length = 80;
    $result = "";
    
    while( strlen($aa_seq) > $max_line_length ){
        $part = substr( $aa_seq, 0, $max_line_length );
        $result .= "<span class='do_UNDETERMINED ss_hover'>".$part."</span><br>";
        $aa_seq = substr( $aa_seq, $max_line_length );
    }
    
    $result .= "<span class='do_UNDETERMINED ss_hover'>".$aa_seq."</span>";
    return "<p class='sequence aa aa_dom simple'>".$result."</p>";
}


/**
 * Show a link to the proteininfor.php page for the given gene
 * return a string containing an html "a" tag
 */
function get_proteininfor_link($species,$grassius_name)
{
    if( is_null($grassius_name) or (strlen(trim($grassius_name)) <= 0) ){
        return "";
    } else {
        return "<a href='/proteininfor/$species/$grassius_name'>$grassius_name</a>";
    }
}


/**
 * Show a link to the pubmed article with the given id
 * return a string containing an html "a" tag
 */
function get_pubmed_link($pubmed_id, $visible_id=FALSE)
{
    if( $visible_id ) {
        $label = "PMID: $pubmed_id";
    } else {
        $label = "View on PubMed";
    }
    return "<a class='external' href='https://pubmed.ncbi.nlm.nih.gov/$pubmed_id/' target='_blank'>$label</a>";
}

/**
 * get one or more links to the tfomeinfor page
 * return a string containing html "a" tags
 *
 * parameter "clone" may contain one clone name, or a comma-separated list of clone names
 */
function get_tfomeinfor_link($clone)
{
    $result = "";

    if( is_null($clone) ){
        return $result;
    }

    $splitclone = explode(" ", $clone);
    foreach ($splitclone as $tfome) {
        if (!empty($tfome)) {

            $label = $tfome;
            if ($tfome !== end($splitclone)) {
                $label .= ", ";
            }
            $tfome = trim($tfome);

            $result .= "<a href='/tfomeinfor/$tfome'>$label</a>";
        }
    }
    
    return $result;
}

/**
 * returns true if the current session has admin credentials
 */
function user_is_admin()
{
    return \Config\Services::session()->get("isAdmin");
}

/**
 * get a list of short species names (old grassius species)
 */
function list_basic_species()
{
    return [
        "Maize","Rice","Sorghum","Sugarcane","Brachypodium"
    ];
}


/**
 * Conveniently process a string that contains a species name
 *
 * the given species name may be in any form (traditional or chado)
 *
 * return an array containing two strings: 
 *      - traditional species name
 *      - chado species name
 */
function parse_species( $input_string )
{
    $parts = explode( "_", $input_string );
    $species = get_basic_species_name($parts[0]);
    $new_species = get_chado_species($species);
    return [$species,$new_species];
}
    

/**
 * PRIVATE, only reference in this file
 *
 * translate old grassius species into new grassius species
 * return a string
 */
function _get_chado_species($species)
{
    $species_map = array(
        "Maize" => "Zea mays",
        "Rice" => "Oryza sativa",
        "Sorghum" => "Sorghum bicolor",
        "Sugarcane" => "Saccharum officinarum",
        "Brachypodium" => "Brachypodium distachyon"
    );

    if (array_key_exists($species, $species_map)) {
        return $species_map[$species];
    } else {
        return null;
    }
}

/**
 * PRIVATE, only reference in this file
 *
 * lookup a value based on a species name
 * the given map must have chado species names for keys
 * the given species may be a traditional name or a chado name
 * the given default value is returned if no match is found
 */
function _lookup_by_species($map,$species,$default)
{

    // check for chado species name
    if (array_key_exists($species, $map)) {
        return $map[$species];
    } else {
        // check for traditional species name
        $new_species = _get_chado_species($species);
        if (is_null($new_species)) {
            return $default;
        } else {
            return $map[$new_species];
        }
    }
}


/**
 * All functions below follow the same pattern
 *  - all take species (string) as a parameter
 *  - all use _lookup_by_species()
 * so the species parameter may be given in either the old or new form
 */


/**
 * get the traditional short species name matching the old grassius website
 * the species may be a traditional name or a chado name
 * echo an html img tag
 */
function get_basic_species_name($species)
{
    $species_map = array(
        "Zea mays" => "Maize",
        "Oryza sativa" => "Rice",
        "Sorghum bicolor" => "Sorghum",
        "Saccharum officinarum" => "Sugarcane",
        "Brachypodium distachyon" => "Brachypodium"
    );

    return _lookup_by_species($species_map, $species, "");
}



/**
 * get full species name in chado database
 * the species may be a traditional name or a chado name
 * echo an html img tag
 */
function get_chado_species($species)
{
    // redundancy here for convenience
    $species_map = array(
        "Zea mays" => "Zea mays",
        "Oryza sativa" => "Oryza sativa",
        "Sorghum bicolor" => "Sorghum bicolor",
        "Saccharum officinarum" => "Saccharum officinarum",
        "Brachypodium distachyon" => "Brachypodium distachyon"
    );

    return _lookup_by_species($species_map, $species, "");
}

/**
 * get an external link for a specific species and gene identifier
 * return a string containing an html a tag
 */
function get_external_db_link($species,$id_name)
{
    $url_prefix_map = array(
        "Zea mays" => "http://maizegdb.org/gene_center/gene/",
        "Oryza sativa" => "http://rice.plantbiology.msu.edu/cgi-bin/ORF_infopage.cgi?orf=",
        "Sorghum bicolor" => "http://archive.gramene.org/db/searches/quick_search?search_for=",
        "Brachypodium distachyon" => "http://brachypodium.org/gmod/searches?query=",
        "Saccharum officinarum" => "http://archive.gramene.org/db/searches/quick_search?search_for="
    );   

    $url_prefix = _lookup_by_species($url_prefix_map, $species, null);

    if (is_null($url_prefix)) {
        return "<a href=''>$id_name</a>";
    }else{
        return "<a target = '_blank' href='$url_prefix$id_name'>$id_name</a>";
    }
}

/**
 * display icon for the given species
 * the species may be a traditional name or a chado name
 * echo an html img tag
 */
function show_species_icon($species)
{
    $icon_map = array(
        "Zea mays" => "/images/maize_log_transp_0.png",
        "Oryza sativa" => "/images/rice_trans_4.png",
        "Sorghum bicolor" => "/images/sorghum_trans_3.png",
        "Saccharum officinarum" => "/images/sugarcane_trans_3.png",
        "Brachypodium distachyon" => "/images/brachypodium_trans__0.png"
    );

    $img_src = _lookup_by_species($icon_map, $species, "");

    echo "<img src='$img_src'>";
}

/**
 * get banner image for the given species
 * the species may be a traditional name or a chado name
 * return a string
 */
function get_species_banner_image_src($species)
{
    $banner_map = array(
        "Zea mays" => "/images/old/1banner.png",
        "Oryza sativa" => "/images/old/2banner.png",
        "Sorghum bicolor" => "/images/old/3banner.png",
        "Saccharum officinarum" => "/images/old/4banner.png",
        "Brachypodium distachyon" => "/images/old/5banner.png"
    );

    return _lookup_by_species($banner_map, $species, "");
}

/**
 * display infobox image for the given species
 * the species may be a traditional name or a chado name
 * echo an html img tag
 */
function show_species_infobox_image($species)
{
    $image_map = array(
        "Zea mays" => "/images/maize_organism.jpeg",
        "Oryza sativa" => "/images/oryza_sativa_organism.png",
        "Sorghum bicolor" => "/images/sorghum_organism.png",
        "Saccharum officinarum" => "/images/saccharum_officinarum_organism.png",
        "Brachypodium distachyon" => "/images/brachypodium_distachyon_organism_0.png"
    );

    $image_src = _lookup_by_species($image_map, $species, "");

    echo "<img style='width:300px' src='$image_src' align='right'>";
}


/**
 * quick-fix for linking to ncbi for organism details
 * return an integer
 */
function get_ncbi_taxonomy_id($species)
{
    $id_map = array(
        "Zea mays" => 4577,
        "Oryza sativa" => 4530,
        "Sorghum bicolor" => 4558,
        "Saccharum officinarum" => 4547,
        "Brachypodium distachyon" => 15368
    );

    return _lookup_by_species($id_map, $species, "");    
}

/**
 * get a species-specific external database
 * return an array with two elements, 
 *   - url 
 *   - readable label
 */
function get_external_species_db($species)
{
    
    $map = array(
        "Zea mays" => [
            "http://maizegdb.org/",
            "MaizeGDB"],
        
        "Oryza sativa" => [
            "http://rice.uga.edu/",
            "Rice Genome Annotation Project"],
        
        "Sorghum bicolor" => [
            "https://archive.gramene.org/species/sorghum/sorghum_intro.html",
            "Sorghum on Gramene"],
        
        "Brachypodium distachyon" => [
            "https://archive.gramene.org/species/brachypodium/brachypodium_intro.html",
            "Brachypodium on Gramene"],
        
        "Saccharum officinarum" => [
            "https://sugarcane-genome.cirad.fr/",
            "Sugarcane Genome Hub"]
    );

    return _lookup_by_species($map, $species, ["",""]);  
}

