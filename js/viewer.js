//---------------------------------------------------------------------------
// JavaScript for the ARSS Viewer

$(function() {

    $(window).load( resize_columns );
    
    // Choose Feed button clicked: Open the feeds panel.
    
    $(document).on( 'click', "a.open-feeds", function() {
        $("div#feeds").slideDown( 600 );
    });

    // Close button (or feed button) clicked: Close the feeds panel.

    $(document).on( 'click', "a.close-button, a.feed-button", function() {
        $("div#feeds").fadeOut( 600 );
	});
});


//---------------------------------------------------------------------------
// Resize the image and main columns if a feed image fits in to a one-wide 
// column OK.

function resize_columns()
{
    var $imgs = $("#items img");
    
    $imgs.each( function( idx ) {
        if( this.width < 80 )
        {
            var $ourDiv = $(this).parent( 'a' ).parent('div'),
                $next   = $ourDiv.next( 'div' );
        
            $ourDiv.removeClass( 'col-md-2' ).addClass( 'col-md-1' );
            $next.removeClass( 'col-md-8' ).addClass( 'col-md-9' );
        }
    });
}