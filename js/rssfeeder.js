//---------------------------------------------------------------------------
// JavaScript for the RSSFeeder

$(function() {

    // Choose Feed button clicked: Open the feeds panel.
    
    $(document).on( 'click', "a.open-feeds", function() {
        $("div#feeds").fadeIn( 600 );
    });

    // Close button clicked: Close the feeds panel.

    $(document).on( 'click', "a.close-button", function() {
        $("div#feeds").fadeOut( 600 );
	});
    
    // Add Feed button clicked: Show an input field.
    
    $(document).on( 'click', "a#add-feed", function() {
         $("form#add-feed-form").slideDown( 600 );
    });
    
});