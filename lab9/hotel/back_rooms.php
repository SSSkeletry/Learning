<?php
header('Content-Type: application/json');

require_once 'db.php';

try {
$stmt = $db->prepare("SELECT * FROM rooms ORDER BY NAME");
$stmt->execute();
$rooms = $stmt->fetchAll();

class Room {}

$result = array();

foreach($rooms as $room) {
  $r = new Room();
  $r->id = $room['id'];
  $r->name = $room['NAME'];        
  $r->capacity = $room['capacity'];
  $r->status = $room['STATUS'];     
  $result[] = $r;
}

header('Content-Type: application/json');
echo json_encode($result);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Помилка отримання даних: " . $e->getMessage()
    ]);
}
?>
