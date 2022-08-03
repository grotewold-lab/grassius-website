<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

<?php require "common/subdomain_urls.php";?>
        

<?php 
    if( $uniprot_id ) {
        $real_uid = true;
    }else{
        $real_uid = false;
        $uniprot_id = "A0A1D6PDQ1";
    }
?>

<?php require_once "common/species_banner.php"; ?>



<table class="infobox">
   <tbody>
      <tr>
          <th class="infobox-above" colspan="2">Protein <?php echo $genename; ?></th>
      </tr>
      <tr>
          <th class="infobox-image" colspan="2">
            <iframe id="icn3d-structure" allowFullScreen="true" width="320" height="320" style="border:none"></iframe>
            <div class="infobox-caption">
                <?php echo $genename; ?> is a protein in the <?php echo $family; ?> family.
              </div>
          </th>
      </tr>
      <tr>
          <th class="infobox-header" colspan="2">Information</th>
      </tr>
      <tr>
        <td class="infobox-label"><?php echo $class; ?> Name:</td>
        <td class="infobox-data"><?php echo $genename; ?></td>
      </tr>
      <tr>
        <td class="infobox-label">Species:</td>
          <td class="infobox-data"><a href="/species/<?php echo $species; ?>"><?php echo $species; ?></a></td>
      </tr>
      <tr>
        <td class="infobox-label"><?php echo $class; ?> Family:</td>
        <td class="infobox-data"><a href="/family/<?php echo $species; ?>/<?php echo $family; ?>"><?php echo $family; ?></a></td>
      </tr>
      <tr>
        <td class="infobox-label">Gene Name(Synonym):</td>
        <td class="infobox-data"><?php echo $synonym; ?></td>
      </tr>
      <tr>
        <td class="infobox-label">Uniprot ID:</td>
        <td class="infobox-data"><a class='external' target = '_blank' href="https://www.uniprot.org/uniprot/<?php echo $uniprot_id; ?>"> <?php echo $uniprot_id; ?></td>
      </tr>
   </tbody>
</table>




<h2 style='color:red'>WARNING some sequences have been removed to save memory</h2>
<h2 class="wiki-top-header">Protein <?php echo $genename; ?></h2>
<p><?php echo $genename; ?> is a protein in the <?php echo $family; ?> family. 
    
<?php if( !$real_uid ) { ?>
    <font color="red">NOTE the "Uniprot ID" shown on the right is a placeholder for testing. The interactive structure shown is based on that id. </font>
<?php } ?>

</p>


<?php 
if( ($domain_table !== NULL) ){
    
    $ncols = count($domain_table[0]);
    
    echo "<table class='domain_table wikitable'>";
    echo "<th class='infobox-header' colspan='$ncols'>Overview of domains present in v5 transcripts</th>";
    echo "<tr>";
    foreach( $domain_table[0] as $col ){
        $col = explode('.',$col)[0];
        echo "<th><a target='_blank' href='/download/hmm/$col.hmm'>$col</a></th>";
    }
    echo "</tr>";
    
    for($i =1; $i<count($domain_table);$i++){
    
        $tid = $domain_table[$i][0];
        echo "<tr><td>$tid</td>";
        
        for($j =1; $j<count($domain_table[$i]);$j++){
            if($domain_table[$i][$j]) {
                echo "<td class='checked'><img src='/images/check.svg'></img></td>";
            } else {
                echo "<td class='unchecked'><img src='/images/x.svg'></img></td>";
            }
        }
    
        echo "</tr>";
    
    }  
    
    echo "</table>";
} 
?>


<?php for($i =0; $i<count($results);$i++){ ?>
    
    <div class="at at_<?php echo $results[$i]["species_version"]; ?>">
        <h2 class="wiki-section-header" style="margin-top:80px; font-size:30px; clear:both;">
            <a class='external' target = '_blank' href="http://maizegdb.org/gene_center/gene/<?php echo $results[$i]['id_name']; ?>"> <?php echo $results[$i]['id_name']; ?></a><span class="maize_version_label <?php echo $results[$i]["species_version"]; ?>">from maize genome <?php echo $results[$i]["species_version"]; ?></span>
        </h2>

        <?php if( !is_null($results[$i]["clone_names"]) ){ ?>
            <br>
            <span class="related_tfome_link">
                Related TFome: <?php echo get_tfomeinfor_link($results[$i]["clone_names"]); ?>
            </span>
        <?php } ?>

        <h2 class="wiki-section-header">
            Amino Acid Sequence 

            <?php echo get_copy_button($results[$i]["proteinsequence"]); ?>


            <?php if($i == 0){require "common/aa_colorcode_controls.php";} ?>

        </h2>

        <?php if($i == 0) { ?>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
            <p class="sequence aa aa_none"><?php echo $results[$i]["proteinsequence_none"]; ?></p>
            <p class="sequence aa aa_ss"><?php echo $results[$i]["proteinsequence_ss"]; ?></p>
            <?php echo $results[$i]["proteinsequence_dom"]; ?>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <?php require "common/aa_colorcode_legend.php"; ?>
            <?php 
                if( isset($domains) and (count($domains)>0) ){
                    require "common/dom_colorcode_legend.php";
                }
            ?>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <p class="sequence"><?php echo $results[$i]["proteinsequence_none"]; ?></p>
        <?php } ?>

        <?php
            $full_dna_seq = $results[$i]['nucleotidesequence'];
            if( strlen($full_dna_seq) > 25 ) {
                $short_dna_seq = substr($full_dna_seq, 0, 25)."...";
            } else {
                $short_dna_seq = $full_dna_seq;
            }
        ?>
        <h2 class="wiki-section-header">Nucleotide Sequence <?php echo get_copy_button($results[$i]['nucleotidesequence']); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo get_expand_button("dna_".$i); ?></h2>
        <p class="wrap sequence short dna_<?php echo $i;?>"><?php echo $short_dna_seq; ?></p>
        <p hidden class="wrap sequence long dna_<?php echo $i;?>"><?php echo $full_dna_seq; ?></p>
    </div>

<?php } ?>


<h2 class="wiki-section-header" style="margin-top:80px; font-size:30px; clear:both;">
    Protein-DNA interactions
</h2>
<p><small style="color:red">All interactions are based on Maize genome v3</small></p>

<?php 
$specs = [
    ["regulator", $pdi_count_regulator, $pubmed_ids_regulator, $pdi_table_regulator],
    ["target", $pdi_count_target, $pubmed_ids_target, $pdi_table_target],
];
foreach( $specs as [$label, $pdi_count, $all_pubmed_ids, $pdi_table] ){ 
?>
        


    <h2 class="wiki-section-header">
        Interactions where <?php echo $genename; ?> is the <b><?php echo $label; ?></b>

    <?php 
        if( $pdi_count > 0 ){ 
            echo get_expand_button("pdi_table_$label"); 
    ?>
            </h2>
            <br>
            <div>
                <?php if( $pdi_count > 1) {
                    echo "There are $pdi_count protein-dna interactions that fit this criteria.";
                } else {
                    echo "There is 1 protein-dna interaction that fits this criteria.";
                }?>
                <a href="/proteininfor/download_table_filter_by_<?php echo "$label/$genename"; ?>">download excel sheet</a>
                <br>
                Related pubmed articles:
                <?php foreach( $all_pubmed_ids as $pubmed_id )
                {
                    echo get_pubmed_link($pubmed_id, TRUE);   
                    if ($pubmed_id !== end($all_pubmed_ids)) {
                        echo ", ";
                    }
                }
                ?>
            </div>
            <div hidden class="long pdi_table_<?php echo $label; ?>">
                <br>
                <?php echo $pdi_table ?>
            </div>

    <?php 
        } else {
    ?>
            </h2>
            <br>
            <div>
                There are no protein-dna interactions that fit this criteria.
            </div>
    <?php
        } 
    ?>

<?php } ?>



<?php

    if ( $class === "Coreg" ){
        $nav_id_suffix = 'grasscoregdb';
    }
    else {
        $nav_id_suffix = 'grasstfdb';
    }
?>

<form hidden action="<?php echo $blast_tool_url; ?>" method="POST" target="_blank" id="blast_form">
  <input type="hidden" name="input_sequence" id="blast_input_sequence"/>
  <input type="submit" value="Submit">
</form>

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){     
        $("#nav_access").addClass("active");
        $("#icn3d-structure").attr("src","https://www.ncbi.nlm.nih.gov/Structure/icn3d/full.html?afid=<?php echo $uniprot_id; ?>&width=300&height=300&showcommand=0&shownote=0&mobilemenu=1&showmenu=0&showtitle=0");
        
        
        // add hover menu for each domain legend entry
        $(".dom_legend_hover:not(.dlhi_all)").each(function(i){
            var title = $(this).data('title');
            var desc = $(this).data('desc');
            var acc = $(this).data('acc');
            $(this).addClass('ss_hover');
            $(this).append('<div class="dom_legend_hovermenu"><ul><lh>'+title+'</lh></ul><p>'+desc+'</p><a target="_blank" href="https://pfam.xfam.org/family/'+acc+'">view on pfam.org</a></div>');
        })
        
        
        // start logic for highlighted sequence hover-menu
        
        // get sequence for each segment
        var ssi_i = 0
        var all_ssi_seqs = {}
        while( true ){
            var class_name = "ssi_"+ssi_i;
            var all_elems = $("." + class_name);
            if(all_elems.length == 0){
                break;
            }
            
            var seq = "";
            all_elems.each(function(i){
                seq += $(this).html()
            })
            all_ssi_seqs[class_name] = seq
            
            ssi_i += 1;
        }
        
        // convenience function
        function get_ssi_class(elem){
            return elem.attr('class').split(" ").filter(function (c) {
              return c.startsWith("ssi_");
            })[0];
        }
        
        // add hover menu for each secondary structure segment
        $("p.aa_ss span").each(function(i){
            var flavor = $(this).attr('class').split(" ")[1].substring(3);
            var seq = all_ssi_seqs[get_ssi_class($(this))]
            $(this).addClass('ss_hover');
            $(this).append('<div class="ss_hovermenu"><ul><lh>'+flavor+'</lh><li class="ss_blast" data-seq="'+seq+'">BLAST</li><li class="ss_copy" data-seq="'+seq+'">Copy</li></ul></div>');
        })
        
        // add hover menu for each domain segment
        $("p.aa_dom span.hl").each(function(i){
            var acc = $(this).data('acc');
            var seq = $(this).data('seq');
            $(this).addClass('ss_hover');
            $(this).append('<div class="ss_hovermenu"><ul><lh>'+acc+'</lh><li class="ss_blast" data-seq="'+seq+'">BLAST</li><li class="ss_copy" data-seq="'+seq+'">Copy</li></ul></div>');
        })
        
        // highlight hovered segment
        $(".ss_hover").mouseover(function(){
            var ssi_class = get_ssi_class($(this))
            $("."+ssi_class).addClass("ss_hover_selected")
        });
        $(".ss_hover").mouseout(function(){
            var ssi_class = get_ssi_class($(this))
            $("."+ssi_class).removeClass("ss_hover_selected")
        });
        
        // copy button in hover menu
        $("li.ss_copy").click(function(){
            var text_to_copy = $(this).attr('data-seq');
            var text_to_show = text_to_copy;
            if(text_to_show.length > 5){
                text_to_show = text_to_show.substring(0,5) + '...';
            }
            navigator.clipboard.writeText(text_to_copy);
            $(this).html("Copied: " + text_to_show);
        });
        $("li.ss_copy").mouseout(function(){
            $(this).html("Copy");
        });
        
        // blast button in hover menu
        $("li.ss_blast").click(function(){
            var seq = $(this).attr('data-seq');
            $('#blast_input_sequence').val(seq)
            $('#blast_form').submit()
        });
        $("li.ss_blast").mouseout(function(){
            $(this).html("BLAST");
        });
        
        
        $("p.aa_ss span").each(function(i){
            $(this).addClass('ss_hover');
        })
        
    })
</script>


<?= $this->endSection() ?>