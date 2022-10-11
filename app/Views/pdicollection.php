<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

<br>
<h2 class="wiki-top-header">
    PDI (Protein-DNA Interaction) Collection
</h2>
<br>
<br>

<div class="search-form">
    
    <label for="select_sort">Sort by: </label>
    <select id="select_sort" name="sort_col_index">
        <?php 
            $i = 0;
            foreach( $sort_options as $label ) {
                if( $i == 8 ){
                    $selected = "selected";   
                } else {
                    $selected = "";   
                }
                echo "<option value='$i' $selected>$label</option>";
                $i += 1;
            }
        ?>
    </select>
    
    &nbsp;&nbsp;&nbsp;
    
    <input id="sort_dir_asc" type="radio" name="sort_dir" value="ASC" checked>
    <label for="sort_dir_asc">ascending</label>
    
    <input id="sort_dir_desc" type="radio" name="sort_dir" value="DESC">
    <label for="sort_dir_desc">descending</label>
    
    <hr>

    
    <div class="ui-slider-outer-container">
        <p>
            <label>Distance between</label>
            <input type="text" id="distance_range_min" class="ui-slider-text-box">
            <label>and</label>
            <input type="text" id="distance_range_max" class="ui-slider-text-box">
            <label>kb</label>
        </p>
        <div class="ui-slider-inner-container">
            <canvas id="histogram" width='470' height='100'></canvas>
            <div id="slider-range" style="width:470px;"></div>
        </div>
    </div>

    <hr>
    
    <label>Search term:</label>
    <input type="text" id="search_term">
    &nbsp;
    <label>(gene, protein, or experiment)</label>
    
        
    <hr>
    <button id="submit" type="button">Filter PDIs</button>
    
</div>

<br>
<a href="#" id="download" hidden>download excel sheet</a>
<br>

  <br>

  <?php echo $datatable; ?>

  <br>
  <br>

<script>
    var $ = jQuery.noConflict();
    
        
    min = -2.1
    max = 2.1
    scale = 100
        
    function update_distance_range_label(){
        var min_val = $( "#slider-range" ).slider( "values", 0 )/scale
        var max_val = $( "#slider-range" ).slider( "values", 1 )/scale
        $( "#distance_range_min" ).val(min_val)
        $( "#distance_range_max" ).val(max_val)
    }
    
    function update_distance_range_slider(){
        var min_val = parseFloat($( "#distance_range_min" ).val()) * scale
        var max_val = parseFloat($( "#distance_range_max" ).val()) * scale
        $( "#slider-range" ).slider( "option", "values", [ min_val, max_val ] );
    }
        
    function isEmptyOrSpaces(str){
        return str === null || str.match(/^ *$/) !== null;
    }
    
    function update_datatable(){
        var sort_col_index = $('#select_sort').val()
        var sort_dir = $('input[name="sort_dir"]:checked').val()
        var min_val = parseFloat($( "#distance_range_min" ).val())
        var max_val = parseFloat($( "#distance_range_max" ).val())
        var search_term = $('#search_term').val()
        var url_suffix = [sort_col_index, sort_dir, min_val, max_val, search_term].join('/')
        
        var url = '/pdicollection/filtered_datatable/'+url_suffix
        pdi_table.ajax.url( url ).load();
        
        var csv_url = '/pdicollection/download_table/'+url_suffix
        $('#download').attr("href", csv_url).show();
    
        if (!isEmptyOrSpaces(search_term)) {
            show_histogram_loading();
            $.ajax({
              method: "POST",
              url: "/pdicollection/filtered_histogram/" + search_term,
            }).done(function( bin_counts ) {
                update_histogram(JSON.parse(bin_counts));
            });
        }
    }
    
    function show_histogram_loading() {
        var canvas = document.getElementById("histogram");
        var ctx = canvas.getContext("2d");
        ctx.font = "12px Arial";
        ctx.fillStyle = "black";
        ctx.fillText("Loading filtered histogram...", 10,20);
    }
    
    <?php 
        echo "var total_bin_counts = ".json_encode( $distance_hist ).";\n";
    ?>
    function update_histogram( filtered_bin_counts=null ){
    
        var c = document.getElementById("histogram");
        var ctx = c.getContext("2d");
        var w = c.width;
        var h = c.height;
        ctx.clearRect(0, 0, w, h);
    
        ctx.fillStyle = '#AAAAFF';
        draw_hist( ctx, w, h, total_bin_counts );
    
        if( filtered_bin_counts != null ){
            ctx.fillStyle = "rgba(255, 150, 150, 0.5)";
            draw_hist( ctx, w, h, filtered_bin_counts );
    
            var canvas = document.getElementById("histogram");
            var ctx = canvas.getContext("2d");
            ctx.font = "12px Arial";
            ctx.fillStyle = "blue";
            ctx.fillText("All Data", 10,20);
            ctx.fillStyle = "red";
            ctx.fillText("Filtered Data", 10,35);
        }
    }
    
    function draw_hist( ctx, w, h, bin_counts ){
        var n = bin_counts.length;
        var max_count = Math.max(...bin_counts);
        for (var i = 0; i < n; i++) {
            var height = h*bin_counts[i]/max_count;
            ctx.fillRect(w*i/n, h-height, (w/n)+1, h);
        }
    }
    
    $(document).ready(function(){
        $('#nav_access').addClass("active");
        
        //build histogram
        update_histogram();
                              
        // build slider
        var $slider =  $('#slider-range');
        $slider.slider({
          range: true,
          min: min*scale,
          max: max*scale,
          values: [ min*scale, max*scale ],
          slide: function( event, ui ) {
            update_distance_range_label()
          }
        });
        update_distance_range_label()
        $("#distance_range_min").on("input", update_distance_range_slider);
        $("#distance_range_max").on("input", update_distance_range_slider);
        
        // add ticks to slider  
        var start = -2
        var spacing = .5
        var tick_offset = -.015
        var label_offset_pct = -2
        var decimals = 1
        $slider.find('.ui-slider-tick-mark').remove();
        for (var i = start; i < max ; i+=spacing) {
            var tick_left = (100-100*(max-(i+tick_offset))/(max-min)) + '%'
            var label_left = (label_offset_pct+100-100*(max-(i+tick_offset))/(max-min)) + '%'
            $('<span class="ui-slider-tick-mark"></span>').css('left', tick_left).appendTo($slider); 
            $('<span class="ui-slider-tick-label">' + i.toFixed(decimals) + '</span>').css('left', label_left).appendTo($slider); 
         }
        
        // enable button "Filter PDIs"
        $('#submit').click(update_datatable);
    })
    
    
</script>

<style>
    .dataTables_filter {
        display: none;
    }
</style>

<?= $this->endSection() ?>