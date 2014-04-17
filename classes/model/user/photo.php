<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User_photo extends ORM {
	const STATUS_DELETED =		0x01;
	const STATUS_FEATURED =		0x02;

	const SIZE_THUMBNAIL =			0x01;
	const SIZE_SMALL =				0x05;
	const SIZE_MEDIUM =				0x02;
	const SIZE_BIG =				0x03;
	const SIZE_FULL =				0x04;

	protected $_table_name = "user_photos";

	protected $_belongs_to = array(
			'user' => array(
            "model" => "user",
            "foreign_key", "id"
         )
	);

	protected $sizes = array(
			self::SIZE_THUMBNAIL => "thumbnail",
			self::SIZE_SMALL => "small",
			self::SIZE_MEDIUM => "medium",
			self::SIZE_BIG => "big",
			self::SIZE_FULL => ""
	);


	public function addPhoto($user_id, $file, $description) {
		$featured = ($this->count_all()==0)?self::STATUS_FEATURED:0;
		
		//get new image name
		$file_name = explode(DIRECTORY_SEPARATOR, $file);
		$file_name = array_pop($file_name);

		list($id, $total_rows_affected) = DB::insert('user_photos', array("id", "user_id", "file", "description", "date", "status"))
		->values(array(null, $user_id, $file_name, $description, time(), $featured))
		->execute();
		
		//make thumb image
		$image = new Kohana_Image_Imagick($file);
		$image->resize(800, 600)
		->save($this->getStore($id, self::SIZE_FULL, true).$file_name);
		$iw = $image->width;
		$ih = $image->height;
		
		$image->resize(150, 150)
		->save($this->getStore($id, self::SIZE_THUMBNAIL, true).$file_name);
		unlink($file);
		
		if($iw<$ih) $ih=$iw;
		else $iw = $ih;
		
		if($featured) $this->setFeatured($id, null, null, $iw, $ih);
		return $this;
	}

	function delete(){
		if($this->loaded()) {
			if($this->isFeatured()) {
				throw new Kohana_Exception(__("Can't delete featured photo"));
			}
			$this->status |= self::STATUS_DELETED;
			$this->save();
		} else {
			throw new Kohana_Exception(__("Photo not loaded"));
		}
		return $this->clear();
	}

	function get($id){
      return $this->where('id', "=", $id)->find();
	}

	function enabled() {
		return $this->where('status', '&'.(self::STATUS_DELETED).'=', 0);
	}

	function featured() {
		return $this->where('status', '&'.(self::STATUS_FEATURED).'=', self::STATUS_FEATURED);
	}

	function getPhotoUrl($size=self::SIZE_BIG) {
		return $this->getURL($size);
	}
	
	function getURL($size=self::SIZE_BIG) {
		if(!$this->loaded()) throw new Kohana_Exception(__("No photo loaded"));
		if($size===true) {
			$ret = array();
			foreach(array_keys($this->sizes) as $v) {
				$ret[$v] = $this->getURL($v);
			}
			return $ret;
		}
		return $this->getStore($this->id, $size).$this->file;
	}
	
	function setFeatured($id, $x, $y, $w, $h) {
		$curr = clone $this;
		
		if(!$this->get($id)->loaded()) throw new Kohana_Exception(__("Image #:id not found", array(":id"=>$id)));
		
		if($curr->featured()->find()->loaded()) {
			$curr->status = $curr->status ^ self::STATUS_FEATURED;
			$curr->save();
		}
		unset($curr);
		
		$this->status = $this->status | self::STATUS_FEATURED;
		$this->save();

		$this->getImage(self::SIZE_FULL)
		->resize(255, null)
		->save($this->getStore($this->id, self::SIZE_BIG, true).$this->file);

		$this->getImage(self::SIZE_FULL)
		->crop($w, $h, $x, $y)
		->resize(100, 100)
		->save($this->getStore($this->id, self::SIZE_MEDIUM, true).$this->file);
		
		$this->getImage(self::SIZE_MEDIUM)
		->resize(63, 63)
		->save($this->getStore($this->id, self::SIZE_SMALL, true).$this->file);

		return $this;
	}

	function getImage($size){
		if(!$this->loaded()) {
			throw new Kohana_Exception(__("No photo loaded"));
		}
		return Image::factory($this->getStore($this->id, $size).$this->file, "imagick");
	}

	public function isDeleted() {
		return ($this->status & self::STATUS_DELETED) != 0;
	}

	public function isFeatured() {
      return ($this->status & self::STATUS_FEATURED) != 0;
	}

	public function user_available($id) {
		return ORM::factory('user', $id)->loaded();
	}

	public function rules() {
		return array(
				'user_id' => array(
						array('not_empty'),
						array('numeric'),
						array(array($this, 'user_available')),
				),
				'file' => array(
						array('not_empty'),
						array('max_length', array(":value", 160))
				),
				'description' => array(
						array('max_length', array(":value", 250))
				),
				'date' => array(
						array('not_empty'),
						array('numeric')
				),
				'status' => array(
						array('not_empty'),
						array('numeric')
				)
		);
	}

	public function filters() {
		return array(
				'user_id' => array(
						array('intval')
				),
				'file' => array(
						array('trim')
				),
				'description' => array(
						array('trim')
				),
				'date' => array(
						array('intval')
				),
				'status' => array(
						array('intval')
				)
		);
	}

	function rotate($clockwise) {
		if(!$this->loaded()) {
			throw new Kohana_Exception(__("No photo loaded"));
		}
		$direction = ($_POST['direction'] == 'clockwise') ? 90 : -90;
		foreach(array_keys($this->sizes) as $size) { 
			$this->getImage($size)->rotate($direction)->save();
		}
	}
	
	public function getStore($id, $size=null, $autocreate=false) {
		if(!array_key_exists($size, $this->sizes)) throw new Kohana_Exception(__("Bad image size: :size", array(":size"=>$size)));
		$str = strval($id);
		if (strlen($str) % 2 != 0)
			$str = "0" . $str;
		$dir = "upload".DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, str_split($str, 2));
		if($size) $size = $this->sizes[$size];
		$dir .= (empty($size)?"":DIRECTORY_SEPARATOR) . $size . DIRECTORY_SEPARATOR;
		if($autocreate and !file_exists(DOCROOT.$dir)) {
			$oldumask = umask(0);
			mkdir(DOCROOT.$dir, 0777, true);
			umask($oldumask);
		}
		return $dir;
	}
}
