<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Model_Course extends ORM {

//  protected $_table_name = "courses";
  // Relationships

  protected $_belongs_to = array(
    "courses" => array(
      "model" => "user_courses",
      "foreign_key" => "course_id"
    ),
    "test" => array(
       "model" => "test",
       "foreign_key" => "id"
    )
  );

  protected $_has_many = array(
    "books" => array(
      "model" => "book",
      "foreign_key" => "course_id"
    )
  );

  public function rules() {
    return array(
      'code' => array(
        array('not_empty'),
        array('min_length', array(':value', 3)),
        array('max_length', array(':value', 50)),
        array(array($this, 'unique'), array('code', ':value')),
      ),
      'name' => array(
        array('not_empty'),
        array('max_length', array(':value', 255)),
      ),
      'description' => array(
        array('not_empty'),
        array('max_length', array(':value', 255)),
      ),

    );
  }

}