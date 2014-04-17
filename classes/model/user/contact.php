<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_contact extends ORM {
	protected $_table_name = "user_contacts";

	static public $types = array(
		self::TYPE_PHONE => "Phone",
		self::TYPE_FAX => "Fax",
		self::TYPE_EMAIL => "E-Mail",
	);

	static public $subtypes = array(
		self::SUBTYPE_HOME => "Home",
		self::SUBTYPE_WORK => "Work",
		self::SUBTYPE_CELL => "Cell",
		self::SUBTYPE_PRIMARY => "Primary",
		self::SUBTYPE_NONE => ""
	);

	static public $typeSubtypes = array(
		self::TYPE_PHONE => array(
			self::SUBTYPE_HOME,
			self::SUBTYPE_WORK,
			self::SUBTYPE_CELL,
		),
		self::TYPE_FAX => array(
			self::SUBTYPE_HOME,
			self::SUBTYPE_WORK
		),
		self::TYPE_EMAIL => array(
			self::SUBTYPE_NONE,
			self::SUBTYPE_PRIMARY
		)
	);

	const TYPE_PHONE		= 0x01;
	const TYPE_FAX			= 0x02;
	const TYPE_EMAIL		= 0x03;

	const SUBTYPE_HOME		= 0x01;
	const SUBTYPE_WORK		= 0x02;
	const SUBTYPE_CELL		= 0x03;
	const SUBTYPE_PRIMARY	= 0x04;
	const SUBTYPE_NONE		= 0x05;


	// Relationships
	protected $_belongs_to = array(
		'user'	 => array(
			"model"			 => "user",
			"foreign_key"	 => "user_id"
		)
	);

	public function update(Validation $validation = NULL) {
		if(in_array("value", $this->_changed) and $this->type!=self::TYPE_EMAIL) $this->value = preg_replace("/^\+?(\d+)\s+\(?(\d+)\)?\s+(\d{3})(\d+)$/", "+$1 $2 $3-$4", str_replace("-", "", $this->value));
      $logged_in_user = Session::instance()->get("current_user", $this->user);
		parent::update($validation);

		$prev = $this->user->history->column("contacts")->last()->find();
		if($prev->loaded()) {
			$prev->status &= Model_User_history::STATUS_APPROVED;
			$prev->update();
		}
		$this->user->history->values(array(
			"user_id" => $this->user->id,
			"column" => "contacts",
			"type" => $this->type,
			"subtype" => $this->subtype,
			"field" => $this->id,
			"value" => $this->value,
			"date" => time(),
			"editor_id" => $logged_in_user->id,
			"status" => Model_User_history::STATUS_LAST
		))->save();
		return $this;
	}

	public function create(Validation $validation = NULL) {
		if(in_array("value", $this->_changed)) $this->value = preg_replace("/^\+?(\d+)\s+\(?(\d+)\)?\s+(\d{3})(\d+)$/", "+$1 $2 $3-$4", str_replace("-", "", $this->value));
      $logged_in_user = Session::instance()->get("current_user", $this->user);

		parent::create($validation);

		$prev = $this->user->history->column("contacts")->last()->find();
		if($prev->loaded()) {
			$prev->status &= Model_User_history::STATUS_APPROVED;
			$prev->update();
		}
		$this->user->history->values(array(
			"user_id" => $this->user->id,
			"column" => "contacts",
			"type" => $this->type,
			"subtype" => $this->subtype,
			"field" => $this->id,
			"value" => $this->value,
			"date" => time(),
			"editor_id" => $logged_in_user->id,
			"status" => Model_User_history::STATUS_LAST
		))->save();
		return $this;
	}

	function delete() {
		$values = $this->as_array();
      $logged_in_user = Session::instance()->get("current_user", $this->user);

		parent::delete();

		$prev = $this->user->history->column("contacts")->last()->find();
		if($prev->loaded()) {
			$prev->status &= Model_User_history::STATUS_APPROVED;
			$prev->update();
		}
		$this->user->history->values(array(
				"user_id" => $values["user_id"],
				"column" => "contacts",
				"type" => $values["type"]["id"],
				"subtype" => $values["subtype"]["id"],
				"field" => $values["id"],
				"date" => time(),
				"editor_id" => $logged_in_user->id,
				"status" => Model_User_history::STATUS_LAST
		))->save();

		return $this;
	}

	function as_array() {
		$ret = parent::as_array();
		$ret["type"] = array(
			"id" => $ret["type"],
			"name" => $this->typeName($ret["type"])
		);
		$ret["subtype"] = array(
			"id" => $ret["subtype"],
			"name" => $this->subtypeName($ret["subtype"])
		);
		return $ret;
	}

	function typeName($type=null) {
		if(empty($type)) $type = $this->type;
		return $type?__(self::$types[$this->type]):"";
	}

	function subtypeName($subtype=null) {
		if(empty($subtype)) $subtype = $this->subtype;
		return $subtype?__(self::$subtypes[$subtype]):"";
	}

	public static function typeSubtypes($type) {
		$ret = array();
		if(!empty($type) and array_key_exists($type, self::$typeSubtypes)) {
			foreach(self::$typeSubtypes[$type] as $v) {
				$ret[$v] = __(self::$subtypes[$v]);
			}
		}
		return $ret;
	}

 	public function filters () {
		return array(
			'type'	 => array(
				array('intval')
			),
			'subtype'	 => array(
				array('intval')
			),
			'value'	 => array(
				array('trim'),
				array('strtolower')
			)
		);
	}

	public function rules () {
		return array(
			'type' => array(
				array('not_empty'),
                array(
                	function($value, Validation $validation, $types) {if(!array_key_exists($value, $types)) throw new ORM_Validation_Exception("type", $validation, "Unknown type \":type\" provided", array(":type" => $value)); },
                	array(":value", ":validation", self::$types)
                )
			),
			'subtype' => array(
				array('not_empty'),
                array(
                	function($value, Validation $validation, $subtypes) {if(!array_key_exists($value, $subtypes)) throw new ORM_Validation_Exception("type", $validation, "Unknown subtype \":type\" provided", array(":type" => $value)); },
                	array(":value", ":validation", self::$subtypes)
                )
			),
			'value'	 => array(
				array('not_empty'),
				array(array($this, "checkValue"), array(':value', ':validation'))
			)
		);
	}

   /*
    * Returns a primary contact by specified type
    */
   public function getPrimary ($nType = self::TYPE_PHONE) {

      if(!array_key_exists($nType, self::$types)) throw new Kohana_Exception("Unknown type \":type\" provided", array(":type" => $nType), 101);
      $nSubtype = ($nType == self::TYPE_EMAIL) ? self::SUBTYPE_PRIMARY : self::SUBTYPE_WORK;
      return $this->where("type", "=", $nType)->and_where("subtype", "=", $nSubtype)->find();
   }

	public function checkValue($value, Validation $validation) {
		if($this->type==self::TYPE_PHONE or $this->type==self::TYPE_FAX) {
			//die($value);
			if(!preg_match("/^\+?\d+\s+\(?\d+\)?\s+[\d\-]+$/", $value)) {
				throw new ORM_Validation_Exception("value", $validation, "Wrong :type value \":value\" provided", array(":type" => UTF8::strtolower($this->typeName()), ":value"=>$value));
			}
		} elseif($this->type==self::TYPE_EMAIL) {
			if(!Valid::email($value)) throw new ORM_Validation_Exception("value", $validation, "Wrong :type value \":value\" provided", array(":type" => UTF8::strtolower($this->typeName()), ":value"=>$value));
		}
	}
}
