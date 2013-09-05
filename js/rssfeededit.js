//---------------------------------------------------------------------------
// JavaScript for the RSSFeeder Editor

$(function() {

    // Edit button clicked: Show a form
    
    $(document).on( 'click', "a.edit", function() {
        var $this   = $(this),
            $form   = $("form#update-feed"),
            id      = $this.attr( 'data-id' ),
            name    = $this.attr( 'data-name' ),
            url     = $this.attr( 'data-url' );
            
        alert( id + '\n' + name + '\n' + url );
        
        $form.find( "#updated-id" ).val( id );
        $form.find( "#updated-name" ).val( name );
        $form.find( "#updated-url" ).val( url );
        
        $form.slideDown( 600 );
    });
});