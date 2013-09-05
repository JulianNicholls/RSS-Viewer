<?php
    require_once "sites.php";

    $sites = new Sites();  // open a link to the Mongo DB for a list of possible URLs to present.

// Any updates or deletes to do?

    if( isset( $_GET['delete'] ) )
        $sites->remove_by_id( new MongoId( $_GET['delete'] ) );
    elseif( isset( $_POST['updated-id'] ) )
    {
        $sites->update( $_POST['updated-id'], 
            array( 'name' => $_POST['updated-name'], 'url' => $_POST['updated-url'] ) 
        );
    }
    
// Load the list of URLs to present
        
    $urllist    = $sites->all();
    $self       = $_SERVER['PHP_SELF'];

?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ARSS Editor">
    <meta name="author" content="Julian Nicholls">
    <title>ARSS Editor</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/rssfeededit.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
    
  </head>
  
  <body>
    <header>
      <h1>ARSS Editor</h1>
    </header>
    
    <div class="container">
      <h2>Feeds</h2>
      <table class="table table-striped table-bordered">
        <thead>
          <tr><th>&nbsp;</th><th>Name</th><th>URL</th></tr>
        </thead>
        <tbody>
          <?php foreach( $urllist as $cur ) : 
            $id   = $cur['_id']; 
            $name = $cur['name'];
            $url  = $cur['url']; ?>
          <tr>
            <td>
              <a href="<?php echo "$self?delete=$id;" ?>">
                <img src="images/deletebutton.png" alt="Delete" />
              </a>
              <a class="edit" href="#" data-name="<?php echo $name; ?>" data-id="<?php echo $id; ?>" data-url="<?php echo $url; ?>">
                <img src="images/editbutton.png" alt="Edit" />
              </a>            
              <a href="<?php echo "rssfeeder.php?url=$url;" ?>">
                <img src="images/go.png" alt="Delete" />
              </a>
            </td>
            <td><?php echo $name; ?></td>
            <td><?php echo $url; ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      
      <form id="update-feed" role="form" class="form-horizontal" action="<?php echo $self; ?>" method="post">
        <fieldset> 
          <legend>Update Feed</legend>

          <input type="hidden" id="updated-id" name="updated-id">
          <div class="form-group">
            <label for="updated-name" class="col-lg-1 control-label">Name</label>
            <div class="col-lg-8">
              <input type="text" class="form-control" id="updated-name" name="updated-name" placeholder="Name" />
            </div>
          </div>
          
          <div class="form-group">
            <label for="updated-url" class="col-lg-1 control-label">URL</label>
            <div class="col-lg-8">
              <input type="url" class="form-control" id="updated-url" name="updated-url" placeholder="URL" />
            </div>  
          </div>
          
          <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
              <button type="submit" class="btn btn-default">Update Feed</button>
            </div>
          </div>
        </fieldset>
      </form>
      
    </div>      <!-- container -->
        
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>    
    <script src="js/rssfeededit.js"></script>  
  </body>
</html>
