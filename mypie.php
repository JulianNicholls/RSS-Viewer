<?php
//create the canvas

define( 'WIDTH', 400 );
define( 'HEIGHT', 300 );

define ( 'HEADER_FONT', '/Windows/Fonts/Consola.ttf' );
define ( 'LEGEND_DATA_FONT', '/Windows/Fonts/arial.ttf' );

$myImage = ImageCreate( WIDTH, HEIGHT );

//set up some colors for use on the canvas

$bk_blue     = ImageColorAllocate( $myImage, 255, 255, 255 );
$frame_color = ImageColorAllocate( $myImage, 0, 0, 0 );
$text_color  = ImageColorAllocate( $myImage, 0, 0, 0 );

$color[0]    = ImageColorAllocate( $myImage, 0, 0, 0 );
$color[1]    = ImageColorAllocate( $myImage, 0, 0, 255 );
$color[2]    = ImageColorAllocate( $myImage, 0, 255, 0 );
$color[3]    = ImageColorAllocate( $myImage, 0, 255, 255 );
$color[4]    = ImageColorAllocate( $myImage, 255, 0, 0 );
$color[5]    = ImageColorAllocate( $myImage, 255, 0, 255 );
$color[6]    = ImageColorAllocate( $myImage, 255, 255, 0 );
$color[7]    = ImageColorAllocate( $myImage, 240, 240, 240 );    // Avoid actual white
$color[8]    = ImageColorAllocate( $myImage, 0, 0, 128 );
$color[9]    = ImageColorAllocate( $myImage, 0, 128, 0 );
$color[10]   = ImageColorAllocate( $myImage, 128, 0, 0 );

$shaded[0]   = ImageColorAllocate( $myImage, 128, 128, 128 );
$shaded[1]   = ImageColorAllocate( $myImage, 128, 128, 255 );
$shaded[2]   = ImageColorAllocate( $myImage, 128, 255, 128 );
$shaded[3]   = ImageColorAllocate( $myImage, 128, 255, 255 );
$shaded[4]   = ImageColorAllocate( $myImage, 255, 128, 128 );
$shaded[5]   = ImageColorAllocate( $myImage, 255, 128, 255 );
$shaded[6]   = ImageColorAllocate( $myImage, 255, 255, 128 );

// Frame it

ImageRectangle( $myImage, 0, 0, WIDTH-1, HEIGHT-1, $frame_color );
ImageRectangle( $myImage, 310, 40, WIDTH-10, HEIGHT-20, $frame_color );

// Add a title

ImageTTFText( $myImage, 15, 0, 20, 30, $text_color, HEADER_FONT, "Country Population '000s" );

$parts = rand( 5, 11 );
$total = 0;

for( $i = 0; $i < $parts; $i++ )
{
    $size[$i]  = rand( 5, 50 );
    $total += $size[$i];
}

$total = 360.0 / $total;

for( $j = 160; $j > 150; --$j )
{
    $start = 0;
    
    for( $i = 0; $i < 7 && $start < 180; $i++ )
    {
        if( $i == 0 )
            ImageFilledArc( $myImage, 170, $j + 5, 250, 150, $start, $start += $size[$i] * $total, $shaded[$i], IMG_ARC_PIE );
        else
            ImageFilledArc( $myImage, 150, $j, 250, 150, $start, $start += $size[$i] * $total, $shaded[$i], IMG_ARC_PIE );
    }
}

$start = 0;

for( $i = 0; $i < $parts; $i++ )
{
    if( $i == 0 )
        ImageFilledArc( $myImage, 170, 155, 250, 150, $start, $start += $size[$i] * $total, $color[$i], IMG_ARC_PIE );
    else
        ImageFilledArc( $myImage, 150, 150, 250, 150, $start, $start += $size[$i] * $total, $color[$i], IMG_ARC_PIE );
    ImageFilledRectangle( $myImage, 320, 50 + $i * 20, 335, 65 + $i * 20, $color[$i] );
    ImageTTFText( $myImage, 11, 0, 342, 63 + $i * 20, $text_color, LEGEND_DATA_FONT, sprintf( "%2d000", $size[$i] ) ); 
}


//output the image to the browser
header ("Content-type: image/png");
ImagePNG($myImage);

//clean up after yourself
ImageDestroy($myImage);
?>
