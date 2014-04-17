<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_address extends ORM {
	protected $_table_name = "user_address";
	protected $_no_history = array("id", "user_id");

	const TYPE_PERMANENT		= 0x01;
	const TYPE_CURRENT			= 0x02;

	const LOCATION_HOME			= 0x01;
	const LOCATION_OFFICE		= 0x02;
	const LOCATION_PARENTS		= 0x03;
	const LOCATION_OTHER		= 0x04;

	static public $types = array(
			self::TYPE_PERMANENT => "Permanent",
			self::TYPE_CURRENT => "Current"
	);

	static public $locations = array(
			self::LOCATION_HOME => "Home",
			self::LOCATION_OFFICE => "Office",
			self::LOCATION_PARENTS => "Parents",
			self::LOCATION_OTHER => "Other"
	);

	// Relationships
	protected $_belongs_to = array(
		'user'	 => array(
			"model"			 => "user",
			"foreign_key"	 => "user_id"
		)
	);

	public function update(Validation $validation = NULL) {
		$updated = $this->_changed;
      $logged_in_user = Session::instance()->get("current_user", $this->user);
		parent::update($validation);

		foreach($updated as $v) {
			if(in_array($v, $this->_no_history)) continue;
			$prev = $this->user->history->column("address")->type($this->type)->last()->find();
			if($prev->loaded()) {
				$prev->status &= Model_User_history::STATUS_APPROVED;
				$prev->update();
			}
			$this->user->history->values(array(
					"user_id" => $this->user->id,
					"column" => "address",
					"type" => $this->type,
					"field" => $v,
					"value" => $this->$v,
					"date" => time(),
					"editor_id" => $logged_in_user->id,
					"status" => Model_User_history::STATUS_LAST
			))->save();
		}
		return $this;
	}

	public function create(Validation $validation = NULL) {
		$updated = $this->_changed;
      $logged_in_user = Session::instance()->get("current_user", $this->user);
		parent::create($validation);

		foreach($updated as $v) {
			if(in_array($v, $this->_no_history)) continue;
			$prev = $this->user->history->column("address")->type($this->type)->last()->find();
			if($prev->loaded()) {
				$prev->status &= Model_User_history::STATUS_APPROVED;
				$prev->update();
			}
			$this->user->history->values(array(
					"user_id" => $this->user->id,
					"column" => "address",
					"type" => $this->type,
					"field" => $v,
					"value" => $this->$v,
					"date" => time(),
					"editor_id" => $logged_in_user->id,
					"status" => Model_User_history::STATUS_LAST
			))->save();
		}
		return $this;
	}

	function as_array() {
		$ret = parent::as_array();
		$ret["type"] = array(
			"id" => $ret["type"],
			"name" => $this->typeName($ret["type"])
		);
		$ret["location"] = array(
			"id" => $ret["location"],
			"name" => $this->locationName($ret["location"])
		);
		$ret["country"] = array(
			"id" => $ret["country"],
			"name" => ORM::factory("country", $ret["country"])->name
		);
		return $ret;
	}

	function typeName($type=null) {
		if(empty($type)) $type = $this->type;
		return $type?__(self::$types[$type]):"";
	}

	function locationName($location=null) {
		if(empty($location)) $location = $this->location;
		return $location?__(self::$locations[$location]):"";
	}

	function countryName($country=null) {
		if(empty($country)) $country = $this->country;
		return ORM::factory("country", $country)->name;
	}

	public static function types() {
		$ret = array();
		foreach(self::$types as $k=>$v) {
			$ret[$k] = __($v);
		}
		return $ret;
	}

	public static function locations() {
		$ret = array();
		foreach(self::$locations as $k=>$v) {
			$ret[$k] = __($v);
		}
		return $ret;
	}

	public function filters () {
		return array(
			'type' => array(
				array('intval')
			),
			'country' => array(
				array('intval')
			),
			'state' => array(
				array('trim')
			),
			'city' => array(
				array('trim')
			),
			'address' => array(
				array('trim')
			),
			'location' => array(
				array('intval')
			),
			'zip' => array(
				array('trim')
			)
		);
	}

	public function rules () {
		return array(
			'type' => array(
				array('not_empty'),
                array(
                	function($value, Validation $validation, $types) {if(!array_key_exists($value, $types)) throw new ORM_Validation_Except("type", $validation, "Unknown type #:type provided", array(":type" => $value)); },
                	array(":value", ":validation", self::$types)
                )
			),
			'country' => array(
				array('not_empty'),
				array(
					function($value, Validation $validation){ if(!empty($value) and !ORM::factory("country",$value)->loaded()) throw new ORM_Validation_Exception("residence", $validation, "Wrong country code"); },
					array(':value', ':validation')
				)
			),
			'state' => array(
				array('max_length', array(':value', 100))
			),
			'city' => array(
				array('not_empty'),
				array('max_length', array(':value', 200))
			),
			'address' => array(
				array('not_empty'),
				array('max_length', array(':value', 250))
			),
			'location'	 => array(
				array('not_empty'),
                array(
                	function($value, Validation $validation, $locations) {if(!array_key_exists($value, $locations)) throw new ORM_Validation_Except("location", $validation, "Unknown location #:location provided", array(":location" => $value)); },
                	array(":value", ":validation", self::$locations)
                )
			),
			'zip' => array(
				array('max_length', array(':value', 50))
			)
		);
	}

	function type($type) {
		if(!is_array($type)) return $this->where("type", "=", $type);
		else return $this->where("type", "in", $type);
	}

	function user($user) {
		return $this->where("user_id", "=", $user);
	}

}
