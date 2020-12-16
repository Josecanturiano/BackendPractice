<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
define('__ROOT__', dirname(dirname(__FILE__))); 

include_once(__ROOT__.'/config/db.php');
include_once(__ROOT__.'/models/contact_model.php');
  
// instantiate database and contact object
$database = new Database();
$db = $database->getConnection();
  
// initialize object
$contact = new Contact($db);

// query contact
$stmt = $contact->read();
$num = $stmt->rowCount();
  
// check if more than 0 record found
if($num>0){
  
    // contact array
    $contact_arr=array();
    $contact_arr["records"]=array();
    $phones_arr=array();

    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
        
        $nestedStatement = $contact->getPhoneNumbers($Id);
        $nestedNum = $nestedStatement->rowCount();
        
        if($nestedNum>0){
            $phones_arr=array();
            while ($nestedRow = $nestedStatement->fetch(PDO::FETCH_ASSOC)){
                extract($nestedRow);
                $contact_phone=array(
                    "number" => $Numero
                );  
                array_push($phones_arr, $contact_phone);
            };            
        }else{
            $phones_arr=array();
        }

        $contact_item=array(
            "id" => $Id,
            "name" => $Nombre,
            "lastname" => $Apellido,
            "email" => $Email,
            "phoneNumbers" => $phones_arr,
        );        
            
        array_push($contact_arr["records"], $contact_item);
    }
  
    // set response code - 200 OK
    http_response_code(200);
  
    // show contact data in json format
    echo json_encode($contact_arr);
}else{
  
    // set response code - 404 Not found
    http_response_code(404);
  
    // tell the user no contact found
    echo json_encode(
        array("message" => "No contact found.")
    );
} 
?>
  