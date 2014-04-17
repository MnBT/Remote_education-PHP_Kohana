<?php

defined('SYSPATH') or die('Restricted access!');

/**
 * Description of curriculums
 *
 * @author n.alex
 */
class Controller_Curriculums extends Controller_Role_admin {

  public function before() {
    parent::before();

    if ($this->request->is_ajax())
      $this->auto_render = false;
  }

  protected function createCurriculumList($view) {
    $auto_render = $this->auto_render;
    $this->auto_render = true;

    $major_id = $this->request->param('major_id');

    if (empty($major_id)) {
      Message::set(Message::ERROR, __('Major id must be set.'));
      return;
    }

    $major = Model::factory('major')->where('id', '=', $major_id)->find()->as_array();
       
    $type_id = $this->request->param('type_id');
    
    if (empty($type_id)) {
      $types = Model::factory('course_type')->find_all();
      foreach ($types as $type) {
        $data[] = array(
          'type' => $type,
          'courses' => ORM::factory('curriculum')
            ->where('major_id', '=', $major_id)
            ->and_where('course_type_id', '=', $type->id)
            ->order_by('order')
            ->find_all()
        );
      }
    } else {
      $data['type'] = Model::factory('course_type', $type_id);
      $data['courses'] = ORM::factory('curriculum')
        ->where('major_id', '=', $major_id)
        ->and_where('course_type_id', '=', $type_id)
        ->order_by('order')
        ->find_all();
    }

    switch ($view) {
      case 'manage':
        $model = Model::factory('curriculum');
        $model->major_id = $major_id;

        $form = View::factory('curriculums/form', array('model' => $model, 'errors' => null));

        $content = View::factory('curriculums/manage', array('data' => $data, 'major' => $major, 'form' => $form));
        break;
      case 'list':
        $content = View::factory('curriculums/list', array('data' => $data));
        break;
    }

    $this->auto_render = $auto_render;

    return $content;
  }

  /**
   * отображение списка курсов
   */
  public function action_index() {
    $this->action_manage();
  }

  /**
   * создание нового curriculum'а
   * т.к. формально новый curriculum не создается, то сразу выполняется переход к управлению
   */
  public function action_create() {
    $this->action_manage();
  }

  /**
   * управление списком curriculum'а
   * если GET-запрос, то возвращает список курсов
   * если POST-запрос, то возвращает ошибку
   */
  public function action_list() {
    switch ($this->request->method()) {
      case Request::GET:
        $content = $this->createCurriculumList('list');

        if ($this->request->is_ajax())
          $this->response->body($content);
        else
          $this->template->content = $content;
        break;
      default:
        Message::set(Message::ERROR, __('Wrong request type. Try use GET'));
        break;
    }
  }

  /**
   * управление списком curriculum'а
   * если GET-запрос, то возвращает список курсов
   * если POST-запрос, то возвращает ошибку
   */
  public function action_manage() {
    switch ($this->request->method()) {
      case Request::GET:
        $content = $this->createCurriculumList('manage');

        if ($this->request->is_ajax())
          $this->response->body($content);
        else
          $this->template->content = $content;
        break;
      default:
        Message::set(Message::ERROR, __('Wrong request type. Try use GET'));
        break;
    }
  }

  /**
   * удаление curriculums'а (всех входящих курсов)
   * если GET-запрос, то возвращает форму подтверждения
   * удаляет записи при POST-запросе
   */
  public function action_delete() {
    switch ($this->request->method()) {
      case Request::GET:
        //  TODO: create view with confirmation.
        break;
      case Request::POST:
        $id = $this->request->param('major_id');

        $result = DB::delete('curriculums')->where('major_id', '=', $id)->execute();

        if (is_integer($result) && $result > 0) {
          $this->response->body(
            json_encode(array(
              'result' => 'success',
              'content' => $this->createCurriculumList('manage')
              )
            )
          );
        } else {
          $this->response->body(json_encode(array('result' => 'fail', 'message' => 'Model not loaded.')));
        }
        break;
    }
  }

  /**
   * добавление нового курса в curriculum
   * если GET-запрос, то возвращает форму добавления
   * если POST-запрос, то создает новую запись
   */
  public function action_add_course() {
    switch ($this->request->method()) {
      case Request::GET:
        $model = Model::factory('curriculum');
        $model->major_id = $this->request->param('major_id');
        $model->course_type_id = $this->request->param('type_id');
        $content = View::factory('curriculums/form', array('model' => $model, 'errors' => null));

        if ($this->request->is_ajax())
          $this->response->body($content);
        else
          $this->template->content = $content;
        break;
      case Request::POST:
        $curriculum = ORM::factory('curriculum')
          ->set('major_id', $this->request->param('major_id'))
          ->values($_POST, array('course_id', 'course_type_id', 'deactivation_type_id', 'order',
          'credits', 'semestr', 'hours', 'course_limit', 'control_type_id', 'control_period'));

        $course_start = Arr::get($_POST, 'course_start');
        if (!empty($course_start)) {
          $course_start = date('Y-m-d', strtotime($course_start));
          $limit = ARR::get($_POST, 'course_limit', 0);
          $course_end = date('Y-m-d', strtotime($course_start . ' + ' . $limit . ' days'));

          $control_start = date('Y-m-d', strtotime($course_end . ' + 1 day'));
          $period = ARR::get($_POST, 'control_period', 0);
          $control_end = date('Y-m-d', strtotime($control_start . ' + ' . $period . ' days'));

          $curriculum
            ->set('course_start', $course_start)
            ->set('course_end', $course_end)
            ->set('control_start', $control_start)
            ->set('control_end', $control_end);
        }
        try {
          $curriculum->check();
          try {
            $curriculum->create();

            if ($this->request->is_ajax()) {
              $curriculums = ORM::factory('curriculum')
                ->where('major_id', '=', $curriculum->major_id)
                ->and_where('course_type_id', '=', $curriculum->course_type_id)
                ->order_by('order')
                ->find_all();

              $this->response->body(
                json_encode(
                  array(
                    "result" => "success",
                    "content" => View::factory(
                      'curriculums/_manage', array('model' => $curriculums, 'pagination' => null)
                    )->render()
                  )
                )
              );
            }
            else
              $this->request->redirect(
                Route::get('curriculums_list')->uri(array('action' => 'list', 'major_id' => $curriculum->major_id))
              );
          } catch (Database_Exception $e) {
            $errors = array();
            $code = $e->getCode();
            switch ($code) {
              case 1062:
                $errors[] = __('Дублируется порядковый номер курса.');
                break;
              default:
                $errors[] = __('Database error was occurred. Error code: ' . $code . '. Message: ' . $e->getMessage());
                break;
            }

            if ($this->request->is_ajax())
              $this->response->body(json_encode(
                  array(
                    'result' => 'fail',
                    'errors' => View::factory('_errors', array('errors' => $errors))->render()
                  )
                ));
            else
              $this->template->content =
                View::factory('curriculums/manage', array('model' => $curriculum, 'errors' => $errors));
          }
        } catch (ORM_Validation_Exception $e) {
          $errors = $e->errors('models');

          if ($this->request->is_ajax())
            $this->response->body(json_encode(
                array(
                  'result' => 'fail',
                  'errors' => View::factory('_errors', array('errors' => $errors))->render()
                )
              ));
          else
            $this->template->content =
              View::factory('curriculums/manage', array('model' => $curriculum, 'errors' => $errors));
        }
        break;
    }
  }

  /**
   * редактирование курса curriculum'а
   * если GET-запрос, то возвращает форму редактирования
   * если POST-запрос, то сохраняет запись
   */
  public function action_update_course() {
    switch ($this->request->method()) {
      case Request::GET:
        $id = $this->request->param('id');

        if (empty($id)) {
          Message::set(Message::ERROR, __('Id must be set.'));
          return;
        }

        $model = Model::factory('curriculum')->where('id', '=', $id)->find();

        $content = View::factory('curriculums/form', array('model' => $model, 'errors' => null));

        if ($this->request->is_ajax())
          $this->response->body($content);
        else
          $this->template->content = $content;

        break;
      case Request::POST:
        $id = $this->request->param('id');
        $curriculum = ORM::factory('curriculum', $id);

        if (!$curriculum->loaded())
          $this->request->redirect('curriculums/index');
        else
          $curriculum
            ->values($_POST, array('course_id', 'course_type_id', 'deactivation_type_id', 'order',
              'credits', 'semestr', 'hours', 'course_limit', 'control_type_id', 'control_period'));

        $course_start = Arr::get($_POST, 'course_start');
        if (!empty($course_start)) {
          $course_start = date('Y-m-d', strtotime($course_start));
          $limit = ARR::get($_POST, 'course_limit', 0);
          $course_end = date('Y-m-d', strtotime($course_start . ' + ' . $limit . ' days'));

          $control_start = date('Y-m-d', strtotime($course_end . ' + 1 day'));
          $period = ARR::get($_POST, 'control_period', 0);
          $control_end = date('Y-m-d', strtotime($control_start . ' + ' . $period . ' days'));

          $curriculum
            ->set('course_start', $course_start)
            ->set('course_end', $course_end)
            ->set('control_start', $control_start)
            ->set('control_end', $control_end);
        }

        try {
          $curriculum->update();

          if ($this->request->is_ajax()) {
            $curriculums = ORM::factory('curriculum')
              ->where('major_id', '=', $curriculum->major_id)
              ->and_where('course_type_id', '=', $curriculum->course_type_id)
              ->order_by('order')
              ->find_all();

            $this->response->body(
              json_encode(
                array(
                  "result" => "success",
                  "content" => View::factory(
                    'curriculums/_manage', array('model' => $curriculums, 'pagination' => null)
                  )->render()
                )
              )
            );
          }
          else
            $this->request->redirect(
              Route::get('curriculums_manage')->uri(array('action' => 'manage', 'major_id' => $curriculum->major_id))
            );
        } catch (ORM_Validation_Exception $e) {
          $errors = $e->errors('models');

          if ($this->request->is_ajax())
            $this->response->body(json_encode(
                array(
                  'result' => 'fail',
                  'errors' => View::factory('_errors', array('errors' => $errors))->render()
                )
              ));
          else
            $this->template->content =
              View::factory('curriculums/update', array('model' => $curriculum, 'errors' => $errors));
        }
        break;
    }
  }

  /**
   * удаление элемента из curriculums'а
   * если GET-запрос, то возвращает форму подтверждения
   * удаляет запись при POST-запросе
   */
  public function action_delete_course() {
    switch ($this->request->method()) {
      case Request::GET:
        //  TODO: create view with confirmation.
        break;
      case Request::POST:
        $id = $this->request->param('id');

        $curriculum = ORM::factory('curriculum', $id);

        if ($curriculum->loaded()) {
          $curriculum->delete();
          $this->response->body(json_encode(array('result' => 'success')));
        }
        else
          $this->response->body(json_encode(array('result' => 'fail', 'message' => 'Model not loaded.')));
        break;
    }
  }

}
