<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

<?php require_once "common/species_banner.php"; ?>

<script src="/js/domain_canvas.js"></script>

<h2 class="wiki-top-header"><?php echo $familyname ?> Family from <?php echo $species ?></h2>

<p>
    <?php    
        if (!empty($famresult["description"])) {  
            echo $famresult['description']; 
        } else {
            echo "Description for the $familyname family is not available at this time";    
        }
    ?>
</p>


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

<?php

require_once "common/maize_version_controls.php";

echo '<a id="download_fasta_aa" style="vertical-align:top" href="/download_family_fasta_aa/'.$species.'/v5/'.$familyname.'">Download v5 FASTA (protein)</a>';

echo '<br><a id="download_fasta_nu" style="vertical-align:top" href="/download_family_fasta_nu/'.$species.'/v5/'.$familyname.'">Download v5 FASTA (cdna)</a><br><br>';


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
        
        // set which columns are visible based on the desired maize genome version
        function update_table( new_version_id ) {
            
            // hide three columns
            for (let i = 2; i <= 4; i++) {
               gene_table.column( i ).visible( false );
            }
            
            // show one column
            var version_number = parseInt(new_version_id.substring(1));
            var column = gene_table.column( version_number-1 ).visible( true );
            
            // update url to download fasta
            $("#download_fasta_aa").attr("href", "/download_family_fasta_aa/<?php echo $species; ?>/" + new_version_id + "/<?php echo $familyname; ?>")
            $("#download_fasta_aa").html("Download " + new_version_id + " FASTA (protein)")
            $("#download_fasta_nu").attr("href", "/download_family_fasta_nu/<?php echo $species; ?>/" + new_version_id + "/<?php echo $familyname; ?>")
            $("#download_fasta_nu").html("Download " + new_version_id + " FASTA (cdna)")
        }
        
        // apply the default species version
        update_table("<?php echo $species_version ?>");
        
        // add listener to detect changed species version
        version_change_listeners.push(update_table);
    })
</script>


<?= $this->endSection() ?>
