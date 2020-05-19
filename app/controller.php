<?php
require_once('model.php');

abstract class Control{
	protected $params;
	public function __construct($params){
		$this->params = $params;
	}
	abstract public function process();
	public function check_admin(){ return false; }
}
class DefaultControl extends Control{
	public function process(){
		print_r($this->params[ 'path' ]);
	}
}
class ShowIndex extends Control{
	public function process(){
		$q =$this->params['path']['query'];
		$column = array_key_exists( 'column', $q ) ? $q[ 'column' ] : 'id';
		$order = array_key_exists( 'order', $q ) ? $q[ 'order' ] : 'desc';
		$page = array_key_exists( 'page', $q ) ? ((int) $q[ 'page' ]) : 1;
		$count = Task::count();
		$pages = ceil( $count / PER_PAGE );
		$page = array_key_exists( 'page', $q ) ? $q[ 'page' ] : 1;
		Phug::displayFile('app/view/tasks.pug', [ 
			'tasks' => Task::all( $column, $order, $page ), 
			'admin' => $_SESSION['admin'],
			'column' => $column,
			'order' => $order,
			'page' => $page,
			'pages' => $pages
		], [ 'pretty' => true ]);
	}
}
class NewTask extends Control{
	public function process(){
		Phug::displayFile('app/view/task_form.pug', [ 'task' => new Task() ], [ 'pretty' => true ]);
	}
}
class CreateTask extends Control{
	public function process(){
		print_r($_POST);
		$task = new Task();
		$task->username = $_POST['username'];
		$task->email = $_POST['email'];
		$task->content = $_POST['content'];
		$task->save();
	}
}
class SignIn extends Control{
	public function process(){
		Phug::displayFile('app/view/signin.pug', [], [ 'pretty' => true ]);
	}
}
class Login extends Control{
	public function process(){
		if( $_POST['login'] == 'admin' && $_POST['password'] == '123' ){
			print_r($_POST);
			$_SESSION['admin'] = true;
		}
	}
}
class Logout extends Control{
	public function process(){
		$_SESSION['admin'] = false;
	}
	public function check_admin(){ return true; }
}
class Edit extends Control{
	public function process(){
		Phug::displayFile('app/view/task_form.pug', [ 'task' => Task::find( $this->params['id'] ), 'admin' => $_SESSION['admin'] ], [ 'pretty' => true ]);
	}
	public function check_admin(){ return true; }
}
class UpdateTask extends Control{
	public function process(){
		print_r($_POST);
		$task = Task::find($_POST['id']);
		$task->username = htmlspecialchars($_POST['username']);
		$task->email = htmlspecialchars($_POST['email']);
		$task->content = htmlspecialchars($_POST['content']);
		$task->done =  array_key_exists( 'done' , $_POST );
		$task->save();
	}
	public function check_admin(){ return true; }
}
?>