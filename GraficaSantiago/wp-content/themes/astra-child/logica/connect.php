<?php
    //Coneccion a la base de datos
    $servername = "localhost";
    $username= "root";
    $password = "";
    $dbname = "graficasantiago";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

?>