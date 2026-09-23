<?php
session_start();

if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Пожалуйста, заполните все поля';
    } elseif ($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен содержать не менее 6 символов';
    } else {
        require_once 'db.php';

        try {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username OR email = :email');
            $stmt->execute([
                'username' => $username,
                'email' => $email
            ]);
            $existing_user = $stmt->fetch();

            if ($existing_user) {
                $error = 'Пользователь с таким именем или email уже существует';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare(
                    'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)'
                );
                $stmt->execute([
                    'username' => $username,
                    'email' => $email,
                    'password' => $hashed_password
                ]);

                $success = 'Регистрация успешна! Теперь вы можете войти.';
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
    <title>Регистрация</title>
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
            background: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            width: 100%;
        }

        button:hover {
            background: #45a049;
        }

        .error {
            color: red;
            margin-bottom: 10px;
            padding: 10px;
            background: #ffebee;
            border-radius: 4px;
        }

        .success {
            color: green;
            margin-bottom: 10px;
            padding: 10px;
            background: #e8f5e9;
            border-radius: 4px;
        }

        .login-link {
            margin-top: 10px;
            display: block;
            text-align: center;
            color: #2196F3;
            text-decoration: none;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        h2 {
            text-align: center;
            color: #333;
        }
    </style>
</head>
<body>
    <h2>Регистрация</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="username">Имя пользователя:</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Подтвердите пароль:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <button type="submit">Зарегистрироваться</button>
    </form>

    <a class="login-link" href="login.php">Уже есть аккаунт? Войти</a>
</body>
</html>