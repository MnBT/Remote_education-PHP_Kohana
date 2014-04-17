<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_location extends ORM {
	protected $_table_name = "user_locations";
	protected $_no_history = array("id", "user_id");

	const TYPE_HOME				= 0x01;
	const TYPE_JOB				= 0x02;
	const TYPE_BUSINESS			= 0x03;
	const TYPE_REST				= 0x04;
	const TYPE_TREATMENT		= 0x05;
	const TYPE_OTHER			= 0x06;

	static public $types = array(
			self::TYPE_HOME => "Home",
			self::TYPE_JOB => "Job",
			self::TYPE_BUSINESS => "Business Trip",
			self::TYPE_REST => "Rest",
			self::TYPE_TREATMENT => "Treatment",
			self::TYPE_OTHER => "Other"
	);

	// Relationships
	protected $_belongs_to = array(
		'user'	 => array(
			"model"			 => "user",
			"foreign_key"	 => "user_id"
		)
	);

	function as_array() {
		$ret = parent::as_array();
		$ret["type"] = array(
			"id" => $ret["type"],
			"name" => $this->typeName($ret["type"]),
			"description" => $ret["type"]==self::TYPE_OTHER?$ret["typeother"]:null
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
			'date_from'	 => array(
				array('trim'),
				array(function($value){if(!is_numeric($value)) return strtotime($value);})
			),
			'date_to'	 => array(
				array('trim'),
				array(function($value){if(!is_numeric($value)) return empty($value)?null:strtotime($value);})
			),
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
			'date_from'	 => array(
				array('not_empty'),
				array(
					function($value, Validation $validation) { if(!$value) throw new ORM_Validation_Exception("date_from", $validation, "Wrong new location begin date format"); },
					array(':value', ':validation')
				)
			),
			'date_to'	 => array(
				array(
					function($value, Validation $validation) { if($value===false) throw new ORM_Validation_Exception("date_to", $validation, "Wrong new location end date format"); },
					array(':value', ':validation')
				)
			),
		);
	}

	function current() {
		return $this
		->where_open()
			->where("date_to", "IS", null)
			->or_where("date_to", ">=", time())
		->where_close()
		->and_where("date_from", "<=", time())
		->order_by(DB::expr($this->_object_name.".date_to IS NOT NULL"), "desc")
		->order_by("date_from", "desc")
		->limit(1)->find();
	}

	function order_by_time($order="desc") {
		return $this->order_by(DB::expr("GREATEST(".$this->_object_name.".date_from, IFNULL(".$this->_object_name.".date_to, 0))"), $order);
	}
}
