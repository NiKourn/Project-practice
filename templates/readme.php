<?php
require_once( 'vendor/autoload.php' );
$Parsedown = new Parsedown();
echo $Parsedown->text(file_get_contents('readme.md'));