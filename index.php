<?php
require_once 'classes/scriptLoader.php';
include_once 'db/dbinstall.php';
$db->set_title('Database Configuration');

includeLoader::include( 'header', $db->get_title() );
?>

<?php

?>
	<a href="index.php">Index</a>
<?php
includeLoader::include( 'footer' );