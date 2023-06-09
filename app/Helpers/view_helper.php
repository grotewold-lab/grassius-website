<?php

/**
 * this file contains helpers that return complex html strings
 */


/**
 * given a color name like "red",
 * get a specific rgb code with tweaks for readability
 */
function get_real_color_for_domain_image( $color_name )
{
    switch($color_name){
        case "red":
            return "#F55";
        case "green":
            return "#0F0";
        case "blue":
            return "#88F";
        case "yellow":
            return "#FF0";
        case "gray":
            return "#AAA";
    }
    return "#FFF";
}

/**
 * Create a small canvas element showing an overview 
 * of domain annotations, given as json string
 *
 * example: 
 * https://pfam.xfam.org/family/PF15963.8#tabview=tab1
 */
function get_domain_image( $protein_name, $sanno, $cf_req_doms=NULL )
{
    if( is_null($sanno) ){
        $domains = [];   
    } else {
        $domains = json_decode( $sanno );
        if( is_null($domains) ){
            $domains = [];
        }
    }

    if( count($domains) == 0 ){
        return "Domain Annotations Pending";
    }

    $dom_id = "canvas_$protein_name";
    $width = 300;
    $height = 20;
    $padding = 10;
    
    $result = "<canvas width='$width' height='$height' id='$dom_id'></canvas>";
    $result .= "<script>";
    $result .= "var c = document.getElementById('$dom_id');";
    $result .= "var ctx = c.getContext('2d');";
    $result .= "ctx.font = '12px monospaced';";

    # draw horizontal line through the center
    $y = $height/2;
    $result .= "ctx.beginPath();";
    $result .= "ctx.moveTo(0, $y);";
    $result .= "ctx.lineTo($width, $y);";
    $result .= "ctx.stroke();";
    
    # draw domains
    foreach( [FALSE,TRUE] as $do_colors ){
        foreach( $domains as $dom ){

            $acc = $dom->{'accession'};
            $dom_info = lookup_dom_info( $acc );
            $acc = explode( '.', $acc )[0];
            $dstart = $dom->{'start'};
            $dend = $dom->{'end'};
            $seq_len = $dom->{'seqlen'};
            $color = $dom->{'color'};

            //$title = $acc.': '.$dom_info[0];
            //$desc = $dom_info[1].'<br/>coordinates: '.$dstart.' - '.$dend;

            $title = $acc;
            $desc = $dom_info[0].'<br>coordinates: '.$dstart.' - '.$dend;

            if( $do_colors and isset($cf_req_doms) ){
                $ci = array_search( $acc, $cf_req_doms );
                if( $ci === false ){
                    $color = 'gray'; 
                }else {  
                    $color = ['green','blue','yellow'][$ci % 3];
                }
            }
            if( $color == 'none' ){
                if( $do_colors ){
                    continue;   
                }
                $color = 'gray';
            } else {
                if( !$do_colors ){
                    continue;
                }
            }
            $color = get_real_color_for_domain_image( $color );

            $rx = $padding+($width-2*$padding)*$dstart/$seq_len;
            $rw = ($width-2*$padding)*($dend-$dstart)/$seq_len;
            $ry = 1;
            $rh = $height-2;
            $tx = $rx + 2;
            $ty = $height*.7;
            $nchars = floor($rw/7);
            $label = substr($acc, 0, $nchars );

            $result .= "add_domain_to_canvas( ctx, $rx,$ry,$rw,$rh, '$color', '$label', '$title', '$desc' );";
        }
    }

    $result .= "add_mouse_listener_to_canvas(c,ctx);";
    $result .= "</script>";
    
    return $result;
}


/**
 * Create a hoverable element for use in tables that list genes
 * when hovering, show a list of all gene ids associated with that row/gene
 *
 * input is a space-separated list of gene ids
 */
function get_agids_hover_element( $s_gene_ids )
{
    $all_gene_ids = explode( ' ', $s_gene_ids );
    $count = count( $all_gene_ids );
    if( $count == 1 ){
        $text = "$count Gene ID...";
    } else {
        $text = "$count Gene IDs...";
    }
    
    // group IDs by version
    $ids_by_version = [];
    foreach( $all_gene_ids as $gid ){
        $version = _get_maize_version($gid);
        if( !array_key_exists( $version, $ids_by_version ) ){
            $ids_by_version[$version] = [];
        }
        $ids_by_version[$version][] = $gid;
    }
    
    // start building html
    $result = "
        <div class='agids_hover'>
            $text
            <div class='agids_hovermenu'>
                <table  border='1' cellspacing='0' cellpadding='0'>
                    <tbody>
    ";
        
    
    // render table that will appear when hovering
    foreach( ["v3","v4","v5"] as $version ){
        if( !array_key_exists( $version, $ids_by_version ) ){
            continue;
        }
        $all_vids = $ids_by_version[$version];
        $count_vids = count($all_vids);
        $result .= "
                        <tr class='agids_$version'>
                            <td class='agids_header' rowspan='$count_vids'>$version</td>
                            <td style='border-bottom:none'>
                                <a target = '_blank' href='http://maizegdb.org/gene_center/gene/$all_vids[0]'>
                                    $all_vids[0]
                                </a>
                            </td>
                        </tr>
        ";
        
        for($i =1; $i<$count_vids;$i++)
        {
            $result .= "
                        <tr class='agids_$version'>
                            <td style='border-top:none; border-bottom:none;'>
                                <a target = '_blank' href='http://maizegdb.org/gene_center/gene/$all_vids[$i]'>
                                    $all_vids[$i]
                                </a>
                            </td>
                        </tr>
            ";
        } 
    }
    
    
    //add closing tags
    $result .= "
                    </tbody>
                </table>
            </div>
        </div>
    ";
    
    return $result;
}
    

/**
 * SHORTCUT just for use within the function above
 *
 * given an allele id e.g. "GRMZM2G026833".
 * get the corresponding maize version e.g. "v3"
 */
function _get_maize_version( $allele_id )
{
    if ( (substr($allele_id,0,1)==="Z") and (strpos($allele_id,"d")>-1) ){
        return "v4";
    } elseif ( (substr($allele_id,0,1)==="Z") and (strpos($allele_id,"e")>-1) ){
        return "v5";
    } else {
        return "v3";
    }
}
    
/**
 * Create a button for the user to copy text to their clipboard
 * 
 * return a string containing html tags
 */
function get_copy_button($string_to_copy)
{
    $uid = random_int(0,PHP_INT_MAX);
    
    return "
        <div class='tooltip'>
            <image class='copy-button' src='/images/clipboard.svg' onclick='myFunction$uid()' onmouseout='outFunc$uid()' >
                <span class='tooltiptext' id='myTooltip$uid'>Copy to clipboard</span>
            </image>
        </div>

        <script>
        function myFunction$uid() {
          var text_to_copy = '$string_to_copy';
          navigator.clipboard.writeText(text_to_copy);
          
          var text_to_show = text_to_copy;
          if(text_to_show.length > 7){
            text_to_show = text_to_show.substring(0,7) + '...';
          }
          
          var tooltip = document.getElementById('myTooltip$uid');
          tooltip.innerHTML = 'Copied: ' + text_to_show;
        }

        function outFunc$uid() {
          var tooltip = document.getElementById('myTooltip$uid');
          tooltip.innerHTML = 'Copy to clipboard';
        }
        </script>
    ";
}
    
    
/**
 * Create a button for the user expand a section of text
 * 
 * return a string containing an html tag
 */
function get_expand_button( $target_class )
{
    $uid = random_int(0,PHP_INT_MAX);
    
    return "
        <div class='tooltip'>
            <image class='expand-button'  id='myTooltipImg$uid' src='/images/arrows-expand.svg' onclick='myFunction$uid()'>
                <span class='tooltiptext' id='myTooltip$uid' data-state='collapsed'>Expand</span>
            </image>
        </div>

        <script>
        function myFunction$uid() {  
          var $ = jQuery.noConflict();        
          var tooltip = $('#myTooltip$uid');
          var image = $('#myTooltipImg$uid');
          var state = tooltip.attr('data-state');
          
          $('.$target_class').hide();
          
          if(state=='collapsed'){
            $('.$target_class.long').show();
            tooltip.attr('data-state','expanded');
            tooltip.html('Collapse');
            image.attr('src','/images/arrows-collapse.svg')
          } else {
            $('.$target_class.short').show();
            tooltip.attr('data-state','collapsed');
            tooltip.html('Expand');
            image.attr('src','/images/arrows-expand.svg')
          }
        }
        </script>
    ";
}
