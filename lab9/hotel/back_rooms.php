<?php
header('Content-Type: application/json');
require_once 'db.php';

try {
    $capacity = $_POST['capacity'] ?? 0;

    if ($capacity == 0) {
        $stmt = $db->prepare("SELECT * FROM rooms ORDER BY NAME");
        $stmt->execute();
    } else {
        $stmt = $db->prepare("SELECT * FROM rooms WHERE capacity = :capacity ORDER BY NAME");
        $stmt->execute(['capacity' => $capacity]);
    }

    $rooms = $stmt->fetchAll();

    class Room {}

    $result = [];

    foreach($rooms as $room) {
        $r = new Room();
        $r->id = $room['id'];
        $r->name = $room['NAME'];        
        $r->capacity = $room['capacity'];
        $r->status = $room['STATUS'];     
        $result[] = $r;
    }

    echo json_encode($result);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Помилка отримання даних: " . $e->getMessage()
    ]);
}
?>

