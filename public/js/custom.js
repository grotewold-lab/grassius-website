 $(document).ready(function(){
		
    $('input.typeahead').typeahead({
        name: 'typeahead',
        remote:'search.php?key=%QUERY',
        limit : 10
    });
    
    
   $('#selection').submit(function()
    {
	    valid = true;
		
        var selected = $('#family').val();


    if (selected == '0')
    {
        $.alert({
					title: 'Alert!',
					content: 'Please select a family!',
					boxWidth: '20%',
					useBootstrap: false,
				});
        valid = false;
    }
        return valid;
});
    });
    
