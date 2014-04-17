<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Model_Book extends ORM {
  const STATUS_DISABLED = 0x01;
  const STATUS_MAIN = 0x02;

  // Relationships
  protected $_belongs_to = array(
    "course" => array(
      "model" => "course",
      "foreign_key" => "course_id",
    ),
    "type" => array(
      "model" => "book_type",
      "foreign_key" => "type_id"
    ),
    "lang" => array(
      "model" => "language",
      "foreign_key" => "language_id"
    )
  );

  public function rules() {
    return array(
     'course_id' => array(
        array('not_empty'),
      ),
     'type_id' => array(
        array('not_empty'),
      ),
      'lang_id' => array(
        array('not_empty'),
      ),
      'version' => array(
        array('not_empty'),
        array('max_length', array(':value', 2)),
        array('Valid::digit')
      ),
      'days' => array(
        array('not_empty'),
        array('max_length', array(':value', 3)),
        array('Valid::digit')
      ),
      'progress' => array(
        array('not_empty'),
        array('max_length', array(':value', 3)),
        array('Valid::digit')
      ),
    );
  }

  function current() {
    return $this->with("courses")->where(DB::expr("UNIX_TIMESTAMP()"), "BETWEEN", array(DB::expr("courses.date_study_start"), DB::expr("courses.date_study_end")));
  }

  function language($lang_id) {
    return $this->where("language_id", "=", $lang_id);
  }

  function main() {
    return $this->where("status", "&" . self::STATUS_MAIN . "=", self::STATUS_MAIN);
  }

  function enabled() {
    return $this->where("status", "&" . self::STATUS_DISABLED . "=", 0);
  }

}