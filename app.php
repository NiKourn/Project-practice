<?php
require __DIR__ . '/load.php';
includeLoader::include( 'header', 'App123 title' );

echo '<h1>App Page: Connection with db succesfull</h1>';
if ( dbinstall::get_db() ) {
echo '<pre>' . print_r(dbinstall::get_db(), true) . '</pre>';
}
//echo "<div class=''>";
//$count = 1;
//foreach ( get_included_files() as $included_file ) {
//	echo $count . '. ' . $included_file . '<br>';
//	$count ++;
//}
//echo "</div>";
//
//echo dirname( __DIR__ );