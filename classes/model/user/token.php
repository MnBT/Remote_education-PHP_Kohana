<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_token extends ORM {
   protected $_table_name = "user_tokens";

   // Relationships
   protected $_belongs_to = array(
      'user'	 => array(
         "model"         => "user",
         "foreign_key"   => "user_id"
      )
   );
   /**
    * Handles garbage collection and deleting of expired objects.
    * @return  void
    */
   public function __construct($id = NULL)
   {
      parent::__construct($id);

      if (mt_rand(1, 100) === 1)
      {
         // Do garbage collection
         $this->delete_expired();
      }

      if ($this->created + $this->lifetime < time() AND $this->_loaded)
      {
         // This object has expired
         $this->delete();
      }
   }

   /**
    * Deletes all expired tokens.
    *
    * @return  ORM
    */
   public function delete_expired()
   {
      // Delete all expired tokens
      $expired = ORM::factory('user_token')
         ->where(DB::expr('(created + lifetime)'), '<', time())
         ->find_all();
      $expired->delete();

      return $this;
   }

   public function create(Validation $validation = NULL)
   {
      $this->token = $this->create_token();
      $this->lifetime = Kohana_Date::DAY;
      $this->created = time();

      return parent::create($validation);
   }

   protected function create_token()
   {
      do
      {
         $token = sha1(uniqid(Text::random('alnum', 32), TRUE));
      }
      while (ORM::factory('user_token', array('token' => $token))->loaded());

      return $token;
   }

}
