<?php
//---------------------------------------------------------------------------
//
    $url = "http://feeds.bbci.co.uk/news/rss.xml";
//    $url = "http://rss1.smashingmagazine.com/feed/";
//    $url = "https://www.ruby-lang.org/en/feeds/news.rss";
//
//---------------------------------------------------------------------------

    require_once "simplepie/autoloader.php";
    require_once "humantime.php";
        
    $feed = new SimplePie();
    $feed->set_feed_url( $url );
    $feed->set_cache_duration( 600 );   // Ten minutes
    if( !$feed->init() )
    {
        $title      = "Cannot read $url";
        $feed_img   = null;
        $items      = null;
    }
    else
    {
        $title      = $feed->get_title();
        $items      = $feed->get_items();
        $copyright  = $feed->get_copyright();
        $feed_img   = $feed->get_image_url();
    }
?>
    
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>RSS <?php echo $title; ?></title>
    <link rel="stylesheet" href="rssfeeder.css">
  </head>
  <body>
    <div id="container">
      <header>
        <?php if( $feed_img ) : ?>
          <img src="<?php echo $feed_img; ?>" alt="<?php echo $title; ?>" />
        <?php endif; ?>
        <h1><?php echo $title; ?></h1>
        <?php if( !$items ) { die(); } ?>
        <h2><?php echo $feed->get_description(); ?></h2>
        <h3><?php echo $feed->get_item_quantity(); ?> Items</h3>
        <?php if( $copyright ) { echo "<p>$copyright</p>\n"; } ?>
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
//      $encs     = $item->get_enclosures();
      $enc      = $item->get_enclosure();
    ?>
      <article>
<?php /*
      <?php if( $encs ) :
          foreach( $encs as $enc ) :
            if( $tns = $enc->get_thumbnails() ) :
              foreach( $tns as $tn ) :
                echo $tn . ', ';
              endforeach;
              
            endif;
          endforeach; ?>
          </p>
        <?php endif; ?>
*/
?>
        <?php if( $enc ) :
          if( $tn = $enc->get_thumbnail() ) :
            make_link( $link, "<img src=\"$tn\" alt=\"$title\" />" );
          endif;
        endif; ?>
            
        <h1><?php make_link( $link, $title); ?></h1>
        <?php if( $desc ) :
          echo '<p>' . summarised( $desc, $link ) . "</p>\n";
        elseif( $content ) :
          echo '<p>' . summarised( $content, $link ) . "</p>\n";
        endif ?>
        
        <?php if( $author ) : ?>
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

        <span class="stamp"><?php echo human_time( $item->get_date('U') ); ?></span>
      </article>
    <?php endforeach; ?>
    </div>      <!-- container -->
  </body>
</html>

<?php
// Summarise a text potentially, and if so, add a link to a place to read the whole text

function summarised( $text, $link )
{
    preg_match('/^\s*+(?:\S++\s*+){1,75}/', $text, $matches);
    
    if( strlen( $text ) == strlen( $matches[0] ) )  // No truncation necessary
        return $text;
    
    return $matches[0] . '&hellip; <a href="' . $link . '" target="_blank">Read More</a>';
}


function make_link( $href, $text )
{
    echo "<a href=\"$href\" target=\"_blank\">$text</a>";
}
