//---------------------------------------------------------------------------
// JavaScript for the ARSS Viewer

$(function() {
    $(window).load(resize_columns);

    // Choose Feed button clicked: Open the feeds panel.

    $("a.open-feeds").click(function() {
        $("div#feeds").slideDown(400);
    });

    // Close button clicked: Close the feeds panel.

    $("a.close-button").click(function() {
        $("div#feeds").fadeOut(400);
	});

    // Feed clicked on. Add a loading message
    $("a.list-group-item").click(function() {
        $("div#feeds").append('<h3 class="text-center">Loading&hellip;</h3>');
    });
});


//---------------------------------------------------------------------------
// Resize the image and main columns if a feed image doesn't fit in to a
// one-wide column OK.

function resize_columns()
{
    var $imgs = $("#items img");

    $imgs.each(function() {
        var width = this.width;

        if(width > 180)
            this.width = 180

        if(width > 80) {
            var $ourDiv = $(this).parent( 'a' ).parent('div'),
                $next   = $ourDiv.next('div');

            $ourDiv.removeClass('col-sm-1').addClass('col-sm-2');
            $next.removeClass('col-sm-9').addClass('col-sm-8');
        }
    });
}
