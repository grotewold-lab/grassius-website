<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

<h2 class="wiki-top-header">Maize TFome Collection</h2>
<p>GRASSIUS will serve as a portal for access to the Grasses Transcription Factor ORFome collection, currently under development. 
This aspect of the project involves a very significant educational component, as part of the F.I.R.E. undergraduate education program at the University of Toledo (OH).
<br>
If you have an interest in control of gene expression in the grasses, and would like your favorite transcription factor to be included in the ORFome collection, 
or have antibodies that eventually you would like to use in chromatin immunoprecipitation (ChIP) experiments, we would very much like to hear from you.
<br>
You can download a spreadsheet of the entire collection here: 
<a href="/download/Maize_TFome_Bulk_data.xls">First release of the maize TFome bulk data download
</a>
</p>

<?php require_once "common/maize_version_controls.php" ?>
<style>
    .mgvc_container {
        float:right;
    }
</style>

<br>
<?php echo $datatable; ?>

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $("#nav_access").addClass("active");
        
        // set which columns are visible based on the desired maize genome version
        function update_table( new_version_id ) {
            
            // hide three columns
            for (let i = 3; i <= 5; i++) {
               tfome_table.column( i ).visible( false );
            }
            
            // show one column
            var version_number = parseInt(new_version_id.substring(1));
            var column = tfome_table.column( version_number ).visible( true );
        }
        
        // apply the default species version
        update_table("<?php echo $species_version ?>");
        
        // add listener to detect changed species version
        version_change_listeners.push(update_table);
    })
</script>

<?= $this->endSection() ?>