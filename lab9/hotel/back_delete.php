<?php
require_once 'db.php';

header('Content-Type: application/json');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    echo json_encode([
        'result' => 'ERROR',
        'message' => 'Invalid reservation ID'
    ]);
    exit;
}

$stmt = $db->prepare("DELETE FROM reservations WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);

if (!$stmt->execute()) {
    $error = $stmt->errorInfo();
    http_response_code(500);
    echo json_encode([
        'result' => 'ERROR',
        'message' => $error[2]
    ]);
    exit;
}

echo json_encode([
    'result' => 'OK',
    'message' => 'Reservation deleted successfully'
]);
