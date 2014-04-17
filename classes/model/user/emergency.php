<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_emergency extends ORM {
	protected $_table_name = "user_emergency_contacts";

   // Relationships
   protected $_belongs_to = array(
      'user'	 => array(
         "model"			 => "user",
         "foreign_key"	 => "user_id"
      ),
      'type'    => array(
         "model"			 => "emergency_type",
         "foreign_key"	 => "type_id"
      )
   );

	function primary() {
		return $this->where("primary", "=", '1');
	}
}