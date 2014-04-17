<?php

defined('SYSPATH') or die('Restricted access!');

/**
 * Description of books
 *
 * @author n.alex
 */
class Controller_Books extends Controller_Role_admin {

  protected $items_per_page = 20;
  protected $uploads_dir;

  public function __construct(Request $request, Response $response) {
    parent::__construct($request, $response);

    $this->uploads_dir = UPLOAD_DIR . DIRECTORY_SEPARATOR . 'books' . DIRECTORY_SEPARATOR;
  }

  /**
   * отображение списка книг
   */
  public function action_index() {
    $count = ORM::factory('book')->count_all();

    $pagination = Pagination::factory(array(
        'current_page' => array('source' => 'route', 'key' => 'page'),
        'total_items' => $count,
        'items_per_page' => $this->items_per_page,
        'auto_hide' => true,
        'view' => 'pagination/basic',
        'first_page_in_url' => true
      ))
      ->route_params(array(
      'controller' => Request::current()->controller(),
      'action' => Request::current()->action(),
      ));

    $books = ORM::factory('book')->limit($pagination->items_per_page)->offset($pagination->offset)->find_all();

    $this->template->content = View::factory('books/index', array('model' => $books, 'pagination' => $pagination));
  }
  
  /**
   * отображение списка книг, если указан код курса, то показываются только книги этого курса
   */
  public function action_list() {
    $course_id = $this->request->param('id');
    
    if ($course_id)
      $count = ORM::factory('book')->where('course_id', '=', $course_id)->count_all();
    else
      $count = ORM::factory('book')->count_all();

    $pagination = Pagination::factory(array(
        'current_page' => array('source' => 'route', 'key' => 'page'),
        'total_items' => $count,
        'items_per_page' => $this->items_per_page,
        'auto_hide' => true,
        'view' => 'pagination/basic',
        'first_page_in_url' => true
      ))
      ->route_params(array(
      'controller' => Request::current()->controller(),
      'action' => Request::current()->action(),
      ));

    if ($course_id)
      $books = ORM::factory('book')
        ->where('course_id', '=', $course_id)
        ->limit($pagination->items_per_page)
        ->offset($pagination->offset)->find_all();
    else
      $books = ORM::factory('book')->limit($pagination->items_per_page)->offset($pagination->offset)->find_all();
    
    if ($this->request->is_ajax()) {
      echo View::factory('books/list', array('model' => $books, 'pagination' => $pagination))->render();
      die;
    }
      
    $this->template->content = View::factory('books/list', array('model' => $books, 'pagination' => $pagination));
  }

  /**
   * просмотр книги
   * возвращает информацию о книге при GET-запросе
   */
  public function action_view() {

  }

  /**
   * создание новой книги
   * если GET-запрос, то возвращает форму добавления
   * если POST-запрос, то создает новую запись
   */
  public function action_create() {
    switch ($this->request->method()) {
      case Request::GET:
        $this->template->content = View::factory('books/create', array('model' => null, 'errors' => null));
        break;
      case Request::POST:
        // проверка загруженного файла
        $validation = Validation::factory($_FILES)
          ->label('file', 'Book')
          ->rules('file', array(
          array('Upload::not_empty'),
          array('Upload::valid'),
          ));
        
        if ($validation->check()) {
          $path = Upload::save($_FILES['file'], $_FILES['file']['name'], $this->uploads_dir);
          if ($path) {
            try {
              $filename = basename($path);
              $code = substr($filename, 0, strrpos($filename, '.'));
              
              $parts = explode('-', $code);
              
//              dBug::prn($parts);

              $course = Model::factory('course')->where('code', 'like', '%'.$parts[0])->find();
              $lang = Model::factory('book_lang')->where('code', '=', $parts[3])->find();
              
//              dBug::prn($course);
              
              preg_match('/[A-Za-z]*/', $parts[1], $matches);
              $type = Model::factory('book_type')->where('name', '=', $matches[0])->find();
              
              $number = substr($parts[1], strlen($matches[0]));
              
              $version = substr($parts[4], 1);
              
//              dBug::prn($type);
                           
              $book = ORM::factory('book');

              $book->course_id = $course->id;
              $book->type_id = $type->id;
              $book->lang_id = $lang->id;
              $book->code = $code;
              $book->number = $number;  
              $book->order = $parts[2];
              $book->version = $version;
              $book->days = $parts[5];
              $book->progress = Arr::get($_POST, 'progress');
              $book->added = date('Y-m-d');
              $inactive = ceil(($course->ended - $course->started) / 86400) / 100 * $book->days;
              $book->available = date('Y-m-d', strtotime($book->added . ' + ' . $inactive . ' days'));
              //  для типов книг Bm и Bex срок доступности равен количеству дней, указанных в поле Days
              if ($book->type_id == 3 or $book->type_id == 3)
                $book->expired = date('Y-m-d', strtotime($book->added . ' + ' . $book->days . ' days'));
              $book->file = basename($path);
              
//              dBug::prn($book); die;
              
              $book->save();

              $this->request->redirect('books/index');
            } catch (ORM_Validation_Exception $e) {
              unlink($path);

              $errors = $e->errors('models');

              $this->template->content = View::factory('books/create', array('model' => $book, 'errors' => $errors));
            }
          }
        } else {
          $errors = $validation->errors('models/book');

          $this->template->content = View::factory('books/create', array('model' => $book, 'errors' => $errors));
        }
        break;
    }
  }

  /**
   * редактирование книги
   * если GET-запрос, то возвращает форму редактирования
   * если POST-запрос, то сохраняет запись
   */
  public function action_update() {
    switch ($this->request->method()) {
      case Request::GET:
        $id = $this->request->param('id');
        if (!isset($id))
          $this->request->redirect('books/index');

        $book = ORM::factory('book')->where('id', '=', $id)->find();

        $this->template->content = View::factory('books/update', array('model' => $book, 'errors' => null));
        break;
      case Request::POST:
        $id = Arr::get($_POST, 'id');

        $book = ORM::factory('book', $id);

        if (!$book->loaded())
          $this->request->redirect('books/index');
        else
          $book->values($_POST, array('code', 'type_id', 'lang_id', 'name', 'description', 'time_limit'));

        try {
          $book->update();

          $this->request->redirect('books/index');
        } catch (ORM_Validation_Exception $e) {
          $errors = $e->errors('models');
          $this->template->content = View::factory('books/update', array('model' => $book, 'errors' => $errors));
        }
        break;
    }
  }

  /**
   * удаление книги
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

        $book = ORM::factory('book', $id);

        if ($book->loaded()) {
          $filename = $this->uploads_dir. $book->file;
          if (file_exists($filename))
            unlink($filename);
          $book->delete();
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
