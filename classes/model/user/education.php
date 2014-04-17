<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_education extends ORM {
	protected $_table_name = "user_education";

   // Relationships
   protected $_belongs_to = array(
      'user'	 => array(
         "model"			 => "user",
         "foreign_key"	 => "user_id"
      )
   );

   public function filters () {
      return array(
         'date_expected'	 => array(
            array('trim'),
            array(function($value){ if(!is_numeric($value)) return strtotime($value); })
         ),
         'date_entered'	 => array(
            array('trim'),
            array(function($value){ if(!is_numeric($value)) return strtotime($value); })
         ),
      );
   }
}