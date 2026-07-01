<?php
$host = "localhost";
$port = "5432";
$username = "postgres";
$password = "1122";
$database ="lms";


    try{
   $dsn = "pgsql:host=$host;port=$port;dbname=$database";
   
   $pdo = new PDO($dsn, $username, $password);

   $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   $pdo -> setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}catch (PDOexception $e){
    die("Connection failed:". $e->getMessage());
}
