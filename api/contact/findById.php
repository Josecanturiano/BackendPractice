<?php
    // required headers
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');
    
    // include database and object files
    define('__ROOT__', dirname(dirname(__FILE__))); 

    include_once(__ROOT__.'/config/db.php');
    include_once(__ROOT__.'/models/contact_model.php');
    
    // get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // prepare contact object
    $contact = new Contact($db);
    
    // set ID property of record to read
    $contact->id = isset($_GET['id']) ? $_GET['id'] : die();
    
    // read the details of contact to be edited
    $contact->findById();
    if($contact->name!=null){

        $nestedStatement = $contact->getPhoneNumbers($contact->id);
        $nestedNum = $nestedStatement->rowCount();
        $phones_arr=array();

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

        $contact_arr = array(
            "id" =>  $contact->id,
            "name" => $contact->name,
            "lastname" => $contact->lastname,
            "email" => $contact->email,
            "phoneNumbers" => $phones_arr
        );
    
        // set response code - 200 OK
        http_response_code(200);
    
        // make it json format
        echo json_encode($contact_arr);
    }
    
    else{
        // set response code - 404 Not found
        http_response_code(404);
    
        // tell the user contact does not exist
        echo json_encode(array("message" => "contact does not exist."));
    }
?>