<?php
abstract class Controller_Role_admin extends Controller_Template
{
   public $template = 'main';

   protected $user = FALSE;
   protected $roles;
   protected $profile;
   protected $sid;
   protected $userpic;
   protected $courses;
   protected $date;
   protected $timeoffset;
   protected static $date_format = "M. d, Y";

   function before()
   {
      if (!Auth::instance()->logged_in('login')) {
         Request::initial()->redirect('auth/login');
      }
      ACL::check_access();

      $this->user = Auth::instance()->get_user();
      //only user with specific roles is allowed
      $this->roles = $this->user->roles->find_all()->as_array('id', 'name'); // Get user's roles
      $isAllowedToLogin = FALSE;
      foreach ($this->roles as $role) {
         if (in_array($role, array("admin", "curator", "manager", "moderator", "accountant"))) {
            $isAllowedToLogin = TRUE;
            break;
         }
      }
      if (!$isAllowedToLogin) {
         Message::set(Message::ERROR, __("Permission denied"));
         Request::current()->redirect("auth/login");
      }

      try {
         $this->userpic = $this->user->photos->featured()->find()->getURL(TRUE);
      } catch (Exception $e) {
         $this->userpic = "media/img/noimage.png";
      }

      $this->date = new DateTime();
      $this->timeoffset = $this->date->getOffset();
      $this->date->setTimezone(new DateTimeZone(($this->user->details->tz) ? $this->user->details->tz : "Europe/Kiev"));
      $this->timeoffset -= $this->date->getOffset();
      $this->timeoffset = array($this->timeoffset, (int)floor($this->timeoffset / 3600), (int)floor($this->timeoffset % 3600 / 60));

      I18n::lang($this->user->details->study_language->name);

      $controller = $this->request->controller();
      View::bind_global('controller', $controller);

      parent::before();

      Session::instance()->set("current_user", $this->user);
   }

   function after()
   {
      $this->template->userpic = $this->userpic;
      $this->template->first_name = $this->user->details->first_name;
      $this->template->last_name = $this->user->details->last_name;
      $this->template->language = $this->user->details->study_language->name;
      $this->template->timeoffset = $this->timeoffset;
      $this->template->sid = $this->user->username;
      $this->template->status = $this->user->details->application_status->name;
      parent::after();
   }

   public static function age($birthday)
   {
      list($year, $month, $day) = explode("-", date("Y-m-d", $birthday));
      $year_diff = date("Y") - $year;
      $month_diff = date("m") - $month;
      $day_diff = date("d") - $day;
      if ($month_diff < 0) $year_diff--;
      elseif ($month_diff == 0 and $day_diff < 0) $year_diff--;
      return $year_diff;
   }

   public function dateFormat($timestamp, $format = NULL)
   {
     if (empty($timestamp))
       return '';
     
     if (is_numeric($timestamp)) 
       $timestamp = date(self::$date_format, $timestamp);
     
      $dd = new DateTime($timestamp);
      $ret = $dd->format(($format === NULL) ? self::$date_format : $format);
      return $ret;
   }

   public function currentDate($unixtimestamp = NULL)
   {
      $dd = $this->date->getTimestamp();
      if (!is_null($unixtimestamp)) $this->date->setTimestamp($unixtimestamp);
      $ret = $this->date->format(self::$date_format);
      $this->date->setTimestamp($dd);
      return $ret;
   }
}