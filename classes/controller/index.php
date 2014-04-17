<?php defined('SYSPATH') or die('Restricted access!');
/**
 *
 * Контроллер
 * @author prophet
 *
 */

class Controller_Index extends Controller_Role_admin {

	public $template = 'main';

	public function action_index() {
      Request::current()->redirect('students');
	}
  
	public function action_error() {
    $this->template->content = View::factory('_errors', array(
        'errors' => Message::get(), 
        'htmlOptions' => array('class' => 'errors') ) 
    );
	}
}
