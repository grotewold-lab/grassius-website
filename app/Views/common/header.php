<div id="header" class="header header-wrapper">
  <div class="container">
    <div class="row">
      <!-- Site info -->
    <div class="container" style="position:relative">
	<div class="navbar-header" >
		<a class="logo-link navbar-brand" >
			<div class="logo" >
                <a href="/" title="Home" rel="home" id="logo">
                  <img
                    src="/images/banner.jpg"
                    alt="Grassius Logo"
                    class="banner-logo"
                    style="width:300px; position:absolute; left:0px; bottom:2px"
                  />
                </a>
            </div>
		</a>
	</div>
	<ul class="search-box-nav nav navbar-nav navbar-right" style="position:absolute; bottom:0px; right:0px;">
		<span class="input-group" >
			<input type="search" value="" tabindex="1" placeholder="Search for families, genes, and clones" autocomplete="off" spellcheck="false" autocorrect="off" autocapitalize="off" id="search-box" name="search-box" class="no-highlight form-control" >
            <div id="append_autocomplete"></div>
				<span class="input-group-btn" >
					<div class="dropdown btn-group" >
						<button id="search-help-button" role="button" aria-haspopup="true" aria-expanded="false" type="button" class="dropdown-toggle btn btn-default"  onclick="location.href='/about';">
							<span class="glyphicon glyphicon-question-sign" ></span>
						</button>
								</div>
								<div class="dropdown btn-group" >
									<button id="genomes-of-interest-dropdown" role="button" aria-haspopup="true" aria-expanded="false" type="button" class="dropdown-toggle btn btn-default" onclick="location.href='/';">
										<div class="results-summary" >
											<span class="gene-count" >
												<strong class="">9046</strong>
                                                
												<!-- react-text: 52 --> TFs
												<!-- /react-text -->
											</span>
											<span class="join" > in </span>
											<span class="genomes-count" >
												<strong class="">5</strong>
												<!-- react-text: 59 --> grasses
												<!-- /react-text -->
											</span>
										</div>
										<!-- react-text: 60 -->
										<!-- /react-text -->
										<span class="caret" ></span>
									</button>
								</div>
							</span>
						</span>
						<ol class="list-inline search-filters"></ol>
					</ul>
				</div>
        
        
        
        <!--
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 logo site-info">
        <a href="/" title="Home" rel="home" id="logo">
          <img
            src="/images/banner.jpg"
            alt="Grassius Logo"
            class="banner-logo"
          />
        </a>
        <div id="name-and-slogan">
          <div id="site-name"><strong> </strong></div>
        </div>
      </div>
        -->
        
      <!-- /.site-info -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container -->
</div>

<style>
    .ui-autocomplete {
        z-index: 100 !important;
    }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
var $ =jQuery.noConflict();

function try_open_url(url) {
    if (url.startsWith("/")) {
        location.href = url;
    }
}
    
$(document).ready(function(){
    ac_instance = $( "#search-box" ).autocomplete({
        source: function( request, response ) {
            $.ajax( {
              url: "/search/" + request.term,
              dataType: "jsonp",
              success: function( data ) {
                response( data );
              }
            } );
        },
        select: function( event, ui ) {
            try_open_url(ui.item.value);
        }
    }).autocomplete("instance")
        
    ac_instance._renderItem = function(ul, item) {
        var link = $('<a>').append(item.label).attr("href", item.value );
        return $("<li>").addClass("term").append(link).appendTo(ul);
    };
    
    ac_instance._renderMenu = function( ul, items ) {
        var that = this,
            currentCategory = "";
        $.each( items, function( index, item ) {
            if ( item.category != currentCategory ) {
                ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
                currentCategory = item.category;
            }
            that._renderItemData( ul, item );
        });
    }
    
    $('#search-box').keypress(function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){ 
            try_open_url($(this).val());
        }
    });
    
    /*
    // workaround chrome bug 
    // pause script execution with F8
    // https://stackoverflow.com/a/60283510
    document.addEventListener('keydown', function (e) {
        if (e.keyCode == 119) { // F8
            debugger;
        }
    }, {
        capture: true
    });
    */
});
</script>

<?php require_once "navigation_bar.php"; ?>

<?php require_once "message_alert.php"; ?>

              
              





