//---------------------------------------------------------------------------
// JavaScript for the ARSS Editor

$(function() {
    // Delete button clicked, POST the id to delete.

    $(document).on('click', "button.delete", function() {
        if(confirm('Delete ' + this.dataset.name + ' feed?')) {
            $.post({
                url: window.location,
                data: {
                    delete: this.dataset.id
                }
            });
        }
    });


    // Edit button clicked: Show the update feed form with the fields filled
    // in, based on the item clicked on.

    $("button.edit").click(function() {
        var $form = $("form#feed");

        $form.find("#feed-id").val(this.dataset.id);
        $form.find("#feed-name").val(this.dataset.name);
        $form.find("#feed-url").val(this.dataset.url);

        document.getElementById('feed-agg').checked = (this.dataset.agg != 0);

        $form.find("legend").text("Update Feed");
        $form.find("#submit-button")
            .html('<span class="fa fa-check-square"></span> Update Feed')
            .val('update');

        $form.slideDown(400);
    });

    // Go button clicked: Reload ARSS viewer with passed url

    $("button.go").click(function() {
        window.location = 'viewer.php?url=' + this.dataset.url;
    });

    // Add Feed button pressed: Set up and show the add feed form

    $("button#new").click(function() {
        var $form = $("form#feed");

        $form.find("#feed-name").val('');
        $form.find("#feed-url").val('');

        document.getElementById('feed-agg').checked = true;

        $form.find("legend").text("Add Feed");
        $form.find("#submit-button")
            .html('<span class="fa fa-plus"></span> Add New Feed')
            .val('add');

        $form.slideDown(400);
    });
});
