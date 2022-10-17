<?php
require_once 'classes/scriptLoader.php';


includeLoader::include( 'header' );
?>

<?php


echo "<div class=''>";
$count = 0;
foreach ( get_included_files() as $included_file ) {
	echo $count . '. ' . $included_file . '<br>';
	$count ++;
}
echo "</div>";


?>
	<a href="index.php">Index</a>
<?php
includeLoader::include( 'footer' );