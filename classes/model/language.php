<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Language extends ORM {
	// Relationships
	protected $_has_many = array(
		"users"	 => array(
			"model"			 => "user_detail",
			"foreign_key"	 => "study_language_id"
		),
		"books"	 => array(
			"model"			 => "book",
			"foreign_key"	 => "language_id"
		)
	);
}
