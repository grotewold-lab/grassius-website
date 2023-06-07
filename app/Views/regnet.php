<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

<br>
<h2 class="wiki-top-header">
    PDI (Protein-DNA Interaction) Network
</h2>
<br>
<br>

<a href="/pdicollection">Click here to view ALL PDIs</a>

<br>
<br>
<br>


<b>Related Publication</b>
<p align="justify">Yang, F., Li, W., Jiang, N., Yu, H., Morohashi, K., Ouma, W.Z., Morales-Mantilla, D., Gomez-Cano, F., Mukundi, E., Prada-Salcedo, L.D., Alers Velazquez, R., Valentin, J., Mejía-Guerra, M.K., Gray, J., Doseff, A.I., and Grotewold, E. (2017).<br>
    <b>A maize gene regulatory network for phenolic metabolism.</b><br>
<a href="http://www.plantphysiol.org">Mol Plant</a>, 10: 489-515.<br>
<a href="https://pubmed.ncbi.nlm.nih.gov/27871810/">PMID 27871810</a>
</p>


<br>
<b>Special thanks to contributer <a href="https://scholar.google.com/citations?user=5PzN_KYAAAAJ">Sarah Percival</a></b>

<br>

<br>
<br>

<div id="tutorial">
    <table>
        <tr>
            <td style="width:400px; padding-right:50px">
                <b>RegNet Tutorial</b>
                <ol>
                    <li>Start typing a TF name, and choose from the auto-complete suggestions</li>
                    <li>Click “Show Interactions” to visualize interactions involving the selected TF</li>
                    <li>In the visualization, click a circle representing a TF to show additional information<ul><li>A table listing related interactions will appear below the visualization</li></ul></li>
                    <li>Click “Load more interactions” to load more interactions involving the selected TF</li>
                    <li>Hover over the help icon in the top-left for more information</li>
                </ol>
                <a href="#" id='hide_tut_button'>Hide Tutorial</a>
            </td>
            <td>
                <img style="width:350px" src="/images/regnet-tutorial.png">
            </td>
        </tr>
    </table>
</div>
<a href="#" id='show_tut_button' style='display:none'>Show Tutorial</a>
<style>
    #tutorial, #explain {
        border: 1px solid black;
        border-radius: 10px;
        padding: 10px;
        padding-left: 30px;
        background-color: white;
    }
    
    #tutorial li {
        margin: 10px 0;
    }
    
    #tutorial table, #tutorial td {
        border: none   
    }
</style>
<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
          $('#hide_tut_button').click(function(){
                $('#tutorial').hide()
                $('#show_tut_button').show()
          })
          $('#show_tut_button').click(function(){
                $('#tutorial').show()
                $('#show_tut_button').hide()
          })
          $('#hide_exp_button').click(function(){
                $('#explain').hide()
                $('#show_exp_button').show()
          })
          $('#show_exp_button').click(function(){
                $('#explain').show()
                $('#show_exp_button').hide()
          })
    })
</script>

<br>
<br>
<br>


TF name:
<input type="text" id="filter_protein_name" placeholder="start typing TF name..." value="<?php echo $default_tf_name; ?>">
<button id="submit" type="button">Show Interactions</button>

<div class="hovermenu"></div>

<div style="display:block">
    <canvas id="my_canvas" width="800" height="600">
</div>    
    
<div id="table_placeholder">
    Click a node to show a table of related interactions
</div>
<div id="table_container">
    <span id="table_header"></span>
    <a href="/regcollection">Click here to view ALL interactions</a>
    <div id="table_legend">
        <span>Legend:</span>
        &nbsp;&nbsp;&nbsp;
        <span style="color:blue">Interaction loaded in visualization</span>
        &nbsp;&nbsp;&nbsp;
        <span style="color:red">Interaction not loaded in visualization</span>
    </div>
    <table align='center' class='wikitable' style='border-collapse:collapse;' id='pdi_table'>
        <thead></thead>
        <tbody><tbody>
    </table>
</div>
<style id="table_style"></style>
    
<script src="/js/fancy-net-vis.js?filever=<?=filesize(FCPATH.'/js/fancy-net-vis.js')?>"></script>


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
    <?php echo $db_specific_js_funcs; ?>
    
    function build_ajax_url_suffix(){    
        return "0,ASC,," + $('#filter_protein_name').val() + ",,"
    }
    
    pdi_table = null
    function update_pdi_table(selected_node){
        console.log(selected_node)
        
        if( selected_node == null ){
            $('#table_container').hide();
            $('#table_placeholder').show();
        } else {
        
            var selected_gid = selected_node.data.gene_id

            // generate API url for the selected node
            var suffix = get_api_url_suffix(ctx)
            var api_url = '/regcollection/filtered_datatable/'+suffix

            // initialize or reset datatable
            if( pdi_table == null ){
                pdi_table = $('#pdi_table').DataTable( {
                    serverSide: true,
                    ajax: api_url,
                    columns: [
                        {"data":"reg_gene","title":"TF GeneID"},
                        {"data":"tar_gene","title":"Target GeneID"},
                        {"data":"edge_id","title":"edge_id"}
                    ],
                } );
            } else {
                pdi_table.ajax.url( api_url ).load();
            }

            $('#table_container').show();
            $('#table_placeholder').hide();
            $('#table_header').html('Listing all interactions involving gene ID "' + selected_gid + '"')
            
            // replace style tag contents to highlight rows that are shown in netvis
            var style = "#pdi_table td {color:red} #pdi_table td a {color:red}"
            var all_pdis = selected_node.data.related_pdis;
            for( var i = 0 ; i < all_pdis.length ; i++ ){
                var edge_id = all_pdis[i].edge_id
                style += '.edge_id_' + edge_id + ' {color:blue !important;}';
            }
            $('#table_style').text(style)
        }
        
    }
    
    function reset_netvis(){
        var url_suffix = build_ajax_url_suffix();
        var vis_url = '/regnet/get_vis_json/'+url_suffix
        var old_c = document.getElementById("my_canvas");
        var c = old_c.cloneNode(true);
        old_c.parentNode.replaceChild(c, old_c);
        ctx = c.getContext('2d');
        show_network_with_api(ctx,800,600,vis_url,function(){
            add_mouse_listener_to_canvas(c,ctx);
            add_node_selection_listener(ctx,update_pdi_table);
        },$('#filter_protein_name').val());
    }
    
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $("#nav_regnet").addClass("active");
        
        $('#filter_protein_name').autocomplete({
            source: "/regnet/autocomplete",
            select: function(event,ui){
            $('#filter_protein_name').val(ui.item.value); 
                return false;
            }
        });
        
        $('#submit').click(reset_netvis);
        reset_netvis();
        update_pdi_table(null);
    })
</script>

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $('#nav_access').addClass("active");
    })
</script>

<?= $this->endSection() ?>