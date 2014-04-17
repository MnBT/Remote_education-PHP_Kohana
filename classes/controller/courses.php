<?php

defined('SYSPATH') or die('Restricted access!');

/**
 * Description of courses
 *
 * @author n.alex
 */
class Controller_Courses extends Controller_Role_admin {
  protected $items_per_page = 20;

  /**
   * отображение списка курсов
   */
  public function action_index() {
    $count = ORM::factory('course')->count_all();

    $pagination = Pagination::factory( array(
      'current_page' => array('source' => 'route', 'key' => 'page'),
      'total_items' => $count,
      'items_per_page' => $this->items_per_page,
      'auto_hide' => true,
      'view' => 'pagination/basic',
      'first_page_in_url' => true
      ))
      ->route_params( array(
        'controller' => Request::current()->controller(),
        'action' => Request::current()->action(),
      ));

    $courses = ORM::factory('course')->limit($pagination->items_per_page)->offset($pagination->offset)->find_all();
    $this->template->content = View::factory('courses/index', array('model' => $courses, 'pagination' => $pagination));
  }

  /**
   * просмотр курса
   * возвращает информацию о курсе при GET-запросе
   */
  public function action_view() {

  }

  /**
   * создание нового курса
   * если GET-запрос, то возвращает форму добавления
   * если POST-запрос, то создает новую запись
   */
  public function action_create() {
    switch ($this->request->method()) {
      case Request::GET:
        $this->template->content = View::factory('courses/create', array('model' => null, 'errors' => null));
        break;
      case Request::POST:
        $courses = ORM::factory('course')
          ->values($_POST, array('code', 'name', 'description'));
        try {
          $courses->create();

          $this->request->redirect('courses/index');
        } catch (ORM_Validation_Exception $e) {
          $errors = $e->errors('models');
          $this->template->content = View::factory('courses/create', array('model' => $courses, 'errors' => $errors));
        }
        break;
    }
  }

  /**
   * редактирование курса
   * если GET-запрос, то возвращает форму редактирования
   * если POST-запрос, то сохраняет запись
   */
  public function action_update() {
    switch ($this->request->method()) {
      case Request::GET:
        $id = $this->request->param('id');
        if (!isset($id))
          $this->request->redirect('courses/index');

        $course = ORM::factory('course')->where('id', '=', $id)->find();

        $this->template->content = View::factory('courses/update', array('model' => $course, 'errors' => null));
        break;
      case Request::POST:
        $id = $this->request->param('id');

        $course = ORM::factory('course', $id);

        if (!$course->loaded())
          $this->request->redirect('courses/index');
        else
          $course->values($_POST, array('code', 'name', 'description'));

        try {
          $course->update();

          $this->request->redirect('courses/index');
        } catch (ORM_Validation_Exception $e) {
          $errors = $e->errors('models');
          $this->template->content = View::factory('courses/update', array('model' => $course, 'errors' => $errors));
        }
        break;
    }
  }

  /**
   * удаление курса
   * если GET-запрос, то возвращает форму подтверждения
   * удаляет запись при POST-запросе
   */
  public function action_delete() {
    switch ($this->request->method()) {
      case Request::GET:
        //  TODO: create view with confirmation.
        break;
      case Request::POST:
        $id = $this->request->param('id');

        $course = ORM::factory('course', $id);

        if ($course->loaded()) {
          $course->delete();
          echo '{"result": "success"}';
          die;
        } else {
          echo json_encode(array('result' => 'fail', 'message' => 'Model not loaded.'));
          die;
        }
        break;
    }
  }

}
