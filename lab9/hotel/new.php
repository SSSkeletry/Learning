<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Reservation</title>
    <link type="text/css" rel="stylesheet" href="media/layout.css" />    
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
</head>
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    background: transparent;
  }

  .modal-form {
    padding: 20px;
    background-color: #fff;
    border-radius: 10px;
    font-size: 14px;
    color: #333;
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

$start_raw = filter_input(INPUT_GET, 'start', FILTER_SANITIZE_STRING);
$end_raw = filter_input(INPUT_GET, 'end', FILTER_SANITIZE_STRING);
$resource_id = filter_input(INPUT_GET, 'resource', FILTER_VALIDATE_INT);

$start = date("Y-m-d H:i:s", strtotime($start_raw));
$end = date("Y-m-d H:i:s", strtotime($end_raw));

$rooms = $db->query('SELECT * FROM rooms');
?>

<div class="modal-form">
  <h2>Нове бронювання</h2>
  <form id="f" action="back_create.php" method="post">
    <label>Ім’я гостя:</label>
    <input type="text" name="name" required />

    <label>Початок:</label>
    <input type="text" name="start" value="<?= htmlspecialchars($start) ?>" readonly />

    <label>Кінець:</label>
    <input type="text" name="end" value="<?= htmlspecialchars($end) ?>" readonly />

    <label>Кімната:</label>
    <select name="room" required>
      <?php foreach ($rooms as $room): ?>
        <?php
          $id = (int)$room['id'];
          $name = htmlspecialchars($room['NAME']);
          $selected = ($resource_id === $id) ? ' selected' : '';
        ?>
        <option value="<?= $id ?>"<?= $selected ?>><?= $name ?></option>
      <?php endforeach; ?>
    </select>

    <div class="actions">
      <input type="submit" value="Зберегти" />
      <a href="#" onclick="parent.DayPilot.Modal.close(); return false;">Скасувати</a>
    </div>
  </form>
</div>

</body>
</html>
