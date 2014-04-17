<?php defined('SYSPATH') OR die('No direct access allowed.');

defined('SYSPATH') OR die('No direct access allowed.');

class Model_User extends Model_Auth_User
{

   protected $_table_name = "users";

   // Relationships
   protected $_has_many = array(
      "user_tokens" => array(
         "model"       => "user_token",
         "foreign_key" => "user_id"
      ),
      "roles"       => array(
         "model"       => "role",
         "foreign_key" => "user_id",
         "through"     => "roles_users",
         "far_key"     => "role_id"
      ),
      'courses'     => array(
         "model"       => "user_courses",
         "foreign_key" => "user_id",
      ),
      'photos'      => array(
         "model"       => "user_photo",
         "foreign_key" => "user_id"
      ),
      'contacts'    => array(
         "model"       => "user_contact",
         "foreign_key" => "user_id",
      ),
      "history"     => array(
         "model"       => "user_history",
         "foreign_key" => "user_id"
      ),
      'address'     => array(
         "model"       => "user_address",
         "foreign_key" => "user_id",
      ),
      'locations'   => array(
         "model"       => "user_location",
         "foreign_key" => "user_id",
      ),
      'books'       => array(
         "model"       => "user_books",
         "foreign_key" => "user_id",
      ),
      'testresults' => array(
         "model"       => "user_testresults",
         "foreign_key" => "user_id",
      ),
      'operations' => array(
         "model" => "user_operations",
         "foreign_key" => "user_id",
      ),
      'experience' => array(
         "model" => "user_experience",
         "foreign_key" => "user_id",
      ),
      'emergency' => array(
         "model" => "user_emergency",
         "foreign_key" => "user_id",
      ),
      'education' => array(
         "model" => "user_education",
         "foreign_key" => "user_id",
      ),
      'user_majors' => array(
         "model" => "user_majors",
         "foreign_key" => "user_id"
      ),
      'majors'  => array(
         "model"       => "major",
         "through" => "user_majors",
      ),
      'tokens'  => array(
         "model"       => "user_token",
         "foreign_key" => "user_id",
      ),
   );

   protected $_has_one = array(
      'speciality' => array(
         "model"       => "speciality",
         "foreign_key" => "id",
         "through"     => "user_details",
         "far_key"     => "speciality_id"
      ),
      'details'    => array(
         "model"       => "user_detail",
         "foreign_key" => "id"
      )
   );

   public function __set($key, $value)
   {
      if ($key === 'password') {
         // Use Auth to hash the password
         $value = Auth::instance()->hash_password($value);
      }

      parent::__set($key, $value);
   }

   /**
    * Validates and optionally saves a new user record from an array.
    *
    * @param  array    values to check
    * @param  boolean  save the record when validation succeeds
    *
    * @return boolean
    */
   public function validate(array &$array, $save = FALSE)
   {
      $array = Validation::factory($array)
         ->rule('email', 'not_empty')
         ->rule('email', 'min_length', array(":value", 4))
         ->rule('email', 'max_length', array(":value", 127))
         ->rule('email', 'email')
         ->rule('email', array($this, 'email_available'))
         ->rule('username', 'not_empty')
         ->rule('username', 'min_length', array(":value", 4))
         ->rule('username', 'max_length', array(":value", 32))
         ->rule('username', 'regex', array('/^[a-z0-9\_\.\-]+$/i'))
         ->rule('username', array($this, 'username_available'))
         ->rule('password', 'not_empty')
         ->rule('password', 'min_length', array(":value", 4))
         ->rule('password', 'max_length', array(":value", 42));
//		->rule('password_confirm', 'matches', array("password", ":value"));

      if ($array->check()) return TRUE;
      foreach ($array->errors("user") as $error) {
         Message::set(Message::ERROR, $error);
      }
      return FALSE;
   }

   /**
    * Validates login information from an array, and optionally redirects
    * after a successful login.
    *
    * @param  array    values to check
    * @param  string   URI or URL to redirect to
    *
    * @return boolean
    */
   public function login(array &$array, $redirect = FALSE)
   {
      $array = Validation::factory($array)
         ->rule('username', 'not_empty')
         ->rule('username', 'min_length', array(":value", 4))
         ->rule('username', 'max_length', array(":value", 127))
         ->rule('password', 'not_empty')
         ->rule('password', 'min_length', array(":value", 5))
         ->rule('password', 'max_length', array(":value", 42));

      // Login starts out invalid
      $status = FALSE;

      if ($array->check()) {
         // Attempt to load the user
         $this->find($array['username']);
         if ($this->loaded) {
            if (!in_array("login", $this->roles->find_all()->as_array('id', 'name'))) {
               $array->error('username', 'User login restricted');
            } elseif (Auth::instance()->login($this, $array['password'])) {
               if (is_string($redirect)) {
                  // Redirect after a successful login
                  url::redirect($redirect);
               }
               // Login is successful
               return TRUE;
            }
         } else {
            $array->error('username', 'Invalid username');
         }
      }
      foreach ($array->errors("user") as $error) {
         Message::set(Message::ERROR, $error);
      }
      return FALSE;
   }

   /**
    * Validates an array for a matching password and password_confirm field.
    *
    * @param  array    values to check
    * @param  string   save the user if
    *
    * @return boolean
    */
   public function change_password(array & $array, $save = FALSE)
   {
      $array = Validation::factory($array)
         ->pre_filter('trim')
         ->add_rules('password', 'required', 'length[5,127]')
         ->add_rules('password_confirm', 'matches[password]');

      if ($status = $array->validate()) {
         // Change the password
         $this->password = $array['password'];

         if ($save !== FALSE AND $status = $this->save()) {
            if (is_string($save)) {
               // Redirect to the success page
               url::redirect($save);
            }
         }
      }

      return $status;
   }

   /**
    * Tests if a username exists in the database. This can be used as a
    * Valdidation rule.
    *
    * @param   mixed    id to check
    *
    * @return  boolean
    *
    */
   public function username_exists($id)
   {
      return $this->unique_key_exists($id);
   }

   /**
    * Does the reverse of unique_key_exists() by returning TRUE if user id is available
    * Validation rule.
    *
    * @param    mixed    id to check
    *
    * @return   boolean
    */
   public function username_available($username)
   {
      return !$this->unique_key_exists($username);
   }

   /**
    * Does the reverse of unique_key_exists() by returning TRUE if email is available
    * Validation Rule
    *
    * @param string $email
    *
    * @return void
    */
   public function email_available($email)
   {
      return !$this->unique_key_exists($email);
   }

//TODO move all db process methods from students controller -> here
   protected function get_students_all_count($p_nPerPage = 1, $p_nOffset = 0, $p_bAsOrmObjects = FALSE, $p_arrFilters)
   {
      if (!$this->loaded()) throw new Kohana_Exception(__("No Users loaded"));
      $orm_users = ORM::factory("user");
      //if this is an initial request we set first row as selected(store this in session)
      $orm_users = $orm_users->where($this->strFilterColumn, "LIKE", "%" . $this->strFilterValue . "%")->order_by($this->strSortedColumn, $this->strSortedMethod)->limit($p_nPerPage)->offset($p_nOffset)->find_all();

      $arrReturn = array();
      foreach ($orm_users as $orm_user) {
         /*$orm_user->details;*/
         /*$orm_user->address;*/
         /*$orm_user->locations;*/
         $arrReturn[$orm_user->id] = ($p_bAsOrmObjects) ? $orm_user : $orm_user->as_array();
      }
      return $arrReturn;
   }

   protected function get_students_all($p_nPerPage = 1, $p_nOffset = 0, $p_bAsOrmObjects = FALSE)
   {
      $orm_users = ORM::factory("user");
      //if this is an initial request we set first row as selected(store this in session)
      $orm_users = $orm_users->where($this->strFilterColumn, "LIKE", "%" . $this->strFilterValue . "%")->order_by($this->strSortedColumn, $this->strSortedMethod)->limit($p_nPerPage)->offset($p_nOffset)->find_all();

      $arrReturn = array();
      foreach ($orm_users as $orm_user) {
         /*$orm_user->details;*/
         /*$orm_user->address;*/
         /*$orm_user->locations;*/
         $arrReturn[$orm_user->id] = ($p_bAsOrmObjects) ? $orm_user : $orm_user->as_array();
      }
      return $arrReturn;
   }

   protected function get_students_profiles($p_nPerPage = 1, $p_nOffset = 0, $p_bAsOrmObjects = FALSE)
   {
      $orm_users = ORM::factory("user");
      //if this is an initial request we set first row as selected(store this in session)
      $orm_users = $orm_users->where($this->strFilterColumn, "LIKE", "%" . $this->strFilterValue . "%")->order_by($this->strSortedColumn, $this->strSortedMethod)->limit($p_nPerPage)->offset($p_nOffset)->find_all();

      $arrReturn = array();
      foreach ($orm_users as $orm_user) {
         /*$orm_user->details;*/
         /*$orm_user->address;*/
         /*$orm_user->locations;*/
         $arrReturn[$orm_user->id] = ($p_bAsOrmObjects) ? $orm_user : $orm_user->as_array();
      }
      return $arrReturn;
   }

   protected function get_students_progress($p_nPerPage = 1, $p_nOffset = 0, $p_bAsOrmObjects = FALSE)
   {
      $orm_users = ORM::factory("user");
      //if this is an initial request we set first row as selected(store this in session)
      $orm_users = $orm_users->where($this->strFilterColumn, "LIKE", "%" . $this->strFilterValue . "%")->order_by($this->strSortedColumn, $this->strSortedMethod)->limit($p_nPerPage)->offset($p_nOffset)->find_all();

      $arrReturn = array();
      foreach ($orm_users as $orm_user) {
         /*$orm_user->details;*/
         /*$orm_user->address;*/
         /*$orm_user->locations;*/
         $arrReturn[$orm_user->id] = ($p_bAsOrmObjects) ? $orm_user : $orm_user->as_array();
      }
      return $arrReturn;
   }

   /**
    * method to get user's current course
    * @return object of model('user_courses')
    */
   public function get_current_course()
   {
      /*$user_courses = ORM::factory("user_courses");*/
      return $this->courses->where('status', 'NOT IN', array('noactive'))->and_where("user_id", "=", $this->id);
      /*->where(DB::expr("UNIX_TIMESTAMP()"), "BETWEEN", array(DB::expr("date_study_start"), DB::expr("date_study_end")))*/

   }

   /**
    * method to get user's current course test result
    * @return object of model('user_testresults')
    */
   public function get_current_course_testresults()
   {
      /*$testResults = ORM::factory("user_testresults");*/
      $currentTest = self::get_current_course()->course->test->find();

      return $this->testresults->where('test_id', '=', $currentTest->id);
      /*->where(DB::expr("UNIX_TIMESTAMP()"), "BETWEEN", array(DB::expr("date_study_start"), DB::expr("date_study_end")))*/

   }

   /**
    * method to get user's token
    * @return object of model('user_testresults')
    */
   public function get_token()
   {
      return $this->tokens->where(DB::expr('(created + lifetime)'), '>', time())->find()->token;
   }

}