<?php
require_once 'db.php';

$name     = $_POST['name'] ?? null;
$capacity = filter_input(INPUT_POST, 'capacity', FILTER_VALIDATE_INT);
$status   = $_POST['status'] ?? null;

if (!$name || !$capacity || !$status) {
    http_response_code(400);
    echo json_encode(['result' => 'ERROR', 'message' => 'Усі поля обов’язкові для заповнення.']);
    exit;
}

$stmt = $db->prepare("INSERT INTO rooms (NAME, capacity, STATUS) VALUES (:name, :capacity, :status)");

$stmt->bindValue(':name', $name);
$stmt->bindValue(':capacity', $capacity, PDO::PARAM_INT);
$stmt->bindValue(':status', $status);

if (!$stmt->execute()) {
    $error = $stmt->errorInfo();
    http_response_code(500);
    echo json_encode(['result' => 'ERROR', 'message' => $error[2]]);
    exit;
}

$response = new stdClass();
$response->result = 'OK';
$response->message = 'Кімната додана успішно';

header('Content-Type: application/json');
echo json_encode($response);
?>
