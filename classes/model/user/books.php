<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_books extends ORM {
	protected $_table_name = "user_books";

	protected $_belongs_to = array(
			"book" => array(
					"model" => "book",
					"foreign_key" => "id",
			),
			"user" => array(
					"model" => "user",
					"foreign_key", "id"
			),
      	"course" => array(
					"model" => "course",
					"foreign_key", "id"
			)
	);
}
