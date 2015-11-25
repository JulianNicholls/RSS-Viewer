//---------------------------------------------------------------------------
// JavaScript for the ARSS Editor

$(function() {
    // Delete button clicked, POST the id to delete.

    $(document).on('click', "button.delete", function() {
        var data = this.dataset;

        if(confirm('Delete ' + data.name + ' feed?')) {
            $.post({
                url: window.location,
                data: {
                    delete: data.id
                }
            });
        }
    });


    // Edit button clicked: Show the update feed form with the fields filled
    // in, based on the item clicked on.

    $("button.edit").on('click', function() {
        var $form = $("form#feed"),
            data = this.dataset;

        $form.find("#feed-id").val(data.id);
        $form.find("#feed-name").val(data.name);
        $form.find("#feed-url").val(data.url);

        document.getElementById('feed-agg').checked = (data.agg != 0);

        $form.find("legend").text("Update Feed");
        $form.find("#submit-button")
            .html('<i class="fa fa-check-square-o"></i> Update Feed')
            .val('update');

        $form.slideDown(400);
    });

    $("button#cancel").on('click', function () {
        $("form#feed").fadeOut(400);

        return false;
    });

    // Go button clicked: Reload ARSS viewer with passed url

    $("button.go").click(function() {
        window.location = 'viewer.php?url=' + this.dataset.url;
    });

    // Add Feed button pressed: Set up and show the add feed form

    $("button#new").on('click', function() {
        var $form = $("form#feed");

        $form.find("#feed-name").val('');
        $form.find("#feed-url").val('');

        document.getElementById('feed-agg').checked = true;

        $form.find("legend").text("Add Feed");
        $form.find("#submit-button")
            .html('<i class="fa fa-plus"></i> Add New Feed')
            .val('add');

        $form.slideDown(400);
    });
});
