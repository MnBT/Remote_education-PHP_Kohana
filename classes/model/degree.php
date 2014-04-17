<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Model_Degree extends ORM {

  public function rules() {
    return array(
      'name' => array(
        array('not_empty'),
        array('max_length', array(':value', 50))
      )
    );
  }

}