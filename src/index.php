<?php
session_start();

// Если в сессии нет имени — пользователь не авторизован.
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная страница</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            text-align: center;
        }

        .logout-link {
            display: inline-block;
            margin-top: 20px;
            color: #2196F3;
            text-decoration: none;
        }

        .logout-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Вы успешно вошли в систему</h1>

    <p>
        Привет,
        <strong>
            <?php echo htmlspecialchars($_SESSION['username']); ?>
        </strong>!
    </p>

    <!-- <a class="logout-link" href="logout.php">Выйти из аккаунта</a> -->
</body>
</html>