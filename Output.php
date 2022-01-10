<?php  

class Output {


	public function clean($input){
		return trim($input);
	}
	
	
	public function show($message){
		echo "| ".$message."  \n";
	}
	
	public function showLine($i = 0){
		if($i == 1) 
			echo "\n\n\n------------------------------------------------------------\n";
		else 
			     echo  "------------------------------------------------------------\n";
	}
	
	
	public function error($error){
		echo "\n";
		echo "------------------------------------------------- \n";
		echo "| ".$error;
		echo "\n------------------------------------------------- \n\n\n";
		die();
	}
	
	


}