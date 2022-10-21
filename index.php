<?php
require __DIR__ . '/load.php';
includeLoader::include( 'header', 'Project title' );


echo "<div class=''>";
$count = 1;
foreach ( get_included_files() as $included_file ) {
	echo $count . '. ' . $included_file . '<br>';
	$count ++;
}
echo "</div>";


?>
	<a href="index.php">Index</a>
<?php
//includeLoader::include( 'footer' );