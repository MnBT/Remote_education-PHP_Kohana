<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_experience extends ORM {
	protected $_table_name = "user_work_experience";

   // Relationships
   protected $_belongs_to = array(
      'user'	 => array(
         "model"			 => "user",
         "foreign_key"	 => "user_id"
      ),
      'country'	 => array(
         "model"			 => "country",
         "foreign_key"	 => "country_id"
      )
   );

   public function filters()
   {
      return array(
         'employed_from'    => array(
            array('trim'),
            array(function ($value) {
               if (!is_numeric($value)) return strtotime($value);
            })
         ),
         'employed_to'    => array(
            array('trim'),
            array(function ($value) {
               if (!is_numeric($value)) return strtotime($value);
            })
         ),
         'current_status' => array(
            array('intval'),
         ),
         'country_id'   => array(
            array('intval'),
         ),
      );
   }

   function current() {
      return $this->where("current_status", "=", '1');
   }
}