<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

    <div class="contenttop">
        
      <br>
      <br>
      <h2>Add/Replace genes</h2>
        <p><a href="#">download template spreadsheet</a></p>
        <p><a href="#">upload completed spreadsheet</a></p>
      <hr>
      <br>
      <h2>Manage Existing Genes</h2>
    </div>
    <div id="content_bottom">
      <br>

      <?php echo $datatable; ?>
          
      <br>
      <br>
   </div> 

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $('#nav_gene_admin').addClass("head")
    })
</script>

<?= $this->endSection() ?>