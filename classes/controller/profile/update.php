<?php defined('SYSPATH') or die('Restricted access!');
/**
 *
 * Контроллер
 * @author esprit
 *
 */

class Controller_Profile_update extends Controller_Students {


	public function action_index () {
		$name = Arr::get($_REQUEST, 'name');
      if (!method_exists($this, "do_".$name)) {
			$ret = array(
				"error"	 => __("Unknown field"),
			);
		} else {
			$value = Arr::get($_REQUEST, 'value');
			$error = null;
			try {
            $this->orm_selectedUser->details->$name = $value;
				$this->orm_selectedUser->details->save();
			} catch (ORM_Validation_Exception $e) {
				$error = $e->getMessage();
			}
			$func = "do_".$name;
			$ret = Arr::merge(array("error" => $error, "name" => $name, "value" => $value), $this->$func());
		}
		echo json_encode($ret);
		exit;
	}

	public function action_contacts() {
		$name = Arr::get($_REQUEST, 'name');
		$cid = sscanf($name, "contacts[%d]");
		$cid = $cid[0];
		if(is_null($cid)) {
			$ret = array(
					"error"	 => __("Unknown field"),
			);
		} else {
			$value = Arr::get($_REQUEST, 'value');
			$error = null;
			try {
				$contact = ORM::factory("user_contact", $cid);
				if(!$contact->loaded()) throw new Kohana_Exception("Requested contact not found");
				$contact->value = $value;
				$contact->save();

				$hist = $contact->user->history->last()->column("contacts")->find()->as_array();
				$user = Arr::get($hist, "moder", Arr::get($hist, "editor"));
				$resp = array(
						"value"			 => $contact->value,
						"displayValue"	 => $contact->value." ".$contact->subtypeName(),
						"verified"		 => array(
								"date"	 => date("M. d, Y", Arr::get($hist, "verified", Arr::get($hist, "date", time()))),
								"user"	 => Arr::get($user, "username", __("Unknown"))
						)
				);
			} catch (Kohana_Exception $e) {
				$error = $e->getMessage();
				$resp = array();
			}

			$ret = Arr::merge(array("error" => $error, "name" => "contacts_".$cid, "value" => $value), $resp);
		}
		echo json_encode($ret);
		exit;
	}

	public function action_address() {
		$name = Arr::get($_REQUEST, 'name');
		list($type, $name) = sscanf($name, "address[%d][%s]");
		$name = trim($name, "][");
		if(!array_key_exists($type, Model_User_address::$types)) {
			$ret = array(
					"error"	 => __("Unknown address type"),
			);
		} else {
			$value = Arr::get($_REQUEST, 'value');
			$error = null;
			try {
				$address = $this->orm_selectedUser->address->type($type)->find();
				if(!$address->loaded()) throw new Kohana_Exception("Requested address not found");
				$address->$name = $value;
				$address->save();

				$hist = $this->orm_selectedUser->history->last()->column("address")->type($type)->find()->as_array();
				$user = Arr::get($hist, "moder", Arr::get($hist, "editor"));

				switch($name) {
					case "type":
						$displayValue = $address->typeName();
						break;
					case "location":
						$displayValue = $address->locationName();
						break;
					case "country":
						$displayValue = $address->countryName();
						break;
					default:
						$displayValue = $address->$name;
				}

				$resp = array(
						"value"			 => $address->$name,
						"displayValue"	 => $displayValue,
						"verified"		 => array(
								"date"	 => date("M. d, Y", Arr::get($hist, "verified", Arr::get($hist, "date", time()))),
								"user"	 => Arr::get($user, "username", __("Unknown"))
						)
				);
			} catch (Kohana_Exception $e) {
				$error = $e->getMessage();
				$resp = array();
			}

			$ret = Arr::merge(array("error" => $error, "name" => "address_".$type."_".$name, "value" => $value), $resp);
		}
		echo json_encode($ret);
		exit;
	}

   public function action_general_info() {
      $name = Arr::get($_REQUEST, 'name');
      sscanf($name, "general_info[%s]", $name);
      $name = trim($name, "][");

      $value = Arr::get($_REQUEST, 'value');
      $error = null;

      try {
         switch($name) {
            case "major":
               $major = $this->orm_selectedUser->user_majors->where("active", "=", "1")->find();
               if($major->loaded())
               {
                  $major->active = 0;
                  $major->save();
               }
               $new_major = ORM::factory('user_majors');
               $new_major->user_id = $this->orm_selectedUser->id;
               $new_major->major_id = $value;
               $new_major->active = 0x01;
               $new_major->save();
               $displayValue = ORM::factory('major', $new_major->major_id)->name;
               break;
            case "study_language":
               $this->orm_selectedUser->details->study_language_id = $value;
               $this->orm_selectedUser->details->save();
               $displayValue = ORM::factory('language', $this->orm_selectedUser->details->study_language_id)->description;
               break;
            case "application_status":
            case "admission_type":
               $name_id = $name . "_id";
               $this->orm_selectedUser->details->$name_id = $value;
               $this->orm_selectedUser->details->save();
               $displayValue = ORM::factory($name, $this->orm_selectedUser->details->$name_id)->name;
               break;
            case "admission_date":
               $this->orm_selectedUser->details->reg_date = $value;
               $this->orm_selectedUser->details->save();
               $displayValue = self::dateFormat($this->orm_selectedUser->details->reg_date);
               break;
            default:
               $this->orm_selectedUser->details->$name = $value;
               $this->orm_selectedUser->details->save();
               $displayValue = $this->orm_selectedUser->details->$name;
               break;
         }

         $resp = $this->buildReply($name, $displayValue, $value);
      } catch (Kohana_Exception $e) {
         $error = $e->getMessage();
         $resp = array();
      }

         $ret = Arr::merge(array("error" => $error, "name" => "general_info_".$name, "value" => $value), $resp);

      echo json_encode($ret);
      exit;
   }

   public function action_emergency_contacts() {
      $name = Arr::get($_REQUEST, 'name');
      sscanf($name, "emergency_contacts[%d][%s]", $id, $name);
      $id = trim($id, "][");
      $name = trim($name, "][");
      $emergency_contact = ORM::factory('user_emergency', $id);

      $value = Arr::get($_REQUEST, 'value');
      $error = null;
         try {
            if(!$emergency_contact->loaded()) {
               throw new Kohana_Exception("Requested emergency contact not found");
            }
            $emergency_contact->$name = $value;
            $emergency_contact->save();
            $displayValue = $emergency_contact->$name;

            $hist = $this->orm_selectedUser->history->last()->column("emergency_contacts")->type($id)->find()->as_array();
            $user = Arr::get($hist, "moder", Arr::get($hist, "editor"));

            $resp = array(
               "value"			 => $emergency_contact->$name,
               "displayValue"	 => $displayValue,
               "verified"		 => array(
                  "date"	 => date("M. d, Y", Arr::get($hist, "verified", Arr::get($hist, "date", time()))),
                  "user"	 => Arr::get($user, "username", __("Unknown"))
               )
            );
         } catch (Kohana_Exception $e) {
            $error = $e->getMessage();
            $resp = array();
         }

         $ret = Arr::merge(array("error" => $error, "name" => "emergency_contacts_".$id."_".$name, "value" => $value), $resp);

      echo json_encode($ret);
      exit;
   }

   public function action_education() {
      $name = Arr::get($_REQUEST, 'name');
      if(substr_count($name, "[") > 1)
      {
         sscanf($name, "education[%d][%s]", $id, $name);
      }
      else
      {
         sscanf($name, "education[%s]", $name);
      }

      $name = trim($name, "][");
      if(isset($id))
      {
         $id = trim($id, "][");
         $education = ORM::factory('user_education', $id);
      }
      else
      {
         $education = ORM::factory('user_detail', $this->orm_selectedUser->id);
      }


      $value = Arr::get($_REQUEST, 'value');
      $error = NULL;

      try {
         if(!$education->loaded()) {
            throw new Kohana_Exception("Requested education record not found");
         }

         switch($name)
         {
            case "currently_student":
            case "native_english":
               $education->$name = $value;
               $education->save();
               $displayValue = __(($education->$name == 1) ? "Yes" : "No");
               break;
            case "date_entered":
            case "date_expected":
               $education->$name = $value;
               $education->save();
               $displayValue = self::dateFormat($education->$name);
               break;
            default:
               $education->$name = $value;
               $education->save();
               $displayValue = $education->$name;
               break;

         }

         $hist = $this->orm_selectedUser->history->last()->column((isset($id)) ? "education" : "details")->type((isset($id)) ? $id : $name)->find()->as_array();
         $user = Arr::get($hist, "moder", Arr::get($hist, "editor"));

         $resp = array(
            "value"			 => $education->$name,
            "displayValue"	 => $displayValue,
            "verified"		 => array(
               "date"	 => date("M. d, Y", Arr::get($hist, "verified", Arr::get($hist, "date", time()))),
               "user"	 => Arr::get($user, "username", __("Unknown"))
            )
         );
      } catch (Kohana_Exception $e) {
         $error = $e->getMessage();
         $resp = array();
      }

      $ret = Arr::merge(array("error" => $error, "name" => "education_". ((isset($id)) ? $id."_" : "" ) . $name, "value" => $value), $resp);

      echo json_encode($ret);
      exit;
   }

   public function action_work_experience() {
      $name = Arr::get($_REQUEST, 'name');
      sscanf($name, "work_experience[%d][%s]", $id, $name);


      $name = trim($name, "][");
      $id = trim($id, "][");
      $work_expierence = ORM::factory('user_experience', $id);

      $value = Arr::get($_REQUEST, 'value');
      $error = NULL;

      try
      {
         if(!$work_expierence->loaded())
         {
            throw new Kohana_Exception("Requested work experience record not found");
         }

         switch($name)
         {
            case "current_status":
               $work_expierence->$name = $value;
               $work_expierence->save();
               $displayValue = __(($value == 1) ? "Yes" : "No");
               break;
            case "country":
               $_name = $name . "_id";
               $work_expierence->$_name = $value;
               $work_expierence->save();
               $displayValue = ORM::factory($name, $work_expierence->$_name)->name;
               break;
            case "employed_from":
            case "employed_to":
               $work_expierence->$name = $value;
               $work_expierence->save();
               $displayValue = self::dateFormat($work_expierence->$name);
               break;
            default:
               $work_expierence->$name = $value;
               $work_expierence->save();
               $displayValue = $work_expierence->$name;
               break;

         }

         $hist = $this->orm_selectedUser->history->last()->column("work_experience")->type(($id))->find()->as_array();
         $user = Arr::get($hist, "moder", Arr::get($hist, "editor"));

         $resp = array(
            "value"			 => $work_expierence->$name,
            "displayValue"	 => $displayValue,
            "verified"		 => array(
               "date"	 => date("M. d, Y", Arr::get($hist, "verified", Arr::get($hist, "date", time()))),
               "user"	 => Arr::get($user, "username", __("Unknown"))
            )
         );
      } catch (Kohana_Exception $e) {
         $error = $e->getMessage();
         $resp = array();
      }

      $ret = Arr::merge(array("error" => $error, "name" => "work_experience_". ((isset($id)) ? $id."_" : "" ) . $name, "value" => $value), $resp);

      echo json_encode($ret);
      exit;
   }



	public function action_contacts_add() {
		$type = Arr::get($_REQUEST, 'type');
		$subtype = Arr::get($_REQUEST, 'subtype');
		$value = Arr::get($_REQUEST, 'value');

		try {
			$contact = ORM::factory("user_contact");
			$contact->user_id = $this->orm_selectedUser->id;
			$contact->type = $type;
			$contact->subtype = $subtype;
			$contact->value = $value;
			$cid = $contact->save()->id;

			$hist = $contact->user->history->last()->column("contacts")->find()->as_array();
			$user = Arr::get($hist, "moder", Arr::get($hist, "editor"));
			$resp = array(
					"value"			 => $contact->value,
					"displayValue"	 => $contact->value." ".$contact->subtypeName(),
					"verified"		 => array(
							"date"	 => date("M. d, Y", Arr::get($hist, "verified", Arr::get($hist, "date", time()))),
							"user"	 => Arr::get($user, "username", __("Unknown"))
					)
			);
			$error = null;
		} catch (Exception $e) {
			$error = $e->getMessage();
			$resp = array();
			$cid=0;
		}

		$ret = Arr::merge(array("error" => $error, "name" => "contacts_".$cid, "value" => $value), $resp);
		echo json_encode($ret);
		exit;
	}

	public function action_contacts_remove() {
		$name = Arr::get($_REQUEST, 'name');
      $cid = sscanf($name, "contacts[%d]");
		$cid = $cid[0];
		if(is_null($cid)) {
			$ret = array(
					"error"	 => __("Unknown field"),
			);
		} else {
			$value = Arr::get($_REQUEST, 'value');
			$error = null;
			try {
				$contact = ORM::factory("user_contact", $cid);
				if(!$contact->loaded()) throw new Kohana_Exception("Requested contact not found");
				$contact->delete();

				$hist = $this->orm_selectedUser->details->history->last()->column("contacts")->find()->as_array();
				$user = Arr::get($hist, "moder", Arr::get($hist, "editor"));
				$resp = array(
						"verified"		 => array(
								"date"	 => date("M. d, Y", Arr::get($hist, "verified", Arr::get($hist, "date", time()))),
								"user"	 => Arr::get($user, "username", __("Unknown"))
						)
				);
			} catch (Kohana_Exception $e) {
				$error = $e->getMessage();
				$resp = array();
			}

			$ret = Arr::merge(array("error" => $error, "name" => "contacts_".$cid), $resp);
		}
		echo json_encode($ret);
		exit;
	}

   public function action_emergency_contacts_add() {
      $type = Arr::get($_REQUEST, 'type');
      $name = Arr::get($_REQUEST, 'name');
      $phone = Arr::get($_REQUEST, 'phone');
      $email = Arr::get($_REQUEST, 'email');

      try {
         $emergency_contact = ORM::factory("user_emergency");
         $emergency_contact->user_id = $this->orm_selectedUser->id;
         $emergency_contact->type_id = $type;
         $emergency_contact->name = $name;
         $emergency_contact->phone = $phone;
         $emergency_contact->email = $email;
         $cid = $emergency_contact->save()->id;

         $hist = $emergency_contact->user->history->last()->column("emergency_contacts")->find()->as_array();
         $user = Arr::get($hist, "moder", Arr::get($hist, "editor"));
         $resp = array(
            "value"			 => $emergency_contact->phone,
            "displayValue"	 => $emergency_contact->phone." ".$emergency_contact->email,
            "verified"		 => array(
               "date"	 => date("M. d, Y", Arr::get($hist, "verified", Arr::get($hist, "date", time()))),
               "user"	 => Arr::get($user, "username", __("Unknown"))
            )
         );
         $error = NULL;
      } catch (Exception $e) {
         $error = $e->getMessage();
         $resp = array();
         $cid=0;
      }

      $ret = Arr::merge(array("error" => $error, "name" => "emergency_contacts_".$cid, "value" => $phone), $resp);
      echo json_encode($ret);
      exit;
   }

   public function action_emergency_contacts_remove() {
      $name = Arr::get($_REQUEST, 'name');
      $cid = sscanf($name, "emergency_contacts[%d]");
      $cid = $cid[0];
      if(is_null($cid)) {
         $ret = array(
            "error"	 => __("Unknown emergency contact id"),
         );
      } else {
         $error = null;
         try {
            $emergency_contact = ORM::factory("user_emergency", $cid);
            if(!$emergency_contact->loaded()) throw new Kohana_Exception("Requested emergency contact not found");
            $emergency_contact->delete();

            $hist = $this->orm_selectedUser->details->history->last()->column("emergency_contacts")->find()->as_array();
            $user = Arr::get($hist, "moder", Arr::get($hist, "editor"));
            $resp = array(
               "verified"		 => array(
                  "date"	 => date("M. d, Y", Arr::get($hist, "verified", Arr::get($hist, "date", time()))),
                  "user"	 => Arr::get($user, "username", __("Unknown"))
               )
            );
         } catch (Kohana_Exception $e) {
            $error = $e->getMessage();
            $resp = array();
         }

         $ret = Arr::merge(array("error" => $error, "name" => "emergency_contacts_".$cid), $resp);
      }
      echo json_encode($ret);
      exit;
   }

   public function action_education_add() {
      $name = Arr::get($_REQUEST, 'name');
      $location = Arr::get($_REQUEST, 'location');
      $date_entered = Arr::get($_REQUEST, 'date_entered');
      $major = Arr::get($_REQUEST, 'major');
      $degree_expected = Arr::get($_REQUEST, 'degree_expected');
      $date_expected = Arr::get($_REQUEST, 'date_expected');
      $prev_degree = Arr::get($_REQUEST, 'prev_degree');

      try {
         $education = ORM::factory("user_education");
         $education->user_id = $this->orm_selectedUser->id;
         $education->name = $name;
         $education->location = $location;
         $education->date_entered = $date_entered;
         $education->major = $major;
         $education->degree_expected = $degree_expected;
         $education->date_expected = $date_expected;
         $education->prev_degree = $prev_degree;
         $cid = $education->save()->id;

         $hist = $education->user->history->last()->column("education")->find()->as_array();
         $user = Arr::get($hist, "moder", Arr::get($hist, "editor"));
         $resp = array(
            "value"			 => $education->name,
            "displayValue"	 => $education->name." ".$education->name,
            "verified"		 => array(
               "date"	 => date("M. d, Y", Arr::get($hist, "verified", Arr::get($hist, "date", time()))),
               "user"	 => Arr::get($user, "username", __("Unknown"))
            )
         );
         $error = NULL;
      } catch (Exception $e) {
         $error = $e->getMessage();
         $resp = array();
         $cid=0;
      }

      $ret = Arr::merge(array("error" => $error, "name" => "education_".$cid, "value" => $date_entered), $resp);
      echo json_encode($ret);
      exit;
   }

   public function action_education_remove() {
      $name = Arr::get($_REQUEST, 'name');
      $cid = sscanf($name, "education[%d]");
      $cid = $cid[0];
      if(is_null($cid)) {
         $ret = array(
            "error"	 => __("Unknown education id"),
         );
      } else {
         $error = null;
         try {
            $education = ORM::factory("user_education", $cid);
            if(!$education->loaded()) throw new Kohana_Exception("Requested contact not found");
            $education->delete();

            $hist = $this->orm_selectedUser->details->history->last()->column("education")->find()->as_array();
            $user = Arr::get($hist, "moder", Arr::get($hist, "editor"));
            $resp = array(
               "verified"		 => array(
                  "date"	 => date("M. d, Y", Arr::get($hist, "verified", Arr::get($hist, "date", time()))),
                  "user"	 => Arr::get($user, "username", __("Unknown"))
               )
            );
         } catch (Kohana_Exception $e) {
            $error = $e->getMessage();
            $resp = array();
         }

         $ret = Arr::merge(array("error" => $error, "name" => "education_".$cid), $resp);
      }
      echo json_encode($ret);
      exit;
   }

   public function action_work_experience_add() {
      $employer_name = Arr::get($_REQUEST, 'employer_name');
      $current_status = Arr::get($_REQUEST, 'current_status');
      $job_title = Arr::get($_REQUEST, 'job_title');
      $employed_from = Arr::get($_REQUEST, 'employed_from');
      $employed_to = Arr::get($_REQUEST, 'employed_to');
      $city = Arr::get($_REQUEST, 'city');
      $country = Arr::get($_REQUEST, 'country');
      $job_description = Arr::get($_REQUEST, 'job_description');


      try {
         $work_experience = ORM::factory("user_experience");
         $work_experience->user_id = $this->orm_selectedUser->id;
         $work_experience->employer_name = $employer_name;
         $work_experience->current_status = $current_status;
         $work_experience->job_title = $job_title;
         $work_experience->employed_from = $employed_from;
         $work_experience->employed_to = $employed_to;
         $work_experience->city = $city;
         $work_experience->country_id = $country;
         $work_experience->job_description = $job_description;
         $cid = $work_experience->save()->id;

         $hist = $work_experience->user->history->last()->column("work_experience")->find()->as_array();
         $user = Arr::get($hist, "moder", Arr::get($hist, "editor"));
         $resp = array(
            "value"			 => $work_experience->city,
            "displayValue"	 => $work_experience->city." ".$work_experience->city,
            "verified"		 => array(
               "date"	 => date("M. d, Y", Arr::get($hist, "verified", Arr::get($hist, "date", time()))),
               "user"	 => Arr::get($user, "username", __("Unknown"))
            )
         );
         $error = NULL;
      } catch (Exception $e) {
         $error = $e->getMessage();
         $resp = array();
         $cid=0;
      }

      $ret = Arr::merge(array("error" => $error, "name" => "work_experience_".$cid, "value" => $job_title), $resp);
      echo json_encode($ret);
      exit;
   }

   public function action_work_experience_remove() {
      $name = Arr::get($_REQUEST, 'name');
      $cid = sscanf($name, "work_experience[%d]");
      $cid = $cid[0];
      if(is_null($cid)) {
         $ret = array(
            "error"	 => __("Unknown work experience id"),
         );
      } else {
         $error = null;
         try {
            $work_experience = ORM::factory("user_experience", $cid);
            if(!$work_experience->loaded()) throw new Kohana_Exception("Requested contact not found");
            $work_experience->delete();

            $hist = $this->orm_selectedUser->details->history->last()->column("work_experience")->find()->as_array();
            $user = Arr::get($hist, "moder", Arr::get($hist, "editor"));
            $resp = array(
               "verified"		 => array(
                  "date"	 => date("M. d, Y", Arr::get($hist, "verified", Arr::get($hist, "date", time()))),
                  "user"	 => Arr::get($user, "username", __("Unknown"))
               )
            );
         } catch (Kohana_Exception $e) {
            $error = $e->getMessage();
            $resp = array();
         }

         $ret = Arr::merge(array("error" => $error, "name" => "work_experience_".$cid), $resp);
      }
      echo json_encode($ret);
      exit;
   }

	public function action_location() {
		$id = (int)Arr::get($_REQUEST, "lid");
		$loc = ORM::factory("user_location", $id);

		if($id and !$loc->loaded()) {
			$error =  __("Unknown location");
		} else {
			$error = null;
			try {
				$date_to = Arr::get($_REQUEST, "breakto");
				$loc->values(array("user_id"=>$this->orm_selectedUser->id)+$_POST, array("user_id", "type", "country", "city", "typeother", "date_from", "date_to"));
				$loc->save();
			} catch (Kohana_Exception $e) {
				$error = $e->getMessage();
			}

		}
		$ret = array("error" => $error);
		echo json_encode($ret);
		exit;
	}

	public function action_location_delete() {
		$id = (int)Arr::get($_REQUEST, "lid");
		$loc = ORM::factory("user_location", $id);

		if(!$loc->loaded()) {
			$error =  __("Unknown location");
		} else {
			$error = null;
			try {
				$loc->delete();
			} catch (Kohana_Exception $e) {
				$error = $e->getMessage();
			}

		}
		$ret = array("error" => $error);
		echo json_encode($ret);
		exit;
	}

	protected function do_birthday () {
		$value = self::dateFormat($this->orm_selectedUser->details->birthday);
		$displayValue = $value.'<span class="age">'.self::age($this->orm_selectedUser->details->birthday).'</span>';
		return $this->buildReply("birthday", $displayValue, $value);
	}

	protected function do_sex() {
      static $sex = array("m"=>"Male", "f"=>"Female");
		$displayValue = __(Arr::get($sex, $this->orm_selectedUser->details->sex, $sex["m"]));
		return $this->buildReply("sex", $displayValue);
	}

	protected function do_citizenship () {
      $displayValue = ORM::factory("country", $this->orm_selectedUser->details->citizenship)->name;
		return $this->buildReply("citizenship", $displayValue);
	}

	protected function do_residence () {
      $displayValue = ORM::factory("country", $this->orm_selectedUser->details->residence)->name;
		return $this->buildReply("residence", $displayValue);
	}

	protected function buildReply($name, $displayValue, $value=null) {
      $hist = $this->orm_selectedUser->history->last()->column("details")->field($name)->find()->as_array();
		$user = Arr::get($hist, "moder", Arr::get($hist, "editor"));
		return array(
			"value"			 => $value?:$this->orm_selectedUser->details->$name,
			"displayValue"	 => $displayValue,
			"verified"		 => array(
				"date"	 => date("M. d, Y", Arr::get($hist, "verified", Arr::get($hist, "date", time()))),
				"user"	 => Arr::get($user, "username", __("Unknown"))
			)
		);
	}
}
