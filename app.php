<?php
require __DIR__ . '/load.php';
includeLoader::include( 'header', 'App 1Title' );
echo '<pre>' . print_r(db_install::get_db_conn(), true) . '</pre>';
echo '<h1>App Page: Connection with db succesfull</h1>';
trigger_error('The message', E_USER_WARNING);
//echo "<div class=''>";
//$count = 1;
//foreach ( get_included_files() as $included_file ) {
//	echo $count . '. ' . $included_file . '<br>';
//	$count ++;
//}
//echo "</div>";
//
//echo dirname( __DIR__ );
includeLoader::include( 'footer');
