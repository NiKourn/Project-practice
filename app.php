<?php
echo '<h1>App Page: Connection with db succesfull</h1>';

echo "<div class=''>";
$count = 1;
foreach ( get_included_files() as $included_file ) {
	echo $count . '. ' . $included_file . '<br>';
	$count ++;
}
echo "</div>";