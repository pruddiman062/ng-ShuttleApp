<?php
//header('Content-Type: application/xml; charset=utf-8');

require_once './classes/utils.php';
require_once './classes/Shuttle.php';
$shuttle = new Shuttle();

echo $shuttle->buildJSON();

?>