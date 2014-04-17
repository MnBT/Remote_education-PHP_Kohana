<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_courses extends ORM {
	protected $_table_name = "user_courses";

	protected $_belongs_to = array(
			"course" => array(
					"model" => "course",
					"foreign_key" => "course_id",
			),
			"user" => array(
					"model" => "user",
					"foreign_key", "user_id"
			)
	);
	
	protected $_has_one = array(
			"books" => array(
					"model" => "book",
					"foreign_key" => "id",
					"through" => "courses_books",
					"far_key" => "course_id"
			)
	);

   protected $_has_many = array(
      'testresults' => array(
         "model" => "user_testresults",
         "foreign_key" => "user_course_id",
      )
   );


}
