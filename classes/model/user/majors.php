<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_majors extends ORM {
	protected $_table_name = "user_majors";

	protected $_belongs_to = array(
			"user" => array(
					"model" => "user",
					"foreign_key", "user_id"
			),
      	"major" => array(
					"model" => "major",
					"foreign_key", "major_id"
			)
	);
}
