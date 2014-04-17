<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Model_Major extends ORM {

  // Relationships
  protected $_belongs_to = array(
    "curriculum" => array(
      "model" => "curriculum",
      "foreign_key" => "major_id"
    ),
    "degree" => array(
      "model" => "degree",
      "foreign_key" => "degree_id"
    )
  );
  
  protected $_has_many = array(

  );

  public function rules() {
    return array(
     'degree_id' => array(
        array('not_empty'),
      ),
      'name' => array(
        array('not_empty'),
        array('max_length', array(':value', 200))
      ),
      'duration' => array(
        array('not_empty'),
        array('max_length', array(':value', 3)),
        array('Valid::digit')
      ),
    );
  }

}