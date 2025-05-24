<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Нова кімната</title>
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
  .modal-form input[type="number"],
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

<div class="modal-form">
  <h2>Нова кімната</h2>
  <form id="f" method="post">
    <label>Назва кімнати:</label>
    <input type="text" name="name" required />

    <label>Кількість місць:</label>
    <input type="number" name="capacity" min="1" required />

    <label>Статус:</label>
    <select name="status" required>
      <option value="Чиста">Чиста</option>
      <option value="Брудна">Брудна</option>
      <option value="Прибирається">Прибирається</option>
    </select>

    <div class="actions">
      <input type="submit" value="Зберегти" />
      <a href="#" onclick="parent.DayPilot.Modal.close(); return false;">Скасувати</a>
    </div>
  </form>
</div>

<script>
  $("#f").submit(function (e) {
    e.preventDefault();
   $.post("back_room_new.php", $(this).serialize(), function (data) {

      if (data.result === "OK") {
        parent.DayPilot.Modal.close({ result: "OK" });
      } else {
        alert("Помилка: " + data.message);
      }
    }, "json").fail(function (xhr) {
      alert("Помилка при з'єднанні: " + xhr.responseText);
    });
  });
</script>

</body>
</html>
