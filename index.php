<?php
require __DIR__ . '/load.php';
includeLoader::include( 'header', 'Project title' );

echo "<div class='' style='border:1px solid black;height : 500px;'>TEST TEST</div>";
//autoload initialized classes like db built and fetch
//echo '<pre>' . print_r(dbinstall::get_db_conn(), true) . '</pre>';
//if ( dbinstall::get_db_conn() ) {
//	//clear_html_contents();
//	echo '<br><h2>Database Already Created, Redirecting...</h2>';
//	echo '<pre>' . print_r(dbinstall::get_db_conn(), true) . '</pre>';
//	redirect( 'app.php' );
//}else{
//	echo 'false';
//}
//echo "<div class=''>";
//$count = 1;
//foreach ( get_included_files() as $included_file ) {
//	echo $count . '. ' . $included_file . '<br>';
//	$count ++;
//}
//echo "</div>";

includeLoader::include( 'footer' );