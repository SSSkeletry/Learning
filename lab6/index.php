<?php
session_start();

if (!isset($_SESSION['board'])) {
    $_SESSION['board'] = array_fill(0, 9, '');
} else {
    foreach ($_SESSION['board'] as &$value) {
        if ($value === null) {
            $value = '';
        }
    }
    unset($value);
}

if (isset($_POST['reset'])) {
    $_SESSION = [];
    session_destroy();
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['current_player'])) {
    $_SESSION['current_player'] = 'X';
}

if (isset($_POST['cell'])) {
    $cell = (int)$_POST['cell'];
    if ($cell >= 0 && $cell <= 8 && $_SESSION['board'][$cell] === '') {
        $_SESSION['board'][$cell] = $_SESSION['current_player'];
        $_SESSION['current_player'] = ($_SESSION['current_player'] === 'X') ? 'O' : 'X';
    }
}

function check_winner($board) {
    $wins = [
        [0,1,2], [3,4,5], [6,7,8], 
        [0,3,6], [1,4,7], [2,5,8], 
        [0,4,8], [2,4,6]           
    ];
    foreach ($wins as $line) {
        if ($board[$line[0]] !== '' &&
            $board[$line[0]] === $board[$line[1]] &&
            $board[$line[1]] === $board[$line[2]]) {
            return $board[$line[0]];
        }
    }
    return null;
}

$winner = check_winner($_SESSION['board']);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>TickTackToe</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <h1>TickTackToe</h1>

    <div class="status">
        <?php if ($winner): ?>
            Переміг: <?= htmlspecialchars($winner) ?>
        <?php elseif (in_array('', $_SESSION['board'])): ?>
            Хід: <?= htmlspecialchars($_SESSION['current_player']) ?>
        <?php else: ?>
            Ничья!
        <?php endif; ?>
    </div>

    <form method="post" action="index.php">
        <div class="board">
            <?php foreach ($_SESSION['board'] as $i => $cell): ?>
                <button class="cell" type="submit" name="cell" value="<?= $i ?>" 
                    <?= ($cell !== '' || $winner) ? 'disabled' : '' ?>>
                    <?= htmlspecialchars($cell ?? '') ?>
                </button>
            <?php endforeach; ?>
        </div>

       <div class="button-wrapper">
    <button type="submit" name="reset">Рестарт гри</button>
</div>

    </form>
</body>
</html>
