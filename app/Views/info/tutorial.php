<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>
<?php require APPPATH."/Views/common/subdomain_urls.php"; ?>

<br>
<h2 class="wiki-top-header">GRASSIUS Website Tutorial</h2>
<br>     
<b>Fasta/CSV downloads</b>
<br>
Species and family pages include several links to export gene lists and sequences
<ul>
    <li>Download gene list - download a table where each row represents a gene, similar to the on-screen tables on famiy pages</li>
    <li>Download sequences (csv) - download a table where each row contains a transcript ID and an amino acid sequence</li>
    <li>Download sequences (fasta) - download transcript IDs and amino acid sequences in fasta format
</ul>
<br>

    <b>Maize Genome Versions and Translations</b>
    <br>
    Grassius supports 3 version of the Maize genome:
    <ul>
        <li>Maize genome B73 RefGen_v3</li>
        <li>Maize genome B73 RefGen_v4</li>
        <li>Maize genome B73 RefGen_v5</li>
    </ul>

    When viewing Maize data, click radio buttons to select the genome version. Each genome version has a different set of gene IDs, transcript IDs, and sequences. The selected genome versions determines the primary gene ID shown in on-screen gene lists, and downloaded csv files. Downloadable sequences are different for each genome version.

<br>
<br>
<br>
<b>Custom Family Builder</b>
<br>
    Users can create custom Maize gene families using the <a href="/customfamily/Maize">Custom Family Builder</a>.
    <br><br>
    Like published families, a custom family is defined by required and forbidden protein domains. Domains are denoted by their name or pfam ID as shown on curated family pages and protein pages. 
    <br>
    <br>
    For example, <a href="/family/Maize/bHLH">family bHLH</a> has the required domain PF00010
    <br>
    For example, <a href="/proteininfor/Maize/ZmbHLH1">gene ZmbHLH1</a> has domains PF00010 and PF14215
    <br>
    
    <br>
    
    To use the <a href="/customfamily/Maize">Custom Family Builder</a>, at least one required domain must be selected. Using the textbox under "Required Domains", start typing a domain ID such as "PF00010". Choose a domain from the auto-complete dropdown. A table of matching maize transcripts will appear. The selected domain will be given a color-code which is shown under "required" domains and in the "Domains" column in the table.
    
    <br><br>
    Custom families can be saved using bookmarks in your web browser. Share the url in your address bar to allow others to see your custom family. <a href="/customfamily/Maize?q=Example;PF00010,PF14215;None">example</a>


<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $('#nav_help').addClass("active")
    })
</script>

<?= $this->endSection() ?>