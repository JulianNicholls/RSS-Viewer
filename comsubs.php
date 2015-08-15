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

function make_link($href, $text)
{
    return "<a href=\"$href\" target=\"_blank\">$text</a>";
}

function show_image($image)
{
    if(!$image)
        return;

    echo "<img src=\"$image\" alt=\"$title\" />\n";
}

function show_copyright($text)
{
    if(!$text)
        return;

    echo '<p class="text-center"><small>' . make_links($text) . "</small></p>\n";
}

function show_categories($cats)
{
    if(!$cats)
        return;

    echo "<p>Categories: ";

    foreach( $cats as $cat )
        echo $cat->get_label() . ', ';

    echo "</p>\n";
}

function show_contributors($conts)
{
    if(!$conts)
        return;

    echo "<p>Contributors: ";

    foreach( $conts as $cont )
        echo '(' . $cont->get_name() . ', ' . $cont->get_link() . ', ' . $cont->get_email() . '), ';

    echo "</p>\n";
}

function make_links($text)
{
    return preg_replace('/(https?:\/\/)(\S+)/', '<a href="$1$2" target="_blank">$2</a>', $text);
}
