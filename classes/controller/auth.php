<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 * Специальный класс
 * @author prophet
 *
 *
 *
 */
class Controller_Auth extends Controller_Template {

	public $template = 'login';

	public function action_index()	{
		Request::current()->redirect("auth/login");
	}

	public function action_login() {
// 		Event::instance()->listen("DEMO_TEST EVENT!", function(Event_Instance $evnt, $prms){ $evnt->halt();	$evnt->set_data("me", "my"); });
// 		$evnt = Event::instance()->fire('DEMO_TEST EVENT!', array(123, 456));
// 		var_dump($evnt);
// 		if($evnt->is_halted()) die("halted!");
//		exit;
		if($this->request->method() == 'POST') {
			$user = ORM::factory('user');
			if($user->username_exists($_POST['username']))	{
				if (Auth::instance()->login($_POST['username'], $_POST['password'])) {
               $user = Auth::instance()->get_user();
               $old_tokens = $user->tokens->find();
               if ($old_tokens->loaded()) $old_tokens->delete();
               $user_token = ORM::factory('user_token');
               $user_token->user_id = $user->id;
               $user_token->user_agent = Request::$user_agent;
               $user_token->save();

					Request::current()->redirect('index');
				} else {
					Message::set(Message::ERROR, __('User ID or password is not correct'));
				}
			}
			else {
				Message::set(Message::ERROR, __('Student not found'));
			}
		}
		$this->template->content = View::factory('auth/login');
	}

	public function action_logout() {
		if (Auth::instance()->logout()) {
			Request::current()->redirect('auth/login');
		}
	}

	public function action_lostpw() {
		$textPass = text::random($type = 'alnum', $length = 6);
		$forgotPass = array('password'=>$textPass, 'password_confirm'=>$textPass);
		if (Request::current()->post()) {
			$post = Validation::factory($_POST);
			$post
				->rule('email', 'not_empty')
				->rule('email', 'email');

			if (!$post->check()) {
				// Validation failed, collect the errors
				foreach($post->errors('user') as $error) {
					Message::set(Message::ERROR, $error);
				}
			} else {
				$user = ORM::factory('user')->where('email', "=" ,$post['email'])->find();
				if ($user->username_exists($post['email']))  {
					$user->change_password($forgotPass, TRUE);
					if ($user->save()) {
						$to      = $post['email'];  // Address can also be array('to@example.com', 'Name')
						$from    = array('support@neff-mba.com', 'Neff Consulting');
						$subject = __('Password reminder');
						$message = 'Neff Consulting login new password '.$forgotPass['password'];
						email::send($to, $from, $subject, $message, TRUE);
						url::redirect(url::base(FALSE).'auth/login');
						$error = Message::set(Message::SUCCESS, __('На Ваш email пришло письмо с новым паролем'));
					}
					else Message::set(Message::ERROR, __('Ошибка создания пользователя'));
				} else Message::set(Message::ERROR, __('Пользователь с таким email не существует!'));
			}
		}
		$this->template->content = View::factory('auth/lostpw');
	}
}
