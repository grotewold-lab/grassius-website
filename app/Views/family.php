<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

<?php require_once "common/species_banner.php"; ?>

<script src="/js/domain_canvas.js"></script>


<table>
    <tr>
        <td width="50%" style="vertical-align:top; padding-right:30px;">
            <h2 class="wiki-top-header"><?php echo $familyname ?> Family from <?php echo $species ?></h2>
            <br><br>
            <?php
                if( count($domain_colors) > 0 ){
                    echo "Required domains for $familyname family:";
                    foreach( $domain_colors as $dc ){
                        $name = $dc['domain'];
                        $color = get_real_color_for_domain_image($dc['color']);
                        echo "<span class='required_domain_label' style='background-color:$color'><a href='/download/hmm/$name.hmm' target='_blank'>$name</a></span>";
                    }
                }
            ?>



            <?php
            if (user_is_admin()) 
            {
                echo '<br><a href="/edit_family/'.$familyname.'">edit description for '.$familyname.' family</a><br>';    
            }          
            ?>

            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>

            <?php

                if( $species == "Maize" ){
                    require_once "common/maize_version_controls.php";
                }

                if( $species_version == "_" ){
                    $label_prefix = "Download ";
                } else {
                    $label_prefix = "Download $species_version ";
                }
                   
                echo "

                    <a id='download_gene_list' 
                        href='/download_family_gene_list/$species/$species_version/$familyname'
                        >$label_prefix gene list (csv)</a>

                    <br>

                    <a id='download_seqs' 
                        href='/download_sequences_csv/$species/$species_version/$clazz/$familyname'
                        >$label_prefix sequences (csv)</a>

                    <br>

                    <a id='download_fasta_seqs' 
                        href='/download_sequences_fasta/$species/$species_version/$clazz/$familyname'
                        >$label_prefix sequences (fasta)</a>

                ";
            ?>

            
    <!--<a id="download_fasta_nu" style="vertical-align:top" href="/download_family_fasta_nu/'.$species.'/'.$species_version.'/'.$familyname.'">'.$label_prefix.' (cdna)</a><br><br>-->
            
        </td>
        <td width="50%" style="padding:30px;">
            <div style="height:300px;overflow-y:auto;overflow-x:hidden">
                <br>
                <?php    
                    if (!empty($famresult["description"])) {  
                        echo $famresult['description']; 
                    } else {
                        echo "Description for the $familyname family is not available at this time";    
                    }
                ?>
            </div>
        </td>
    </tr>
</table> 


<?php
//show_gene_table($species, $familyname, $results);
echo $datatable;

if ( $famresult['class'] === "Coreg" ){
    $nav_id_suffix = 'grasscoregdb';
}
else {
    $nav_id_suffix = 'grasstfdb';
}
?>

<div class="familypage_dom_hovermenu"><ul><lh id="familypage_dom_hovermenu_title">title</lh></ul><p id="familypage_dom_hovermenu_desc">description</p></div>

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $("#nav_access").addClass("active");
        
        <?php if( $species=='Maize' ){ ?>
        
            // set which columns are visible based on the desired maize genome version
            function update_table( new_version_id ) {

                // hide three columns
                for (let i = 2; i <= 4; i++) {
                   gene_table.column( i ).visible( false );
                }

                // show one column
                var version_number = parseInt(new_version_id.substring(1));
                var column = gene_table.column( version_number-1 ).visible( true );

                // update download links
                $("#download_gene_list").html("Download " + new_version_id + " gene list (csv)")
                $("#download_gene_list").attr("href", "/download_family_gene_list/<?php echo $species; ?>/" + new_version_id + "/<?php echo $familyname; ?>")
                $("#download_seqs").html("Download " + new_version_id + " sequences (csv)")
                $("#download_seqs").attr("href", "/download_sequences_csv/<?php echo $species; ?>/" + new_version_id + "/<?php echo $clazz; ?>/<?php echo $familyname; ?>")
                $("#download_fasta_seqs").html("Download " + new_version_id + " sequences (fasta)")
                $("#download_fasta_seqs").attr("href", "/download_sequences_fasta/<?php echo $species; ?>/" + new_version_id + "/<?php echo $clazz; ?>/<?php echo $familyname; ?>")
            }

            // apply the default species version
            update_table("<?php echo $species_version ?>");

            // add listener to detect changed species version
            version_change_listeners.push(update_table);
    
        <?php } ?>
    })
</script>


<?= $this->endSection() ?>
