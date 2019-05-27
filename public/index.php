<?php
//Display errors.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//set json content type
header('Content-Type: application/json');

//example class for json return
class Example {
	public $message; //must be public for json to encode and return it.
	
	//constuctor
	public function __construct(string $message) {
		$this->message = $message; //set test variable
	}
}

$obj = new Example('Hello world!'); //create object

if($_GET['phpinfo'])
    phpinfo();
else
    echo json_encode($obj); //retun json encoded object to user,

//By Jari Ketting