 
<?php
    if( !isset($i) ){
        $i = 0;
    }
?>

<label style="margin-left:50px; font-weight:bold">Color Coding:</label>
<input type="radio" class="aa_radio" id="aa_none" name="aa_color_<?php echo $i?>" value="none" checked >
<label for="aa_none">None</label>
<input type="radio" class="aa_radio" id="aa_ss" name="aa_color_<?php echo $i?>" value="ss" >
<label for="aa_ss">Secondary Structure</label>
<input type="radio" class="aa_radio" id="aa_dom" name="aa_color_<?php echo $i?>" value="dom" >
<label for="aa_dom">Domain</label>
<script>
    var $ = jQuery.noConflict();
    function update_aa( val ){
        $('.aa').hide();
        $('.aa_' + val).show();
        
        var match = $('input[type=radio].aa_radio').filter('[value="'+val+'"]')
        if( match.length ) {
            $('input[type=radio].aa_radio').attr('checked', false);
            match.attr('checked', true);
        }
    }

    $(document).ready(function(){

        $.ajax({
            url: "/get_session_var/aa_colors",
        }).done(function( data ) {
            update_aa(data);
        });

        update_aa();

        $('input[type=radio].aa_radio').change(function(e) {
            update_aa(this.value)
            $.ajax({
              url: "/set_session_var/aa_colors/" + this.value
            });
        });
    })
</script>