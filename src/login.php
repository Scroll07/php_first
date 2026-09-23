<?php
session_start();

if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        require_once 'db.php';

        try {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if (!$user) {
                $error = 'Неверное имя пользователя или пароль';
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];

                header('Location: index.php');
                exit;
            } else {
                $error = 'Неверное имя пользователя или пароль';
            }
        } catch (PDOException $e) {
            $error = 'Ошибка базы данных: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 50px auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            padding: 10px 20px;
            background: #2196F3;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            width: 100%;
        }

        button:hover {
            background: #0b7dda;
        }

        .error {
            color: red;
            margin-bottom: 10px;
            padding: 10px;
            background: #ffebee;
            border-radius: 4px;
        }

        .register-link {
            margin-top: 10px;
            display: block;
            text-align: center;
            color: #2196F3;
            text-decoration: none;
        }

        .register-link:hover {
            text-decoration: underline;
        }

        h2 {
            text-align: center;
            color: #333;
        }
    </style>
</head>
<body>
    <h2>Вход</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="username">Имя пользователя:</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit">Войти</button>
    </form>

    <a class="register-link" href="register.php">Нет аккаунта? Зарегистрироваться</a>
</body>
</html>