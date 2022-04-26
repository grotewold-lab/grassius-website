<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

    <div class="contenttop">
        
      <br>
      <br>
      <h2>Edit Family</h2>
    </div>
    <div id="content_bottom">
      <br>

          <table class="protein">
              <tr>
                <td class="tg-lkh3">Family Name:</td>
                <td><input type="text" value="<?php echo $results['family']; ?>"></td>
              </tr>
              <tr>
                <td class="tg-lkh3">Class:</th>
                <td><input type="text" value="<?php echo $results['class']; ?>"></td>
              </tr>
        <tr> 
            <td>
            <button class="col-1 btn btn-primary btn-block" type="submit">Save</button>
            </td>
          </tr>
            </table>
      <br>
   </div>     

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $('#nav_family_admin').addClass("head")
    })
</script>

<?= $this->endSection() ?>