<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Study extends ORM {
	protected $_table_name = "user_courses";

	// Relationships
//	protected $_belongs_to = array(
//			"course" => array(
//					"model" => "course",
//					"foreign_key" => "course_id"
//			)
//	);
	protected $_has_many = array(
			"course" => array(
					"model" => "course",
					"foreign_key" => "course_id"
			)
	);
}