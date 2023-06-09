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

<a href="#" id='show_pubs_button'>Show References</a>

<div id="pubs" style="display:none">
    <b>References:</b>&nbsp;<a href="#" id='hide_pubs_button'>hide</a>

    <p>
    Morohashi K, Casas MI, Falcone Ferreyra ML, Falcone Ferreyra L, Mejía-Guerra MK, Pourcel L, Yilmaz A, Feller A, Carvalho B, Emiliani J, Rodriguez E, Pellegrinet S, McMullen M, Casati P, Grotewold E. <br>
        <i>A genome-wide regulatory framework identifies maize pericarp color1 controlled genes. </i><br>
        <a href="https://academic.oup.com/plcell">Plant Cell</a>. 2012 Jul;24(7):2745-64.<br>
        <a href="https://pubmed.ncbi.nlm.nih.gov/22822204/">PMID: 22822204</a>
    </p>

    <p>
    Bolduc N, Yilmaz A, Mejia-Guerra MK, Morohashi K, O'Connor D, Grotewold E, Hake S. <br>
        <i>Unraveling the KNOTTED1 regulatory network in maize meristems. </i><br>
        <a href="https://genesdev.cshlp.org/">Genes Dev</a>. 2012 Aug 1;26(15):1685-90.<br>
        <a href="https://pubmed.ncbi.nlm.nih.gov/22855831/">PMID: 22855831</a>
    </p>

    <p>
    Eveland AL, Goldshmidt A, Pautler M, Morohashi K, Liseron-Monfils C, Lewis MW, Kumari S, Hiraga S, Yang F, Unger-Wallace E, Olson A, Hake S, Vollbrecht E, Grotewold E, Ware D, Jackson D. <br>
        <i>Regulatory modules controlling maize inflorescence architecture. </i><br>
        <a href="https://genome.cshlp.org/">Genome Res</a>. 2014 Mar;24(3):431-43.<br>
        <a href="https://pubmed.ncbi.nlm.nih.gov/24307553/">PMID: 24307553</a>
    </p>

    <p>
    Pautler M, Eveland AL, LaRue T, Yang F, Weeks R, Lunde C, Je BI, Meeley R, Komatsu M, Vollbrecht E, Sakai H, Jackson D. <br>
        <i>FASCIATED EAR4 encodes a bZIP transcription factor that regulates shoot meristem size in maize. </i><br>
        <a href="https://academic.oup.com/plcell">Plant Cell</a>. 2015 Jan;27(1):104-20. <br>
        <a href="https://pubmed.ncbi.nlm.nih.gov/25616871/">PMID: 25616871</a>
    </p>

    <p>
    Li C, Qiao Z, Qi W, Wang Q, Yuan Y, Yang X, Tang Y, Mei B, Lv Y, Zhao H, Xiao H, Song R. <br>
        <i>Genome-wide characterization of cis-acting DNA targets reveals the transcriptional regulatory framework of opaque2 in maize. </i><br>
        <a href="https://academic.oup.com/plcell">Plant Cell</a>. 2015 Mar;27(3):532-45.<br>
        <a href="https://pubmed.ncbi.nlm.nih.gov/25691733/">PMID: 25691733</a>
    </p>

    <p>Yang, F., Li, W., Jiang, N., Yu, H., Morohashi, K., Ouma, W.Z., Morales-Mantilla, D., Gomez-Cano, F., Mukundi, E., Prada-Salcedo, L.D., Alers Velazquez, R., Valentin, J., Mejía-Guerra, M.K., Gray, J., Doseff, A.I., and Grotewold, E. (2017).<br>
        <i>A maize gene regulatory network for phenolic metabolism.</i><br>
    <a href="http://www.plantphysiol.org">Mol Plant</a>, 10: 489-515.<br>
    <a href="https://pubmed.ncbi.nlm.nih.gov/27871810/">PMID: 27871810</a>
    </p>

</div>

<br>
<br>
<b>Special thanks to contributer <a href="https://scholar.google.com/citations?user=5PzN_KYAAAAJ">Sarah Percival</a></b>

<br>
<br>

<div id="tutorial">
    <table>
        <tr>
            <td style="width:600px; padding-right:50px">
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
    
    #pubs i {
        font-weight: bold;
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
          $('#hide_pubs_button').click(function(){
                $('#pubs').hide()
                $('#show_pubs_button').show()
          })
          $('#show_pubs_button').click(function(){
                $('#pubs').show()
                $('#show_pubs_button').hide()
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
    <canvas id="my_canvas" width="1000" height="800">
</div>    
    
<div id="table_placeholder">
    Click a node to show a table of related interactions
</div>
<div id="table_container">
    <span id="table_header"></span>
    <a href="/pdicollection">Click here to view ALL interactions</a>
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
                    columns: [{"data":"reg_protein","title":"Regulator Protein"},{"data":"reg_protein_order","title":"Regulator Protein"},{"data":"reg_gene","title":"Regulator Gene"},{"data":"tar_protein","title":"Target Protein"},{"data":"tar_protein_order","title":"Target Protein"},{"data":"tar_gene","title":"Target Gene"},{"data":"exp","title":"Experiment"},{"data":"dist","title":"Distance <br>(+ or -) (kb)"},{"data":"abs_dist","title":"Absolute <br>Distance (kb)"},{"data":"pubmed","title":"Reference"},{"data":"note","title":"Note"}],
                  "columnDefs": [ 
                    { "targets": [0,3],"visible": false },
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
                style += ' .edge_id_' + edge_id + ', .edge_id_' + edge_id + ' a {color:blue !important;}';
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
        show_network_with_api(ctx,1000,800,vis_url,function(){
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