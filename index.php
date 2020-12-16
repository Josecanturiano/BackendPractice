<?php
    header("Location: api/contact/read.php");
    exit();
?>

/*
    In postman send raw body like this.
    //for update:
    {
        "id": "15",
        "name": "Mauricio",
        "lastname": "Castro",
        "email": "mc@email.com",
        "phoneNumbers": [
            "809 111 0055",
            "809 555 0999"
        ]
    }

    //for create:
    {
        "id": "15",
        "name": "Mauricio",
        "lastname": "Castro",
        "email": "mc@email.com",
        "phoneNumbers": [
            "809 111 0055",
            "809 555 0999"
        ]
    }

    //for delete: 
    {
        "id": "14"
    }
 */