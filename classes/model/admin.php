<?php
/**
 * Admin Model
 * @author prophet
 *
 */
class Admin_Model extends Database_Core
{
	private $db;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function __construct()
	{
		parent::__construct();
		$this->db = Database::instance();
		
	}
	
	
	/**
	 * Вернуть список всех курсов
	 */
	public function get_courses() {
		
		return $this->db->select('*')
		                ->from('courses')
		                ->orderby('name', 'ASC')
		                ->get()->result(TRUE);
		
	}
	
	public function get_majors() {
		
		return $this->db->select('*')
		                ->from('specialities')
		                ->orderby('name', 'ASC')
		                ->get()->result(TRUE);
		
	}
	
	
	public function get_curriculum($major_id) {
		
		return $this->db->select('course_id, semestr, type, week')
		                ->from('curriculums')
		                ->where('speciality_id='.$major_id.' AND (type=1 OR type=2)')
		                ->orderby('orders', 'ASC')
		                ->get()->result(TRUE);
		
	}
	
	public function get_courses_books($course_id) {
		
		return $this->db->select('*')
		                ->from('books, courses_books')
		                ->where('id=book_id AND course_id='.$course_id)
		                ->orderby('title', 'ASC')
		                ->get()->result(TRUE);
		
	}
	
	public function get_books_combo() {
		
		return $this->db->select('id AS value, title AS text')
		                ->from('books')
		                ->orderby('title', 'ASC')
		                ->get()->result(TRUE);
		
	}
	
	
	public function get_book($book_id) {
		
		return $this->db->select('*')
		                ->from('books')
		                ->where(array('id' => $book_id))
		                ->get()->result(TRUE);
		
	}
	
	public function get_major($major_id) {
		
		return $this->db->select('*')
		                ->from('specialities')
		                ->where(array('id' => $major_id))
		                ->get()->result(TRUE);
		
	}
	
	public function get_course($id) {
		
		return $this->db->select('*')
		                ->from('courses')
		                ->where(array('id' => $id))
		                ->get()->result(TRUE);
		
	}
	
	public function do_delete($table, $id) {
		
		$this->db->from($table)->where(array('id' => $id))->delete();
		
	}
	
	/**
	 * 
	 * Результат записи совпадения текущей даты с датой начала обучения курса
	 */
	public function cron_get_study_start($status, $curdate) {
	
// DEBUG
		return $this->db->select('user_studies.id, email, first_name, middle_name, last_name, username, user_studies.user_id, user_studies.course_id,  date_study_start, date_study_end, name')
				->where('users.id=user_id AND mdl_user_details.id=user_id AND mdl_courses.id=mdl_user_studies.course_id AND status = "'.$status.'" AND DATE(FROM_UNIXTIME('.$curdate.')) = DATE(FROM_UNIXTIME(date_study_start))' )
				->get('users, user_details, user_studies, courses')->result(TRUE);
				
				
//				return $this->db->select('user_studies.id, email, first_name, middle_name, last_name, username, user_studies.user_id, user_studies.course_id,  date_study_start, date_study_end, name')
//				->where('users.id=user_id AND mdl_user_details.id=user_id AND mdl_courses.id=mdl_user_studies.course_id AND status = "'.$status.'" AND CURDATE() = DATE(FROM_UNIXTIME(date_study_start))' )
//				->get('users, user_details, user_studies, courses')->result(TRUE);
		
	}
	
	public function cron_set_status($status, $course, $user) {
		
		$this->db->set(array('status'=>$status))->where(array('course_id'=>$course, 'user_id'=>$user))->update('user_studies');
		
	}
	
	public function cron_get_test_available($status,  $curdate) 
	{

//DEBUG
		return $this->db->select('*')
				->where('user_testresults.status="cur" AND
mdl_user_studies.status="'.$status.'" AND
mdl_user_studies.user_id=mdl_user_testresults.user_id AND 
mdl_user_studies.course_id=mdl_user_testresults.course_id AND
mdl_user_studies.course_id=mdl_courses.id AND
mdl_user_studies.user_id=mdl_users.id AND
mdl_user_details.id=mdl_users.id AND
DATE(FROM_UNIXTIME('.$curdate.')) = DATE(FROM_UNIXTIME(date_of_issue))' )
				->get('user_studies, user_testresults, user_details, users, courses')->result(TRUE);
				
				
//					return $this->db->select('*')
//				->where('user_testresults.status="cur" AND
//mdl_user_studies.status="'.$status.'" AND
//mdl_user_studies.user_id=mdl_user_testresults.user_id AND 
//mdl_user_studies.course_id=mdl_user_testresults.course_id AND
//mdl_user_studies.course_id=mdl_courses.id AND
//mdl_user_studies.user_id=mdl_users.id AND
//CURDATE() = DATE(FROM_UNIXTIME(date_of_issue))' )
//				->get('user_studies, user_testresults, users, courses')->result(TRUE);
	}
	
}	