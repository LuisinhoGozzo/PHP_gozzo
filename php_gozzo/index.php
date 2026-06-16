<?php
include 'db.php';
session_start();

if (isset($_SESSION['user_name'])) {
    header("Location: welcome.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM employees WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && $password == $user['password']) {
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email']  = $user['email'];
        $_SESSION['id_dept']       = $user['id_department'];
        
        header("Location: welcome.php");
        exit();
    } else {
        $error = "There's a mismatch with your e-mail / password.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP_project - Login</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, sans-serif; 
            background-color: #f0f2f5; 
            margin: 0; 
            display: flex; 
            flex-direction: column;
            height: 100vh;
        }

        .top-brand {
            padding: 20px;
            font-weight: bold;
            font-size: 1.2em;
            letter-spacing: 1px;
            color: #2c3e50;
            position: absolute;
            top: 0;
            left: 0;
        }

        .login-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-card { 
            background: white; 
            padding: 35px; 
            border-radius: 12px; 
            box-shadow: 0 8px 20px rgba(0,0,0,0.1); 
            width: 100%; 
            max-width: 360px; 
            text-align: center; 
        }

        h2 { color: #2c3e50; margin-bottom: 25px; font-weight: 600; }
        
        input { 
            width: 100%; 
            padding: 12px; 
            margin: 10px 0; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            box-sizing: border-box; 
            font-size: 14px;
        }

        button { 
            width: 100%; 
            padding: 12px; 
            background-color: #2c3e50; 
            border: none; 
            color: white; 
            font-weight: bold; 
            border-radius: 6px; 
            cursor: pointer; 
            transition: background 0.3s;
            margin-top: 10px;
        }

        button:hover { background-color: #34495e; }
        
        .error { 
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            font-size: 0.85em;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
        }

        .hint {
            font-size: 0.8em;
            color: #95a5a6;
            margin-top: 25px;
        }
    </style>
</head>
<body>

    <div class="top-brand">PHP_GOZZO</div>

    <div class="login-container">
        <div class="login-card">
            <h2>SUPERCINES PAYROLL</h2>
            
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="email" name="email" placeholder="E-mail" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">ACCESS</button>
            </form>
        </div>
    </div>

</body>
</html>