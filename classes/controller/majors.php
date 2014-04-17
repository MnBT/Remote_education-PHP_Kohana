<?php

defined('SYSPATH') or die('Restricted access!');

/**
 * Description of majors
 *
 * @author n.alex
 */
class Controller_Majors extends Controller_Role_admin {
  protected $items_per_page = 10;
  protected $uploads_url;
  protected $uploads_dir;

  public function __construct(Request $request, Response $response) {
    parent::__construct($request, $response);

    $this->uploads_url = UPLOAD_URL . '/majors/';
    $this->uploads_dir = UPLOAD_DIR . DIRECTORY_SEPARATOR . 'majors' . DIRECTORY_SEPARATOR;
  }

  /**
   * отображение списка
   */
  public function action_index() {
    $count = ORM::factory('major')->count_all();

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

    $majors = ORM::factory('major')->limit($pagination->items_per_page)->offset($pagination->offset)->find_all();

    $this->template->content = View::factory('majors/index', array(
      'model' => $majors, 
      'uploads_url' => $this->uploads_url, 
      'pagination' => $pagination)
    );
  }

  /**
   * просмотр
   * возвращает информацию о major'е при GET-запросе
   */
  public function action_view() {

  }

  /**
   * создание нового major
   * если GET-запрос, то возвращает форму добавления
   * если POST-запрос, то создает новую запись
   */
  public function action_create() {
    switch ($this->request->method()) {
      case Request::GET:
        $this->template->content = View::factory('majors/create', array('model' => null, 'errors' => null));
        break;
      case Request::POST:
        // проверка загруженного файла
        $validation = Validation::factory($_FILES)
          ->label('file', 'Major')
          ->rules('file', array(
              array('Upload::not_empty'),
              array('Upload::valid'),
            )
          );
        
        $majors = ORM::factory('major')->values($_POST, array('degree_id', 'name', 'duration'));
        
        if ($validation->check()) {
          $path = Upload::save($_FILES['file'], $_FILES['file']['name'], $this->uploads_dir);
          if ($path) {

            $filename = basename($path);
            $majors->set('filename', $filename);
            
            try {
              $majors->create();

              $this->request->redirect('majors/index');
            } catch (ORM_Validation_Exception $e) {
              $errors = $e->errors('models');
              $this->template->content = View::factory('majors/create', array('model' => $majors, 'errors' => $errors));
            }
            
          }
        } else {
          $errors = $validation->errors('models');

          $this->template->content = View::factory('majors/create', array('model' => $majors, 'errors' => $errors));
        }
        break;
    }
  }

  /**
   * редактирование major'а
   * если GET-запрос, то возвращает форму редактирования
   * если POST-запрос, то сохраняет запись
   */
  public function action_update() {
    switch ($this->request->method()) {
      case Request::GET:
        $id = $this->request->param('id');
        if (!isset($id))
          $this->request->redirect('majors/index');

        $major = ORM::factory('major')->where('id', '=', $id)->find();

        $this->template->content = View::factory('majors/update', array('model' => $major, 'errors' => null));
        break;
      case Request::POST:
        $id = Arr::get($_POST, 'id');
        $major = ORM::factory('major', $id);

        if (!$major->loaded())
          $this->request->redirect('majors/index');
        else
          $major->values($_POST, array('degree_id', 'name', 'duration'));

        try {
          $major->update();

          $this->request->redirect('majors/index');
        } catch (ORM_Validation_Exception $e) {
          $errors = $e->errors('models');
          $this->template->content = View::factory('majors/update', array('model' => $major, 'errors' => $errors));
        }
        break;
    }
  }

  /**
   * удаление majors'а
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

        $major = ORM::factory('major', $id);

        if ($major->loaded()) {
          $major->delete();
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
