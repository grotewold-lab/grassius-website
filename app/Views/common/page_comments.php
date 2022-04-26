
<?php 

if( isset($comments_url) and !($comments_url === null or trim($comments_url) === '') ){ 
    $comments_url = str_replace( "http://", "https://", $comments_url );
    
?>

<br>
<a id='toggle_page_comments' style="cursor:pointer">show/hide comments</a> | <a href="<?php echo $comments_url; ?>" target="_blank">edit comments in a new tab</a>


<div id="page_comments" hidden>
    <iframe id="pc_iframe" src="<?php echo $comments_url; ?>" scrolling="no" frameborder="0px"></iframe>
</div>

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        
        $("#toggle_page_comments").click(function(){
            
            var target = $("#page_comments")
            target.toggle();
            
            if( target.is(":visible") ){
                
                // force iframe to reload
                $( '#pc_iframe' ).attr( 'src', function ( i, val ) { return val; });
                
                $("#toggle_page_comments").addClass('bold_red');
            } else {
                $("#toggle_page_comments").removeClass('bold_red');
            }
        });
        
    });
</script>

<style>
    .bold_red {
        font-weight: bold;
        color: red;
    }
    
    #page_comments {
        height:800px;
        width:90%;
        position:fixed;
        top:200px;
        left:5%;
        z-index:40;
        background-color: white;
        overflow: scroll;
    }
    
    #pc_iframe {
        width:100%;
        height: 2000px;
        pointer-events: none;
    }
</style>


<?php } else { ?>

<p>comments are not available for this page</p>

<?php } ?>