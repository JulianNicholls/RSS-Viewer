//---------------------------------------------------------------------------
// JavaScript for the ARSS Viewer

$(function() {

    $(window).load( resize_columns );

    // Choose Feed button clicked: Open the feeds panel.

    $(document).on( 'click', "a.open-feeds", function() {
        $("div#feeds").slideDown( 400 );
    });

    // Close button (or feed button) clicked: Close the feeds panel.

    $(document).on( 'click', "a.close-button, a.feed-button", function() {
        $("div#feeds").fadeOut( 400 );
	});
});


//---------------------------------------------------------------------------
// Resize the image and main columns if a feed image doesn't fit in to a
// one-wide column OK.

function resize_columns()
{
    var $imgs = $("#items img");

    $imgs.each( function( idx ) {
        if( this.width > 180 )
            this.width = 180

        if( this.width > 80 )
        {
            var $ourDiv = $(this).parent( 'a' ).parent('div'),
                $next   = $ourDiv.next( 'div' );

            $ourDiv.removeClass( 'col-md-1' ).addClass( 'col-md-2' );
            $next.removeClass( 'col-md-9' ).addClass( 'col-md-8' );
        }
    });
}
