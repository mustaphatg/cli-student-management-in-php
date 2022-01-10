<?php 

class Store{

	private $id;
	private $name;
	private $surname;
	private $age;
	private $curiculum;
	
	public $output;
	public $all_id;
	public $all_json;
	
	
	function __construct(){
		$this->output = new Output();
		$this->all_id = [];
		$this->all_json = [];
	}
	
	public function isUnique($id){
		
		$this->getAllId();
		
		if(in_array($id, $this->all_id)){
			return False;
		}else{
			return True;
		}
		
	}
	
	
	
	public function getAllId(){
		$d = scandir("students");
		
		$d = $this->removeFolders($d);
		
		
		foreach($d as $ar){
			$sub_dir = scandir("students/{$ar}");
			$files = $this->removeFolders($sub_dir);
			
			foreach($files as $fi){
				$this->all_id[] = str_replace(".json", "", $fi);
				$this->all_json[] = "students/{$ar}/{$fi}";
			}
			
		}
	} // end of getAllId
	
	
	public function removeFolders($arr){
		array_shift($arr); // remove the . folder
		array_shift($arr); // remove the  .. folder
		
		return $arr;
	}
	
	
	public function save($data){
		$id = $data["id"];
		$folder = preg_match("/(\d{2})/", $id, $ma);
		$folder = $ma[1];  // "first two digits"
		
		// check if directory is available 
		$dir = "students/{$folder}";
		if(is_dir($dir)){
			$op = fopen("{$dir}/{$id}.json", "w");
			$json = json_encode($data);
			
			if(fwrite($op, $json)){
				$this->output->showLine(1);
				$this->output->show("  Student record has been saved");
				$this->output->showLine();
				$this->output->show($json);
			}
		}
		else{
		
			//directory not available, create a new one
			$dir = "students/{$folder}";
			mkdir($dir);   // create directory
			
			$op = fopen("{$dir}/{$id}.json", "w");
			$json = json_encode($data);
			
			if(fwrite($op, $json)){
				$this->output->showLine(1);
				$this->output->show("  Student record has been saved");
				$this->output->showLine();
				$this->output->show($json);
			}
			
		}
		
	}

	
	public function search($arg, $value){
		$this->getAllId();
		
		$seek = [];
		$ids = [];
		$names = [];
		$snames = [];
		$ages = [];
		$curs = [];
		
		foreach($this->all_json as $js){
			$f = file_get_contents($js);
			$obj = json_decode($f);
			
			$seek[] = $js;
			$ids[] = $obj->id;
			$names[] = $obj->name;
			$snames[] = $obj->surname;
			$ages[] = $obj->age;
			$curs[] = $obj->curriculum;
		}
		
		$value = strtolower($value);
		switch($arg){
			case "id":
				if(in_array($value, $ids)){
					$key = array_search($value, $ids);
					return $seek[$key];
				}
				else{
					$this->output->error("There is no student with the id you enteted.");
				}
				break;
			case "name":
				if(in_array($value, $names)){
					$key = array_search($value, $names);
					return $seek[$key];
				}
				else{
					$this->output->error("There is no student with the name you enteted.");
				}
				break;
			case "surname":
				if(in_array($value, $ids)){
					$key = array_search($value, $snames);
					return $seek[$key];
				}
				else{
					$this->output->error("There is no student with the surname you enteted.");
				}
				break;
			case "age":
				if(in_array($value, $ages)){
					$key = array_search($value, $ages);
					return $seek[$key];
				}
				else{
					$this->output->error("There is no student with the age you enteted.");
				}
				break;
			case "curriculum":
				if(in_array($value, $curs)){
					$key = array_search($value, $curs);
					return $seek[$key];
				}
				else{
					$this->output->error("There is no student with the curiculum you enteted.");
				}
				break;
		}
		
		
	} // end search function
	
	
	public function isAvailable($id){
		$this->getAllId();
		
		if(in_array($id, $this->all_id)){
			return True;
		}else{
			return False;
		}
	}
	
	
	public function delete($id){
		$this->getAllId();
		
		$a = "";
		foreach($this->all_json as $file){
			$a = $file;
			if(preg_match("/$id/", $a)){
				break;
			}
		}
		
		unlink($a);
		$this->output->showLine(1);
		$this->output->show("The record has been deleted");
		$this->output->showLine();
		
	}  // end of delete
	
	
	public function getJson($id){
		$this->getAllId();
		
		$a = "";
		foreach($this->all_json as $file){
			$a = $file;
			if(preg_match("/$id/", $a)){
				return $a;
			}
		}
		
		
	} // end of edit
	
	// used when the search criteria is left blank
	public function searchAll(){
		$this->getAllId();
		
		return $this->all_json;
	}
	

}


?>