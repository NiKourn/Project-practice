<?php
require __DIR__ . '/load.php';
echo "<div class=''>";

includeLoader::include( 'header', 'Project title' );
echo '<h1>' . includeLoader::get_title() . '</h1>';
if ( dbinstall::get_db() ) {
	//Header( "Location: app.php" );
	Header( "Refresh:2;url=app.php" );
	echo '<br><h2>Database Already Created, Redirecting...</h2>';
exit();
}

echo "<div class=''>";
$count = 1;
foreach ( get_included_files() as $included_file ) {
	echo $count . '. ' . $included_file . '<br>';
	$count ++;
}
echo "</div>";

//includeLoader::include( 'footer' );