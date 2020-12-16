<?php
    class Contact{
    
        // database connection and table name
        private $conn;
        private $table_name = "Contactos";
    
        // object properties
        public $id;
        public $name;
        public $lastname;
        public $email;
        public $phoneNumbers;
    
        // constructor with $db as database connection
        public function __construct($db){
            $this->conn = $db;
        }

        // read Contact
        function read(){
        
            // select all query
            $query = "SELECT Id, Nombre, Apellido, Email FROM `Contactos`";
        
            // prepare query statement
            $statement = $this->conn->prepare($query);
        
            // execute query
            $statement->execute();
        
            return $statement;
        }
        

        function findById(){
        
            // query to read single record
            $query = "SELECT Id, Nombre, Apellido, Email FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        
            // prepare query statement
            $stmt = $this->conn->prepare( $query );
        
            // bind id of contact to be updated
            $stmt->bindParam(1, $this->id);
        
            // execute query
            $stmt->execute();
        
            // get retrieved row
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
            // set values to object properties
            $this->name = $row['Nombre'];
            $this->lastname = $row['Apellido'];
            $this->email = $row['Email'];
      
        }

        function getPhoneNumbers($id){
            $query = "SELECT Numero FROM `Telefonos` where ContactoId = $id";
        
            // prepare query statement
            $statement = $this->conn->prepare($query);
        
            // execute query
            $statement->execute();
        
            return $statement;
        }

        // create contact
        function create(){
        
            // query to insert record
            $query = "INSERT INTO " . $this->table_name . " SET Nombre=:name, Apellido=:lastname, Email=:email";
    
            // prepare query
            $statement = $this->conn->prepare($query);
            
            // sanitize
            $this->name=htmlspecialchars(strip_tags($this->name));
            $this->lastname=htmlspecialchars(strip_tags($this->lastname));
            $this->email=htmlspecialchars(strip_tags($this->email));
        
            // bind values
            $statement->bindParam(":name", $this->name);
            $statement->bindParam(":lastname", $this->lastname);
            $statement->bindParam(":email", $this->email);

            // execute query
            if($statement->execute()){            

                $LAST_ID = $this->conn->lastInsertId();
                $error = true;
                foreach ($this->phoneNumbers as $value) {
                    
                    $query_phones = "INSERT INTO Telefonos SET Numero=:numero, ContactoId=:id";

                    $nestedStatement = $this->conn->prepare($query_phones);                                                            
                    
                    $nestedStatement->bindParam(":numero", $value);
                    $nestedStatement->bindParam(":id", $LAST_ID);
                    
                    if($nestedStatement->execute()){
                        $error = false;
                    }else{
                        $error = true;
                    }
                };
                if($error){
                    return false;
                }else {
                    return true;
                }
            }
            return false;
            
        }
        // update the contact
        function update(){
    
            // update query
            $query = "UPDATE " . $this->table_name . " SET
                        Nombre = :name,
                        Apellido = :lastname,
                        Email = :email
                    WHERE
                        Id = :id";
            $query_delete_phones = "DELETE FROM Telefonos WHERE ContactoId = :id";
    
            // prepare query statement
            $stmt = $this->conn->prepare($query);
            $stmtDelete = $this->conn->prepare($query_delete_phones);            
        
            // sanitize
            $this->name=htmlspecialchars(strip_tags($this->name));
            $this->lastname=htmlspecialchars(strip_tags($this->lastname));
            $this->email=htmlspecialchars(strip_tags($this->email));
        
            // bind new values
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':lastname', $this->lastname);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':id', $this->id);
            $stmtDelete->bindParam(':id', $this->id);
            
            // execute the query            
            if($stmt->execute()){                
                if($this->phoneNumbers){
                    if(count($this->phoneNumbers) > 0){
                        $stmtDelete->execute();
                        foreach ($this->phoneNumbers as $value) {
                            $query_phones = "INSERT INTO Telefonos SET Numero=:numero, ContactoId=:id";
            
                            $nestedStatement = $this->conn->prepare($query_phones);                                                            
                            
                            $nestedStatement->bindParam(":numero", $value);
                            $nestedStatement->bindParam(":id", $this->id);
                            
                            $nestedStatement->execute();
                        };  
                    }
                }                                
                return true;              
            }
        
            return false;
        }

        function delete(){
  
            // delete query
            $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $query_delete_phones = "DELETE FROM Telefonos WHERE ContactoId = ?";
    
            // prepare query
            $stmt = $this->conn->prepare($query);
            $stmtDelete = $this->conn->prepare($query_delete_phones); 
            // sanitize
            $this->id=htmlspecialchars(strip_tags($this->id));
          
            // bind id of record to delete
            $stmt->bindParam(1, $this->id);
            $stmtDelete->bindParam(1, $this->id);
    
            // execute query and firts delete in telefonos table
            if($stmtDelete->execute()){
                if($stmt->execute()){
                    return true;
                }
            }        
          
            return false;
        }

    }   
     
?>