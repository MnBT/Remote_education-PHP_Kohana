<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Speciality extends ORM {
    
    protected $_table_name = "specialities";
	
	// Relationships
  protected $_belongs_to = array(
    "curriculum" => array(
      "model" => "curriculum",
      "foreign_key" => "speciality_id"
    )
  );
}