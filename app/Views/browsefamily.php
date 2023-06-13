<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

<?php require_once "common/species_banner.php"; ?>

<?php if( $species == 'Maize' ){ ?>
    <a href='/customfamily/<?php echo $species ?>'>build custom family...</a>
    <br>
<?php } ?>

<h2 class="wiki-top-header"><?php echo $species." ".$class." Families" ?></h2>
<p>The number next to each family name indicates the number of proteins in that family</p>
     
  
      <table class="fam">
        <?php
        foreach ($rows as $row) {
            print "<tr>";
            foreach ($row as $column) {
                $counts= $column['total']; //include TF counts for each family
            ?>
            <td>
              <a href="/family/<?php echo $species?>/<?php echo $column['familyname'] ?>"> 
            <?php echo $column['familyname']?> (<?php echo $counts?>)  
          </a>
        </td> 
        <?php
            }
            echo "</tr>";
        }
        echo "</table>";
?>

<br>
<h2 class="wiki-section-header">
    <?php echo $species?> <?php echo $class ?> Families Word Cloud
</h2>
<p>The size of each family name is proportional to the number of proteins in that family</p>
          
<div id="vis"></div>  

<form id="form" hidden>

<p style="position: absolute; right: 0; top: 0" id="status"></p>

<div style="text-align: center">
  <div id="presets"></div>
  <div id="custom-area">
    <p><label for="text">Paste your text below!</label>
    <p><textarea id="text"><?php echo $word_cloud_input; ?></textarea>
    <button id="go" type="submit">Go!</button>
  </div>
</div>

<hr>

<div style="float: right; text-align: right">
  <p><label for="max">Number of words:</label> <input type="number" value="250" min="1" id="max">
  <p><label for="per-line"><input type="checkbox" id="per-line" checked> One word per line</label>
  <!--<p><label for="colours">Colours:</label> <a href="#" id="random-palette">get random palette</a>-->
  <p><label>Download:</label>
    <button id="download-svg">SVG</button><!-- |
    <a id="download-png" href="#">PNG</a>-->
</div>

<div style="float: left">
  <p><label>Spiral:</label>
    <label for="archimedean"><input type="radio" name="spiral" id="archimedean" value="archimedean" checked="checked"> Archimedean</label>
    <label for="rectangular"><input type="radio" name="spiral" id="rectangular" value="rectangular"> Rectangular</label>
  <p><label for="scale">Scale:</label>
    <label for="scale-log"><input type="radio" name="scale" id="scale-log" value="log" checked="checked"> log n</label>
    <label for="scale-sqrt"><input type="radio" name="scale" id="scale-sqrt" value="sqrt"> √n</label>
    <label for="scale-linear"><input type="radio" name="scale" id="scale-linear" value="linear"> n</label>
  <p><label for="font">Font:</label> <input type="text" id="font" value="Impact">
</div>

<div id="angles">
  <p><input type="number" id="angle-count" value="5" min="1"> <label for="angle-count">orientations</label>
    <label for="angle-from">from</label> <input type="number" id="angle-from" value="0" min="-90" max="90"> °
    <label for="angle-to">to</label> <input type="number" id="angle-to" value="0" min="-90" max="90"> °
</div>

<hr style="clear: both">

<p style="float: right"><a href="about/">How the Word Cloud Generator Works</a>.
<p style="float: left">Copyright &copy; <a href="http://www.jasondavies.com/">Jason Davies</a> | <a href="../privacy/">Privacy Policy</a>. The generated word clouds may be used for any purpose.

</form>
        
<script src="/js/d3.min.js"></script>
<script src="/js/grassius_cloud.min.js"></script>

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $('#nav_access').addClass("active");
    })
</script>

<?= $this->endSection() ?>