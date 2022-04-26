
<?php
    // For any views that use this, the corresponding controller should...
    // set species_version using $self->get_session_var('Maize_version')

    if( !isset($species_version) ){
        $species_version = 'v5';
    }
?>


<div class='mgvc_container'>
    <label>Maize Genome Version:</label>
    
    <?php foreach (get_maize_genome_versions() as $v) { 
        $id = "m{$v}";
        $label = substr($v,1);
        echo " <input type='radio' class='mgvc_radio' id='{$id}' name='maize_version' value='{$v}' >";
        echo " <label for='{$id}' class='mgvc_label'>{$label}</label>";
    } ?>
</div>

<script>
    
    // add listeners here
    // (listener = function with one parameter: the new version string e.g. 'v3')
    var version_change_listeners = [];
    
    $(document).ready(function(){
        
        
        $('#m<?php echo $species_version; ?>').prop('checked', true);

        $('input[type=radio].mgvc_radio').change(function(e) {
            val = this.value
            $.ajax({
              url: '/set_session_var/Maize_version/' + val
            }).done(function( data ) {
              version_change_listeners.forEach(function (f, i) {
                f(val)
              });
            });;
        });
    });
</script>