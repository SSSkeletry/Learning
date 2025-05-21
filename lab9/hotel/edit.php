<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Reservation</title>
    <link type="text/css" rel="stylesheet" href="media/layout.css" />    
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
</head>
<body>
<?php
require_once 'db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$reservation = $db->prepare("SELECT * FROM reservations WHERE id = :id");
$reservation->execute([':id' => $id]);
$data = $reservation->fetch(PDO::FETCH_ASSOC);

$rooms = $db->query('SELECT * FROM rooms');
?>
<form id="f" action="back_update.php" method="post" style="padding:20px;">
    <h1>Edit Reservation</h1>

    <input type="hidden" name="id" value="<?= htmlspecialchars($data['id']) ?>">

    <div>Start:</div>
    <div><input type="text" name="start" value="<?= htmlspecialchars($data['START']) ?>" required /></div>

    <div>End:</div>
    <div><input type="text" name="end" value="<?= htmlspecialchars($data['END']) ?>" required /></div>

    <div>Room:</div>
    <div>
        <select name="room" required>
            <?php foreach ($rooms as $room): ?>
                <?php
                    $rid = (int)$room['id'];
                    $rname = htmlspecialchars($room['NAME']);
                    $selected = ($data['room_id'] == $rid) ? ' selected' : '';
                ?>
                <option value="<?= $rid ?>"<?= $selected ?>><?= $rname ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>Name:</div>
    <div><input type="text" name="name" value="<?= htmlspecialchars($data['NAME']) ?>" required /></div>

    <div>Status:</div>
    <div>
        <select name="status">
            <?php
            $statuses = ['new', 'confirmed', 'arrived', 'checkedout'];
            foreach ($statuses as $status) {
                $selected = ($data['STATUS'] === $status) ? ' selected' : '';
                echo "<option value=\"$status\"$selected>" . ucfirst($status) . "</option>";
            }
            ?>
        </select>
    </div><div>Paid:</div>
    <div>
        <select name="paid">
            <?php
            $paid_options = [0, 25, 50, 75, 100];
            foreach ($paid_options as $value) {
                $selected = ((int)$data['paid'] === $value) ? ' selected' : '';
                echo "<option value=\"$value\"$selected>$value%</option>";
            }
            ?>
        </select>
    </div>

    <div class="space" style="margin-top: 10px;">
        <input type="submit" value="Save" />
        <a href="javascript:window.close();">Cancel</a>
    </div>
</form>
</body>
</html>
