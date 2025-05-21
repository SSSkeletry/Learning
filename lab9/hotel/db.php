<?php
$host = "127.127.126.31";
$port = 3306;   
$dbname = 'HotelBD';   
$username = 'root';         
$password = ''; 

try {
$db = new PDO("mysql:host=$host;port=$port",
               $username,
               $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec("use `$dbname`");
    
} catch (PDOException $e) {
    die("Помилка підключення до бази даних: " . $e->getMessage());
}
?>
