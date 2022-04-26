<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

    <div class="contenttop">
        
      <br>
      <br>
      <h2>Edit Clone</h2>
      <hr>
      <br>
    </div>
    <div id="content_bottom">
        
      <table class='infortfome'>
        <tr>
          <td class="ttle">TFome Name
          </td>
          <td class="cont">
              <input type="text" value="<?php echo $clone_name ?>">
              must be unique
          </td>
        </tr>

    <tr>
           <td class="ttle">Template</td> 
          <td>
               <input type="text" value="<?php echo $template ?>">
          </td>
          </tr>

        <tr>
          <td class="ttle">Genbank Insert ID
          </td>
          <td class="cont">
            <input type="text" value="<?php echo $results['insert_gene_bank_id']?>">
            
          </td>
        </tr>
        <!--
<tr><td class="ttle">Template</td> 
<td><a href='http://www.ncbi.nlm.nih.gov/sites/entrez?db=nucest&amp;cmd=search&amp;term='> </a></td></tr>
-->
        <tr>
          <td class="ttle">Gene ID
          </td> 
          <td class="cont"> 
            <input type="text" value="<?php echo $results['id_name'];?>">
            determines class and family. must match existing gene.
          </td>
        </tr>
        <tr>
          <td class="ttle">transcript
          </td>
          <td class="cont"> 
            <input type="text" value="<?php echo $results['transcript_number'];?>">
          </td>
        </tr>
        <tr>
          <td class="ttle">Vector
          </td>
          <td class="cont">
            <input type="text" value="<?php echo $results['vector'];?>">
          </td>
        </tr>
        <tr>
          <td class="ttle">Note
          </td>
          <td class="cont">
            <input type="text" value="<?php echo $results['notes'];?>">
          </td>
        </tr>
        <tr>
          <td class="ttle">Request Information:
          </td> 
          <td >
            <input type="text" value="<?php echo $results['request_info'];?>">
          </td>
        </tr>
      </table>
      <table class='infortfome'>
        <tr>
          <td class="ttle">5' Primer Name:
          </td> 
          <td class="cont">
            <input type="text" value="<?php echo $results['five_prime_name']?>">
          </td>
          <td class="ttle">3' Primer Name:
          </td> 
          <td class="cont">
            <input type="text" value="<?php echo $results['three_prime_name']?>">
          </td> 
        </tr>             
        <tr>
          <td class="ttle">5' Primer Tm (&#186 C):
          </td> 
          <td class="cont">
            <input type="text" value="<?php echo $results['five_prime_temp']?>">
          </td>
          <td class="ttle">3' Primer Tm (&#186 C):
          </td> 
          <td class="cont"> 
            <input type="text" value="<?php echo $results['three_prime_temp']?>">
          </td>
        </tr>
        <tr>
          <td class="ttle">5' Primer Sequence:
          </td>     
          <td class="cont">
            <input type="text" value="<?php echo $results['five_prime_seq']?>">
          </td>
          <td class="ttle" >3' Primer Sequence:
          </td>     
          <td class="cont">
            <input type="text" value="<?php echo $results['three_prime_seq']?>">
          </td> 
        </tr>
      </table>
      <table class='infortfome'>
        <tr>
          <td class="ttle">PCR Condition:
          </td> 
          <td>
            <input type="text" style="width:95%" value="<?php echo $results['pcr_condition']?>">
          </td>
        </tr>
        <tr>
          <td class="ttle">Nucleotide Sequence:
          </td> 
          <td class="cont">
              <textarea style="width:95%" rows="10"><?php echo $results['sequence']?></textarea>
          </td>
        </tr>
        <tr> 
            <td>
            <button class="col-1 btn btn-primary btn-block" type="submit">Save</button>
            </td>
          </tr>
      </table>
   </div>     

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $('#nav_clone_admin').addClass("head")
    })
</script>

<?= $this->endSection() ?>