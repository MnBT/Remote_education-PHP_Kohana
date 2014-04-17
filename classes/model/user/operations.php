<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_operations extends ORM
{
   protected $_table_name = "user_operations";

   protected $_belongs_to = array(
      "type" => array(
         "model"       => "operation_type",
         "foreign_key" => "type_id"
      ),
      "user" => array(
         "model"       => "user",
         "foreign_key" => "user_id"
      ),
   );


   public function rules()
   {
      return array(
         'id'          => array(
            array('not_empty'),
         ),
         'type_id'     => array(
            array('not_empty'),
         ),
         'user_id'     => array(
            array('not_empty'),
         ),
         'description' => array(
            array('max_length' => array(':value', 1000)),
         ),
         'date'        => array(
            array('not_empty'),
            array('date')
         )
      );
   }
}
