<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

    <div class="contenttop">
      <br>
      <h2>Welcome to Coregulator Database
      </h2>
      <br>
      <p>GrassCoRegDB provides a collection of proteins that are transcriptional regulatory factors but do not bind DNA in a sequence specific fashion. 
        These proteins are broadly defined as transcriptional regulators that either act by interacting with transcription factors or as chromatin modifiers 
        restricting or releasing DNA accessibility. Thus, GrassCoRegDB includes proteins with functions such as covalent histone modification
        (acetylation, methylation, phosphorylation, ubiquitination and sumoylation), as members of the ATP dependent chromatin remodeling complexes, 
        and as histone chaperones, among others. The specific role of many of these proteins in transcription is still unknown. In some cases the community 
        has already set-up some rules for systematic naming of those proteins, in these a protein name is reported in Grassius. For proteins lacking a systematic name,
        loci identifiers or common names are used instead. As identification of proteins with roles in transcriptional regulation is a growing research field, GrassCoRegDB 
        will be frequently updated to keep pace with the literature. For this set of proteins the same curation efforts as for other Grassius collections are applied and links
        to other Grassius resources such as the TFome collection are available.
      </p>
      <br>
      <hr>

      <h3>
        <b>Browse families
        </b>
      </h3>
      <hr>
      Please select the family or click on species to browse the coregulators 
      <br>
      <br>
      <table  class="tg">
        <tr>
        </tr>
        <tr>
          <td class="tg-7wmh">
            <a href="browsefamily/Maize/Coreg">Maize Coregs
              </td>
          <td class="tg-7wmh">
            <a href="browsefamily/Rice/Coreg">Rice Coregs
              </td>
          <td class="tg-7wmh">
            <a href="browsefamily/Sorghum/Coreg">Sorghum Coregs
              </td>
          <td class="tg-7wmh">
            <a href="browsefamily/Sugarcane/Coreg">Sugarcane Coregs
              </td>
          <td class="tg-7wmh">
            <a href="browsefamily/Brachypodium/Coreg">Brachypodium Coregs
              </td>
        </tr>
        <tr>
        </tr>
        <tr>
        </tr>
        <tr>
            
              <?php
              foreach( array("Maize","Rice","Sorghum","Sugarcane","Brachypodium") as $species){
              ?>
              <td class="tg-uztx">
                <center>
                    <select  id="select_<?php echo $species; ?>" width="100" style="width: 100px">
                      <option selected value="#">Select family</option>                     
                        <?php 
                        foreach ($family_names as $fam){
                            echo '<option value="family/'.$species.'/'.$fam.'">'.$fam.'</option>';
                        }                    
                        ?>
                    </select>
                    <br>
                    <br>
                    <form id="go_<?php echo $species; ?>" action="#" method="get">
                        <input type="submit" value="Go">
                    </form>
                    <script>
                        var $ =jQuery.noConflict();
                        $(document).ready(function(){
                            $('select#select_<?php echo $species; ?>').on('change', function() {
                              $('form#go_<?php echo $species; ?>').attr( "action", this.value );
                            });
                        });
                    </script>
                </center>
              </td>
              <?php } ?>
          </tr>
      </table> 
    <hr>                        
</div>    
        <!--
tools  section 
-->
        <div id="content_bottom">
          <div id="bottom_left">
            <h2>Search For Coregulators
            </h2>
            <br>
            <b>By Coreg Identifiers:</b>
            <br>
            Start typing to see gene identifier suggestions
            <div id="searchform">
            <input type="text" id="searchterm" name="searchterm" class="typeahead tt-query" placeholder="Search..." required>
            <form id="search" action="#"  autocomplete="off">
                <input class="submit" type="submit" value="Search">
            </form>
            <script>
                $(document).ready(function() {
                  $('form#search').submit(function() {
                    $(this).attr('action', '/search_results/' + $('input#searchterm').val() ); 
                    return true;
                  });
                });
            </script>
                <br>
                <br>(eg. LOC_Os01g01290 or AC177820.3_FGT004)
             
            </div>
          </div> 
          <div id="bottom_right">
           
        <div id="container">
          <h2 style="text-align: left;">
            <strong>Tool Available
            </strong>
          </h2>
          <h3>
            <a href="Jbrowser">Genome Browser:
            </a>
          </h3>
           Visually explore genomic data.<br>
          
          <br>
          <strong>Tools in Development <br><br>
            </strong>

            <b>Grassius Blast:</b>
            
            Perform localized searches for transcription factors.<br>
          
            <b>GRG-X:</b>
              View gene interactions networks in a graphical interface. &nbsp;
              
            </div>
          </div> 
        </div>
 
<script src="/js/typeahead.min.js"></script>
<script>
    var $ =jQuery.noConflict();

    $(document).ready(function(){
        
        $("select").val('#');

        $("#nav_access").addClass("active");

        $('input.typeahead').typeahead({
            name: 'searchterm',
            remote:'autocomplete/Coreg/%QUERY',
            limit : 10
        });
    });
</script>

<?= $this->endSection() ?>
