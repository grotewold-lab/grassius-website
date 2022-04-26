<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>
      
    <div class="contenttop">
      <br>
      <h2>Welcome to The Transcription Factor Database
      </h2>
      <br>
      <p>GrassTFDB provides a comprehensive collection of transcription factors from maize, sugarcane, sorghum and rice.
        Other grasses will be included as sequence information becomes available. Transcription factors, defined here specifically 
        as proteins containing domains that suggest sequence-specific DNA-binding activities, are classified based on the presence of 50+ conserved domains.
        Links to resources that provide information on mutants available, map positions or putative functions for these transcription factors are provided.
        Transcription factors are being named using a standard nomenclature that will simplify references to them as well as comparative analyses between the grasses.
        <br>
        <br>
      <hr>

      <h3>
        <b>Browse families
        </b>
      </h3>
      <hr>
      Please select the family or click on species to browse the transcription factors 
      <br>
      <br>
      <table  class="tg">
        <tr>
          <!--
<th class="tg-uztx" colspan="6">Browse All Families (Click on species name to view and browse TF families)</th>
-->
        </tr>
        <tr>
          <td class="tg-7wmh">
            <a href="/browsefamily/Maize/TF"> 
              <b>Maize TFs
              </b>
            </a>
          </td>
          <td class="tg-7wmh">
            <a href="/browsefamily/Rice/TF">
              <b>Rice TFs
              </b>
            </a>
          </td>
          <td class="tg-7wmh">
            <a href="/browsefamily/Sorghum/TF">
              <b>Sorghum TFs
              </b>
            </a>
          </td>
          <td class="tg-7wmh">
            <a href="/browsefamily/Sugarcane/TF">
              <b>Sugarcane TFs
              </b>
            </a>
          </td>
          <td class="tg-7wmh">
            <a href="/browsefamily/Brachypodium/TF"> 
              <b>Brachypodium TFs
              </b>
            </a>
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
tools div section 
-->
    <div id="content_bottom">
      <div id="bottom_left">
        <h2>Search For Transcription Factors
        </h2>
        <br>
        <b>By Gene Identifiers:
        </b>
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
      <div id="bottom_right" >
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
            remote:'autocomplete/TF/%QUERY',
            limit : 10
        });

    });
</script>

<?= $this->endSection() ?>
