<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

    <div class="contenttop">
        
      <br>
      <br>
        <p><a href="#">add new family</a></p>
      <hr>
      <br>
      <h2>Manage Existing Families</h2>
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
        $('#nav_family_admin').addClass("head")
    })
</script>

<?= $this->endSection() ?>