<?php

defined('SYSPATH') or die('Restricted access!');

/**
 * Description of roles
 *
 * @author rlatyshenko
 */
class Controller_Roles extends Controller_Role_admin {

	private static $defined_rules = array();

	const ACL_RULES = "ACL_Rules";
	const ACL_URL_RULES = "ACL_Url_Rules";

	public function before()
	{
		parent::before();
		if($this->request->is_ajax())
			$this->auto_render = false;

		foreach (array(self::ACL_RULES, self::ACL_URL_RULES) as $class) {
			$klass = new ReflectionClass($class);
			$methods = $klass->getMethods();
			$methods = $this->_clean($methods);
			foreach ($methods as $key => $method) {
				self::$defined_rules[$class][] = $method->name;
			}
		}
	}

	public function action_index()
	{
		$data  = array();
		$roles = array();
		$ids   = array();

		$roles[] = array("id" => ACL::UNLOGGED, "name" => "unlogged");
		$roles[] = array("id" => ACL::ALL, "name" => "all");

		foreach(ORM::factory("role")->find_all() as $role){
			$roles[] = array("id" => $role->id, "name" => $role->name);
			$ids[]   = $role->id;
		}


		$pages_list = DB::select("description", "page")
		->from("access_rules_url")
		->distinct(TRUE)
		->execute()
		->as_array();

		$rules = ORM::factory("ruleurl")->find_all()->as_array();
		$matrix = array();
		foreach ($rules as $rule) {
			if($rule->rule == "access_granted")
				$matrix[$rule->page][] = $rule->role_id;
		}
		$list                    = View::factory("roles/list", array("roles" => $roles));
		$this->template->content = View::factory("roles/index", 
			array("pages" => $pages_list, 
				"list" => $roles, 
				"defined_rules" => array("url" => self::$defined_rules[self::ACL_URL_RULES]),
				"matrix" => $matrix));
	}

	public function action_edit_rule(){
		$id = $this->request->param("id");

		$rule = ORM::factory("rule", $id);

		foreach ($this->request->post() as $key => $value) {
			$result[str_replace("rule_", "", $key)] = $value;
		}
		$rule->rules = json_encode($result);

		return $rule->save();
	}

	public function action_save_rules()
	{

		$rules = $this->request->post("rule");
		if(isset($rules["granted"])){
			foreach($rules["granted"] as $rule){
				$rule = explode("|", $rule);
				$model = ORM::factory("ruleurl")
				->where("page", "=", $rule[0])
				->and_where("role_id", "=", $rule[1])
				->find();
				if($model->loaded()){
					$model
					->set("rule", "access_granted")
					->save();
				} else {
					ORM::factory("ruleurl")
					->set("page", $rule[0])
					->set("role_id", $rule[1])
					->set("description", $rule[2])
					->set("rule", "access_granted")
					->save();
				}
			}
		}
		if(isset($rules["denied"])){
			foreach($rules["denied"] as $rule){
				$rule = explode("|", $rule);
				$model = ORM::factory("ruleurl")
				->where("page", "=", $rule[0])
				->and_where("role_id", "=", $rule[1])
				->find();
				if($model->loaded()){
					$model
					->set("rule", "access_denied")
					->save();
				} else {
					ORM::factory("ruleurl")
					->set("page", $rule[0])
					->set("role_id", $rule[1])
					->set("description", $rule[2])
					->set("rule", "access_denied")
					->save();
				}
			}
		}
		$this->request->redirect($this->request->referrer());
	}

	public function action_get_rules()
	{
		$id        = $this->request->param("id");		
		$rules     = ORM::factory("rule")->where("role_id", "=", $id)->find_all()->as_array();
		$rules_url = ORM::factory("ruleurl")->where("role_id", "=", $id)->find_all()->as_array();
		$data      = array("elements" => array(), "pages" => array());

		foreach ($rules as $rule) {
			$data["elements"][] = array("id" => $rule->id, "page" => $rule->page, "rules" => json_decode($rule->rules));
		}
		foreach ($rules_url as $rule) {
			$data["pages"][] = array("id" => $rule->id, "page" => $rule->page, "rule" => $rule->rule);
		}
		
		$data["defined_rules"] = array("url" => self::$defined_rules[self::ACL_URL_RULES], "elements" => self::$defined_rules[self::ACL_RULES]);

		echo View::factory("roles/rules", $data)->render();

	}

	public function action_elements_rules(){

	}

	private function _clean($methods){
		foreach ($methods as $key => $method) {
			if($method->name == "__callStatic"){
				unset($methods[$key]);
			}
		}
		return $methods;
	}
}