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
    <label for="sort_dir_asc">ascending (lowest first)</label>
    
    &nbsp;&nbsp;&nbsp;
    
    <input id="sort_dir_desc" type="radio" name="sort_dir" value="DESC">
    <label for="sort_dir_desc">descending (highest first)</label>
    
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
            <table>
                <tr>
                    <td>
                        <label>Regulator Protein</label>
                    </td>
                    <td>
                        <input type="text" id="filter_protein_name" placeholder="no filter">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>Regulator Gene</label>
                    </td>
                    <td>
                        <input type="text" id="filter_gene_id" placeholder="no filter">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>Target Protein</label>
                    </td>
                    <td>
                        <input type="text" id="filter_target_name" placeholder="no filter">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>Target Gene</label>
                    </td>
                    <td>
                        <input type="text" id="filter_target_id" placeholder="no filter">
                    </td>
                </tr>

            </table>
    
        
    <hr>
        
        <label>Experiment</label>
        <?php foreach( $exp_types as $exp ){ 
            echo "&nbsp;&nbsp;&nbsp;";
            echo "<input type='checkbox' class='filter_exp' id='filter_exp_$exp' name='filter_exp' value='$exp' checked>";
            echo "&nbsp;";
            echo "<label for='filter_exp_$exp'>$exp</label>";
        }?>
        
    <hr>
        
    <button id="submit" type="button">Filter PDIs</button>
    <button id="update_histogram" type="button">Show Filtered Histogram</button>
    
</div>

<br>
<a href="#" id="download" hidden>download excel sheet</a>
<br>

  <br>

  <?php echo $datatable; ?>

  <br>
  <br>


<div class="hovermenu"></div>

<canvas id="my_canvas" width="800" height="600">

<script src="https://grotewold-lab.github.io/js/vis_tool.js"></script>
<style>
    .hovermenu {
        position: absolute;
        top: 0px;
        left: -300px;
        display: none;
        font-family: sans-serif;
        font-size: 12px;
        width: 200px;
        height: auto;
        background-color: #555;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 8px 6px;
        position: absolute;
        z-index: 100;
        opacity: 1.0;
    }

    .hovermenu.visible {
        display: block;
    }
</style>
    
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
    
    function build_ajax_url_suffix(){
        
        if( $('input.filter_exp').not(':checked').length > 0 ){
            var filter_exps = new Array();
            $('input.filter_exp:checked').each(function(){
                filter_exps.push($(this).val());
            });
            filter_exps = filter_exps.join(';')
        } else {
            filter_exps = ""   
        }
                                     
        return [
            $('#select_sort').val(),
            $('input[name="sort_dir"]:checked').val(),
            $( "#distance_range_min" ).val(),
            $( "#distance_range_max" ).val(),
            $('#filter_gene_id').val(),
            $('#filter_target_id').val(),
            $('#filter_protein_name').val(),
            $('#filter_target_name').val(),
            filter_exps
        ].join(',');
    }
    
    function update_datatable(){
        var url_suffix = build_ajax_url_suffix();
        
        var url = '/pdicollection/filtered_datatable/'+url_suffix
        pdi_table.ajax.url( url ).load();
        
        var csv_url = '/pdicollection/download_table/'+url_suffix
        $('#download').attr("href", csv_url).show();
        
        var vis_url = '/pdicollection/get_vis_json/'+url_suffix
        var old_c = document.getElementById("my_canvas");
        var c = old_c.cloneNode(true);
        old_c.parentNode.replaceChild(c, old_c);
        var ctx = c.getContext('2d');
        show_network_with_api(ctx,800,600,vis_url);
        add_mouse_listener_to_canvas(c,ctx);
    }
    
    function show_histogram_message(message) {
        var canvas = document.getElementById("histogram");
        var ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.font = "12px Arial";
        ctx.fillStyle = "black";
        ctx.fillText(message, 10,20);
    }
    
    function query_filtered_histogram(){
        var url_suffix = build_ajax_url_suffix();
        show_histogram_message("Loading...");
        $.ajax({
          method: "GET",
          url: "/pdicollection/filtered_histogram/" + url_suffix
        }).done(function( bin_counts ) {
            update_histogram(JSON.parse(bin_counts));
        });
    }
    
    <?php 
        echo "var total_bin_counts = ".json_encode( $distance_hist ).";\n";
        echo "var total_n = '".$distance_hist_n."';\n";
    ?>
    var all_hist_colors = [
        [255,150,150],
        [150,255,150],
        [255,255,150],
        [150,150,150],
        [255,255,255]
    ]
    function update_histogram( all_filtered_hists=null ){
    
        var c = document.getElementById("histogram");
        var ctx = c.getContext("2d");
        var w = c.width;
        var h = c.height;
        ctx.clearRect(0, 0, w, h);
    
        ctx.fillStyle = '#AAAAFF';
        draw_hist( ctx, w, h, total_bin_counts );
    
        if( (all_filtered_hists != null) && (all_filtered_hists.length>0) ){
            ctx.fillStyle = "blue";
            ctx.fillText("All Data (n=" + total_n + ")", 10,20);
            var label_y = 35
            for (var i = 0; i < all_filtered_hists.length; i++){
                var filtered_bin_counts = all_filtered_hists[i][1];
                var color = all_hist_colors[i]
                ctx.fillStyle = "rgba(" + color[0] + "," + color[1] + "," + color[2] + ", 0.5)";
                draw_hist( ctx, w, h, filtered_bin_counts );
            }
                                                          
            for (var i = 0; i < all_filtered_hists.length; i++){
                var label = all_filtered_hists[i][0];
                var color = all_hist_colors[i]
                ctx.font = "12px Arial";
                ctx.fillStyle = "rgb(" + (color[0]-150) + "," + (color[1]-150) + "," + (color[2]-150) + ")";
                ctx.fillText(label, 10,label_y);
                label_y += 15
            }
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
        
        $('#edit-params-acc').autocomplete({
            source: "/coexp-accession-autocomplete",
            select: function(event,ui){
                $('#edit-params-acc').val(ui.item.value); 
                return false;
            }
        });
        // enable autocomplete
        <?php foreach( ["gene_id","target_id","protein_name","target_name"] as $field ){ ?>
        $('#filter_<?php echo $field; ?>').autocomplete({
            source: "/pdicollection/autocomplete/<?php echo $field; ?>",
            select: function(event,ui){
                $('#filter_<?php echo $field; ?>').val(ui.item.value); 
                return false;
            }
        });
        <?php } ?>

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
        $('#update_histogram').click(query_filtered_histogram);
    })
    
    
</script>

<style>
    .dataTables_filter {
        display: none;
    }
</style>

<?= $this->endSection() ?>
