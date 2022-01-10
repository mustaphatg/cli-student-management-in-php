<?php 


class Student {

	public $argv;
	public $output;
	public $store;
	
	function __construct($arg){
		
		$this->argv = $arg;
		$this->output = new Output();
		$this->store = new Store();
		
		$c = count($arg);
		
		if($c == 1){
			$this->output->error(" The minimum argument required is 1 \n| Try one of these : \n| php run.php --action=add \n| php run.php --action=edit \n| php run.php --action=delete \n| php run.php --action=search");
		}
		else if($c >= 2){
	
			$argument = $arg[1];
			parse_str($argument, $act);
			
			
			
			if(array_key_exists("--action", $act)){
				$a = $act["--action"];
			}else{
				$this->output->error("The action you specified does not exist. \n| Try one of these : \n| php run.php --action=add \n| php run.php --action=edit \n| php run.php --action=delete \n| php run.php --action=search");
			}
			
			$actions = ["add","edit", "delete", "search"];
			
			if(in_array($a, $actions)){
			
				switch($a){
					case "add":
						$this->add() ;
						break;
					case "search":
						$this->search() ;
						break;
					case "edit":
						$this->edit() ;
						break;
					case "delete":
						$this->delete() ;
						break;
				}
				
			}else{
				$this->output->error(" The action you specified does not exist") ;
			}
		}

	}
	
	
	public function getId($w){
		if(count($this->argv) == 3 && $this->argv[2] != null){
			$idd = $this->argv[2];
			parse_str($idd, $gid);
			
			if(!array_key_exists("--id", $gid)){
				$this->output->error("$w require a compulsory --id argument. \n| Eg. --action=delete  --id=1234567") ;
			}else{
				$id = $gid["--id"];
				return $id;
				
			}
		}
		else{
			// no id argument
			$lower = strtolower($w);
			$this->output->error("$w require a compulsory --id argument. \n| Eg. --action=$lower  --id=1234567") ;
		}
		
	}
	
	
	
	public function add(){
		
		$this->output->showLine(1) ;
		$this->output->show("  Add A New Student") ;
		$this->output->show("  Note: student Id must be a unique 7 integer") ;
		$this->output->showLine() ;
		
		
		$id = readLine("Enter Student Id: ");
		
		// id should be 7
		while(strlen($id) != 7){
			$this->output->showLine(1) ;
			$this->output->show("  Student Id should be 7 integers") ;
			$this->output->showLine() ;
			$id = readLine("Enter Student Id: ");
		}
		
		// unique Id
		while(!$this->store->isUnique($id)){
			$this->output->showLine(1) ;
			$this->output->show("  A student with that ID already exist.") ;
			$this->output->showLine() ;
			$id = readLine("Enter Student Id: ");
		}
		
		
		// first name
		$name = readLine("Enter Name: ");
		while(!$name){
			$this->output->showLine(1) ;
			$this->output->show("  Name should not be empty.") ;
			$this->output->showLine() ;
			$name = readLine("Enter Name: ");
		}
		
		// surname
		$surname = readLine("Enter Surname: ");
		while(!$surname){
			$this->output->showLine(1) ;
			$this->output->show("  Surname should not be empty.") ;
			$this->output->showLine() ;
			$surname = readLine("Enter Surname: ");
		}
		
		// age
		$age = readLine("Enter studen's age: ");
		while(!$age){
			$this->output->showLine(1) ;
			$this->output->show("  Age should not be empty.") ;
			$this->output->showLine() ;
			$age = readLine("Enter studen's age: ");
		}
		
		
		// curriculum
		$curriculum = readLine("Enter Curriculum: ");
		while(!$curriculum){
			$this->output->showLine(1) ;
			$this->output->show("  Curriculum should not be empty.") ;
			$this->output->showLine() ;
			$curriculum = readLine("Enter Curriculum: ");
		}
		
		
		// sanitize data and prepare to store
		$data["id"] = (int) $this->clean($id);
		$data["name"] = $this->clean($name);
		$data["surname"] = $this->clean($surname);
		$data["age"] = (int) $this->clean($age);
		$data["curriculum"] = $this->clean($curriculum);
		
		$this->store->save($data);
	} // end of add
	
	
	
	public function edit(){
		
		$id = $this->getId("Edit") ;
		
		if(!$this->store->isAvailable($id)){
			$this->output->error("The Id you wish to edit does not exist.") ;
		}
		
		$json = $this->store->getJson($id);
		$j = file_get_contents($json);
		$obj = json_decode($j);
		
		$this->output->showLine(1);
		$this->output->show("Editing record with id {$id}. \n| Leave the field blank to keep previous value.");
		$this->output->showLine();
		
		$name = $obj->name;
		$sname = $obj->surname;
		$age = $obj->age;
		$cur = $obj->curriculum;
		
		//name
		$name = readLine("Enter Name [$name]: ");
		if($name) { $obj->name = $this->clean($name); }
		
		//surname
		$sur_name = readLine("Enter Surname [$sname]: ");
		if($sur_name) { $obj->surname = $this->clean($sur_name); }
		
		//age
		$new_age = readLine("Enter Age [$age]: ");
		if($new_age) { $obj->age = $this->clean($new_age); }
		
		// curriculum
		$new_cur = readLine("Enter Curriculum [$cur]: ");
		if($new_cur) { $obj->curriculum = $this->clean($new_cur); }
		
		// upadte the json file
		$dec = json_encode($obj);
		file_put_contents($json, $dec);
		
		$this->output->showLine(1);
		$this->output->show("Student with id {$id} has been updated");
		$this->output->showLine();
		$this->output->show($dec);

	
	}
	
	public function delete(){
	
		$id = $this->getId("Delete") ;
		
		if(!$this->store->isAvailable($id)){
			$this->output->error("The Id you entered does not exist.") ;
		}
		
		$this->store->delete($id);

	}
	
	
	public function search(){
		$this->output->showLine(1);
		$this->output->show("Enter search criteria: \n| Must be one of: \n| 1. --id=? \n| 2. --name=? \n| 3. --surname=? \n| 4. --age=? \n| 5. --curriculum=?");
		$this->output->showLine();
	
		$c = readLine("Enter Criteria: ");
		if(!$c || $c == ""){
			$this->allStudents();
			return;
		}
		
		parse_str($c, $arg);
		
		$student = "";
		
		if(array_key_exists("--id", $arg)){
			$student = $this->store->search("id", $arg["--id"]);
		}
		
		if(array_key_exists("--name", $arg)){
			$student = $this->store->search("name", $arg["--name"]);
		}
		
		if(array_key_exists("--surname", $arg)){
			$student = $this->store->search("surname", $arg["--surname"]);
		}
		
		if(array_key_exists("--age", $arg)){
			$student = $this->store->search("age", $arg["--age"]);
		}
		
		if(array_key_exists("--curriculum", $arg)){
			$student = $this->store->search("curriculum", $arg["--curriculum"]);
		}
		
		if($student == ""){
			$this->output->error("The search criteria you entered is not recognized.");			
		}
		
		
		$file = file_get_contents($student);
		$obj = json_decode($file);
		
		$id = $obj->id;
		$name = ucfirst($obj->name);
		$surname = ucfirst($obj->surname);
		$age = $obj->age;
		$cur = ucfirst($obj->curriculum);
		
		$this->output->showLine(1);
		$mask = "|%10.10s | %-10.10s | %-10.10s | %-5.5s | %-10.10s |\n";
		printf($mask, "Id", "Name", "Surname", "Age", "Curriculum");
		printf($mask, $id, $name, $surname, $age, $cur);
		$this->output->showLine();
		
	}
	
	
	public function allStudents(){
		$s = $this->store->searchAll();
		$cc = count($s);
		
		if($cc == 0){
			$this->output->showLine(1);
			$this->output->show("No student record exist. ");
			$this->output->showLine();
			return;
		}
		
		$this->output->showLine(1);
		$this->output->show("             All Students  ({$cc})");
		$this->output->showLine();
		$mask = "|%10.10s | %-10.10s | %-10.10s | %-5.5s | %-15.15s |\n";
		printf($mask, "Id", "Name", "Surname", "Age", "Curriculum");

		
		foreach($s as $a){
			$file = file_get_contents($a);
			$obj = json_decode($file);
			
			$id = $obj->id;
			$name = ucfirst($obj->name);
			$surname = ucfirst($obj->surname);
			$age = $obj->age;
			$cur = ucfirst($obj->curriculum);
			
			$this->output->showLine();
			printf($mask, $id, $name, $surname, $age, $cur);
		}
		
			$this->output->showLine();
	}
	
	public function clean($input){
		return trim(strtolower($input));
	}
	

}




?>