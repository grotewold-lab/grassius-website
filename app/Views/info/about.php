<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>
<?php require APPPATH."/Views/common/subdomain_urls.php"; ?>

<br>
<h2 class="wiki-top-header">About GRASSIUS</h2>
    <br>      

<p>
GRASSIUS provides a public knowledgebase composed by a collection of databases,
computational and experimental resources that relate to the control of gene expression in the
grasses. As knowledge on the interactions of transcription factors (TFs) and cis-regulatory
elements in the promoters of the genes that they regulate continues to accumulate, the
information is acquired by GRASSIUS, either through contributions by the community, or by
literature analysis. The overarching objective of GRASSIUS is to provide a &quot;one-stop&quot; resource
that will facilitate research and communication within the plant community with regards to
genome-wide regulation of gene expression processes.
</p>
<p>
GRASSIUS currently contains regulatory information on maize, rice, sorghum, sugarcane, Setaria
and Brachypodium. However, as genome sequencing and annotation efforts in other grasses
continue to progress, GRASSIUS will expand to include information on them as well. The success
of this endeavor is largely dependent on community contributions, thus we hope to hear from
you soon.
</p>
<p>
GRASSIUS integrates information from several large databases. <a target="_blank" href="<?php echo $old_grassius_url; ?>/grasstfdb.php">GrassTFDB</a> contains information
on TFs, their DNA-binding properties and the genes that they have been shown to
bind/regulate. TFs are divided into ~60 families based on unique structural characteristics.
Information on TFs can be accessed individually by plant (i.e., accessing <a target="_blank" href="/browsefamily/Maize/TF">MaizeTFDB</a>, 
      <a target="_blank" href="<?php echo $old_grassius_url; ?>/browsefamily.php?sp=Rice&typ=transcription_factor">RiceTFDB</a>, 
      <a target="_blank" href="<?php echo $old_grassius_url; ?>/browsefamily.php?sp=Sorghum&typ=transcription_factor">SorghumTFDB</a> or 
      <a target="_blank" href="<?php echo $old_grassius_url; ?>/browsefamily.php?sp=Sugarcane&typ=transcription_factor">CaneTFDB</a>), or by TF family (e.g., by searching GrassTFDB for a particular TF
family, for example <a target="_blank" href="/family/Maize/MYB">MYB</a>). GrassTFDB also contains information on the availability of clones for
particular TFs, as part of publicly available TFome collections containing constantly increasing
numbers of TFs in recombination-ready vectors. <a target="_blank" href="<?php echo $old_grassius_url; ?>/grasspromdb.php">GrassPROMDB</a> will contain information on
gene regulatory sequences from the various grasses, and when available, information on which
regulatory sequences are important for gene expression. Integrating the information contained
in GrassTFDB and GrassPROMDB will be GrassREGNET, a database that will allow the
visualization of regulatory motifs and networks across the grasses.
</p>
<p>
Gene models for TFs are constantly updated with improvements in genome annotations.
Currently, GRASSIUS harbors data obtained from the following genome annotations:
<ul>
    <li>Maize genome B73 RefGen_v3</li>
    <li>Maize genome B73 RefGen_v4</li>
    <li>Maize genome B73 RefGen_v5</li>
    <li>Rice genome release 6.1 (O. sativa japonica)</li>
    <li>Sorghum genome release 1.4</li>
    <li>Brachypodium genome release 1.2</li>
    <li>R570 Sugarcane genome</li>
</ul>
</p>

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $('#nav_about').addClass("active")
    })
</script>

<?= $this->endSection() ?>