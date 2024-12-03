<?php
session_start();
include 'db.php';  // Conectar ao banco de dados

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    // Consulta o banco de dados para buscar o usuário
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->bind_param('s', $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verifica se o usuário existe e se a senha está correta
    if ($user && password_verify($senha, $user['senha'])) {  // Usando password_verify para comparar o hash
        // Inicia a sessão
        $_SESSION['id'] = $user['id'];
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['tipo'] = $user['tipo'];

        // Redireciona de acordo com o tipo de usuário
        if ($user['tipo'] == 'admin') {
            header("Location: dashboard.php");
        } else {
            header("Location: sistema.php");
        }
        exit();
    } else {
        $erro = "Usuário ou senha inválidos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Escolar</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* O mesmo estilo que você usou para o login, mantendo a fonte e layout */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: #fff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 350px;
            border-radius: 10px;
            text-align: center;
            animation: slideIn 1s ease-out;
        }
        .login-container h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
        }
        .login-container form {
            display: flex;
            flex-direction: column;
        }
        .login-container input {
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
        }
        .login-container input:focus {
            border-color: #4c8bf5;
        }
        .login-container button {
            padding: 12px;
            background-color: #4c8bf5;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .login-container button:hover {
            background-color: #3578e5;
        }
        .login-container p {
            margin-top: 15px;
            color: #888;
        }
        .login-container p a {
            text-decoration: none;
            color: #4c8bf5;
        }
        .logo {
            width: 150px;
            margin-bottom: 20px;
            animation: fadeIn 1.5s ease-in-out;
        }
        .erro {
            color: red;
            font-size: 1rem;
            margin-top: 15px;
        }
        .spinner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: none;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .spinner div {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #4c8bf5;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        @keyframes slideIn {
            0% { transform: translateY(-50px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="logo.png" alt="Logo" class="logo">
        <h1>Bem Vindo ao Sistema</h1>
        <?php if (isset($erro)) { echo "<p class='erro'>$erro</p>"; } ?>
        <form method="POST" id="login-form">
            <input type="text" name="usuario" placeholder="Usuário" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
    </div>

    <div class="spinner" id="spinner">
        <div></div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', function(event) {
            // Exibir o spinner enquanto o formulário está sendo processado
            event.preventDefault(); // Previne o envio imediato do formulário
            document.getElementById('spinner').style.display = 'flex';

            // Simula o tempo de espera para login (3 segundos)
            setTimeout(function() {
                document.getElementById('spinner').style.display = 'none';
                document.getElementById('login-form').submit();  // Envia o formulário após 3 segundos
            }, 3000);  // Aguarda 3 segundos para esconder o spinner após o envio
        });
    </script>
</body>
</html>
