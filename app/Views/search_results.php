<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>


        <div class="contenttop">
     
    <?php require_once "common/species_banner.php"; ?>
    
          <br>
          <br>
          <br>
          <br>
          
        <?php
        if ($results) {
            echo "Search Results for ".$gene."<br>";
            $row = $results;
            ?>
            
            
            <table class="wikitable" style="width:100%">
                <thead>
                    <tr>
                        <th>
                            Protein Name <br><font color=#ce6301>accepted</font>&#x2F;<font color=#808B96>suggested</font>
                        </th>
                        <th>Gene Locus</th>
                        <th>Synonym/<br>Gene Name</th>
                        <th>Clone in TFome</th>
                        <th>Genome Browser</th>
                    </tr>
                </thead>
                
            
            <?php

            if (strcmp($row['grassius_name'], $row['id_name']) == 0) {

                echo "<td class=sugg></td>";

            } elseif (strcmp($row['accepted'], "no") == 0) {
            ?>

                <td class=sugg >
                    <?php echo get_proteininfor_link($row['speciesname'], $row['grassius_name']); ?>
                </td>

            <?php
            } else {
            ?>

                <td class=accpt >
                    <?php echo get_proteininfor_link($row['speciesname'], $row['grassius_name']); ?>
                </td>

            <?php
            }
            ?>

                <td>
                    <?php echo get_external_db_link($row['speciesname'], $row['id_name']); ?>
                </td>
                <td>
                    <?php echo $row['othername'];?>
                </td>


                <td>
                    <?php echo get_tfomeinfor_link($row['clones']); ?>
                </td>
                <td>
                      <a href='javascript:;' onclick='alert("Genome browser is not yet available");'>View in Browser</a>
                </td>
            </tr>
            </table>

    <?php
        } else {
        ?>  
           <h2> Search Result for <font color="blue"> <?php echo $gene; ?></font> </h2> 
            <br>
           <h3><b> Sorry, there are no results for your query   </b></h3>
            <?php
        }
            ?>
          
        </div>
        
     

<?php
    if (strcmp($results['class'], "Coreg") == 0){
        $nav_id_suffix = 'grasscoregdb';
    }
    else {
        $nav_id_suffix = 'grasstfdb';
    }
?>

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $('#nav_<?php echo $nav_id_suffix ?>').addClass("head")
    })
</script>

<?= $this->endSection() ?>