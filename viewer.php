<?php
//---------------------------------------------------------------------------
//
    $default_url = "http://feeds.bbci.co.uk/news/rss.xml";
//    $default_url = "http://rss1.smashingmagazine.com/feed/";
//    $default_url = "https://www.ruby-lang.org/en/feeds/news.rss";
//    $default_url = "http://www.nasa.gov/rss/image_of_the_day.rss"
//    $default_url = "http://www.pcworld.com/index.rss"
//    $default_url = "http://feeds.feedburner.com/TheDailyPuppy"
//    $default_url = "http://api.flickr.com/services/feeds/groups_pool.gne?id=1373979@N22&lang=en-us&format=rss_200"
//
//---------------------------------------------------------------------------

    require_once "../simplepie/autoloader.php";
    require_once "humantime.php";
    require_once "sites.php";

    $sites      = new Sites();  // open a link to the Mongo DB for a list of possible URLs to present.
    $self       = $_SERVER['PHP_SELF'];
    $aggregated = false;

// See if the form has fed us a new URL to add to the list

    if( isset( $_POST['new-url'] ) )
    {
        $name   = $_POST['new-name'];
        $nu     = $_POST['new-url'];
        
        $sites->insert( $name, $nu );
        
        $url = $nu;     // Use the new one
    }
    elseif( isset( $_GET['url'] ) )         // Passed a URL as a GET variable?
        $url = $_GET['url'];
    elseif( isset( $_GET['aggregate'] ) )   // BETA: Aggregate some of the above
    {
        $aggregated = true;
        $url        = array(
            "http://rss1.smashingmagazine.com/feed/",
            "https://www.ruby-lang.org/en/feeds/news.rss",
            "http://www.nasa.gov/rss/image_of_the_day.rss",
            "http://feeds.feedburner.com/TheDailyPuppy"
        );
    }
    else
        $url = $default_url;                // Default to above
        
// Load the list of URLs to present
        
    $urllist    = $sites->all();
    
// Now, we attach to the URL selected.
    
    $feed = new SimplePie();
    $feed->set_feed_url( $url );
    $feed->set_cache_duration( 420 );   // Seven minutes
    
    if( !$feed->init() )
    {
        $title      = "Cannot read $url<br />" . $feed->error();
        $items      = null;
        $image      = null;
    }
    else
    {
        $items      = $feed->get_items();
        
        if( $aggregated )
        {
            $title      = "Aggregated Feed";
            $copyright  = "";
            $image      = "";
        }
        else
        {
            $title      = $feed->get_title();
            $copyright  = $feed->get_copyright();
            $image      = $feed->get_image_url();
        }
    }
?>  
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ARSS Feeder">
    <meta name="author" content="Julian Nicholls">
    <title>ARSS <?php echo $title; ?></title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/viewer.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
    
  </head>
  
  <body>
    <header class="row">
      <div class="col-md-2">
        <?php if( $image ) { echo "<img src=\"$image\" alt=\"$title\" />\n"; } ?>
      </div>
      <div class="col-md-9">
        <h1><?php echo $title; ?></h1>
        <?php if( !$items ) { die(); } ?>
        <h2><?php echo summarised( strip_tags( $feed->get_description() ), $url ); ?></h2>
        <?php if( $copyright ) { echo "<p>$copyright</p>\n"; } ?>
      </div>
      <div class="col-md-1">
        <p><?php echo $feed->get_item_quantity(); ?> Items</p>
        <a class="bright-link open-feeds">Feeds</a>
        <a class="bright-link" href="<?php echo "$self?url=$url"; ?>">Refresh</a>
        <a class="bright-link" href="<?php echo "$self?aggregate=1"; ?>">Aggregate!</a>
      </div>      
    </header>
      
    <div class="container">
        
<?php 
    foreach( $items as $item ) : 
      $title    = $item->get_title();
      $desc     = strip_tags( $item->get_description( true ) );   // Restrict to description
      $content  = strip_tags( $item->get_content( true ) );       // Don't fall back to description
      $author   = $item->get_author();
      $cats     = $item->get_categories();
      $conts    = $item->get_contributors();
      $link     = $item->get_permalink();
      $enc      = $item->get_enclosure();
    ?>
      <article class="row">
      <?php if( $enc && ($tn = $enc->get_thumbnail()) ) : ?>
        <div class="col-md-2">
          <?php echo make_link( $link, "<img src=\"$tn\" alt=\"$title\" />" ); ?>
        </div>
        <div class="col-md-8">
      <?php else : ?>
        <div class="col-md-offset-1 col-md-9">
      <?php endif; ?>
        <h1><?php echo make_link( $link, $title); ?></h1>
        <?php if( $desc ) :
          echo '<p>' . summarised( $desc, $link ) . "</p>\n";
        elseif( $content ) :
          echo '<p>' . summarised( $content, $link ) . "</p>\n";
        endif ?>
        
        <?php if( !empty( $author ) ) : ?>
          <p>Author: <?php echo $author->get_name(); ?></p>
        <?php endif; ?>
        
        <?php if( $cats ) : ?>
          <p>Categories:
          <?php foreach( $cats as $cat ) :
            echo $cat->get_label() . ', ';
          endforeach; ?>
          </p>
        <?php endif; ?>
        
        <?php if( $conts ) : ?>
          <p>Contributors:
          <?php foreach( $conts as $cont ) :
            echo '(' . $cont->get_name() . ', ' . $cont->get_link() . ', ' . $cont->get_email() . '), ';
          endforeach; ?>
          </p>
        <?php endif; ?>
        </div>
        <div class="col-md-2">
          <span class="stamp"><?php echo human_time( $item->get_date('U') ); ?></span>
        </div>
      </article>
    <?php endforeach; ?>
    </div>      <!-- container -->
    
    <div id="feeds">    <!-- Feed Panel -->
      <a class="close-button">&nbsp;</a>
      <h1>Feeds</h1>
      <?php foreach( $urllist as $url ) : 
        echo "<a class=\"feed-button\" href=\"" . 
            "$self?url=" . $url['url'] . "\">" .
            $url['name'] . "</a>\n";
      endforeach; ?>
      <a class="feed-button" id="feed-edit" href="editor.php">Edit Feeds &hellip;</a>
    </div>  <!-- feeds -->
    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>    
    <script src="js/viewer.js"></script>    
  </body>
</html>


<?php
//---------------------------------------------------------------------------
// Summarise a text potentially, and if so, add a link to a place to read 
// the whole text.

function summarised( $text, $link )
{
// If there's no match (no text, probably) or the text has less than 75 words
// then just return the text unmodified.

    if( preg_match('/^\s*+(?:\S++\s*+){1,75}/', $text, $matches) != 1 || 
        strlen( $text ) == strlen( $matches[0] ) )
        return $text;
        
// Otherwise, return the first 75 words and a link

    return rtrim( $matches[0] ) . ' [&hellip;] ' . make_link( $link, 'Read&nbsp;More' );
}


//---------------------------------------------------------------------------
// Make a link that opens in a new Window/Tab

function make_link( $href, $text )
{
    return "<a href=\"$href\" target=\"_blank\">$text</a>";
}
