<?php
require_once('model.php');
abstract class Control{
	protected $params;
	public function __construct($params){
		$this->params = $params;
	}
	abstract public function process();
}
class DefaultControl extends Control{
	public function process(){
		print_r($this->params[ 'path' ]);
	}
}
class ShowIndex extends Control{
	public function process(){
		Phug::displayFile('app/view/tasks.pug', [ 'tasks' => Task::all() ], [ 'pretty' => true ]);
	}
}
class NewTask extends Control{
	public function process(){
		Phug::displayFile('app/view/task_form.pug', [], [ 'pretty' => true ]);
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
?>