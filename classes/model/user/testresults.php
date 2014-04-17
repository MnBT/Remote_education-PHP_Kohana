<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_testresults extends ORM {
	protected $_table_name = "user_testresults";

   protected $_belongs_to = array(
      "test" => array(
         "model" => "test",
         "foreign_key" => "test_id"
      ),
      "user" => array(
         "model" => "user",
         "foreign_key" => "user_id"
      ),
      "status" => array(
         "model" => "test_status",
         "foreign_key" => "status_id"
      ),
      "checkedby" => array(
         "model" => "user",
         "foreign_key" => "checked_by"
      ),
   );


   public function rules() {
      return array(
         'id' => array(
            array('not_empty'),
         ),
         'test_id' => array(
            array('not_empty'),
         ),
         'user_id' => array(
            array('not_empty'),
         ),
         'attempt' => array(
            array('not_empty'),
            array('digit'),
         ),
         'start_date' => array(
            array('not_empty'),
            array('date')
         ),
         'end_date' => array(
            array('not_empty'),
            array('date')
         ),
         //TODO Checked - счетчик сколько суток прошло
         // с даты End Date. Если не прошло 24 часа то значение “0".Если прошло 24 часа - значение “1".
         // Как только тест был проверен счетчик останавливается. Счетчик нужен для контроля, на сколько быстро
         //проверяются тесты.
         'check_date' => array(
            array('date')
         ),
         //TODO change this checked by should be from users table
         'checked_by' => array(
            array('max_length', array(':value', 100))
         ),
         'status_id' => array(
            array('not_empty'),
         ),

         //TODO decimal check ?? % - Percentage - Оценка в
         //процентах. Данные из Moodle.
      );
   }
}
