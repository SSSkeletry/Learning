<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Reservation</title>
    <link type="text/css" rel="stylesheet" href="media/layout.css" />    
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
</head>
<body>
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    background: transparent;
  }

  .modal-form {
    padding: 20px;
    background: #fff;
    border-radius: 10px;
    font-size: 14px;
    color: #333;
    max-width: 400px;
  }

  .modal-form h2 {
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 20px;
  }

  .modal-form label {
    display: block;
    margin-bottom: 6px;
    font-weight: bold;
  }

  .modal-form input[type="text"],
  .modal-form select {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
  }

  .modal-form .actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .modal-form .actions input[type="submit"] {
    background-color: #1a9d13;
    border: none;
    padding: 10px 20px;
    color: white;
    border-radius: 6px;
    cursor: pointer;
  }

  .modal-form .actions a {
    color: #888;
    text-decoration: none;
    font-size: 13px;
  }

  .modal-form .actions a:hover {
    text-decoration: underline;
  }
</style>

<?php
require_once 'db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$reservation = $db->prepare("SELECT * FROM reservations WHERE id = :id");
$reservation->execute([':id' => $id]);
$data = $reservation->fetch(PDO::FETCH_ASSOC);

$rooms = $db->query('SELECT * FROM rooms');
?>

<div class="modal-form">
  <h2>Редагувати бронювання</h2>
  <form action="back_update.php" method="post">
    <input type="hidden" name="id" value="<?= htmlspecialchars($data['id']) ?>">

    <label>Початок:</label>
    <input type="text" name="start" value="<?= htmlspecialchars($data['START']) ?>" required />

    <label>Кінець:</label>
    <input type="text" name="end" value="<?= htmlspecialchars($data['END']) ?>" required />

    <label>Кімната:</label>
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

    <label>Ім’я гостя:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($data['NAME']) ?>" required />

    <label>Статус:</label>
    <select name="status">
      <?php
        $statuses = ['new', 'confirmed', 'arrived', 'checkedout'];
        foreach ($statuses as $status) {
          $selected = ($data['STATUS'] === $status) ? ' selected' : '';
          echo "<option value=\"$status\"$selected>" . ucfirst($status) . "</option>";
        }
      ?>
    </select>

    <label>Оплачено:</label>
    <select name="paid">
      <?php
        $paid_options = [0, 25, 50, 75, 100];
        foreach ($paid_options as $value) {
          $selected = ((int)$data['paid'] === $value) ? ' selected' : '';
          echo "<option value=\"$value\"$selected>$value%</option>";
        }
      ?>
    </select>

    <div class="actions">
      <input type="submit" value="Зберегти" />
      <a href="#" onclick="parent.DayPilot.Modal.close(); return false;">Скасувати</a>
    </div>
  </form>
</div>
