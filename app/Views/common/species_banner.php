<?php
    $banner_src = get_species_banner_image_src($species);
    echo "<a href='/species/$species'><img style='float:none; width:100%;' src='$banner_src' align='right'></a>";

    [$esd_url, $esd_label] = get_external_species_db($species);
?>


<style>
#species-banner-container ul.menu > li{
    width:auto;
    list-style-image:none;
    list-style-type:none;
}
.species-links.fusion-inline-menu .inner ul.menu li{
    border-right-color:#97abb6;
}
.fusion-inline-menu .inner ul.menu li.last{
    border-right:none;
}
</style>

<div class="full-width">
    <div id="species-banner-container" class="container">
      <div class="row page-bottom-inner inner clearfix">
        <div class="species-links even col-md-12 fusion-center-content fusion-inline-menu">
          <div class="inner"> 
            <ul class="menu">
                <li class="leaf first"><a href="/species/<?php echo $species; ?>"><?php echo $species; ?> Portal</a></li> 
                <li class="leaf"><a href="https://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?id=<?php echo get_ncbi_taxonomy_id($species) ?>" target="_blank">NCBI</a> </li>
                <li class="leaf last"><a href="<?php echo $esd_url; ?>" target="_blank"><?php echo $esd_label; ?></a> </li> 
            </ul>
          </div>
        </div>
      </div><!-- /page-bottom-inner --> 
    </div><!-- /page-bottom --> 
  </div>

