<div id="postscript-bottom-wrapper" class="postscript-bottom-wrapper full-width">
    <div id="postscript-bottom" class="postscript-bottom full-width">
        <div id="postscript-bottom-inner" class="postscript-bottom-inner inner clearfix">

            <!-- REGION: Postscript bottom - - - - - - - - - - - - - - - - - - - - - -->
            <div class="region region-postscript-bottom">
                <div id="backtotop-container" class="block block-block">
                    <div class="inner">
                        <div class="content">
                            <p><a href="javascript:window.scrollTo(0,0);" class="backToTop">Back to top</a></p>        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /postscript-bottom-inner -->
</div>

		
<div id="footer" class="footer-wrapper">
      <div class="container">
        <div class="row">

            <div class="col-md-2"></div>
            <div class="col-md-2">
                    <div class="grassius-footer-section">
                        <h3><a href="/about" title="About Grassius">About Grassius</a></h3>
                        <ul>
                            <li>
                                <a href="/about" title="about">About</a>
                            </li>
                            <li>
                                <a href="/people" title="people">People</a>
                            </li>
                            <li>
                                <a href="/links" title="links">Links</a>
                            </li>
                            <li>
                                <a href="/info" title="information">Information</a>
                            </li>
                        </ul>
                    </div>
                </div>
            <div class="col-md-2">
                    <div class="grassius-footer-section">
                    <h3><a href="#" title="Analyze Data">About the Data</a></h3>
                            <ul class="analyze-data-menu">
                            <li><a href="/translation_tool" >Translation Tool</a></li>
                            <li><a href="#" >Placeholder</a></li>
                            <li><a href="#" >Placeholder</a></li>
                        </ul>
                            </div>
                </div>
            <div class="col-md-2">
                    <div class="grassius-footer-section">
                    <h3><a href="#" title="Analyze Data">Analyze Data</a></h3>
                            <ul class="analyze-data-menu">
                            <li><a href="#" >Placeholder</a></li>
                            <li><a href="#" >Placeholder</a></li>
                            <li><a href="#" >Placeholder</a></li>
                        </ul>
                            </div>
                </div>
            <div class="col-md-2">
                    <div class="grassius-footer-section">
                    <h3><a href="/" title="Access Data">Access Data</a></h3>
                            <ul class="access-data-menu">


                            <?php foreach( list_basic_species() as $species ){ 
    
                                // link back to old grassius for non-maize data
                                if( $species == "Maize" ){
                                    $href = "/species/$species";
                                } else {
                                    $href = "http://grassius.org";
                                }
                                
                                ?>
                                
                            <li>
                                <a href="<?php echo $href; ?>" title="<?php echo $species ?>"><?php echo $species ?></a>
                            </li>
                                
                            <?php } ?>


                            <li>
                                <a href="/browsefamily/Maize/Coreg" title="CoregDB">CoregDB</a>
                            </li>
                            <li>
                                <a href="/browsefamily/Maize/TF" title="TFDB">TFDB</a>
                            </li>

                        </ul>
                            </div>
                </div>
        
          </div>
        <div class="row">
            
            
            <div class="col-md-2"></div>
            <div class="col-md-2">
                    <div class="grassius-footer-section">
                    <h3><a href="#" title="Submit Data">Submit Data</a></h3>
                            <ul>
                            <li><a href="#" >Placeholder</a></li>
                            <li><a href="#" >Placeholder</a></li>
                            <li><a href="#" >Placeholder</a></li>
                        </ul>
                            </div>
                </div>
            <div class="col-md-2">
            <div class="grassius-footer-section">
            <h3><a href="#" title="For Developers">For Developers</a></h3>
                    <ul>
                    <li><a href="#" >Placeholder</a></li>
                    <li><a href="#" >Placeholder</a></li>
                    <li><a target="_blank" href="https://github.com/grotewold-lab/new-grassius-no-tripal" >View on Github</a></li>
                </ul>
                    </div>
            </div>
            <div class="col-md-2">
                    <div class="grassius-footer-section">
                    <h3><a href="#" title="Contact Us">Support</a></h3>
                            <ul>
                            <li><a href="#" >Placeholder</a></li>
                            <li><a href="#" >Placeholder</a></li>
                            <li><a href="#" >Placeholder</a></li>
                        </ul>
                        </div>
                </div>
            <div class="col-md-2">
                    <div class="grassius-footer-section">
                    <h3><a href="#">News</a></h3>
                            <ul>
                            <li><a href="#" >Placeholder</a></li>
                            <li><a href="#" >Placeholder</a></li>
                            <li><a href="#" >Placeholder</a></li>
                        </ul>
                        </div>
                </div>


        </div><!-- /.row --> 
      </div><!-- /.container --> 
      </div>




<div id="page-bottom-wrapper" class="page-bottom-wrapper full-width">
    <div id="page-bottom" class="page-bottom container">
      <div id="page-bottom-inner" class="row page-bottom-inner inner clearfix">
        <br/>
        <div class="nci-links-agency even col-md-12 fusion-center-content fusion-inline-menu">
          <div class="inner"> 
            <ul class="menu">
              <li class="leaf first"><a href="http://agris-knowledgebase.org/" target="_blank">AGRIS</a></li> 
              <li class="leaf"><a href="http://www.msu.edu" target="_blank">MSU</a> </li>
              <li class="leaf"><a href="https://bmb.natsci.msu.edu/faculty/erich-grotewold/" target="_blank">GROTEWOLD LAB</a> </li> 
              <li class="leaf"><a href="https://bmb.natsci.msu.edu/"  target="_blank">BIOCHEMISTRY DEPARTMENT</a> </li>
              <li class="leaf"><a href="mailto:grotewold.grassius@gmail.com"  target="_blank">CONTACT</a> </li>
              <li class="leaf last"><a href="/login" target="_blank" class="last link-usa">ADMIN LOGIN</a> </li>
            </ul>
          </div>
        </div>
        <div class="nci-tagline block odd last fusion-center-content grid12-12">
          <div class="inner clearfix">
            <blockquote>Copyright © 2021 Grassius.org | Last updated: 2021-12-15</blockquote>
          </div><!-- /nci-tagline inner --> 
        </div><!-- /nci-tagline --> 
      </div><!-- /page-bottom-inner --> 
    </div><!-- /page-bottom --> 
  </div>
