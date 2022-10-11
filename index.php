<?php
require_once 'classes/scriptLoader.php';
$title = 'Database Configuration';

includeLoader::include( 'header', $title );
?>

<?php

include_once 'db/dbinstall.php';


?>
	<a href="index.php">Index</a>
<?php
includeLoader::include( 'footer' );