<?php
const PER_PAGE = 3;

class Table{
	public $name;
	private $model_class;
	public $validations;
	public static $mysqli;
	public function __construct( $name, $model_class ){
		$this->name = $name;
		$this->model_class = $model_class;
	}
	public function all( $column, $order, $page ){
		$column = self::escape( $column );
		$oder = $order == 'desc' ? 'desc' : 'asc';
		$offset = ($page - 1) * PER_PAGE;
		$res = self::$mysqli->query("SELECT * FROM $this->name  ORDER BY $column $order LIMIT $offset, " . PER_PAGE);
		$arr =[];
		while( $task = $res->fetch_object( $this->model_class ) ){
			array_push( $arr, $task );
		}
		return $arr;
	}
	public function find($id){
		$res = self::$mysqli->query( "SELECT * FROM $this->model_class WHERE id = " . ((int) $id) );
		return $res->fetch_object( $this->model_class );
	}
	public function count(){
		$res = self::$mysqli->query( "SELECT COUNT(*) AS count FROM $this->name" );
		return $res->fetch_object()->count;
	}
	public function escape( $str ){
		return self::$mysqli->real_escape_string($str);
	}
}
Table::$mysqli = new mysqli("localhost", "bj", "123456", "bj");

abstract class Model{
	protected $table;
	public function __construct(){
		$this->table = static::$table_obj;
	}
}

class Task extends Model{
	public static $table_obj;
	public function __construct(){
		parent::__construct();
		if( !property_exists( $this, 'username' ) ) $this->username = '';
		if( !property_exists( $this, 'email' ) ) $this->email = '';
		if( !property_exists( $this, 'content' ) ) $this->content = '';
	}
	public function save(){
		$username = $this->table->escape( $this->username );
		$email = $this->table->escape( $this->email );
		$content = $this->table->escape( $this->content );
		$done =$this->done ? 1 : 0;
		$sql =$this->id
			? "UPDATE tasks SET username='$username', email='$email', content='$content', done=$done, edited=1 WHERE id=$this->id"
			: "INSERT INTO tasks (username, email, content) VALUES ('$username', '$email', '$content');";
		Table::$mysqli->query( $sql );
	}
}
Task::$table_obj = new Table( 'tasks', 'Task' );

class User extends Model{
	public static $table_obj;
	public static $admin;
	private $login;
	private $password;
	public function __construct( $login, $password ){
		parent::__construct();
		$this->login = $login;
		$this->password = $password;
	}
	public function check_admin(){
		return $this->login == 'admin' && $this->password == '123';
	}
}
User::$table_obj = new User( '', 'User' );
?>