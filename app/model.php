<?php
class Task{
	public static $mysqli;
	public static function all(){
		$res = self::$mysqli->query("SELECT * FROM tasks");
		$arr =[];
		while( $task = $res->fetch_object( 'Task' ) ){
			array_push( $arr, $task );
		}
		return $arr;
	}
	public static function escape( $str ){
		return self::$mysqli->real_escape_string($str);
	}
	public function save(){
		$username = static::escape( $this->username );
		$email = static::escape( $this->email );
		$content = static::escape( $this->content );
		static::$mysqli->query( "INSERT INTO tasks (username, email, content) VALUES ('$username', '$email', '$content');" );
		//INSERT INTO `bj`.`tasks` (`username`, `email`, `content`) VALUES ('asdf', 'asdf@sd.dd', 'zxcv');
	}
}
Task::$mysqli = new mysqli("localhost", "bj", "123456", "bj");
?>