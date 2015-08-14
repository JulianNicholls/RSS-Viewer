<?php
//---------------------------------------------------------------------------
// Default URL used if one isn't specified via GET.

    $default_url = "http://feeds.bbci.co.uk/news/rss.xml";

//---------------------------------------------------------------------------

    require_once "simplepie/autoloader.php";
    require_once "humantime.php";
    require_once "sites.php";

    $sites      = new Sites();      // Open a link to the Mongo DB
    $urllist    = $sites->all();    // Collect the list of URLs to present.
    $self       = $_SERVER['PHP_SELF'];
    $aggregated = false;

    if(isset( $_GET['url']))         // Passed a URL as a GET variable?
        $display_url = $_GET['url'];
    elseif(isset($_GET['aggregate']))   // Aggregate the selected feeds
    {
        $aggregated  = true;
        $display_url = array();

        foreach($sites->aggregated_feed() as $cur)
            $display_url[] = $cur['url'];
    }
    else
        $display_url = $default_url;                // Default URL

// Now, we attach to the URL(s) selected.

    $feed = new SimplePie();
    $feed->set_feed_url($display_url);
    $feed->set_cache_duration(420);   // Seven minutes

    if(!$feed->init())
    {
        $title  = "<small>Cannot read $display_url<br />{$feed->error()}</small>";
        $items  = null;
        $image  = null;
    }
    else
    {
        $items  = $feed->get_items();

        if($aggregated)
        {
            $title      = "Aggregated Feed";
            $copyright  = "";
            $image      = "";
            $display_url= $display_url[0];
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
    <title>ARSS <?php echo $title; ?></title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <link href="css/viewer.css" rel="stylesheet">
  </head>

  <body>
    <header class="row">
      <div class="col-sm-2">
        <?php if( $image ) { echo "<img src=\"$image\" alt=\"$title\" />\n"; } ?>
      </div>
      <div class="col-sm-8">
        <h1><?php echo $title; ?> <small><span class="badge"><?php echo $feed->get_item_quantity(); ?></span></small></h1>
        <?php if( $items ) :
          echo '<h2>' . summarised( strip_tags( $feed->get_description() ), $display_url ) . "</h2>\n";
          if( $copyright ) { echo "<p class=\"text-center\"><small>$copyright</small></p>\n"; }
        endif; ?>
      </div>
      <div class="col-sm-2">

        <a class="bright-link" data-toggle="modal" data-target="#feeds"><span class="glyphicon glyphicon-align-justify"></span> Feeds</a>
        <a class="bright-link" href="<?php echo "$self?url=$display_url"; ?>"><span class="glyphicon glyphicon-refresh"></span> Refresh</a>
        <a class="bright-link" href="<?php echo "$self?aggregate=1"; ?>"><span class="glyphicon glyphicon-compressed"></span> Aggregate</a>
      </div>
    </header>

    <div class="container">
      <section id="items">            <!-- Start of Items Section -->

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
        <div class="col-sm-1">
          <?php echo make_link( $link, "<img src=\"$tn\" alt=\"$title\" />" ); ?>
        </div>
        <div class="col-sm-9">
      <?php else : ?>
        <div class="col-sm-offset-1 col-sm-9">
      <?php endif; ?>
        <h3><?php echo make_link( $link, $title); ?></h3>
        <?php if( $desc ) :
          echo '<p>' . summarised( $desc, $link ) . "</p>\n";
        elseif( $content ) :
          echo '<p>' . summarised( $content, $link ) . "</p>\n";
        endif ?>

        <?php if( !empty( $author ) && !empty( $author->get_name() ) ) : ?>
          <p>Author: <?php echo $author->get_name(); ?></p>
        <?php endif; ?>

        <?php if( $cats ) :
          echo "<p>Categories: ";
          foreach( $cats as $cat ) :
            echo $cat->get_label() . ', ';
          endforeach;
          echo "</p>\n";
        endif; ?>

        <?php if( $conts ) :
          echo "<p>Contributors: ";
          foreach( $conts as $cont ) :
            echo '(' . $cont->get_name() . ', ' . $cont->get_link() . ', ' . $cont->get_email() . '), ';
          endforeach;
          echo "</p>\n";
        endif; ?>
        </div>
        <div class="col-sm-2">
          <small class="pull-right"><?php echo human_time( $item->get_date('U') ); ?></small>
        </div>
      </article>
    <?php endforeach; ?>
      </section>  <!-- items -->
    </div>        <!-- container -->

    <div id="feeds" class="modal fade" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Feeds</h4>
          </div>
          <div class="modal-body">
            <div class="list-group">
            <?php foreach( $urllist as $cur ) :
              $active = ($cur['url'] == $display_url) ? 'active' : '';
              echo "<a class=\"list-group-item $active\" href=\"$self?url={$cur['url']}\">" .
                   $cur['name'] . "</a>\n";
            endforeach; ?>
            </div>
          </div>
          <div class="modal-footer">
            <a href="editor.php" class="btn btn-success">
              <i class="glyphicon glyphicon-edit"></i> Edit
            </a>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <script src="//code.jquery.com/jquery.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
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
