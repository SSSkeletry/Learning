<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Reservation</title>
    <link type="text/css" rel="stylesheet" href="media/layout.css" />    
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
</head>
<body>
<?php
require_once 'db.php';

$start_raw = filter_input(INPUT_GET, 'start', FILTER_SANITIZE_STRING);
$end_raw = filter_input(INPUT_GET, 'end', FILTER_SANITIZE_STRING);
$resource_id = filter_input(INPUT_GET, 'resource', FILTER_VALIDATE_INT);

$start = date("Y-m-d H:i:s", strtotime($start_raw));
$end = date("Y-m-d H:i:s", strtotime($end_raw));

$rooms = $db->query('SELECT * FROM rooms');
?>
<form id="f" action="back_create.php" method="post" style="padding:20px;">
    <h1>New Reservation</h1>

    <div>Name:</div>
    <div><input type="text" id="name" name="name" value="" required /></div>

    <div>Start:</div>
    <div><input type="text" id="start" name="start" value="<?= htmlspecialchars($start) ?>" readonly /></div>

    <div>End:</div>
    <div><input type="text" id="end" name="end" value="<?= htmlspecialchars($end) ?>" readonly /></div>

    <div>Room:</div>
    <div>
        <select id="room" name="room" required>
            <?php foreach ($rooms as $room): ?>
                <?php
                    $id = (int)$room['id'];
                    $name = htmlspecialchars($room['NAME']);
                    $selected = ($resource_id === $id) ? ' selected' : '';
                ?>
                <option value="<?= $id ?>"<?= $selected ?>><?= $name ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="space">
        <input type="submit" value="Save" />
        <a href="javascript:window.close();">Cancel</a>
    </div>
</form>
</body>
</html>
