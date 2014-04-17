<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_detail extends ORM {
	protected $_need_verify = array("birthday", "sex");
	protected $_no_history = array("id");
	protected $_table_name = "user_details";

	// Relationships
	protected $_belongs_to = array(
		'user'	 => array(
			"model"			 => "user",
			"foreign_key"	 => "id"
		)
	);

   protected $_has_one = array(
      'study_language' => array(
         "model"         => "language",
         "foreign_key"   => "id"
      ),
      'application_status' => array(
         "model"         => "application_status",
         "foreign_key"   => "id"
      ),
      'admission_type' => array(
         "model"         => "admission_type",
         "foreign_key"   => "id"
      ),
      'speciality' => array(
         "model"         => "speciality",
         "foreign_key"   => "id"
      )
   );

	protected $_has_many = array(
		"history"	 => array(
			"model"			 => "user_history",
			"foreign_key"	 => "user_id"
		)
	);

	public function update(Validation $validation = NULL) {
		$updated = $this->_changed;
      $logged_in_user = Session::instance()->get("current_user", $this->user);
		parent::update($validation);

		foreach($updated as $v) {
			if(in_array($v, $this->_no_history)) continue;
			$prev = $this->history->column("details")->field($v)->last()->find();
			if($prev->loaded()) {
				$prev->status &= Model_User_history::STATUS_APPROVED;
				$prev->update();
			}
			$this->history->values(array(
				"user_id" => $this->id,
				"column" => "details",
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
			$prev = $this->history->column("details")->field($v)->last()->find();
			if($prev->loaded()) {
				$prev->status &= Model_User_history::STATUS_APPROVED;
				$prev->update();
			}
			$this->history->values(array(
				"user_id" => $this->id,
				"column" => "details",
				"field" => $v,
				"value" => $this->$v,
				"date" => time(),
				"editor_id" => $logged_in_user->id,
				"status" => Model_User_history::STATUS_LAST
			))->save();
		}
		return $this;
	}

   public function filters()
   {
      return array(
         'birthday'    => array(
            array('trim'),
            array(function ($value) {
               if (!is_numeric($value)) return strtotime($value);
            })
         ),
         'reg_date'    => array(
            array('trim'),
            array(function ($value) {
               if (!is_numeric($value)) return strtotime($value);
            })
         ),
         'sex'         => array(
            array('trim'),
         ),
         'citizenship' => array(
            array('intval'),
         ),
         'residence'   => array(
            array('intval'),
         ),
         'study_language_id'   => array(
            array('intval'),
         ),
         'native_english'   => array(
            array('intval'),
         ),
         'native_language'   => array(
            array('trim'),
         ),
         'admission_type_id'   => array(
            array('intval'),
         ),
         'application_status'   => array(
            array('intval'),
         ),
         'currently_student'   => array(
            array('intval'),
         ),
      );
   }

	public function rules () {
		return array(
			'birthday' => array(
				array('not_empty'),
				array(
					function($value, Validation $object) { if(empty($value)) throw new ORM_Validation_Exception("birthday", $object, "Wrong date format"); },
					array(':value', ':validation')
				)
			),
			'sex' => array(
				array('not_empty'),
                array('regex', array(':value', '/^[mf]$/i')),
			),
			'citizenship'	 => array(
 				array('not_empty'),
				array(
                	function($value, Validation $object){ if(!empty($value) and !ORM::factory("country",$value)->loaded()) throw new ORM_Validation_Exception("citizenship", $object, "Wrong country code"); },
                	array(':value', ':validation')
                )
			),
			'residence'	 => array(
				array(
					function($value, Validation $object){ if(!empty($value) and !ORM::factory("country",$value)->loaded()) throw new ORM_Validation_Exception("residence", $object, "Wrong country code"); },
					array(':value', ':validation')
				)
			)
		);
	}

//         Admit Date - поле Admission Date в профиле, это дата зачисления студента и
//         отправки ему логина и пароля. В графике рисуется значек Apply. Apply нужно
//         заменить на Admitted. По сути Apply это дата заполнения студентом
//         заявления. Эту дату можно считать моментом передачи информации от
//         партнера (база neff-mba) в админку. Поэтому эта дата нам не важна.
//         Admit это подтверждение университетом принятия студента. Но это еще не
//         начало обучения.
//         Day - новое поле (еще нет в профиле, добавлять ли?), количество дней прошедших с
//         даты Admit Date. Необходимо для того чтобы выявить старые профили
//         которые не приступили к обучению.
//         Enroll Date - новое поле (добавим в профиль как Enrollment Date) Это дата
//         назначения занятий. Она соответствует на графике Start Date или дате
//         начала первого курса.
//         Day - количество дней после Enrollment Date, после начала обучения. Это число
//         пишется на графике под “сегодняшней датой”. По сути это количество
//         учебных дней. Перерывы в обучении останавливают счетчик.
   /**
    * Admission date - is the date, when login and password was sent to student.
    * This function returns number of days, passed since Admit Date till Start Date of first curse
    *
    * @param void
    * @return integer
    */
   public function  getAdmitDays() {
      $enrollDate = self::getEnrollDate();
      $curDate = new DateTime();
      return ceil(((($enrollDate !== false) ? $enrollDate :  $curDate->getTimestamp()) - $this->reg_date)/Kohana_Date::DAY);
   }

   public function getEnrollDate() {
      $last_course = $this->user->courses->order_by("date_study_start", "DESC")->find();
      if($last_course->loaded()){
         return $last_course->date_study_start;
      }
      return false;

   }

   public function getEnrollDays() {
      //if(!$this->user->loaded()) throw new Kohana_Exception(__("No User loaded"));
      $courses = $this->user->courses->find_all();
      if(count($courses) > 0) {
         $enrollTimeCount = 0;
         $curDate = new DateTime();
         foreach($courses as $course) {
            if(isset($course->date_study_end) && $course->date_study_end > 0 && $course->date_study_end < $curDate->getTimestamp()){
               $enrollTimeCount += $course->date_study_end - $course->date_study_start;
            } elseif ($curDate->getTimestamp() > $course->date_study_start){
               $enrollTimeCount += $curDate->getTimestamp() - $course->date_study_start;
            }
            if ($enrollTimeCount < 0) throw new Kohana_Exception($course->date_study_end);
         }
         return ceil($enrollTimeCount/Kohana_Date::DAY);
      }
      return false;
   }

}
