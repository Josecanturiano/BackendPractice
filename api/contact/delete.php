<?php
    // required headers
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    // include database and object files
    define('__ROOT__', dirname(dirname(__FILE__))); 

    include_once(__ROOT__.'/config/db.php');
    include_once(__ROOT__.'/models/contact_model.php');
    
    // get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // prepare contact object
    $contact = new Contact($db);
    
    // get contact id
    $data = json_decode(file_get_contents("php://input"));
    
    // set contact id to be deleted
    $contact->id = $data->id;
    
    // delete the contact
    if($contact->delete()){
    
        // set response code - 200 ok
        http_response_code(200);
    
        // tell the user
        echo json_encode(array("message" => "contact was deleted."));
    }
    
    // if unable to delete the contact
    else{
    
        // set response code - 503 service unavailable
        http_response_code(503);
    
        // tell the user
        echo json_encode(array("message" => "Unable to delete contact."));
    }
?>