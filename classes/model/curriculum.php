<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Curriculum extends ORM {
  protected $_table_name = "curriculums";
  
  // Relationships

  protected $_belongs_to = array(
    "major" => array(
      "model" => "major",
      "foreign_key" => "major_id"
    ),
    "course" => array(
      "model" => "course",
      "foreign_key" => "course_id"
    ),
    "course_type" => array(
      "model" => "course_type",
      "foreign_key" => "course_type_id"
    ),
    "deactivation_type" => array(
      "model" => "deactivation_type",
      "foreign_key" => "deactivation_type_id"
    ),
    "control_type" => array(
      "model" => "control_type",
      "foreign_key" => "control_type_id"
    ),
  );
  
public function rules() {
    return array(
      'major_id' => array(
        array('not_empty'),
      ),
     'course_id' => array(
        array('not_empty'),
      ),
     'course_type_id' => array(
        array('not_empty'),
      ),
     'deactivation_type_id' => array(
        array('not_empty'),
      ),
      'credits' => array(
        array('not_empty'),
        array('max_length', array(':value', 3)),
        array('Valid::digit')
      ),
      'hours' => array(
        array('not_empty'),
        array('max_length', array(':value', 3)),
        array('Valid::digit')
      ),
      'course_start' => array(
        array('not_empty'),
        array('Valid::date')
      ),
      'course_limit' => array(
        array('not_empty'),
        array('max_length', array(':value', 3)),
        array('Valid::digit')
      ),
      'control_type_id' => array(
        array('not_empty'),
      ),
      'control_period' => array(
        array('not_empty'),
        array('max_length', array(':value', 3)),
        array('Valid::digit')
      ),
    );
  }
}