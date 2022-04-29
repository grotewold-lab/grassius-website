<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

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
                    
                    // link back to old grassius for non-maize data
                    if( $species == "Maize" ){
                        $href = "/species/$species";
                    } else {
                        $href = "http://grassius.org";
                    }
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
                  <img src="/images/gramene/tools.png">
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
            href="https://blast.eglab-dev.com"
            class="gramene-tool col-md-6 list-group-item"
          >
            <div class="media" >
              <div class="media-middle media-left" >
                  <img src="/images/gramene/ExpressionAtlas.png">
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
          <h2 >Latest News <p style="font-size:10px;">(placeholder, showing publications)</p></h2>
        <ul class="posts list-unstyled" style="overflow: auto; height: 600px;">
          <li>
            <strong>Design of Knowledge Bases for Plant Gene Regulatory Networks</strong>
            <br />
            Mukundi E, Gomez-Cano F, Ouma WZ, Grotewold E. <a href="https://link.springer.com/protocol/10.1007%2F978-1-4939-7125-1_14">Methods Mol Biol. 1629:207-223</a>. 
            <span>2017-06-17</span>
          </li>
          <li>
            <strong>A Maize Gene Regulatory Network for Phenolic Metabolism</strong>
            <br />
            Yang F, Li W, Jiang N, Yu H, Morohashi K, Ouma WZ, Morales-Mantilla DE, Gomez-Cano FA, Mukundi E, Prada-Salcedo LD, Velazquez RA, Valentin J, Mejía-Guerra MK, Gray J, Doseff AI, Grotewold E. <a href="https://www.sciencedirect.com/science/article/pii/S1674205216302751?via%3Dihub">Mol Plant. 10(3):498-515</a>.
            <br />
            <span>2017-03-06</span>
          </li>
          <li>
            <strong>Establishing the Architecture of Plant Gene Regulatory NetworksS</strong>
            <br />
            Yang F, Ouma WZ, Li W, Doseff AI, Grotewold E. <a href="https://www.sciencedirect.com/science/article/pii/S0076687916001154?via%3Dihub">Methods Enzymol. 576:251-304</a>.
            <br />
            <span>2016-03-04</span>
          </li>
          <li>
            <strong>The Maize TFome - Development of a transcription factor open reading frame collection for functional genomics</strong>
            <br />
            Burdo B, Gray J, Goetting-minesky MP, et al. <a href="https://onlinelibrary.wiley.com/doi/10.1111/tpj.12623/abstract">Plant J. 80(2):356-366</a>. <a href="tfomecollection.php">Grassius TFome Collection</a>.
            <br />
            <span>2014-07-23</span>
          </li>
        </ul>
      </div>
    </div>
  </div>



<?= $this->endSection() ?>