<?php
include_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/app/router.php';
new Routing( $_SERVER['REQUEST_URI'] );

?>