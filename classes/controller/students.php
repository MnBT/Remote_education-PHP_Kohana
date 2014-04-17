<?php

defined('SYSPATH') or die('Restricted access!');

/**
 *
 * Контроллер
 * @author prophet
 *
 */
class Controller_Students extends Controller_Role_admin {

    public $template = 'main';
    public $nUsersPerPage = 20;
    protected $orm_selectedUser;
    protected $nCurrentPage;
    protected $strActiveTab;
    protected $strSortedColumn;
    protected $strSortedMethod;
    protected $strFilterColumn;
    protected $strFilterValue;
    private static $arrValidTabs = array("profile", "profiles", "curriculum", "progress", "timeline", "exams", "mygrade", "payment", "mailing");
    protected $debugLogActions = array();
    protected $filter;

    function before() {

        if (!isset($this->nUsersPerPage))
            $this->nUsersPerPage = Pagination::factory()->items_per_page;

        /* =====================================detecting sorting params=================================================== */
        $strSortColumn = Session::instance()->get("sort_column", "id");
        $strSortMethod = Session::instance()->get("sort_method", "ASC");
        if (!$this->request->is_ajax() && (Session::instance()->get("sort_column") !== $strSortColumn || Session::instance()->get("sort_method") !== $strSortMethod)) {
            Session::instance()->set("sort_column", $strSortColumn);
            Session::instance()->set("sort_method", $strSortMethod);
        }
        $this->sorting();


        /* =====================================detecting filtering params=================================================== */
        $strFilterColumn = Session::instance()->get("filter_column", "id");
        $strFilterValue = Session::instance()->get("filter_value", "");
        if (!$this->request->is_ajax() && (Session::instance()->get("filter_column") !== $strFilterColumn || Session::instance()->get("filter_value") !== $strFilterValue)) {
            Session::instance()->set("filter_column", $strFilterColumn);
            Session::instance()->set("filter_value", $strFilterValue);
        }
        $this->filtering();


        /* =====================================detecting current page number=================================================== */

        $nPage = $this->request->param('page', 1);
        //save page in session only if this is an index or students action.
        if (Session::instance()->get("page") != $nPage && (!$this->request->is_ajax() || $this->request->action() === 'students')) {
            Session::instance()->set("page", $nPage);
        }
        $this->nCurrentPage = Session::instance()->get("page", $nPage);
        
        /* ====================================detected active filter================================================== */
        $this->filter = Session::instance()->get("select_filter");

        /* =====================================detecting currently selected user=================================================== */
        $orm_users = $this->get_students($this->nUsersPerPage, $this->nUsersPerPage * ($this->nCurrentPage - 1), TRUE);
        $orm_user = array_shift($orm_users);
        $strUserId = Session::instance()->get("selected_user", (isset($orm_user->id)) ? $orm_user->id : "");
        if (Session::instance()->get("selected_user") !== $strUserId && !$this->request->is_ajax()) {
            Session::instance()->set("selected_user", $strUserId);
        }
        $this->current_user();

        /* =====================================detecting active tab=================================================== */
        if (in_array($this->request->action(), self::$arrValidTabs)) {
            Session::instance()->set("active_tab", $this->request->action());
        }
        $this->strActiveTab = Session::instance()->get("active_tab", "profile");

        //for debugging purposes
        /*      $this->response->body(
          var_dump(
          array(
          "before users_per_page" => $this->nUsersPerPage,
          "page_get"              => $nPage,
          "page_session"          => $this->nCurrentPage,
          "user"                  => $this->orm_selectedUser->id,
          "active_tab"            => $this->strActiveTab,
          "action"                => $this->request->action(),
          "sort_column"           => $this->strSortedColumn,
          "sort_method"           => $this->strSortedMethod,
          "filter_column"         => $this->strFilterColumn,
          "filter_value"          => $this->strFilterValue
          )
          )
          ); */

        parent::before();
    }

    /*   public function action_index()
      { */

    /*      $arrDebug = array(
      "before users_per_page" => $this->nUsersPerPage,
      "page_session"          => $this->nCurrentPage,
      "user"                  => $this->orm_selectedUser->id,
      "active_tab"            => $this->strActiveTab,
      "action"                => $this->request->action(),
      "sort_column"           => $this->strSortedColumn,
      "sort_method"           => $this->strSortedMethod,
      "filter_column"         => $this->strFilterColumn,
      "filter_value"          => $this->strFilterValue,
      "is_ajax"               => $this->request->is_ajax(),
      "debug_actions"         => $this->debugLogActions,
      "ROUTE_page"            => $this->request

      );

      $this->template->content= View::factory('index')
      ->set("debug", $arrDebug);
      $this->response->body(
      $this->template->content->render()
      ); */
    /* } */
    /*
     * display students main grid + tab functionality
     */

    public function action_index() {
        $nOffset = $this->nUsersPerPage * ($this->nCurrentPage - 1);
        $nCount = ORM::factory('user')->count_all();
        $pagination = Pagination::factory(array
                    (
                    //'total_items'     => $nCount,
                    /* 'items_per_page'  => $nPerPage */ /* ,
                      'current_page'    => array(
                      'source' => 'route',
                      'key'    => 'page'
                      ) */

                    'current_page' => array('source' => 'route', 'key' => 'page'), // source: "query_string" or "route"
                    'total_items' => $nCount,
                    'items_per_page' => $this->nUsersPerPage,
                    'view' => 'pagination/basic',
                    'auto_hide' => FALSE,
                    'first_page_in_url' => TRUE,
                ))
                ->route_params(array(
            'controller' => Request::current()->controller(),
            'action' => "index"
                ));
        //uncomment to see this debug info in view
        /*      $arrDebug = array(
          "before users_per_page" => $this->nUsersPerPage,
          "page_session"          => $this->nCurrentPage,
          "user"                  => $this->orm_selectedUser->id,
          "active_tab"            => $this->strActiveTab,
          "action"                => $this->request->action(),
          "sort_column"           => $this->strSortedColumn,
          "sort_method"           => $this->strSortedMethod,
          "filter_column"         => $this->strFilterColumn,
          "filter_value"          => $this->strFilterValue,
          "is_ajax"               => $this->request->is_ajax(),
          "debug_actions"         => $this->debugLogActions,
          "ROUTE_page"            => $this->request->param('page'),


          ); */
        $this->filter = Session::instance()->get("select_filter");
        $arrStudents = $this->get_students($this->nUsersPerPage, $nOffset);
        $this->template->content = View::factory('students/students')
                ->set("students", $arrStudents)
                ->set("pagination", $pagination)
                ->set("selectedUserId", $this->orm_selectedUser->id)
                ->set("studentsdetails", View::factory('students/studentsdetails')->set("activeTab", $this->strActiveTab))
                ->set("colNameToSort", $this->strSortedColumn)
                ->set("methodToSort", $this->strSortedMethod)
                ->set('activeFilter',$this->filter);
        $this->template->studentCreateGrid = View::factory('students/create_student');
        $this->template->enrolled_count = $this->get_students_enrollment();
        if (isset($arrDebug))
            $this->template->content->set("debug", $arrDebug);
        if ($this->request->is_ajax()) {
            $this->auto_render = FALSE;
            $this->response->body(json_encode(array(
                        "success" => TRUE,
                        "content" => $this->template->content->render(),
                        "studentCreateGrid" => $this->template->studentCreateGrid->render()
                    )));
        } else {
            $this->response->body(
                    $this->template->content->render(),
                    $this->template->studentCreateGrid->render()
            );
        }
    }

    public function action_profile() {
        $this->auto_render = FALSE;


        //uncomment to see this debug info in view
        /*      $arrDebug = array(
          "before users_per_page" => $this->nUsersPerPage,
          "page_session"          => $this->nCurrentPage,
          "user"                  => $this->orm_selectedUser->id,
          "active_tab"            => $this->strActiveTab,
          "action"                => $this->request->action(),
          "sort_column"           => $this->strSortedColumn,
          "sort_method"           => $this->strSortedMethod,
          "filter_column"         => $this->strFilterColumn,
          "filter_value"          => $this->strFilterValue,
          "is_ajax"               => $this->request->is_ajax()
          ); */

        try {
            $strUserPictureURL = $this->orm_selectedUser->photos->featured()->find()->getURL(TRUE);
        } catch (Exception $e) {
            $strUserPictureURL = "media/img/noimage.png";
        }

        $arrData = array('first_name' => '', 'middle_name' => '', 'last_name' => '', 'sex' => '', 'birthday' => '',
            'speciality_id' => '', 'study_language_id' => '', 'native_language' => '', 'native_english' => '',
            'nation' => '', 'education' => '', 'citizenship' => '', 'residence' => '', 'type' => '', 'note' => '',
            'reg_date' => '', 'tz' => '', 'application_status_id' => '', 'admission_type_id' => '', 'age' => '',
            'toefl_ielts' => '', 'currently_student' => '');

        $profile = Arr::overwrite($arrData, $this->orm_selectedUser->details->as_array());
        $profile["language"] = $this->orm_selectedUser->details->study_language->name;
        if (!empty($profile["birthday"]))
            $profile["age"] = self::age($profile["birthday"]);

        $history_last = array();
        foreach ($this->orm_selectedUser->details->history->last()->find_all() as $history_record) {
            switch ($history_record->column) {
                case "details":
                    $history_last[$history_record->field] = $history_record->as_array();
                    break;
                case "address":
                    $history_last["address"][$history_record->type] = $history_record->as_array();
                    break;
                default:
                    $history_last[$history_record->column] = $history_record->as_array();
            }
        }

        $contacts = array();
        foreach ($this->orm_selectedUser->contacts->find_all() as $contact) {
            $contacts[$contact->id] = $contact->as_array();
        }

        $address = array();
        foreach ($this->orm_selectedUser->address->find_all() as $addr) {
            $address[$addr->type] = $addr->as_array();
        }

        $locations = array();
        foreach ($this->orm_selectedUser->locations->order_by_time()->limit(3)->find_all() as $loc) {
            $locations[$loc->id] = $loc->as_array();
        }

        try {
            require_once "Net/GeoIP.php";
            $file = DOCROOT . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'GeoIP.dat';
            if (!file_exists($file))
                throw new Exception(__("\":file\" file not found", array(":file" => "GeoIP.dat")));
            $geoip = Net_GeoIP::getInstance($file);
            $location = $geoip->lookupLocation($_SERVER['REMOTE_ADDR']);
        } catch (Exception $e) {
            Message::set(Message::ERROR, $e->getMessage());
            $location = NULL;
        }

        //===================EMERGENCY CONTACTS===================================
        $emergency_contacts = array();
        foreach ($this->orm_selectedUser->emergency->order_by("primary", "desc")->find_all() as $emergency_contact) {
            $emergency_contacts[$emergency_contact->id] = $emergency_contact->as_array();
            $emergency_contacts[$emergency_contact->id]["type"] = __($emergency_contact->type->name);
        }
        //===================EDUCATION==========================================
        $education = array();
        $education["native_english"] = $profile["native_english"];
        $education["native_language"] = $profile["native_language"];
        $education["toefl_ielts"] = $profile["toefl_ielts"];
        $education["currently_student"] = $profile["currently_student"];
        $education["records"] = array();
        foreach ($this->orm_selectedUser->education->order_by("date_entered", "desc")->find_all() as $record) {
            $education["records"][$record->id] = $record->as_array();
            $education["records"][$record->id]["date_entered"] = self::dateFormat($record->date_entered);
            $education["records"][$record->id]["date_expected"] = self::dateFormat($record->date_expected);
        }
        //===================WORK EXPERIENCE===========================================
        $experience = array();
        foreach ($this->orm_selectedUser->experience->order_by("employed_from", "desc")->find_all() as $work) {
            $experience[$work->id] = $work->as_array();
            $experience[$work->id]["country"] = $work->country_id;
            $experience[$work->id]["employed_from"] = self::dateFormat($work->employed_from);
            $experience[$work->id]["employed_to"] = self::dateFormat($work->employed_to);
        }

        //=================GENERAL INFORMATION============================================
        $general_info = array();
        //Major
        $major = $this->orm_selectedUser->user_majors->where("active", "=", "1")->find();
        $general_info["major"] = ($major->loaded()) ? $major->major_id : "";
        //Language of study
        $general_info["study_language"] = $this->orm_selectedUser->details->study_language_id;
        //Application status
        $general_info["application_status"] = $this->orm_selectedUser->details->application_status_id;
        //Admission Date
        $general_info["admission_date"] = self::dateFormat($this->orm_selectedUser->details->reg_date);
        //Admission Type
        $general_info["admission_type"] = $this->orm_selectedUser->details->admission_type_id;
        //Admission Officer
        $general_info["admission_officer"] = ($this->orm_selectedUser->details->admission_officer == '1') ? __("Yes") : __("No");
        //Admissions Partner
        $general_info["partner"] = $this->orm_selectedUser->details->partner;
        //Personal Consultant
        $general_info["personal_consultant"] = $this->orm_selectedUser->details->personal_consultant;
        //Promotional program
        $general_info["promotional_program"] = $this->orm_selectedUser->details->promotional_program;
      $tmp_contacts = array();
      foreach($contacts as $contact){
         $tmp_contacts[$contact["type"]["name"]][] = $contact;
      }

        $this->template->content = View::factory('students/profile')
                ->set('userpic', $strUserPictureURL)
                ->set('profile', $profile)
                ->set("date", new DateTime())
                ->set("location", $location)
                ->set("history", $history_last)
                ->set("country", ORM::factory('country')->find_all()->as_array('id', "name"))
                ->set("study_languages", ORM::factory('language')->find_all()->as_array('id', "description"))
                ->set("majors", ORM::factory('major')->find_all()->as_array('id', "name"))
                ->set("emergency_types", ORM::factory('emergency_type')->find_all()->as_array('id', "name"))
                ->set("admission_types", ORM::factory('admission_type')->find_all()->as_array('id', "name"))
                ->set("application_statuses", ORM::factory('application_status')->find_all()->as_array('id', "name"))
                ->set("contacts", $tmp_contacts)
                ->set("address", $address)
                ->set("locations", $locations)
                ->set("current_location", $this->orm_selectedUser->locations->current()->as_array())
                ->set("general_info", $general_info)
                ->set("emergency_contacts", $emergency_contacts)
                ->set("education", $education)
                ->set("work_experience", $experience);
        if (isset($arrDebug))
            $this->template->content->set("debug", $arrDebug);
        $this->response->body(json_encode(array("success" => TRUE, "content" => $this->template->content->render())));
    }

    public function action_curriculum() {
        $this->auto_render = FALSE;

//     $major = $this->orm_selectedUser->majors->where('active', '=', 1)->order_by('id', 'desc')->limit(1)->offset(0)->find();
        $major = $this->orm_selectedUser->majors->where('active', '=', 1)->order_by('id', 'desc')->limit(1)->offset(0)->find();

        $types = Model::factory('course_type')->find_all();
        foreach ($types as $type) {
            $data[] = array(
                'type' => $type,
                'courses' => ORM::factory('curriculum')
                        ->where('major_id', '=', $major->id)
                        ->and_where('user_id', '=', $this->orm_selectedUser->id)
                        ->and_where('course_type_id', '=', $type->id)
                        ->order_by('order')
                        ->find_all()
            );
        }

        $content = View::factory('students/curriculum', array('data' => $data, 'major' => $major->as_array()))->render();

        $this->response->body(json_encode(array("success" => TRUE, "content" => $content)));
    }

    /**
     * редактирование курса curriculum'а
     * если GET-запрос, то возвращает форму редактирования
     * если POST-запрос, то сохраняет запись
     */
    public function action_update_curriculum_course() {
        switch ($this->request->method()) {
            case Request::GET:
                $id = $this->request->param('id');

                if (empty($id)) {
                    Message::set(Message::ERROR, __('Id must be set.'));
                    return;
                }

                $model = Model::factory('curriculum')
                        ->select(
                                array(
                                    'course_start',
                                    'course_limit',
                                    'control_period'
                                )
                        )
                        ->where('id', '=', $id)
                        ->find();

                $this->auto_render = false;

                $content = View::factory('students/_curriculum_manage_form', array('model' => $model, 'errors' => null))->render();

                if ($this->request->is_ajax())
                    $this->response->body($content);
                else
                    $this->template->content = $content;

                break;
            case Request::POST:
                $id = $this->request->param('id');
                $curriculum = ORM::factory('curriculum', $id);

                if (!$curriculum->loaded())
                    $this->request->redirect('students/curriculum');
                else
                    $curriculum
                            ->values($_POST, array('course_limit', 'control_period'));

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

                    $this->auto_render = false;

                    if ($this->request->is_ajax()) {
                        $curriculums = ORM::factory('curriculum')
                                ->where('major_id', '=', $curriculum->major_id)
                                ->and_where('user_id', '=', $this->orm_selectedUser->id)
                                ->and_where('course_type_id', '=', $curriculum->course_type_id)
                                ->order_by('order')
                                ->find_all();

                        $this->response->body(
                                json_encode(
                                        array(
                                            "result" => "success",
                                            "content" => View::factory(
                                                    'students/_curriculum_manage', array('model' => $curriculums, 'pagination' => null)
                                            )->render()
                                        )
                                )
                        );
                    }
                    else
                        $this->request->redirect(
                                Route::get('students')->uri(array('action' => 'curriculum_manage'))
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
                                View::factory('students/_curriculums_manage_form', array('model' => $curriculum, 'errors' => $errors));
                }
                break;
        }
    }

    public function action_profiles() {
        $this->auto_render = FALSE;
        //uncomment to see this debug info in view
        /*      $arrDebug = array(
          "before users_per_page" => $this->nUsersPerPage,
          "page_session"          => $this->nCurrentPage,
          "user"                  => $this->orm_selectedUser->id,
          "active_tab"            => $this->strActiveTab,
          "action"                => $this->request->action(),
          "sort_column"           => $this->strSortedColumn,
          "sort_method"           => $this->strSortedMethod,
          "filter_column"         => $this->strFilterColumn,
          "filter_value"          => $this->strFilterValue,
          "is_ajax"               => $this->request->is_ajax()
          ); */
        $nOffset = $this->nUsersPerPage * ($this->nCurrentPage - 1);
        $arrStudentsOrm = $this->get_students($this->nUsersPerPage, $nOffset, TRUE);

        $arrData = array(
            'id' => '',
            'birthday' => '&nbsp',
            'sex' => '&nbsp',
            'country' => '&nbsp',
            'city' => '&nbsp',
            'location' => '&nbsp',
            'phone' => '&nbsp',
            'email' => '&nbsp',
            'language' => '&nbsp',
            'admit_date' => '&nbsp',
            'admit_day' => '&nbsp',
            'enroll_date' => '&nbsp',
            'enroll_day' => '&nbsp',
            'del' => '&nbsp',
            're' => '&nbsp',
            'partner' => '&nbsp');
        $arrStudents = array();
        /* $intAddrType = Model_User_address::TYPE_CURRENT; */
        foreach ($arrStudentsOrm as $ormStudent) {
            $arrStudent = Arr::overwrite($arrData, $ormStudent->as_array());
            $arrStudent = Arr::overwrite($arrStudent, $ormStudent->details->as_array());
            $arrStudent['language'] = $ormStudent->details->study_language->name;
            $arrCurrentAddress = $ormStudent->address->where("type", "=", Model_User_address::TYPE_CURRENT)->find()->as_array();
            $arrStudent['country'] = (!empty($arrCurrentAddress["country"]["name"])) ? $arrCurrentAddress["country"]["name"] : "&nbsp";
            $arrStudent['city'] = (!empty($arrCurrentAddress["city"])) ? $arrCurrentAddress["city"] : "&nbsp";

            $arrStudent["birthday"] = parent::age($arrStudent["birthday"]);
            unset($arrCurrentAddress);
            //TODO Location - (город, страна) местонахождение определенное по IP, по последнему посещению
            $contact = $ormStudent->contacts->getPrimary(Model_User_contact::TYPE_PHONE);
            $contact = ($contact->loaded()) ? $contact->as_array() : array();
            $arrStudent["phone"] = (!empty($contact["value"])) ? $contact["value"] : "&nbsp";

            $contact = $ormStudent->contacts->getPrimary(Model_User_contact::TYPE_EMAIL);
            $contact = ($contact->loaded()) ? $contact->as_array() : array();
            $arrStudent["email"] = (!empty($contact["value"])) ? $contact["value"] : "&nbsp";
            unset($contact);

            $arrStudent["del"] = (int) $ormStudent->photos->where("status", "=", Model_User_photo::STATUS_DELETED)->find_all()->count();

            $arrLastChange = $ormStudent->details->history->last()->order_by("date", "DESC")->find()->as_array();
            $arrStudent["re"] = (!empty($arrLastChange["date"])) ? ceil(((time() - $arrLastChange["date"]) / 24 / 3600)) : 0;

            $admitTimestamp = $ormStudent->details->reg_date;
            $arrStudent["admit_date"] = (isset($admitTimestamp)) ? self::dateFormat($admitTimestamp) : "&nbsp";

            $admitDays = $ormStudent->details->getAdmitDays();
            $arrStudent["admit_day"] = ($admitDays !== FALSE) ? $admitDays : "&nbsp";

            $enrollTimestamp = $ormStudent->details->getEnrollDate();
            $arrStudent["enroll_date"] = ($enrollTimestamp !== FALSE) ? self::dateFormat($enrollTimestamp) : "&nbsp";

            $enrollDays = $ormStudent->details->getEnrollDays();
            $arrStudent["enroll_day"] = ($enrollDays !== FALSE) ? $enrollDays : "&nbsp";

            $arrStudents[$ormStudent->id] = $arrStudent;
        }

        $this->template->content = View::factory("students/profiles")
                ->set("students", $arrStudents)
                ->set("selectedUserId", $this->orm_selectedUser->id);
        if (isset($arrDebug))
            $this->template->content->set("debug", $arrDebug);
        $this->response->body(json_encode(array("success" => TRUE, "content" => $this->template->content->render())));
    }

    public function action_progress() {
        $this->auto_render = FALSE;
        //uncomment to see this debug info in view
        /*      $arrDebug = array(
          "before users_per_page" => $this->nUsersPerPage,
          "page_session" => $this->nCurrentPage,
          "user" => $this->orm_selectedUser->id,
          "active_tab" => $this->strActiveTab,
          "action" => $this->request->action(),
          "sort_column" => $this->strSortedColumn,
          "sort_method" => $this->strSortedMethod,
          "filter_column" => $this->strFilterColumn,
          "filter_value" => $this->strFilterValue,
          "is_ajax" => $this->request->is_ajax()
          ); */

        $nOffset = $this->nUsersPerPage * ($this->nCurrentPage - 1);
        $arrStudentsOrm = $this->get_students($this->nUsersPerPage, $nOffset, TRUE);

        $arrStudents = array();
        /* $intAddrType = Model_User_address::TYPE_CURRENT; */
        foreach ($arrStudentsOrm as $ormStudent) {
            $arrStudent = Arr::overwrite(array("id" => ""), $ormStudent->as_array());

            $enrollDay = $ormStudent->details->getEnrollDays();
            $arrStudent["enroll_day"] = ($enrollDay !== FALSE) ? $enrollDay : "&nbsp";

            $currentCourse = $ormStudent->get_current_course()->find();
            /* throw new Kohana_Exception($currentCourse->id); */
            $arrStudent["current_course"] = ($currentCourse->loaded()) ? $currentCourse->course->name : "&nbsp";

            $currentCourseBook = $ormStudent->books->where("course_id", "=", $currentCourse->course_id)->find();
            $arrStudent["version"] = ($currentCourseBook->loaded()) ? $currentCourseBook->version : "&nbsp";
            $arrStudent["download_date"] = ($currentCourseBook->loaded()) ? $currentCourseBook->download : "&nbsp";

            $arrStudent["start_date"] = ($currentCourse->loaded()) ? self::dateFormat($currentCourse->date_study_start) : "&nbsp";
            //TODO implement depending on vacations
            if ($currentCourse->loaded() && !empty($currentCourse->date_study_end)) {
                if ($currentCourse->date_study_end > ($currentCourse->date_study_start + $currentCourse->course->time_limit * Kohana_Date::DAY)) {
                    $arrStudent["end_date"] = self::dateFormat($currentCourse->date_study_end);
                } else {
                    $arrStudent["end_date"] = self::dateFormat($currentCourse->date_study_start + $currentCourse->course->time_limit * Kohana_Date::DAY);
                }
            } else {
                $arrStudent["end_date"] = "&nbsp";
            }
            //TODO deadline should be = date_study_start + course_time_limit + sum(period of vacations)
            //currently deadline = date_study_start + course_time_limit
            $arrStudent["deadline"] = ($currentCourse->loaded()) ? self::dateFormat($currentCourse->date_study_start + $currentCourse->course->time_limit * Kohana_Date::DAY) : "&nbsp";

            $arrStudent["time_limit"] = ($currentCourse->loaded()) ? $currentCourse->course->time_limit : "&nbsp";

            $currDate = new DateTime();
            $currDate = $currDate->getTimestamp();
            $endDate = ($currentCourse->loaded() && !empty($currentCourse->date_study_end)) ? $currentCourse->date_study_end : $currDate;

            //
            if ($currentCourse->loaded() && $currentCourse->date_study_start < $currDate) {
                $arrStudent["progress_days"] = ceil(($endDate - $currentCourse->date_study_start) / Kohana_Date::DAY);
                $arrStudent["progress_percents"] = ceil(100 * ($endDate - $currentCourse->date_study_start) / Kohana_Date::DAY / $currentCourse->course->time_limit);
            } else {
                $arrStudent["progress_days"] = ($currentCourse->loaded()) ? 0 : "&nbsp";
                $arrStudent["progress_percents"] = ($currentCourse->loaded()) ? 0 : "&nbsp";
            }

            $arrStudents[$ormStudent->id] = $arrStudent;
        }

        $this->template->content = View::factory("students/progress")
                ->set("students", $arrStudents)
                ->set("selectedUserId", $this->orm_selectedUser->id);
        if (isset($arrDebug))
            $this->template->content->set("debug", $arrDebug);
        $this->response->body(json_encode(array("success" => TRUE, "content" => $this->template->content->render())));
    }

    public function action_timeline() {
        $this->auto_render = FALSE;
        /*      //uncomment to see this debug info in view
          $arrDebug = array(
          "before users_per_page" => $this->nUsersPerPage,
          "page_session" => $this->nCurrentPage,
          "user" => $this->orm_selectedUser->id,
          "active_tab" => $this->strActiveTab,
          "action" => $this->request->action(),
          "sort_column" => $this->strSortedColumn,
          "sort_method" => $this->strSortedMethod,
          "filter_column" => $this->strFilterColumn,
          "filter_value" => $this->strFilterValue,
          "is_ajax" => $this->request->is_ajax()
          ); */

        $arrGraphData = array();
        /*      $arrGraphData["admit_date_start"] = "20.05.2013";
          $arrGraphData["admit_date_end"] = "20.08.2013";
          $arrGraphData["test_date"] = "20.10.2013";
          $arrGraphData["course_name"] = "BUS 9550 BUISINESS FINANCE";
          $arrGraphData["study_term_percent"] = 45;
          $arrGraphData["test_mark"] = 100; */


        $admitTimestamp = $this->orm_selectedUser->details->reg_date;
        $arrGraphData["admit_date"] = (isset($admitTimestamp)) ? self::dateFormat($admitTimestamp, "d.m.Y") : NULL;

        $currentCourse = $this->orm_selectedUser->get_current_course()->find();


        if ($currentCourse->loaded()) {
            $arrGraphData["course_name"] = $currentCourse->course->name;
            $arrGraphData["date_study_start"] = self::dateFormat($currentCourse->date_study_start, "d.m.Y");
            $arrGraphData["date_study_end"] = self::dateFormat($currentCourse->date_study_start + $currentCourse->course->time_limit * Kohana_Date::DAY, "d.m.Y");
            //TODO change to enhance deadline time with time spend by user on vacations
            $currDate = new DateTime();
            $currDate = $currDate->getTimestamp();
            $endDate = ($currentCourse->loaded() && !empty($currentCourse->date_study_end)) ? $currentCourse->date_study_end : $currDate;
            $arrGraphData["study_term_percent"] = ceil(100 * ($endDate - $currentCourse->date_study_start) / Kohana_Date::DAY / $currentCourse->course->time_limit);

            $ormTestResult = $this->orm_selectedUser->get_current_course_testresults()->find();
            if ($ormTestResult->loaded()) {
                $arrGraphData["test_date"] = self::dateFormat($ormTestResult->end_date, "d.m.Y");
                $arrGraphData["test_mark"] = (isset($ormTestResult->percentage)) ? $ormTestResult->percentage : "&nbsp";
            }
        }
        //operations Grid filling:
        $operations = $this->orm_selectedUser->operations->limit(20)->find_all();
        $arrOperations = array();

        foreach ($operations as $operation) {
            $arrOperationData = array();
            $arrOperationData["id"] = $operation->id;
            $arrOperationData["date"] = (isset($operation->date)) ? self::dateFormat($operation->date) : "&nbsp";
            $arrOperationData["time"] = (isset($operation->date)) ? self::dateFormat($operation->date, "g:i A") : "&nbsp";
            $arrOperationData["type"] = $operation->type->name;
            $arrOperationData["description"] = $operation->description;
            $arrOperationData["income"] = $operation->income;
            $arrOperationData["expenses"] = $operation->expenses;
            $arrOperationData["blocked"] = $operation->blocked;
            $arrOperationData["balance"] = $operation->balance;
            $arrOperations[] = $arrOperationData;
        }


        $this->template->content = View::factory("students/timeline")
                ->set("graphData", json_encode($arrGraphData))
                ->set("operations", $arrOperations);

        if (isset($arrDebug))
            $this->template->content->set("debug", $arrDebug);
        $this->response->body(json_encode(array("success" => TRUE, "content" => $this->template->content->render())));
    }

    public function action_exams() {
        $this->auto_render = FALSE;
        //for debugging purposes
        /*      $this->response->body(
          var_dump(
          array(
          "tab"                   => strtoupper(substr(__FUNCTION__, 7)),
          "users_per_page"        => $this->nUsersPerPage,
          "page_session"          => $this->nCurrentPage,
          "user"                  => $this->orm_selectedUser->id,
          "active_tab"            => $this->strActiveTab,
          "action"                => $this->request->action(),
          "sort_column"           => $this->strSortedColumn,
          "sort_method"           => $this->strSortedMethod
          )
          )
          ); */
        $arrData = array(
            'id' => '',
            'enroll_day' => '',
            'current_test' => '',
            'type' => '',
            'attempt' => '',
            'start_date' => '',
            'end_date' => '',
            'checked' => '',
            'check_date' => '',
            'checked_by' => '',
            'percentage' => '',
            'grade_point' => '',
            'letter_grade' => '',
            'status' => '',
            'action' => '');
        $nOffset = $this->nUsersPerPage * ($this->nCurrentPage - 1);
        $arrStudentsOrm = $this->get_students($this->nUsersPerPage, $nOffset, TRUE);

        $arrStudents = array();
        /* $intAddrType = Model_User_address::TYPE_CURRENT; */
        foreach ($arrStudentsOrm as $ormStudent) {
            $arrStudent = Arr::overwrite(array("id" => ""), $ormStudent->as_array());
//         Day - поле дублируется из профиля - количество дней после Enrollment Date, после
//               начала обучения. Это число пишется на графике под “сегодняшней датой”.
//               По сути это количество учебных дней. Перерывы в обучении останавливают счетчик.
            $enrollDays = $ormStudent->details->getEnrollDays();
            $arrStudent["enroll_day"] = ($enrollDays !== FALSE) ? $enrollDays : "";

            $currentTest = $ormStudent->get_current_course_testresults()->find();
            if ($currentTest->loaded()) {
//         Current Test - текущий тест, который ожидает студента. Название теста почти всегда
//                        совпадает с названием курса и ожидающий тест автоматически добавляется
//                        вместе с курсом. Исключение составляют добавленные вручную
//                        дополнительные тесты.

                $arrStudent["current_test"] = $currentTest->test->name;
//         Type - тип теста
                $arrStudent["type"] = $currentTest->test->type->name;
//         Attempt - попытка. Показывается какую попытку данного теста проходит студент.
                $arrStudent["attempt"] = $currentTest->attempt;
//         Start Date - дата начала теста, когда к тесту будет/был активирован доступ.
                $arrStudent["start_date"] = (isset($currentTest->start_date)) ? self::dateFormat($currentTest->start_date) : "&nbsp";
//         End Date - дата фактического прохождения теста.
                $arrStudent["end_date"] = (isset($currentTest->end_date)) ? self::dateFormat($currentTest->end_date) : "&nbsp";
//         Checked - счетчик сколько суток прошло с даты End Date.
                $currDate = new DateTime();
                $currDate = $currDate->getTimestamp();
                $checkDate = (!empty($currentTest->check_date)) ? $currentTest->check_date : $currDate;
                if ($currentTest->end_date < $currDate) {
                    $arrStudent["checked"] = ceil(($checkDate - $currentTest->end_date) / Kohana_Date::DAY);
                } else {
                    $arrStudent["checked"] = 0;
                }

//         Check Date - дата проверки теста. Moodle
                $arrStudent["check_date"] = (isset($currentTest->check_date)) ? self::dateFormat($currentTest->check_date) : "&nbsp";
//         Check by - кем проверен тест. Moodle
                $arrStudent["checked_by"] = (isset($currentTest->checkedby)) ?
                        $currentTest->checkedby->details->first_name . " " . $currentTest->checkedby->details->last_name :
                        "&nbsp";

//          how many  questions in test
                $arrStudent["num_of_questions"] = (isset($currentTest->questions)) ? $currentTest->questions : "";
//         % - Percentage - Оценка в процентах. Данные из Moodle.
                $arrStudent["percentage"] = (isset($currentTest->percentage)) ? $currentTest->percentage : "";

//         GP - Grade Point - Оценка цифровая градация. Данные из Moodle.
                $arrStudent["grade_point"] = 4 * $arrStudent["percentage"] / 100;
//         LG - Letter Grade - Оценка буквенное выражение. Данные из Moodle.
                switch ($arrStudent["percentage"]) {
                    case ($arrStudent["percentage"] >= 95):
                        $arrStudent["letter_grade"] = "A";
                        break;
                    case ($arrStudent["percentage"] >= 90):
                        $arrStudent["letter_grade"] = "A-";
                        break;
                    case ($arrStudent["percentage"] >= 87):
                        $arrStudent["letter_grade"] = "B+";
                        break;
                    case ($arrStudent["percentage"] >= 84):
                        $arrStudent["letter_grade"] = "B";
                        break;
                    case ($arrStudent["percentage"] >= 80):
                        $arrStudent["letter_grade"] = "B-";
                        break;
                    case ($arrStudent["percentage"] >= 77):
                        $arrStudent["letter_grade"] = "C+";
                        break;
                    case ($arrStudent["percentage"] >= 74):
                        $arrStudent["letter_grade"] = "C";
                        break;
                    case ($arrStudent["percentage"] >= 70):
                        $arrStudent["letter_grade"] = "C-";
                        break;
                    case ($arrStudent["percentage"] >= 67):
                        $arrStudent["letter_grade"] = "D+";
                        break;
                    case ($arrStudent["percentage"] >= 60):
                        $arrStudent["letter_grade"] = "D";
                        break;
                    default:
                        $arrStudent["letter_grade"] = "F";
                        break;
                }
//         Status - статус теста
//                     Current Waiting - ожидающий. Показывается на странице Exam., на графике студента. Доступа к тесту нет.
//                     Test Available - К тесту есть доступ.
//                     Testing - тестирование. Тест к которому студент приступил и сдает его.
//                     Needs Grading - пройден но еще не оценен. (название изменено)
//                     Error - ошибка.
//                     Failed - оценка теста - провален.
//                           для Att<3, если failed, то переход к повторному тесту.
//                           для Att=3 если failed, то переход к следующему курсу.
//                     Passed - оценка теста - пройден
//                              для “B ORQ” если passes, то переход к следующему тесту.
//                              для “B MCQ” если passes, то переход к следующему курсу.
                $arrStudent["status"] = $currentTest->status->name;
            } else {
                $arrStudent = Arr::overwrite($arrData, $arrStudent);
            }
            $arrStudents[] = $arrStudent;
        }
        self::format_results_for_grid_view($arrStudents);
        $this->template->content = View::factory("students/exams")
                ->set("students", $arrStudents)
                ->set("selectedUserId", $this->orm_selectedUser->id);
        if (isset($arrDebug))
            $this->template->content->set("debug", $arrDebug);
        $this->response->body(json_encode(array("success" => TRUE, "content" => $this->template->content->render())));
    }

    public function action_mygrade() {
        $this->auto_render = FALSE;
        //for debugging purposes
        /*      $this->response->body(
          var_dump(
          array(
          "tab"                   => strtoupper(substr(__FUNCTION__, 7)),
          "users_per_page"        => $this->nUsersPerPage,
          "page_session"          => $this->nCurrentPage,
          "user"                  => $this->orm_selectedUser->id,
          "active_tab"            => $this->strActiveTab,
          "action"                => $this->request->action(),
          "sort_column"           => $this->strSortedColumn,
          "sort_method"           => $this->strSortedMethod
          )
          )
          ); */
        $arrData = array(
            'id' => '',
            'course_code' => '',
            'course_name' => '',
            'test_type' => '',
            'attempt' => '',
            'start_date' => '',
            'end_date' => '',
            'checked' => '',
            'check_date' => '',
            'checked_by' => '',
            'num_of_questions' => '',
            'score' => '',
            'percentage' => '',
            'grade_point' => '',
            'letter_grade' => '',
            'status' => '',
            'num_of_comments' => '');

        $arrGradesOrm = $this->orm_selectedUser->testresults->find_all();

        $arrTestResults = array();
        /* $intAddrType = Model_User_address::TYPE_CURRENT; */
        $nCountPercentage = 0;
        $nSumPercentage = 0;
        foreach ($arrGradesOrm as $ormTestResult) {
            $arrTestResult = Arr::overwrite(array("id" => ""), $ormTestResult->as_array());
            $currentCourse = $ormTestResult->user->get_current_course()->find();
            if ($currentCourse->loaded()) {
                $arrTestResult["course_code"] = $currentCourse->course->code;
                $arrTestResult["course_name"] = $currentCourse->course->name;
            }

            $currentTest = $ormTestResult->user->get_current_course_testresults()->find();
            if ($currentTest->loaded()) {
//         Type - тип теста, есть несколько типов теста:
//                     Тип А - студент скачивает файл pdf заполняет его и отправляет обратно,
//                              далее тест проверяется вручную и выставляется оценка.
//                     Тип B - тест на автоматизированной платформе, (платформа готова),
//                              студент получает доступ к тестовой системе и сдает тест.
//                     - B ORQ (open-response questions) Студенту нужно ответить на открытые
//                              вопросы, после чего тест отправляется на проверку. Оценка по данному тесту
//                              не доступна студенту, так как является частью ORQ+MCQ. Инструктор
//                              проверив тест ORQ активирует доступ в MCQ если оценка выше 70%. Если
//                              оценка ниже 70% он активирует повторный тест.
//                     - B MCQ (multiple choice questions) Студент отвечает на вопросы закрытого
//                              типа с вариантами ответов. Сразу после этого теста студент получает
//                              результаты MCQ и среднюю оценку за весь тест ORQ+MCQ. И если общая
//                              оценка 70% и выше автоматически активируется следующий курс.
//                              Если общая оценка за тест меньше 70% то активируется повторный тест, к
//                              которому он сможет приступить немедленно. Студент может отказаться от
//                              сдачи повторного теста в этом случае тест считается провален и студент
//                              должен вернуться к курсу в конце обучения. Повторных тестов может быть 2.
//                              Не сдача их делает тест Failed. И активируется следующий курс.
//                     Тип С - тест на автоматизированной платформе, но в присутствии
//                              преподавателя/ куратора/ партнера. С видео-записью процесса
//                              тестирования. В настоящее время не используется.
//                     Тип D - тест с использованием видео чата. Преподаватель по-английски
//                              задает вопросы, студент отвечает - либо с выбором ответов, либо устно.
//                              Данный тип в настоящее время не используется.
                $arrTestResult["test_type"] = $currentTest->test->type->name;
//         Attempt - попытка. Показывается какую попытку данного теста проходит студент.
                $arrTestResult["attempt"] = $currentTest->attempt;
//         Start Date - дата начала теста, когда к тесту будет/был активирован доступ. Эта дата
//                      совпадает с End Date курса на странице Прогресс. Если курс сдвигается, то
//                      должна сдвигаться и дата начала теста. Если студент берет дополнительное
//                      время, (дополнительный курс), то дата начала теста меняется на End Date
//                      этого дополнительного курса.
                $arrTestResult["start_date"] = (isset($currentTest->start_date)) ? self::dateFormat($currentTest->start_date) : "&nbsp";
//         End Date - дата фактического прохождения теста.
                $arrTestResult["end_date"] = (isset($currentTest->end_date)) ? self::dateFormat($currentTest->end_date) : "&nbsp";
//         Checked - счетчик сколько суток прошло с даты End Date. Если не прошло 24 часа то значение “0".
//                     Если прошло 24 часа - значение “1". Как только тест был проверен счетчик останавливается. Счетчик нужен
//                     для контроля, на сколько быстро проверяются тесты.
                $currDate = new DateTime();
                $currDate = $currDate->getTimestamp();
                $checkDate = (!empty($currentTest->check_date)) ? $currentTest->check_date : $currDate;
                if ($currentTest->end_date < $currDate) {
                    $arrTestResult["checked"] = ceil(($checkDate - $currentTest->end_date) / Kohana_Date::DAY);
                } else {
                    $arrTestResult["checked"] = 0;
                }

//         Check Date - дата проверки теста. Moodle
                $arrTestResult["check_date"] = (isset($currentTest->check_date)) ? self::dateFormat($currentTest->check_date) : "&nbsp";
//         Check by - кем проверен тест. Moodle
                $arrTestResult["checked_by"] = (isset($currentTest->checkedby)) ?
                        $currentTest->checkedby->details->first_name . " " . $currentTest->checkedby->details->last_name :
                        "&nbsp";
//TODO count on questions table?
                $arrTestResult["num_of_questions"] = (isset($currentTest->questions)) ? $currentTest->questions : "";

//         Score
                $arrTestResult["score"] = (isset($currentTest->score)) ? $currentTest->score : "";
//         % - Percentage - Оценка в процентах. Данные из Moodle.
                $arrTestResult["percentage"] = (isset($currentTest->percentage)) ? $currentTest->percentage : "";
                if ($arrTestResult["percentage"] !== "") {
                    $nCountPercentage++;
                    $nSumPercentage += $currentTest->percentage;
                }

//         GP - Grade Point - Оценка цифровая градация. Данные из Moodle.
                $arrTestResult["grade_point"] = 4 * $arrTestResult["percentage"] / 100;
//         LG - Letter Grade - Оценка буквенное выражение. Данные из Moodle.
                switch ($arrTestResult["percentage"]) {
                    case ($arrTestResult["percentage"] >= 95):
                        $arrTestResult["letter_grade"] = "A";
                        break;
                    case ($arrTestResult["percentage"] >= 90):
                        $arrTestResult["letter_grade"] = "A-";
                        break;
                    case ($arrTestResult["percentage"] >= 87):
                        $arrTestResult["letter_grade"] = "B+";
                        break;
                    case ($arrTestResult["percentage"] >= 84):
                        $arrTestResult["letter_grade"] = "B";
                        break;
                    case ($arrTestResult["percentage"] >= 80):
                        $arrTestResult["letter_grade"] = "B-";
                        break;
                    case ($arrTestResult["percentage"] >= 77):
                        $arrTestResult["letter_grade"] = "C+";
                        break;
                    case ($arrTestResult["percentage"] >= 74):
                        $arrTestResult["letter_grade"] = "C";
                        break;
                    case ($arrTestResult["percentage"] >= 70):
                        $arrTestResult["letter_grade"] = "C-";
                        break;
                    case ($arrTestResult["percentage"] >= 67):
                        $arrTestResult["letter_grade"] = "D+";
                        break;
                    case ($arrTestResult["percentage"] >= 60):
                        $arrTestResult["letter_grade"] = "D";
                        break;
                    default:
                        $arrTestResult["letter_grade"] = "F";
                        break;
                }

//         Status - статус теста
//                     Current Waiting - ожидающий. Показывается на странице Exam., на графике студента. Доступа к тесту нет.
//                     Test Available - К тесту есть доступ.
//                     Testing - тестирование. Тест к которому студент приступил и сдает его.
//                     Needs Grading - пройден но еще не оценен. (название изменено)
//                     Error - ошибка.
//                     Failed - оценка теста - провален.
//                           для Att<3, если failed, то переход к повторному тесту.
//                           для Att=3 если failed, то переход к следующему курсу.
//                     Passed - оценка теста - пройден
//                              для “B ORQ” если passes, то переход к следующему тесту.
//                              для “B MCQ” если passes, то переход к следующему курсу.
                $arrTestResult["status"] = $currentTest->status->name;
//TODO count on comments table?
                $arrTestResult["num_of_comments"] = 5;
            } else {
                $arrTestResult = Arr::overwrite($arrData, $arrTestResult);
            }
            $arrTestResults[] = $arrTestResult;
        }

        if ($nCountPercentage > 0) {
            $avgPercentage = $nSumPercentage / $nCountPercentage;
            $avgGradePoint = 4 * $avgPercentage / 100;

            switch ($avgPercentage) {
                case ($avgPercentage >= 95):
                    $avgLetterGrade = "A";
                    break;
                case ($avgPercentage >= 90):
                    $avgLetterGrade = "A-";
                    break;
                case ($avgPercentage >= 87):
                    $avgLetterGrade = "B+";
                    break;
                case ($avgPercentage >= 84):
                    $avgLetterGrade = "B";
                    break;
                case ($avgPercentage >= 80):
                    $avgLetterGrade = "B-";
                    break;
                case ($avgPercentage >= 77):
                    $avgLetterGrade = "C+";
                    break;
                case ($avgPercentage >= 74):
                    $avgLetterGrade = "C";
                    break;
                case ($avgPercentage >= 70):
                    $avgLetterGrade = "C-";
                    break;
                case ($avgPercentage >= 67):
                    $avgLetterGrade = "D+";
                    break;
                case ($avgPercentage >= 60):
                    $avgLetterGrade = "D";
                    break;
                default:
                    $avgLetterGrade = "F";
                    break;
            }
        } else {
            $avgPercentage = "&nbsp";
            $avgGradePoint = "&nbsp";
            $avgLetterGrade = "&nbsp";
        }
        self::format_results_for_grid_view($arrTestResults);
        $this->template->content = View::factory("students/mygrade")
                ->set("grades", $arrTestResults)
                ->set("studentName", $this->orm_selectedUser->details->first_name . " " . $this->orm_selectedUser->details->last_name)
                ->set("studentID", $this->orm_selectedUser->id)
                //TODO implement Major when corresponding db structure will be developed
                ->set("major", "someMajor")
                ->set("avgPercentage", $avgPercentage)
                ->set("avgGradePoint", $avgGradePoint)
                ->set("avgLetterGrade", $avgLetterGrade)
                ->set("selectedUserId", $this->orm_selectedUser->id);
        if (isset($arrDebug))
            $this->template->content->set("debug", $arrDebug);
        $this->response->body(json_encode(array("success" => TRUE, "content" => $this->template->content->render())));
    }

    public function action_payment() {
        $this->auto_render = FALSE;
        /*      //uncomment to see this debug info in view
          $arrDebug = array(
          "before users_per_page" => $this->nUsersPerPage,
          "page_session" => $this->nCurrentPage,
          "user" => $this->orm_selectedUser->id,
          "active_tab" => $this->strActiveTab,
          "action" => $this->request->action(),
          "sort_column" => $this->strSortedColumn,
          "sort_method" => $this->strSortedMethod,
          "filter_column" => $this->strFilterColumn,
          "filter_value" => $this->strFilterValue,
          "is_ajax" => $this->request->is_ajax()
          ); */
        if (isset($arrDebug))
            $this->template->content->set("debug", $arrDebug);
        $this->response->body(json_encode(array("success" => TRUE, "content" => "payment")));
    }

    public function action_mailing() {
        $this->auto_render = FALSE;
        /*      //uncomment to see this debug info in view
          $arrDebug = array(
          "before users_per_page" => $this->nUsersPerPage,
          "page_session" => $this->nCurrentPage,
          "user" => $this->orm_selectedUser->id,
          "active_tab" => $this->strActiveTab,
          "action" => $this->request->action(),
          "sort_column" => $this->strSortedColumn,
          "sort_method" => $this->strSortedMethod,
          "filter_column" => $this->strFilterColumn,
          "filter_value" => $this->strFilterValue,
          "is_ajax" => $this->request->is_ajax()
          ); */
        if (isset($arrDebug))
            $this->template->content->set("debug", $arrDebug);
        $this->response->body(json_encode(array("success" => TRUE, "content" => "mailing")));
    }

    public function action_select() {
        $this->auto_render = FALSE;

        $strUserId = Arr::get($_POST, "id", $this->orm_selectedUser->id);
        $this->current_user($strUserId);
        $strMethodName = 'action_' . $this->strActiveTab;
        $this->$strMethodName();
    }

    public function action_sort() {
        $this->auto_render = FALSE;

        $strSortColumn = Arr::get($_POST, "column", $this->strSortedColumn);
        $strSortMethod = Arr::get($_POST, "method", $this->strSortedMethod);
        $this->sorting($strSortColumn, $strSortMethod);

        /* $this->response->body(json_encode(array("success" => TRUE, "redirect_url" => "/students/?page=" . $this->nCurrentPage ))); */
        /*      $strMethodName = 'action_' . $this->strActiveTab;
          $this->$strMethodName(); */
        $this->action_index();
        /* Request::initial()->redirect('students/?page=' . $this->nCurrentPage); */
    }

    public function action_filter() {
        $this->auto_render = FALSE;

        $strFilterColumn = Arr::get($_POST, "column", $this->strFilterColumn);
        $strFilterValue = Arr::get($_POST, "value", $this->strFilterValue);
        $this->filtering($strFilterColumn, $strFilterValue);

        /* $this->response->body(json_encode(array("success" => TRUE, "redirect_url" => "/students/?page=" . $this->nCurrentPage ))); */
        /*      $strMethodName = 'action_' . $this->strActiveTab;
          $this->$strMethodName(); */
        $this->action_index();
    }
    
    protected function createErrorsmessage($errors)
    {
        $errors_mess = '<ul class="errors">';

        foreach ($errors as $key => $value)
          $errors_mess .= '<li>'. $value. '</li>';

        $errors_mess .= '<ul>';
        
        return $errors_mess;
    }

    public function action_save() {
        //----------------------create user--------------------------
        $this->auto_render = FALSE;
        $user = ORM::factory('user')->where('email', '=', Arr::get($_POST, 'primaryEmail'))->find();

        if ($user->loaded()) {
            $user_id = $user->id;
            $errors = 'Email already exists in the system<div><a href="" id="student_edit" value="'. $user_id. '">Edit user data</a></div>';
            $this->response->body(json_encode(array("success" => FALSE,'errors_mess' => $errors,"userId" => $user_id)));
        } else {
            try {
                $user = ORM::factory('user')
                        ->set('email', Arr::get($_POST, 'primaryEmail'))
                        ->set('username', Arr::get($_POST, 'primaryEmail'))
                        ->set('password', Auth::instance()->hash_password(uniqid(), true))
                        ->set('logins', 0)
                        ->set('ip', $_SERVER['REMOTE_ADDR']);
                $user->create();
                $user = ORM::factory('user')->where('email', '=', Arr::get($_POST, 'primaryEmail'))->find();
                $user_id = $user->id;
            } catch (ORM_Validation_Exception $e) {
                $errors = $e->errors('models');
                $this->response->body(json_encode(array("success" => FALSE,'errors_mess' => $errors)));
                return;
            }
            try {
                //----------------------user_details-------------------------
                $user_detail = ORM::factory('user_detail')
                        ->set('first_name', Arr::get($_POST, 'firstname'))
                        ->set('middle_name', Arr::get($_POST, 'lastname'))
                        ->set('last_name', Arr::get($_POST, 'middlename'))
                        ->set('other_name', Arr::get($_POST, 'othernames'))
                        ->set('sex', Arr::get($_POST, 'sex'))
                        ->set('birthday', Arr::get($_POST, 'birthdate'))
                        ->set('speciality_id', Arr::get($_POST, 'programOfStudy'))
                        ->set('study_language_id', Arr::get($_POST, 'languageOfStudy'))
                        ->set('native_language', Arr::get($_POST, 'firstLanguage'))
                        ->set('native_english', Arr::get($_POST, 'notiveLang'))
                        ->set('nation', Arr::get($_POST, 'country'))
                        ->set('reg_date', date("m.d.Y"))
                        ->set('admission_type_id', 1)
                        ->set('currently_student', Arr::get($_POST, 'currentStudy'))
                        ->set('citizenship', Arr::get($_POST, 'CountryCitizenship'))
                        ->set('birthplace_country', Arr::get($_POST, 'birthplace_country'))
                        ->set('birthplace_city', Arr::get($_POST, 'birthplace_city'))
                        ->set('toefl_ielts', Arr::get($_POST, 'languageResult'))
                        ->set('residence', Arr::get($_POST, 'CountryPermanentResidence'));
                $user_detail->create();
            } catch (ORM_Validation_Exception $e) {
                $user->delete();
                
                $errors_mess = $this->createErrorsMessage($e->errors('models'));
                
                $this->response->body(json_encode(array("success" => FALSE,'errors_mess' => $errors_mess)));
                return;
            }
            
            try {
                //----------------------user_contact---------------------------
                $user_contact = ORM::factory('user_contact')
                        ->set('user_id', $user_id)
                        ->set('type', 1)
                        ->set('subtype', 1)
                        ->set('value', Arr::get($_POST, 'telephone'));

                $user_contact->create();

                $user_contact = ORM::factory('user_contact')
                        ->set('user_id', $user_id)
                        ->set('type', 1)
                        ->set('subtype', 3)
                        ->set('value', Arr::get($_POST, 'mobilephone'));

                $user_contact->create();

                $user_contact = ORM::factory('user_contact')
                        ->set('user_id', $user_id)
                        ->set('type', 2)
                        ->set('subtype', 4)
                        ->set('value', Arr::get($_POST, 'fax'));

                $user_contact->create();

                $user_contact = ORM::factory('user_contact')
                        ->set('user_id', $user_id)
                        ->set('type', 3)
                        ->set('subtype', 5)
                        ->set('value', Arr::get($_POST, 'additEmail'));

                $user_contact->create();
            } catch (ORM_Validation_Exception $e) {
                $user->delete();
                $user_detail->delete();
                
                $contacts = ORM::factory('user_contact')
                        ->where('user_id', '=', $user_id);
                
                foreach ($contacts as $contact)
                  $contact->delete();

                $errors_mess = $this->createErrorsMessage($e->errors('models'));
                
                $this->response->body(json_encode(array("success" => FALSE,'errors_mess' => $errors_mess)));
                return;
            }
            
            try {
                //----------------------user_address---------------------------
                if (Arr::get($_POST, 'permanentAddress') != '' && Arr::get($_POST, 'permanentCity') != '' && Arr::get($_POST, 'permanentState') != '') {
                    $user_address = ORM::factory('user_address')
                            ->set('user_id', $user_id)
                            ->set('type', 2)
                            ->set('country', Arr::get($_POST, 'permanentCountry'))
                            ->set('state', Arr::get($_POST, 'permanentState'))
                            ->set('city', Arr::get($_POST, 'permanentCity'))
                            ->set('address', Arr::get($_POST, 'permanentAddress'))
                            ->set('location', 1)
                            ->set('zip', Arr::get($_POST, 'permanentPostal'));
                    $user_address->create();
                }
                $user_address = ORM::factory('user_address')
                        ->set('user_id', $user_id)
                        ->set('type', 1)
                        ->set('country', Arr::get($_POST, 'correspCountry'))
                        ->set('state', Arr::get($_POST, 'correspondState'))
                        ->set('city', Arr::get($_POST, 'correspondCity'))
                        ->set('address', Arr::get($_POST, 'correspondAddress'))
                        ->set('location', 1)
                        ->set('zip', Arr::get($_POST, 'correspondPostal'));
                $user_address->create();
            } catch (ORM_Validation_Exception $e) {
                $user->delete();
                $user_detail->delete();
                $contacts = ORM::factory('user_contact')
                        ->where('user_id', '=', $user_id);
                
                foreach ($contacts as $contact)
                  $contact->delete();
                
                $errors_mess = $this->createErrorsMessage($e->errors('models'));
                
                $this->response->body(json_encode(array("success" => FALSE,'errors_mess' => $errors_mess)));
                return;
            }
            try {
                //--------------------user_education---------------------------
                $user_education = ORM::factory('user_education')
                        ->set('user_id', $user_id)
                        ->set('name', Arr::get($_POST, 'currentStudyName'))
                        ->set('date_entered', Arr::get($_POST, 'currentStudyEntered'))
                        ->set('major', Arr::get($_POST, 'currentStudyMajor'))
                        ->set('degree_expected', Arr::get($_POST, 'currentStudyDiplom'))
                        ->set('prev_degree', Arr::get($_POST, 'prevDegree'))
                        ->set('date_expected', Arr::get($_POST, 'currentStudyExpected'))
                        ->set('location', Arr::get($_POST, 'currentStudyLocation'));

                $user_education->create();
            } catch (ORM_Validation_Exception $e) {
                $user->delete();
                $user_detail->delete();
                $contacts = ORM::factory('user_contact')
                  ->where('user_id', '=', $user_id);

                foreach ($contacts as $contact)
                  $contact->delete();
                
                $user_address->delete();

                $errors_mess = $this->createErrorsMessage($e->errors('models'));

                $this->response->body(json_encode(array("success"     => FALSE, 'errors_mess' => $errors_mess)));
                return;
            }
            try {
                //----------------user_work_experience--------------------------     

                $user_work_experience = ORM::factory('user_experience')
                        ->set('user_id', $user_id)
                        ->set('employer_name', Arr::get($_POST, 'currentEmpName'))
                        ->set('current_status', Arr::get($_POST, 'currEmployed'))
                        ->set('job_title', Arr::get($_POST, 'currentJobTitle'))
                        ->set('employed_from', Arr::get($_POST, 'currentEmpFrom'))
                        ->set('employed_to', Arr::get($_POST, 'currentJobTo'))
                        ->set('city', Arr::get($_POST, 'currentEmpCity'))
                        ->set('country_id', Arr::get($_POST, 'currentEmpCountry'))
                        ->set('job_description', Arr::get($_POST, 'currentEmpDesc'))
                        ->set('comments', Arr::get($_POST, 'currentEmpComments'));
                        
                $user_work_experience->create();
            } catch (ORM_Validation_Exception $e) {
                $user->delete();
                $user_detail->delete();
                $contacts = ORM::factory('user_contact')
                  ->where('user_id', '=', $user_id);

                foreach ($contacts as $contact)
                  $contact->delete();
                
                $user_address->delete();

                $errors_mess = $this->createErrorsMessage($e->errors('models'));

                $this->response->body(json_encode(array("success"     => FALSE, 'errors_mess' => $errors_mess)));
                return;
            }
            $this->response->body(json_encode(array("success" => TRUE,"userId" => $user_id)));
        }
    }
        
    public function action_enroll_filter() {
        $filter_name = Arr::get($_GET, 'filter');
        if ($filter_name == Session::instance()->get('select_filter')) {
            Session::instance()->set("select_filter", false);
        } else {
            Session::instance()->set("select_filter", $filter_name);
        }
        $this->action_index();
    }
    
    //============AJAX REQUESTS PROCESSING======================================================================


    protected function get_students($p_nPerPage = 1, $p_nOffset = 0, $p_bAsOrmObjects = FALSE) {
        $orm_users = ORM::factory("user");
        //if this is an initial request we set first row as selected(store this in session)
        $orm_users = $orm_users->where($this->strFilterColumn, "LIKE", "%" . $this->strFilterValue . "%")->order_by($this->strSortedColumn, $this->strSortedMethod)->limit($p_nPerPage)->offset($p_nOffset)->find_all();

        $arrReturn = array();
        foreach ($orm_users as $orm_user) {
            /* $orm_user->details; */
            /* $orm_user->address; */
            /* $orm_user->locations; */
            $arrReturn[$orm_user->id] = ($p_bAsOrmObjects) ? $orm_user : $orm_user->as_array();
        }
        if ($this->filter) {
            $user_enrolls = $this->get_students_enrollment();
            $arrUser_enroll = array();
            Session::instance()->get("select_filter")=='total'?$select_filter_enroll = $user_enrolls['enrolled']:$select_filter_enroll = $user_enrolls['last_week_enrolled'];
            foreach ($select_filter_enroll as $user_enroll) {
                $arrUser_enroll[$user_enroll->id] = ($p_bAsOrmObjects) ? $user_enroll : $user_enroll->as_array();
            }
            $user_enrolls = ORM::factory('user')->where('id', 'IN', array_keys($arrUser_enroll))->order_by($this->strSortedColumn, $this->strSortedMethod)->limit($p_nPerPage)->offset($p_nOffset)->find_all();
            $arrReturn = array();
            foreach ($user_enrolls as $user_enroll) {
                $arrReturn[$user_enroll->id] = ($p_bAsOrmObjects) ? $user_enroll : $user_enroll->as_array();
            }
        }
        return $arrReturn;
    }
               
    protected function get_students_enrollment($p_bAsOrmObjects = FALSE) {
        $orm_users = ORM::factory("user_detail");
        $student_enrolled = $orm_users->where('enrollment_date', '!=', null)->find_all();
        $new_date = strtotime(date("d-m-Y"));
        $last_week = $new_date - 604800;
        $student_last_week_enrolled = $orm_users->where('enrollment_date', '>', $last_week)->find_all();
        $arrReturn = array();
        $arrReturn['enrolled'] = $student_enrolled;
        $arrReturn['last_week_enrolled'] = $student_last_week_enrolled;
        return $arrReturn;
    }

    /**
     * method to get/set sorting parameters from/in session
     * @return void
     */
    protected function sorting($p_strSortColumn = NULL, $p_strSortMethod = NULL) {
        if ($p_strSortColumn !== NULL && $p_strSortMethod !== NULL) {
            Session::instance()->set("sort_column", $p_strSortColumn);
            Session::instance()->set("sort_method", $p_strSortMethod);
        }
        $this->strSortedColumn = Session::instance()->get("sort_column");
        $this->strSortedMethod = Session::instance()->get("sort_method");
        return;
    }

    /**
     * method to get/set currently selected user from/in session
     * @return void
     */
    protected function current_user($p_strUserId = NULL) {
        if ($p_strUserId !== NULL) {
            Session::instance()->set("selected_user", $p_strUserId);
        }
        $orm_Users = ORM::factory("user");
        $strUserId = Session::instance()->get("selected_user");
        $this->orm_selectedUser = $orm_Users->where("id", "=", $strUserId)->find();
        return FALSE;
    }

    /**
     * method to get/set sorting parameters from/in session
     * @return void
     */
    protected function filtering($p_strFilterColumn = NULL, $p_strFilterValue = NULL) {
        if ($p_strFilterColumn !== NULL && $p_strFilterValue !== NULL) {
            Session::instance()->set("filter_column", $p_strFilterColumn);
            Session::instance()->set("filter_value", $p_strFilterValue);
        }
        $this->strFilterColumn = Session::instance()->get("filter_column");
        $this->strFilterValue = Session::instance()->get("filter_value");
        return;
    }

    protected function format_results_for_grid_view(&$p_arrData) {
        if (is_array($p_arrData)) {
            foreach ($p_arrData as &$p_arrNode) {
                self::format_results_for_grid_view($p_arrNode);
            }
            unset($p_arrNode);
        } else {
            if ($p_arrData === NULL || $p_arrData === "")
                $p_arrData = "&nbsp";
        }
    }
}
