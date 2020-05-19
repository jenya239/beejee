<?php
const PER_PAGE = 3;

class Task{
	public static $mysqli;
	public function __construct(){
		if( !property_exists( $this, 'username' ) ) $this->username = '';
		if( !property_exists( $this, 'email' ) ) $this->email = '';
		if( !property_exists( $this, 'content' ) ) $this->content = '';
	}
	public static function all( $column, $order, $page ){
		$column = self::escape( $column );
		$oder = $order == 'desc' ? 'desc' : 'asc';
		$offset = ($page - 1) * PER_PAGE;
		$res = self::$mysqli->query("SELECT * FROM tasks ORDER BY $column $order LIMIT $offset, " . PER_PAGE);
		$arr =[];
		while( $task = $res->fetch_object( 'Task' ) ){
			array_push( $arr, $task );
		}
		return $arr;
	}
	public static function find($id){
		$res = self::$mysqli->query( "SELECT * FROM tasks WHERE id = " . ((int) $id) );
		return $res->fetch_object( 'Task' );
	}
	public static function count(){
		$res = self::$mysqli->query( "SELECT COUNT(*) AS count FROM tasks" );
		return $res->fetch_object()->count;
	}
	public static function escape( $str ){
		return self::$mysqli->real_escape_string($str);
	}
	public function save(){
		$username = static::escape( $this->username );
		$email = static::escape( $this->email );
		$content = static::escape( $this->content );
		$done =$this->done ? 1 : 0;
		$sql =$this->id
			? "UPDATE tasks SET username='$username', email='$email', content='$content', done=$done WHERE id=$this->id"
			: "INSERT INTO tasks (username, email, content) VALUES ('$username', '$email', '$content');";
		static::$mysqli->query( $sql );
		//INSERT INTO `bj`.`tasks` (`username`, `email`, `content`) VALUES ('asdf', 'asdf@sd.dd', 'zxcv');
		//UPDATE `bj`.`tasks` SET `username`='33', `email`='ss@ww.w3', `content`='2' WHERE `id`='2';
	}
}
Task::$mysqli = new mysqli("localhost", "bj", "123456", "bj");
?>