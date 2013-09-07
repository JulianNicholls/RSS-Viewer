# ARSS RSS Feed Viewer

Using [SimplePie](http://simplepie.org), this will interpret RSS (and Atom) 
feeds. The default is the BBC Home News feed. There is now an experimental 
aggregated feed, which seems to work OK.

Also included is an editor, which is linked to from the feeds panel.

## MongoDB

[MongoDB](http://www.mongodb.org) is used to store more feeds.
I have it installed as a Windows Service, as that seems the best way to use it.

## Bootstrap

Twitter's [Bootstrap](http://getbootstrap.com/) version 3.0 front-end framework
is used for most of the styling. As part of that, the editor uses 
[GlyphIcons](http://glyphicons.com/) for many of the buttons.

## Human Time

Included is humantime.php that implements a human interpretation of a time 
in the past, like GitHub does. It starts with 'just now' for up to a minute and 
a half, through 'this morning', 'yesterday', and 'a week ago', all the way to 
'n years ago'.

### Known Problems

Feed entries with pictures bigger than about 160px wide look a bit crap at 
the moment.