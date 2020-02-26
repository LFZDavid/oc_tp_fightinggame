<?php

class Char{
	private $_id;
	private $_name;
	private $_damages;
	private $_exp;
	private $_level;
	private $_dps;
	private $_hitcount;
	private $_lasthit;

	

	const MOI =1;
	const PERSO_TUE =2;
	const PERSO_FRAPPE =3;
	const PLUS_DE_FRAPPE =4;

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
		elseif ($this->hitcount() >= 3) {
			$this->resetCounterHit();
			if($this->hitcount() >=3){
				return self::PLUS_DE_FRAPPE;
			}

		}
		else{
			$this->gainExp(5);
			$this->countOneHit();
			$this->timeOfLastHit();
			return $char->receveDamages($this->dps());
		}
	}

	public function receveDamages($damages){

		$this->_damages = $this->_damages + $damages;

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
		$this->_dps ++;
	}

	public function countOneHit(){
		$this->_hitcount ++ ;
	}

	public function timeOfLastHit(){
		$this->_lasthit = date("H");
	}

	public function resetCounterHit(){
		if ($this->lasthit() < (date("H"))){
			
			$this->setHitcount(0);
			$this->timeOfLastHit();
		}
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
	public function dps(){
		return $this->_dps;
	}
	public function hitcount(){
		return $this->_hitcount;
	}
	public function lasthit(){
		return $this->_lasthit;
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
		if ($level >= 1 && $level <= 100){
			$this->_level = $level;
		}
	}
	 public function setDps($dps){
	 	$dps = (int) $dps;
	 	if ($dps >= 1 && $dps <= 100){
	 		$this->_dps = $dps;
	 	}
	 }
	 public function setHitcount($hitcount){
	 	$hitcount = (int) $hitcount;
	 	
	 	if ($hitcount >= 0){
	 		$this->_hitcount = $hitcount;
	 	}
	 }
	 public function setLasthit($lasthit){
	 	$this->_lasthit = $lasthit;
	 }
}