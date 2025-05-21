<?php
require_once 'db.php';

$id     = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$name   = $_POST['name'] ?? null;
$start  = $_POST['start'] ?? null;
$end    = $_POST['end'] ?? null;
$room   = filter_input(INPUT_POST, 'room', FILTER_VALIDATE_INT);
$status = $_POST['status'] ?? null;
$paid   = filter_input(INPUT_POST, 'paid', FILTER_VALIDATE_INT);

$stmt = $db->prepare("UPDATE reservations 
    SET NAME = :name, START = :start, END = :end, room_id = :room, STATUS = :status, paid = :paid 
    WHERE id = :id");

$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->bindValue(':name', $name);
$stmt->bindValue(':start', $start);
$stmt->bindValue(':end', $end);
$stmt->bindValue(':room', $room, PDO::PARAM_INT);
$stmt->bindValue(':status', $status);
$stmt->bindValue(':paid', $paid, PDO::PARAM_INT);

if (!$stmt->execute()) {
    $error = $stmt->errorInfo();
    http_response_code(500);
    echo json_encode(['result' => 'ERROR', 'message' => $error[2]]);
    exit;
}

class Result {}
$response = new Result();
$response->result = 'OK';
$response->message = 'Update successful';

header('Content-Type: application/json');
echo json_encode($response);

?>