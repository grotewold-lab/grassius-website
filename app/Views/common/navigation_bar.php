<?php
    # render navigation bar based on navigation_specs.php
?>

<?php require_once "navigation_specs.php"; ?>

<nav class="navbar navbar-static-top navbar-default">
      <div class="container"> 
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
        </div><!-- .navbar-header --> 
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div id="bs-example-navbar-collapse-1" class="navbar-collapse collapse">
          <nav role="navigation"> 
            <!-- Region: Navigation -->
              <div class="region region-navigation">
    <div class="block block-block">
    <div class="inner">
                                <div class="content">
            
	 
    
	<ul class="wfui-hover-fat-menu main-menu" id="wfui-hover-fat-menu">
        
        <?php 
        $i = 0;
        $n = count($specs);
        foreach($specs as $id=>$sub_specs){ 
            $header_label = $all_header_labels[$id];
            $header_link = $all_header_links[$id];
            $last = (($i == ($n-1)) ? "last" : "");
            $i += 1;
            ?>
        
            <li class="trigger level-1 dd <?php echo $last;?>">
            <?php echo "<a id='$id' href='$header_link' class='dd-link' title='$header_label'>$header_label</a>"; ?>
        
                <div class="submenu">
                    <div class="submenu-inner">
                        <div class="submenu-main-desc">
                            <div class="submenu-main">

                            <?php foreach($sub_specs as $sub_label=>$ss_specs) { ?>

                                <div class="level-2">
                                    <h3><?php echo $sub_label; ?></h3>
                                    <ul class="clearfix">

                                    <?php foreach($ss_specs as $link_text=>$link_url) {
                                        echo "<li><a href='$link_url' title='$link_text'>$link_text</a></li>";
                                    } ?>

                                    </ul>
                                </div>

                            <?php } ?>

                            </div>
                        </div>
                    </div>
                </div>
            </li>
        
        <?php } ?>
                
                </ul>


		        </div>
    </div>
</div>
  </div>
 
          </nav>
        </div><!-- /navbar-collapse --> 
      </div><!-- /.container --> 
    </nav>
	
