<?php
require_once( 'controller.php' );
class Routing{
	private $path;
	public function __construct( $path ){
		$this->path = parse_url( $path );
		$q =[];
		if( array_key_exists( 'query' , $this->path ) ){
			parse_str( $this->path[ 'query' ], $q );
		}
		$this->path[ 'query' ] = $q;
		if( $this->path[ 'path' ] == '/' ){
			$c = new ShowIndex( [ 'path' => $this->path ] );
		}else if( $this->path[ 'path' ] == '/new_task' ){
			$c = new NewTask( [ 'path' => $this->path ] );
		}else if( $this->path[ 'path' ] == '/signin' ){
			$c = new SignIn( [ 'path' => $this->path ] );
		}else if( $this->path[ 'path' ] == '/logout' ){
			$c = new Logout( [ 'path' => $this->path ] );
		}else if( $this->path[ 'path' ] == '/create_task' ){
			$c = new CreateTask( [ 'path' => $this->path ] );
		}else if( preg_match('/^\/tasks\/(\d+)\/edit$/', $this->path[ 'path' ], $matches ) ){
			$c = new UpdateTask( [ 'path' => $this->path ] );
		}else{
			$c = new DefaultControl( [ 'path' => $this->path ] );
		}
		if( !$_SESSION['admin'] && $c->check_admin() ){
			header('Location: /signin');
		}else{
			$c->process();
		}
	}
}
?>