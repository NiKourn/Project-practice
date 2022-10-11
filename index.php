<?php
require_once 'classes/scriptLoader.php';
$title = 'Database Configuration';

includeLoader::include( 'header', $title );
?>

<?php

include_once 'db/dbinstall.php';
	echo '<pre>' . print_r( $db->get_db(), true ) . '</pre>';


?>
	<a href="index.php">Index</a>
<?php
includeLoader::include( 'footer' );