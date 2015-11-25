<?php
    require_once "sites.php";
    require_once "comsubs.php";

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

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/editor.css">
  </head>

  <body>
    <header>
      <h1>ARSS Editor</h1>
    </header>

    <div class="container">
      <?php show_alert($done); ?>

      <table class="table table-bordered table-condensed">
        <thead>
          <tr>
            <th class="action">Actions</th>
            <th>Name</th>
            <th>URL</th>
            <th class="action">Aggregate?</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($urllist as $cur) :
            $id   = $cur['_id'];
            $name = $cur['name'];
            $url  = $cur['url'];
            $agg  = $cur['aggregate'] ? '<i class="fa fa-check fa-2x"></i>' : '<i class="fa fa-close fa-2x"></i>';
          ?>
          <tr>
            <td class="action">
              <div class="btn-group btn-group-sm">
                <button class="delete btn btn-danger btm-sm"
                        data-name="<?php echo $name; ?>"
                        data-id="<?php echo $id; ?>" title="Delete Feed">
                  <i class="fa fa-remove fa-lg"></i>
                </button>
                <button class="edit btn btn-primary btm-sm"
                      data-name="<?php echo $name; ?>"
                      data-id="<?php echo $id; ?>"
                      data-url="<?php echo $url; ?>"
                      data-agg="<?php echo $cur['aggregate']; ?>" title="Edit Feed">
                  <i class="fa fa-edit fa-lg"></i>
                </button>
                <button class="go btn btn-info btm-sm"
                        data-url="<?php echo $url; ?>" title="Show Feed in Viewer">
                  <i class="fa fa-link fa-lg"></i>
                </button>
              </div>
            </td>
            <?php
              echo "<td>$name</td>\n";
              echo "<td>$url</td>\n";
              $agg_class = (preg_match('/close/', $agg)) ? 'no' : 'yes';
              echo "<td class=\"agg $agg_class\">$agg</td>\n</tr>\n";
          endforeach; ?>
        </tbody>
      </table>

      <button id="new" class="btn btn-primary btn-lg">
        <i class="fa fa-plus"></i> Add New Feed
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
              <button type="submit" name="submit-button" id="submit-button" class="btn btn-primary">
                <i class="fa fa-check-square-o"></i> Update Feed
              </button>
              <button class="btn btn-warning" id="cancel">
                <i class="fa fa-toggle-left"></i> Cancel
              </button>
            </div>
          </div>
        </fieldset>
      </form>
    </div>      <!-- container -->

    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="js/editor.js"></script>
  </body>
</html>
