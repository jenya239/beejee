<?php
session_start();
if( !array_key_exists( 'admin', $_SESSION ) ){
	$_SESSION['admin'] = false;
}

include_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/app/router.php';
new Routing( $_SERVER['REQUEST_URI'] );

?>