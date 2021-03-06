<?php
require_once('validation.php');

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
		$res = self::$mysqli->query( "SELECT * FROM $this->name WHERE id = " . ((int) $id) );
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
	public $errors;
	public function __construct(){
		$this->table = static::$table_obj;
		$this->errors = [];
	}
	public function validate(){
		foreach( $this->table->validations as $validation ){
			$validation->validate( $this );
		}
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
		$edited = $this->edited ? 1 : 0;
		$sql =$this->id
			? "UPDATE tasks SET content='$content', done=$done, edited=$edited WHERE id=$this->id"
			: "INSERT INTO tasks (username, email, content) VALUES ('$username', '$email', '$content');";
		Table::$mysqli->query( $sql );
	}
}
Task::$table_obj = new Table( 'tasks', 'Task' );
Task::$table_obj->validations = [
	new Presence( Task::$table_obj, 'username' ),
	new Presence( Task::$table_obj, 'email' ),
	new Presence( Task::$table_obj, 'content' ),
	new EmailValidation( User::$table_obj, 'email' )
];

class User extends Model{
	public static $table_obj;
	public static $admin;
	public $login;
	public $password;
	public function __construct( $login, $password ){
		parent::__construct();
		$this->login = $login;
		$this->password = $password;
	}
}
User::$table_obj = new Table( '', 'User' );
User::$table_obj->validations = [
	new Presence( User::$table_obj, 'login' ),
	new Presence( User::$table_obj, 'password' ),
	new AuthValidation( User::$table_obj, 'login' )
];
?>