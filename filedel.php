<?php
$dirPath = "images/quoteimages";
if ( !( $handle = opendir( $dirPath ) ) ) die( "Cannot open the directory." );
while ( $file = readdir( $handle ) ) {
if ( $file != "." && $file != ".." &&  substr_count( $file, '__convert') == 1) {echo "<li>$file</li>"; 
if (unlink( "images/quoteimages/$file" )){echo '<b>deleted</b><br/>';}
}
}
closedir( $handle );