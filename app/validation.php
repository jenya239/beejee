<?php
	abstract class Validation{
		protected $table;
		protected $column;
		function __construct( $table, $column ){
			$this->table = $table;
			$this->column = $column;
		}
		public function validate( $object ){
			if( ! $this->check( $object ) ){
				if( ! array_key_exists( $this->column, $object->errors ) ){
					$object->errors[ $this->column ] = [];
				}
				array_push( $object->errors[ $this->column ], $this->error( $object ) );
			}
		}
		protected abstract function check( $object );
		protected abstract function error( $object );
	}
	class Presence extends Validation{
		protected function check( $object ){
			return property_exists( $object, $this->column ) && ! is_null( $object->{ $this->column } ) && $object->{ $this->column } !== '';
		}
		protected function error( $object ){
			return "значение не указано";
		}
	}
	class AuthValidation extends Validation{
		protected function check( $object ){
			return $object->login == 'admin' && $object->password == '123';
		}
		protected function error( $object ){
			return "логин/пароль не найдены";
		}
	}
	class EmailValidation extends Validation{
		protected function check( $object ){
			return filter_var($object->{ $this->column }, FILTER_VALIDATE_EMAIL);
		}
		protected function error( $object ){
			return "некорректный email";
		}
	}
?>