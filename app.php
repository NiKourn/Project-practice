<?php
require __DIR__ . '/load.php';
includeLoader::include( 'header', 'App123 title' );
echo '<pre>' . print_r(dbinstall::get_db_conn(), true) . '</pre>';
echo '<h1>App Page: Connection with db succesfull</h1>';
//echo "<div class=''>";
//$count = 1;
//foreach ( get_included_files() as $included_file ) {
//	echo $count . '. ' . $included_file . '<br>';
//	$count ++;
//}
//echo "</div>";
//
//echo dirname( __DIR__ );