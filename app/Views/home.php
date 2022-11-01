<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

<?php require "common/subdomain_urls.php";?>

  <div class="row" >
    <div class="col-sm-12" ></div>
  </div>
  <div class="row" >
    <div class="tools-col col-sm-9" >
      <div class="tools-wrapper" >
        <h2 >Grassius Portals</h2>
        <div class="row" >
        <div style="width:500px; height:600px;">
          <?php 
    
                $all_species = list_basic_species();

                //arrange species icons in a circle
                $cx = 250; // center of circle
                $cy = 250; 
                $a = -pi()/2; //initial angle
                $da = 2*pi() / count($all_species); // change in angle between icons
                $radius = 200; // distance from center to icons

                foreach( $all_species as $species ){ 
                    $x = $cx + cos($a) * $radius;
                    $y = $cy + sin($a) * $radius;
                    $a += $da;
                    $href = "/species/$species";
            ?>
          <a
            href="<?php echo $href; ?>"
            class="homepage-circle-button"
            style="position:absolute; top:<?php echo $y;?>px; left:<?php echo $x;?>px; width:200px; height: 200px;"
          >
            <div class="media" >
              <div class="homepage-species-icon" >
                  <?php show_species_icon($species) ?>
              </div>
              <div class="homepage-species-label">
                  <?php echo $species; ?>
              </div>
            </div>
          </a>
          <?php } ?>
            
          <!--  
          <a
            href="/grasstfdb"
            class="gramene-tool list-group-item"
            style="position:absolute; top:<?php echo $cy;?>px; left:<?php echo $cx;?>px; width:200px; height: 200px;"
          >
            <div class="media" >
              <div class="media-middle media-left" >
                <img src="/images/gramene/ensemblgramene.png"/>
              </div>
              <div class="media-middle gramene-tool-text media-body">
                <h4 class="media-heading" >
                  TFDB
                </h4>
              </div>
            </div>
          </a>
          -->
        </div>
        </div>
        <h2 >Grassius Tools</h2>
        <div class="row list-group" >
          <a
            href="/translation_tool"
            class="gramene-tool col-md-6 list-group-item"
          >
            <div class="media" >
              <div class="media-middle media-left" >
                  <img src="/images/translate.svg">
              </div>
              <div class="media-middle gramene-tool-text media-body">
                <h4 class="media-heading" >
                  Translation Tool
                </h4>
                <p class="gramene-tool-desc" >
                  Translate Gene Model IDs
                </p>
              </div>
            </div>
          </a>
          <a
            href="<?php echo $blast_tool_url; ?>"
            class="gramene-tool col-md-6 list-group-item"
          >
            <div class="media" >
              <div class="media-middle media-left" >
                  <img src="/images/search.svg">
              </div>
              <div class="media-middle gramene-tool-text media-body">
                <h4 class="media-heading" >
                  BLAST
                </h4>
                <p class="gramene-tool-desc" >
                  Homology Search Tool
                </p>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
    <div class="posts-col col-sm-3" >
      <div class="posts-wrapper" >
          <h2 >Recent News</h2><br>
        <ul class="posts list-unstyled" style="overflow: auto; height: 600px;">
          <?php echo $news_html; ?>
        </ul>
      </div>
    </div>
  </div>



<?= $this->endSection() ?>