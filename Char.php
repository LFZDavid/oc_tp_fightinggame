<?php

class Char{
	private $_id;
	private $_name;
	private $_damages;
	private $_exp;
	private $_level;

	const MOI =1;
	const PERSO_TUE =2;
	const PERSO_FRAPPE =3;

// CONSTRUCTEUR
	public function __construct(array $data){
		$this->hydrate($data);
	}

// HYDRATATION
	public function hydrate($data){
		foreach ($data as $key => $value){

			$method = 'set'.ucfirst($key);

			if (method_exists($this, $method)){
				$this->$method($value);
			}

		}
	}

// METHOD
	public function hit(Char $char){

		if ($char == $this->name()){
			return self::MOI;
		}
		else{
			$this->gainExp(5);
			return $char->receveDamages();
		}
	}

	public function receveDamages(){

		$this->_damages +=5;

		if ($this->_damages >= 100){
			return self::PERSO_TUE;
		}
		else{
			return self::PERSO_FRAPPE;
		}
	}

	public function gainExp($xp){
		$xp = (int) $xp;
		$this->_exp += $xp;

		if($this->_exp >= 100){
			$this->setExp(0);
			$this->levelUp();
		}
	}

	public function levelUp(){
		$this->_level ++;
	}



// GETTERS
	public function id(){
		return $this->_id;
	}
	public function name(){
		return $this->_name;
	}
	public function damages(){
		return $this->_damages;
	}
	public function exp(){
		return $this->_exp;
	}
	public function level(){
		return $this->_level;
	}
// SETTERS
	public function setId($id){
		$id = (int) $id;

		if ($id > 0){

			$this->_id = $id;
		}
	}
	public function setName($name){
		if (is_string($name)){
			$this->_name = $name;
		}
	}
	public function setDamages($damages){
		$damages = (int) $damages;
		if ($damages > 0 && $damages <= 100){
			$this->_damages = $damages;
		}
	}
	public function setExp($exp){
		$exp = (int) $exp;
		if ($exp >= 0 && $exp <= 100){
			$this->_exp = $exp;
		}
	}
	public function setLevel($level){
		$level = (int) $level;
		if ($level > 1 && $level <= 100){
			$this->_level = $level;
		}
	}
}