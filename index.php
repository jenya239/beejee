<?php
include_once __DIR__ . '/vendor/autoload.php';

// Phug::display('p=$message', [
//   'message' => 'Hello',
// ]);

$mysqli = new mysqli("localhost", "bj", "123456", "bj");
$res = $mysqli->query("SELECT * FROM tasks");

Phug::displayFile('app/view/index.pug', [ 'res' => $res ], [ 'pretty' => true ]);

while( $row = $res->fetch_assoc() ){
	//print_r($row);
}


?>