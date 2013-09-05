<?php
    require_once "sites.php";

    $sites = new Sites();  // open a link to the Mongo DB for a list of possible URLs to present.

// Any updates or deletes to do?

    if( isset( $_GET['delete'] ) )
        $sites->remove_by_id( new MongoId( $_GET['delete'] ) );
        
// Load the list of URLs to present
        
    $urllist    = $sites->all();

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
          <?php foreach( $urllist as $cur ) : ?>
          <tr>
            <td><a href="<?php echo $_SERVER['PHP_SELF']; ?>?delete=<?php echo $cur['_id']; ?>">
              <img src="images/deletebutton.png" alt="Delete" />
            </a></td>
            <td><?php echo $cur['name']; ?></td>
            <td><?php echo $cur['url']; ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      
      <a class="open-feeds" href="rssfeeder.php">Open Feeds</a>
      
      <form role="form" class="form-horizontal" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
        <fieldset> 
          <legend>New Feed</legend>
          
          <div class="form-group">
            <label for="new-name" class="col-lg-1 control-label">Name</label>
            <div class="col-lg-8">
              <input type="text" class="form-control" id="new-name" name="new-name" placeholder="Name" />
            </div>
          </div>
          
          <div class="form-group">
            <label for="new-url" class="col-lg-1 control-label">URL</label>
            <div class="col-lg-8">
              <input type="url" class="form-control" id="new-url" name="new-url" placeholder="URL" />
            </div>  
          </div>
          
          <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
              <button type="submit" class="btn bth-default">Add Feed</button>
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
