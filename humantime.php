<?php

//---------------------------------------------------------------------------
// $retval = a string with a 'human' representation of how long ago a passed 
// time was.
//
// Entry:   $time   Unix Timestamp of time to report on.
//          $now    Time to compare against, default is now.
//
// Return:  String representation
//
//---------------------------------------------------------------------------

function human_time( $time, $now = 0 )
{
    if( $now == 0 )     // Now
        $now = time();
        
    $midnight = mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) );
    $elapsed  = $now - $time;
    $hours    = ($elapsed / 3600.0 ) + 0.45;
    $days     = ($midnight - $time) / 86400.0;

    if( $elapsed < 60 )                 # Start with a few seconds
        return "just now";              # and bail out because no ' ago' needed
        
    if( $elapsed < 150 )                # Then Minutes
        $retval = "a couple of minutes";
    elseif( $elapsed < 270 )
        $retval = "a few minutes";
    elseif( $elapsed > (3*60+30) && $elapsed < (7*60) )
        $retval = "five minutes";
    elseif( $elapsed > (13*60+30) && $elapsed < (17*60+30) )
        $retval = "a quarter of an hour";
    elseif( $elapsed > (28*60+30) && $elapsed < (32*60+30) )
        $retval = "half an hour";
    elseif( $elapsed > (43*60+30) && $elapsed < (47*60+30) )
        $retval = "three quarters of an hour";
    elseif( $elapsed > (58*60+30) && $elapsed < (65*60) )
        $retval = "an hour";
    elseif( $days < 0 )                 # Still today
        if( $elapsed < (71*60) )
            $retval = sprintf( "%.0f minutes", ($elapsed / 60.0) );
        else
            $retval = sprintf( "%.0f hours", $hours );   # Followed by Hours
    elseif( $days < 1 )
        return "yesterday";             # Then Days
    elseif( $days >= 1 && $days <= 2.5 )
        $retval = "a couple of days";
    elseif( $days >= 2.6 && $days <= 4.5 )
        $retval = "a few days";
    elseif( $days >= 5.5 && $days <= 8.5 )
        $retval = "a week";
    elseif( $days >= 12.5 && $days <= 15.5 )
        $retval = "a fortnight";
    elseif( $days >= 27.5 && $days <= 33.5 )
        $retval = "a month";
    elseif( $days <= 41 )
        $retval = sprintf( "%.0f days", $days );        
    elseif( $days > 360 && $days < 370 )
        $retval = "a year";
    elseif( $days > 720 && $days < 740 )
        $retval = "a couple of years";
    elseif( $days < (20 * 30) )         # Finally months...
        $retval = sprintf( "%.0f months", $days / 30 );
    else
        $retval = sprintf( "%.0f years", ($days / 365) + 0.5 );    # ... and years
        
    return $retval . ' ago';
}