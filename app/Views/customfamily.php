<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

<?php require_once "common/species_banner.php"; ?>

<script src="/js/domain_canvas.js"></script>

<h2 class="wiki-top-header">Custom Family from <?php echo $species ?></h2>


<p id='cf_tip' style="display:none">Bookmark this page to save your custom family</p>

<br>
<p style='display:inline'>Family Name:</p>
<input type='text' id='cf_name' placeholder='Enter Family Name'/>
<button type='button' id='cf_name_update'>Update Family Name</button>

<h2 class="wiki-section-header">Required Domains</h2>
<br>
<div id='req_container'>
    <input type='text' id='req_text' placeholder='Add Required Domain'/>
</div>

<h2 class="wiki-section-header">Forbidden Domains</h2>
<br>
<div id='forb_container'>
    <input type='text' id='forb_text' placeholder='Add Forbidden Domain'/>
</div>

<?php 
    if( isset($family_name) ){
        $display_name = $family_name;
    }else{
        $display_name = 'Unnamed';
    }
?>
<h2 class="wiki-section-header" id='cf_table_label'>Transcripts in <?php echo $display_name; ?> family</h2>
<p id='cf_table_placeholder'>Add a required domain to show transcripts</p>
<br>
<?php echo $datatable; ?>

<script>
    
    var $ = jQuery.noConflict();
    
    $(document).ready(function(){
        
        function add_req( dom ) {
            if( dom.length==0 ){
                return;   
            }
            var new_tag = $("<div class='cf_req_domain'></div>");
            new_tag.append($("<p></p>").text(dom));
            new_tag.append("<button type='button' class='remove_domain'>Remove</button>");
            $('#req_container').append(new_tag);
            $("button.remove_domain").off("click").click(function(e) { 
                $(e.target).parent().remove();
                update();
            });
        }
    
        function add_forb( dom ) {
            if( dom.length==0 ){
                return;   
            }
            var new_tag = $("<div class='cf_forb_domain'></div>");
            new_tag.append($("<p></p>").text(dom));
            new_tag.append("<button type='button' class='remove_domain'>Remove</button>");
            $('#forb_container').append(new_tag);
            $("button.remove_domain").off("click").click(function(e) { 
                $(e.target).parent().remove();
                update();
            });
        }
        
        function sname(){
            var result = $("#cf_name").val().trim();
            if( result.length == 0 ){
                return "Unnamed";
            }       
            return result;
        }
        
        function sreq(){
            var result = '';
            $(".cf_req_domain p").each(function() {
                result += $(this).text() + ','
            });
            if( result.length > 0 ){
                result = result.substring(0,result.length-1);  
            }
            if( result.trim().length == 0 ){
                return "None";
            }            
            return result;
        }

        function sforb(){
            var result = '';
            $(".cf_forb_domain p").each(function() {
                result += $(this).text() + ','
            });
            if( result.length > 0 ){
                result = result.substring(0,result.length-1);  
            }
            if( result.trim().length == 0 ){
                return "None";
            }
            return result;
        }
        
        function update_datatable(){
            var name = sname();
            $('#cf_table_label').html('Transcripts in ' + name + ' family');
            var req = sreq();
            
            if( req === "None" ){
                return null;   
            }
            
            var forb = sforb();
            var data = name.replace(';','') + ';' + req + ';' + forb;
            var url = '/customfamily_datatable/Maize/'+sreq()+'/'+sforb();
            gene_table = $('#gene_table').DataTable( {
                destroy: true,
                serverSide: true,
                ajax: url,
                columns: [{"data":"tid","title":"Transcript ID"},{"data":"domains","title":"Domains"}],
            } );
            return data;
        }

        
        <?php 
            if( isset($family_name) ){
                echo "$('#cf_name').val('$family_name');";
            } 
            if( isset($required) ){
                foreach( $required as $dom ){
                    echo "add_req('$dom');";
                }
            } 
            if( isset($forbidden) ){
                foreach( $forbidden as $dom ){
                    echo "add_forb('$dom');";
                }
            } 
        ?>
        
        function update() {
            var data = update_datatable();
            
            if( data == null ){
                $('#gene_table_wrapper').hide();
                $('#cf_tip').hide();
                $('#cf_table_placeholder').show();
                window.history.pushState("Custom Family", "Custom Family", "/customfamily/Maize");
            } else {
                $('#gene_table_wrapper').show();
                $('#cf_tip').show();
                $('#cf_table_placeholder').hide();
                window.history.pushState("Custom Family", "Custom Family", "/customfamily/Maize?q="+data);
            }
        }
        update();
    
        $('#req_text').autocomplete({
            source: "/customfamily_autocomplete",
            select: function(event,ui){
                add_req( ui.item.value );
                $('#req_text').val(''); 
                update();
                return false;
            }
        });
    
        $('#forb_text').autocomplete({
            source: "/customfamily_autocomplete",
            select: function(event,ui){
                add_forb( ui.item.value );
                $('#forb_text').val(''); 
                update();
                return false;
            }
        });
        
        $("#cf_name_update").off("click").click(function(e) { 
            update();
        });
    })
</script>

<div class="familypage_dom_hovermenu"><ul><lh id="familypage_dom_hovermenu_title">title</lh></ul><p id="familypage_dom_hovermenu_desc">description</p></div>

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $("#nav_access").addClass("active");
    })
</script>


<?= $this->endSection() ?>
