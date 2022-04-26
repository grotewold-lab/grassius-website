<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

    <div class="contenttop">
        
      <br>
      <br>
      <h2>Edit Gene</h2>
    </div>
    <div id="content_bottom">
      <br>
          <table class="protein">
              <tr>
                <td class="tg-lkh3">Gene ID:</td>
                <td><input type="text" value="<?php echo $results['id_name']; ?>"></td>
              </tr>
              <tr>
                <td class="tg-lkh3">Protein Name:</th>
                <td><input type="text" value="<?php echo $results['protein_name']; ?>"></td>
              </tr>
              <tr>
                <td class="tg-lkh3">Species:</td>
                <td><input type="text" value="<?php echo $results['species']; ?>"></td>
              </tr>
              <tr>
                <td class="tg-lkh3">Family:</td>
                <td><input type="text" value="<?php echo $results['family']; ?>"></td>
              </tr>
        <tr> 
            <td>
            <button class="col-1 btn btn-primary btn-block" type="submit">Save</button>
            </td>
          </tr>
            </table>
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