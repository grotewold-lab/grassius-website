<?php
    # render navigation links in footer based on navigation_specs.php
?>

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

            
            <div class="col-md-3"></div>
            
        <?php foreach($specs as $id=>$sub_specs){ 
            $header_label = $all_header_labels[$id];
            ?>

            <div class="col-md-2">
                <div class="grassius-footer-section">
                    <h3><a href="/about" title="<?php echo $header_label; ?>"><?php echo $header_label; ?></a></h3>
                    <ul>
                        <?php foreach($sub_specs as $sub_label=>$ss_specs) { 
                            foreach($ss_specs as $link_text=>$link_url) {
                                echo "<li><a href='$link_url' title='$link_text'>$link_text</a></li>";
                            } 
                        } ?>
                    </ul>
                </div>
            </div>
            
        <?php } ?>
          </div>
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
            <blockquote>Copyright © 2023 Grassius.org | Last updated: 2023-06-13</blockquote>
          </div><!-- /nci-tagline inner --> 
        </div><!-- /nci-tagline --> 
      </div><!-- /page-bottom-inner --> 
    </div><!-- /page-bottom --> 
  </div>
