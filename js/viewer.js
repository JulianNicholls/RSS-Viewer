//---------------------------------------------------------------------------
// JavaScript for the ARSS Viewer

$(function() {

    // Choose Feed button clicked: Open the feeds panel.
    
    $(document).on( 'click', "a.open-feeds", function() {
        $("div#feeds").slideDown( 600 );
    });

    // Close button (or feed button) clicked: Close the feeds panel.

    $(document).on( 'click', "a.close-button, a.feed-button", function() {
        $("div#feeds").fadeOut( 600 );
	});
});