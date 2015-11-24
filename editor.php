<?php
    require_once "sites.php";

    $sites = new Sites();  // open a link to the Mongo DB for a list of possible URLs to present.

    $done = array('func' => 0, 'info' => '');

// Any inserts, updates or deletes to do?

    if(isset($_POST['feed-url']))
    {
        if($_POST['submit-button'] == 'add')
        {
            $sites->insert(array(
                'name'      => $_POST['feed-name'],
                'url'       => $_POST['feed-url'],
                'aggregate' => isset($_POST['feed-agg']) ? 1 : 0
           ));

            $done = array('func' => 1, 'info' => $_POST['feed-name'] . " Added");
        }
        else
        {
            $sites->update($_POST['feed-id'], array(
                'name'      => $_POST['feed-name'],
                'url'       => $_POST['feed-url'],
                'aggregate' => isset($_POST['feed-agg']) ? 1 : 0
           ));

            $done = array('func' => 2, 'info' => $_POST['feed-name'] . " Updated");
        }
    }
    elseif(isset($_POST['delete']))
    {
        $sites->remove_by_id(new MongoId($_POST['delete']));

        $done = array('func' => 3, 'info' => "Feed Deleted");
    }

// Load the list of URLs to present

    $urllist    = $sites->all();
    $self       = $_SERVER['PHP_SELF'];

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARSS Editor</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link href="css/editor.css" rel="stylesheet">
  </head>

  <body>
    <header>
      <h1>ARSS Editor</h1>
    </header>

    <div class="container">
      <?php if($done['func']) :
          echo '<div class="alert alert-success alert-dismissable">';
          echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
          echo $done['info'] . "\n</div>\n";
      endif; ?>

      <table class="table table-bordered table-condensed">
        <thead>
          <tr><th class="action">Actions</th><th>Name</th><th>URL</th><th class="action">Aggregate?</th></tr>
        </thead>
        <tbody>
          <?php foreach($urllist as $cur) :
            $id   = $cur['_id'];
            $name = $cur['name'];
            $url  = $cur['url'];
            $agg  = $cur['aggregate'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-close"></i>';
          ?>
          <tr>
            <td class="action">
              <div class="btn-group btn-group-sm">
                <button class="delete btn btn-danger btm-sm"
                        data-name="<?php echo $name; ?>"
                        data-id="<?php echo $id; ?>" title="Delete Feed">
                  <span class="fa fa-remove"></span>
                </button>
                <button class="edit btn btn-primary btm-sm"
                      data-name="<?php echo $name; ?>"
                      data-id="<?php echo $id; ?>"
                      data-url="<?php echo $url; ?>"
                      data-agg="<?php echo $cur['aggregate']; ?>" title="Edit Feed">
                  <span class="fa fa-edit"></span>
                </button>
                <button class="go btn btn-info btm-sm"
                        data-url="<?php echo $url; ?>" title="Show Feed in Viewer">
                  <span class="fa fa-link"></span>
                </button>
              </div>
            </td>
            <?php
              echo "<td>$name</td>\n";
              echo "<td>$url</td>\n";
              $agg_class = (preg_match('/close/', $agg)) ? 'danger' : 'success';
              echo "<td class=\"agg $agg_class\">$agg</td>\n</tr>\n";
          endforeach; ?>
        </tbody>
      </table>

      <button id="new" class="btn btn-primary btn-lg">
        <span class="fa fa-plus"></span> Add New Feed
      </button>

      <form id="feed" role="form" class="form-horizontal"
            action="<?php echo $self; ?>" method="post">
        <fieldset>
          <legend>Update Feed</legend>

          <input type="hidden" id="feed-id" name="feed-id">

          <div class="form-group">
            <label for="updated-name" class="col-sm-2 control-label">Name</label>
            <div class="col-sm-8">
              <input type="text" class="form-control"
                     id="feed-name" name="feed-name" placeholder="Name" required />
            </div>
          </div>

          <div class="form-group">
            <label for="updated-url" class="col-sm-2 control-label">URL</label>
            <div class="col-sm-8">
              <input type="url" class="form-control"
                     id="feed-url" name="feed-url" placeholder="URL" required />
            </div>
          </div>

          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <div class="checkbox">
                <label>
                  <input type="checkbox" id="feed-agg" name="feed-agg"> Use in Aggregated Feed
                </label>
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <button type="submit" name="submit-button" id="submit-button" class="btn btn-primary btn-lg">
                <span class="fa fa-check-square-o"></span> Update Feed
              </button>
            </div>
          </div>
        </fieldset>
      </form>

    </div>      <!-- container -->

    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="js/editor.js"></script>
  </body>
</html>
