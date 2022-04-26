<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

<h2 class="wiki-top-header">Rice TFome Collection</h2>
<br>
<p>
    GRASSIUS will serve as a portal for access to the Grasses Transcription Factor ORFome collection, currently under development. 
    This aspect of the project involves a very significant educational component, as part of the F.I.R.E. undergraduate education program at the University of Toledo (OH).
    <br>
    If you have an interest in control of gene expression in the grasses, and would like your favorite transcription factor to be included in the ORFome collection, 
    or have antibodies that eventually you would like to use in chromatin immunoprecipitation (ChIP) experiments, we would very much like to hear from you.
</p>
<h2 class="wiki-top-header">Rice TFome Available</h2>
<br>
<?php echo $datatable; ?>


<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $('#nav_RiceTfome').addClass("head")
    })
</script>

<?= $this->endSection() ?>