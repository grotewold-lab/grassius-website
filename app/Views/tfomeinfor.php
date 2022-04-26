<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

<?php
switch ($template) {
    case "Synthetic":
        $tmplt = "<div style='color:red'>Synthesized template </div>";
        break;
    case "gDNA":
        $tmplt = "Genomic DNA";
        break;
    case "RT-PCR":
        $tmplt = "RT-PCR";
        break;
    case "FlcDNA":
        $tmplt = "Full-length cDNA";
        break;
    default:
        $tmplt = "<a target='_blank' href='http://www.ncbi.nlm.nih.gov/sites/entrez?db=nucest&cmd=search&term=$template'> $template </a>";
}
?>
        
<?php require_once "common/species_banner.php"; ?>

<table class='infobox'>
    <tbody>
        <tr>
            <th class="infobox-above" colspan="2">TFome <?php echo $clone_name; ?></th>
        </tr>
        <tr>
            <td class="infobox-label">Template</td>
            <td class="infobox-data"><?php echo $tmplt; ?></td>
        </tr>
        <tr>
            <td class="infobox-label">Genbank Insert ID</td>
            <td class="infobox-data"><?php echo $results['insert_gene_bank_id']; ?></td>
        </tr>
        <tr>
            <td class="infobox-label">Protein Name</td>
            <td class="infobox-data"><?php echo get_proteininfor_link($results['speciesname'], $results['gene_name']); ?></td>
        </tr>
        <tr>
            <td class="infobox-label">Species</td>
            <td class="infobox-data"><?php echo  $results['speciesname']; ?> </td>
        </tr>
        <tr>
            <td class="infobox-label">TF Family</td>
            <td class="infobox-data"><?php echo  $results['family']; ?></td>
        </tr>
        <tr>
            <td class="infobox-label">Gene id</td>
            <td class="infobox-data"><?php echo $results['gene_id']; ?></td>
        </tr>
        <tr>
            <td class="infobox-label">transcript</td>
            <td class="infobox-data"><?php echo  $results['transcript_number']; ?></td>
        </tr>
        <tr>
            <td class="infobox-label">Vector</td>
            <td class="infobox-data"><?php echo  $results['vector'];?></td>
        </tr>
        <tr>
            <td class="infobox-label">Note</td>
            <td class="infobox-data"><?php echo  $results['notes'];?> </td>
        </tr>
        <tr>
            <td class="infobox-label">Request Information</td>
            <td class="infobox-data">
                Deposited to 
                <a href="http://www.arabidopsis.org/servlets/TairObject?id=<?php echo $results['request_info']?>&type=clone">ABRC</a>
             </td>
        </tr>
    </tbody>
</table>


<h2 class="wiki-top-header">TFome <?php echo $clone_name; ?></h1>
<p>Placeholder text:<br>"TFome" is used as a synonym for "clone".<br>"TFome" is short for "transcription factor open reading frame".</p>

<h2 class="wiki-section-header">5' Primer</h2>
<p>
    <table>
        <tr>
            <td class="tfome-primer-label">Name</td>
            <td class="tfome-primer-data"><?php echo $results['five_prime_name'];?></td>
        </tr>
        <tr>
            <td class="tfome-primer-label">Temperature (ºC)</td>
            <td class="tfome-primer-data"><?php echo $results['five_prime_temp'];?></td>
        </tr>
        <tr>
            <td class="tfome-primer-label">Sequence<?php echo get_copy_button($results['five_prime_seq']); ?></td>
            <td class="tfome-primer-data sequence"><?php echo $results['five_prime_seq'];?></td>
        </tr>
    </table>
</p>

<h2 class="wiki-section-header">3' Primer</h2>
<p>
    <table>
        <tr>
            <td class="tfome-primer-label">Name</td>
            <td class="tfome-primer-data"><?php echo $results['three_prime_name'];?></td>
        </tr>
        <tr>
            <td class="tfome-primer-label">Temperature (ºC)</td>
            <td class="tfome-primer-data"><?php echo $results['three_prime_temp'];?></td>
        </tr>
        <tr>
            <td class="tfome-primer-label">Sequence<?php echo get_copy_button($results['three_prime_seq']); ?></td>
            <td class="tfome-primer-data sequence"><?php echo $results['three_prime_seq'];?></td>
        </tr>
    </table>
</p>

<h2 class="wiki-section-header">PCR Condition</h2>
<p><?php echo $results['pcr_condition'];?></p>

<h2 class="wiki-section-header">
    Amino Acid Sequence 
    <?php echo get_copy_button($proteinsequence); ?>
</h2>
<p class="wrap sequence"><?php echo $proteinsequence; ?></p>
<p><small style="color:red">TFome clones do not contain stop codon at 3'end.</small></p>

<h2 class="wiki-section-header">
    Nucleotide Sequence 
    <?php echo get_copy_button($results['sequence']); ?>
</h2>
<p class="wrap sequence"><?php echo $results['sequence']; ?></p>


<?php
    if (strcmp($results['speciesname'], "Zea mays") == 0){
        $nav_id_suffix = 'tfomecollection';
    }
    else if (strcmp($results['speciesname'], "Oryza sativa") == 0){
        $nav_id_suffix = 'RiceTfome';
    }
?>

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $("#nav_access").addClass("active");
        $("#icn3d-structure").attr("src","https://www.ncbi.nlm.nih.gov/Structure/icn3d/full.html?afid=A0A1D6PDQ1&width=300&height=300&showcommand=0&shownote=0&mobilemenu=1&showmenu=0&showtitle=0");
    })
</script>

<?= $this->endSection() ?>