<?php
require_once( 'controller.php' );
class Routing{
	private $path;
	public function __construct( $path ){
		$this->path = $path;
		$c = new DefaultControl( [ 'path' => $this->path ] );
		$c->process();
	}
}
?>