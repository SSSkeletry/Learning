<?php
require_once 'db.php';

$stmt = $db->prepare("
    INSERT INTO reservations (NAME, START, END, room_id, STATUS, paid)
    VALUES (:name, :start, :end, :room, 'new', 0)
");

$stmt->bindParam(':name', $_POST['name']);
$stmt->bindParam(':start', $_POST['start']);
$stmt->bindParam(':end', $_POST['end']);
$stmt->bindParam(':room', $_POST['room']);
$stmt->execute();

class Result {}

$response = new Result();
$response->result = 'OK';
$response->message = 'Created with id: ' . $db->lastInsertId();
$response->id = $db->lastInsertId();

header('Content-Type: application/json');
echo json_encode($response);
?>
