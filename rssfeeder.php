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

    require_once "simplepie/autoloader.php";
    require_once "humantime.php";
        
    $url = (isset( $_GET['url'] )) ? $_GET['url'] : $default_url;
        
    $feed = new SimplePie();
    $feed->set_feed_url( $url );
    $feed->set_cache_duration( 420 );   // Seven minutes
    
    if( !$feed->init() )
    {
        $title      = "Cannot read $url";
        $items      = null;
        $image      = null;
    }
    else
    {
        $title      = $feed->get_title();
        $items      = $feed->get_items();
        $copyright  = $feed->get_copyright();
        $image      = $feed->get_image_url();
    }
?>
    
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="RSS Aggregator">
    <meta name="author" content="Julian Nicholls">
    <title>RSS <?php echo $title; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this -->
    <link rel="stylesheet" href="rssfeeder.css">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
    
  </head>
  <body>
    <div class="container">
      <header class="row">
        <div class="col-md-2">
          <?php if( $image ) { echo "<img src=\"$image\" alt=\"$title\" />\n"; } ?>
        </div>
        <div class="col-md-9">
          <h1><?php echo $title; ?></h1>
          <?php if( !$items ) { die(); } ?>
          <h2><?php echo $feed->get_description(); ?></h2>
          <?php if( $copyright ) { echo "<p>$copyright</p>\n"; } ?>
        </div>
        <div class="col-md-1">
          <p><?php echo $feed->get_item_quantity(); ?> Items</p>
        </div>      
      </header>
        
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
        <div class="col-md-1">
          <?php if( $enc && ($tn = $enc->get_thumbnail()) ) :
            echo make_link( $link, "<img src=\"$tn\" alt=\"$title\" />" );
          endif; ?>
        </div>
        <div class="col-md-9">
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
    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>    
  </body>
</html>

<?php
//---------------------------------------------------------------------------
// Summarise a text potentially, and if so, add a link to a place to read 
// the whole text.

function summarised( $text, $link )
{
    preg_match('/^\s*+(?:\S++\s*+){1,75}/', $text, $matches);
    
    if( strlen( $text ) == strlen( $matches[0] ) )  // No truncation necessary
        return $text;
    
    return rtrim( $matches[0] ) . '&hellip; ' . make_link( $link, 'Read&nbsp;More' );
}


//---------------------------------------------------------------------------
// Make a link that opens in a new Window/Tab

function make_link( $href, $text )
{
    return "<a href=\"$href\" target=\"_blank\">$text</a>";
}
