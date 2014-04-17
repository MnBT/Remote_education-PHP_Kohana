<?php defined('SYSPATH') or die('Restricted access!');
/**
 *
 * Контроллер
 * @author prophet
 *
 */

class Controller_Profile_photos extends Controller_Students {

	public $template = 'main';

	public function action_index() {
		$error = '';
		if ($this->request->method() == 'POST' && isset($_POST['upload'])) {

			$image = Validation::factory($_FILES);
			$image->rule('image', 'Upload::not_empty')
			->rule('image', 'Upload::type', array(':value', array('jpg', 'png', 'gif')));
			$pic = getimagesize($_FILES['image']['tmp_name']);
			if ($image->check() and $pic[0] > 225 and $pic[1] > 225) {
				$filepath = Upload::save($_FILES['image'], $this->request->post('image'), DOCROOT."upload", 0666);
				$photo = $this->orm_selectedUser->photos->addPhoto($this->orm_selectedUser->id, $filepath, $this->request->post('description'));
				//Request::current()->redirect("profile/photos");
			} else {
				$debug = Debug::vars($image->errors('profile/photos'));
				if (strpos($debug, "Upload::not_empty")) {
					$error = "Upload field can't be empty.";
				} elseif (strpos($debug, "Upload::type")) {
					$error = "Accepted only jpg, gif and png format.";
				} elseif ($pic[0] < 255) {
					$error = "Accepted only width pic more then 225px.";
				} elseif ($pic[1] < 255) {
					$error = "Accepted only heigth pic more then 225px.";
				}
			}
		}

		$images = $this->orm_selectedUser->photos->enabled()->find_all();
      $this->auto_render = FALSE;
		$this->template->content = View::factory('students/profile/photos')
		->set('error', $error)
		->set('photos', $images);
      $this->response->body(json_encode(array("success" => TRUE, "content" => $this->template->content->render())));
	}
	
	public function action_remove() {
		$id = $this->request->param('id');
		try {
			$this->orm_selectedUser->photos->get($id)->delete();
		} catch(Exception $e) {
			Message::set(Message::ERROR, $e->getMessage());
		}
      $this->auto_render = FALSE;
		$this->action_index();
	}

	public function action_rotate() {
		if($this->request->is_ajax()) {
			$this->auto_render = false;
			try {
				$this->orm_selectedUser->photos->get($_POST['img_id'])->rotate($_POST['direction'] == 'clockwise');
				echo "ok";
			} catch(Exception $e) {
				echo "fail";
			}
		} else {
			Request::current()->redirect("students");
		}
	}

	public function action_featured() {
      $this->auto_render = false;
      $pic_id = $this->request->param('id');
		if ($this->request->method() == 'POST') {
			//get crop data from view
			$x = (int) $this->request->post('x');
			$y = (int) $this->request->post('y');
			$w = (int) $this->request->post('w');
			$h = (int) $this->request->post('h');
			try {
				$this->orm_selectedUser->photos->setFeatured($pic_id, $x, $y, $w, $h);
			} catch(Exception $e) {
				Message::set(Message::ERROR, $e->getMessage());
			}
			Request::current()->redirect('students');
		}

      $photo = $this->orm_selectedUser->photos->get($pic_id);

      $this->template->content = View::factory('students/profile/photos/featured')
         ->set("height", $photo->getImage(Model_User_photo::SIZE_FULL)->height)
         ->set("imageurl", $photo->getURL(Model_User_photo::SIZE_FULL))
         ->set("photo", $photo->as_array());
      $this->response->body(json_encode(array("success" => TRUE, "content" => $this->template->content->render())));

	}
}
