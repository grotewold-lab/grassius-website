<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

<br>
<h2 class="wiki-top-header">
    PDI (Protein-DNA Interaction) Collection
</h2>
  <p>
    You can download a spreadsheet of the entire collection here: 
    <a href="/download/Grassius_RegNet.xls">All TF interactions based on experimental data
    </a>
  </p>



<h2 class="wiki-section-header">
    Overview
</h2>
    <br>
    There are <?php echo $n_total; ?> total interactions.
    <br>

    There are <?php echo count($distinct_bases); ?> distinct regulator genes:
    <ul>
    <?php
    foreach ($distinct_bases as $base) {
        echo "<li>$base->gene_id ($base->count interactions)</li>";
    }
    ?>
    </ul>


    There are <?php echo count($distinct_types); ?> types of interactions:
    <ul>
    <?php
    foreach ($distinct_types as $base) {
        echo "<li>$base->interaction_type ($base->count interactions)</li>";
    }
    ?>
    </ul>


    <!--
    There are <?php echo count($distinct_exps); ?> types of experiments:
    <ul>
    <?php
    foreach ($distinct_exps as $base) {
        echo "<li>$base->experiment ($base->count interactions)</li>";
    }
    ?>
    </ul>
    -->


<h2 class="wiki-section-header">
    Listing all PDI
</h2>
  <br>

  <?php echo $datatable; ?>

  <br>
  <br>

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $('#nav_access').addClass("active");
    })
</script>

<?= $this->endSection() ?>