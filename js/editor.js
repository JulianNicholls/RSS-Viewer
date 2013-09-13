//---------------------------------------------------------------------------
// JavaScript for the ARSS Editor

$(function() {

    // Delete button clicked, POST the id to delete via a newly-constructed 
    // form

    $(document).on( 'click', "button.delete", function() {
        if( confirm( 'Delete ' + this.dataset.name + ' feed?' ) ) {
            var $form = $('<form action="' + window.location + '" method="post">' +
              '<input type="hidden" name="delete" value="' + this.dataset.id + '" />' +
              '</form>' );
            
            $('body').append( $form );
            $form.submit();
        }
    });
    
    
    // Edit button clicked: Show the update feed form with the fields filled
    // in, based on the item clicked on.
    
    $(document).on( 'click', "button.edit", function() {
        var $form = $( "form#feed" );

        $form.find( "#feed-id" ).val( this.dataset.id );
        $form.find( "#feed-name" ).val( this.dataset.name );
        $form.find( "#feed-url" ).val( this.dataset.url );
        
        document.getElementById( 'feed-agg' ).checked = (this.dataset.agg != 0);
        
        $form.find( "legend" ).text( "Update Feed" );
        $form.find( "#submit-button" )
            .html( '<span class="glyphicon glyphicon-ok-sign"></span> Update Feed' )
            .val( 'update' );
        
        $form.slideDown( 600 );
    });
    
    
    // Go button clicked: Reload ARSS viewer with passed url
    
    $(document).on( 'click', "button.go", function() {
        window.location = 'viewer.php?url=' + this.dataset.url;
    });

    
    // Add Feed button pressed: Set up and show the add feed form
    
    $(document).on( 'click', "button#new", function() {
        var $form = $( "form#feed" );

        $form.find( "#feed-name" ).val( '' );
        $form.find( "#feed-url" ).val( '' );

        document.getElementById( 'feed-agg' ).checked = true;
        
        $form.find( "legend" ).text( "Add Feed" );
        $form.find( "#submit-button" )
            .html( '<span class="glyphicon glyphicon-plus"></span> Add New Feed' )
            .val( 'add' );

//        $(this).slideUp( 600 );
        $form.slideDown( 600 );
    });
});
