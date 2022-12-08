<?php
require __DIR__ . '/load.php';
echo "<div class=''>";
includeLoader::include( 'header', 'Project title' );
//autoload initialized classes like db built and fetch

//if ( ! dbinstall::get_db() ) {
//	clear_html_contents();
//	redirect( 'index.php' );
//	echo '<br><h2>Database Already Created, Redirecting...</h2>';
//}

//echo "<div class=''>";
//$count = 1;
//foreach ( get_included_files() as $included_file ) {
//	echo $count . '. ' . $included_file . '<br>';
//	$count ++;
//}
//echo "</div>";

//includeLoader::include( 'footer' );