<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_history extends ORM {
	protected $_need_verify = array( "birthday", "sex" );
	protected $_table_name = "user_details_history";

	const STATUS_APPROVED = 0x01;
	const STATUS_LAST = 0x02;

	function column($field) {
		return $this->where("column", "=", $field);
	}

	function type($field) {
		return $this->where("type", "=", $field);
	}

	function field($field) {
		return $this->where("field", "=", $field);
	}

	function verified() {
		return $this->where("verified", "IS NOT", DB::expr("NULL"));
	}

	function approved() {
		return $this->where(DB::expr("status&".self::STATUS_APPROVED), "=", self::STATUS_APPROVED);
	}

	function last() {
		return $this->where(DB::expr("status&".self::STATUS_LAST), "=", self::STATUS_LAST);
	}

	function need_verify() {
		return in_array($v, $this->_need_verify);
	}

	function is_verified() {
		return !empty($this->verified);
	}

	function moderator() {
		return ORM::factory("user", $this->moder_id);
	}

	function editor() {
		return ORM::factory("user", $this->editor_id);
	}

	function is_approved() {
		return ($this->status & self::STATUS_APPROVED) == self::STATUS_APPROVED;
	}

	function is_last() {
		return ($this->status & self::STATUS_LAST) == self::STATUS_LAST;
	}

	function as_array() {
		$ret = parent::as_array();
		if($this->is_verified()) {
			$ret["moder"] = $this->moderator()->as_array();
		}
		$ret["editor"] = $this->editor()->as_array();
		$ret["last"] = $this->is_last();
		$ret["approved"] = $this->is_approved();
		return $ret;
	}
}