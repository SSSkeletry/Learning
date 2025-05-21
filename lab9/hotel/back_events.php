<?php
require_once 'db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$start = $_POST['start'] ?? $_GET['start'] ?? null;
$end = $_POST['end'] ?? $_GET['end'] ?? null;

if (!$start || !$end) {
    http_response_code(400);
    echo json_encode(["error" => "Missing 'start' or 'end' parameter."]);
    exit;
}

$stmt = $db->prepare("
    SELECT * FROM reservations 
    WHERE NOT ((end <= :start) OR (start >= :end))
");

$stmt->bindParam(':start', $start);
$stmt->bindParam(':end', $end);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

class Event {
    public $id;
    public $text;
    public $start;
    public $end;
    public $resource;
    public $bubbleHtml;
    public $status;
    public $paid;
}

$events = array();

date_default_timezone_set("UTC");

foreach ($result as $row) {
    $e = new Event();
    $e->id = $row['id'];
    $e->text = $row['NAME'];
    $e->start = $row['START'];
    $e->end = $row['END'];
    $e->resource = $row['room_id'];
    $e->bubbleHtml = "Reservation details: " . $e->text;
    $e->status = $row['STATUS'];
    $e->paid = $row['paid'];
    $events[] = $e;
}

header('Content-Type: application/json');
echo json_encode($events);
?>