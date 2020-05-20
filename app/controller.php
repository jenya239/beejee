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
		$order = array_key_exists( 'order', $q ) ? $q[ 'order' ] : 'asc';
		$page = array_key_exists( 'page', $q ) ? ((int) $q[ 'page' ]) : 1;
		$count = Task::$table_obj->count();
		$pages = ceil( $count / PER_PAGE );
		$page = array_key_exists( 'page', $q ) ? $q[ 'page' ] : 1;
		Phug::displayFile('app/view/tasks.pug', [ 
			'tasks' => Task::$table_obj->all( $column, $order, $page ), 
			'admin' => $_SESSION['admin'],
			'column' => $column,
			'order' => $order,
			'page' => $page,
			'pages' => $pages,
			'flash' => $_SESSION['flash']
		], [ 'pretty' => true ]);
	}
}
class CreateTask extends Control{
	public function process(){
		if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
			$task = new Task();
			$task->username = htmlspecialchars($_POST['username']);
			$task->email = htmlspecialchars($_POST['email']);
			$task->content = htmlspecialchars($_POST['content']);
			$task->validate();
			if( empty( $task->errors ) ){
				$task->save();
				array_push( $_SESSION['flash'], [ 'type' => 'primary', 'message' => 'Задача создана' ] );
				header( 'Location: /' );
				return;
			}else{
				array_push( $_SESSION['flash'], [ 'type' => 'danger', 'message' => 'Ошибка создания задачи' ] );
			}
		}else{
			$task = new Task();
		}
		Phug::displayFile('app/view/task_form.pug', [ 'task' => $task, 'flash' => $_SESSION['flash'] ], [ 'pretty' => true ]);
	}
}
class SignIn extends Control{
	public function process(){
		if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
			$user = new User( $_POST['login'], $_POST['password'] );
			$user->validate();
			if( empty( $user->errors ) ){
				$_SESSION['admin'] = true;
				array_push( $_SESSION['flash'], [ 'type' => 'primary', 'message' => 'Вы авторизованы' ] );
				header( 'Location: /' );
				return;
			}else{
				array_push( $_SESSION['flash'], [ 'type' => 'danger', 'message' => 'Ошибка авторизации' ] );
			}
		}else{
			$user = new User( '', '' );
		}
		Phug::displayFile('app/view/signin.pug', [ 'user' => $user, 'flash' => $_SESSION['flash'] ], [ 'pretty' => true ]);
	}
}
class Logout extends Control{
	public function process(){
		$_SESSION['admin'] = false;
		array_push( $_SESSION['flash'], [ 'type' => 'primary', 'message' => 'Вы разлогинились' ] );
		header( 'Location: /' );
	}
	public function check_admin(){ return true; }
}
class UpdateTask extends Control{
	public function process(){
		if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
			$task = Task::$table_obj->find($_POST['id']);
			$new_content = htmlspecialchars($_POST['content']);
			$task->edited = $task->edited == 1 ? true : false;
			if( $task->content != $new_content ){
				$task->content = $new_content;
				$task->edited = true;
			}
			$task->done = array_key_exists( 'done' , $_POST );
			$task->save();
			array_push( $_SESSION['flash'], [ 'type' => 'primary', 'message' => 'Задача изменена' ] );
			header( 'Location: /' );
			return;
		}else{
			$task = Task::$table_obj->find( $this->params['id'] );
		}
		Phug::displayFile('app/view/task_form.pug', [ 'task' => $task, 'admin' => $_SESSION['admin'], 'flash' => $_SESSION['flash'] ], [ 'pretty' => true ]);
	}
	public function check_admin(){ return true; }
}
?>