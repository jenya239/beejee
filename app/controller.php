<?php
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
?>