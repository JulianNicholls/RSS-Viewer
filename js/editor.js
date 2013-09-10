//---------------------------------------------------------------------------
// JavaScript for the ARSS Editor

$(function() {

    // Delete button clicked, POST the id to delete via a newly-constructed 
    // form

    $(document).on( 'click', "button#delete", function() {
        if( confirm( 'Delete ' + this.dataset.name + ' feed?' ) ) {
            var $form = $('<form action="' + window.location + '" method="post">' +
              '<input type="hidden" name="delete" value="' + this.dataset.id + '" />' +
            '</form>' );
            
            $('body').append( $form );
            $form.submit();
        }
    });
    
    
    // Edit button clicked: Show the update feed form
    
    $(document).on( 'click', "button#edit", function() {
        var $form   = $("form#update-feed");
            
        $form.find( "#updated-id" ).val( this.dataset.id );
        $form.find( "#updated-name" ).val( this.dataset.name );
        $form.find( "#updated-url" ).val( this.dataset.url );
        
        $form.slideDown( 600 );
    });
    
    
    // Go button clicked: Reload ARSS viewer with passed url
    
    $(document).on( 'click', "button#go", function() {
        window.location = 'viewer.php?url=' + this.dataset.url;
    });

    
    // Add Feed button pressed: Show the add feed form
    
    $(document).on( 'click', "button#new", function() {
        $("form#new-feed").slideDown( 600 );
    });
});