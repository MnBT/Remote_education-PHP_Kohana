<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Model_Test extends ORM {

  protected $_table_name = "tests";
  // Relationships

  protected $_belongs_to = array(
    "type" => array(
      "model" => "test_type",
      "foreign_key" => "type_id"
    ),
    "course" => array(
       "model" => "course",
       "foreign_key" => "course_id"
    )

  );
   protected $_has_many = array(
      "testresults" => array(
         "model" => "user_testresults",
         "foreign_key" => "course_id"
      ),
   );


  public function rules() {
    return array(
      'type_id' => array(
        array('not_empty'),
      ),
      'course_id' => array(
         array('not_empty'),
      ),
      'name' => array(
        array('not_empty'),
        array('max_length', array(':value', 255)),
      )
    );
  }

}