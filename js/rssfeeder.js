//---------------------------------------------------------------------------
// JavaScript for the ARSS Viewer

$(function() {

    // Choose Feed button clicked: Open the feeds panel.
    
    $(document).on( 'click', "a.open-feeds", function() {
        $("div#feeds").fadeIn( 600 );
    });

    // Close button clicked: Close the feeds panel.

    $(document).on( 'click', "a.close-button", function() {
        $("div#feeds").fadeOut( 600 );
	});
});