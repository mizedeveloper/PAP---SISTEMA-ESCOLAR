<?php
session_start();
include 'db.php';  // Conectar ao banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");  // Redireciona para o login se não estiver logado
    exit();
}

// Buscar informações do usuário
$user_id = $_SESSION['id'];
$query = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Contagem de alunos e admins
$query_alunos = "SELECT COUNT(*) AS total_alunos FROM usuarios WHERE tipo = 'aluno'";
$stmt_alunos = $conn->prepare($query_alunos);
$stmt_alunos->execute();
$result_alunos = $stmt_alunos->get_result();
$alunos = $result_alunos->fetch_assoc();

$query_admins = "SELECT COUNT(*) AS total_admins FROM usuarios WHERE tipo = 'admin'";
$stmt_admins = $conn->prepare($query_admins);
$stmt_admins->execute();
$result_admins = $stmt_admins->get_result();
$admins = $result_admins->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Reset de estilo e font */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            background-color: #f4f7fc;
            min-height: 100vh;
        }

        /* Painel Lateral */
        .sidebar {
            width: 250px;
            background-color: #4c8bf5;
            color: white;
            padding: 20px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .sidebar .logo {
            width: 80%;
            margin-bottom: 30px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            margin: 10px 0;
            font-size: 1.1rem;
            border-radius: 8px;
            width: 100%;
            display: block;
            transition: background 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #3578e5;
        }

        /* Conteúdo Principal */
        .main-content {
            margin-left: 250px;
            padding: 30px;
            width: 100%;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .dashboard-header h1 {
            font-size: 2rem;
            color: #333;
        }

        .dashboard-header p {
            font-size: 1.2rem;
            color: #666;
        }

        /* Blocos de Informações */
        .info-blocks {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .info-block {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            width: calc(33.333% - 20px);
            padding: 20px;
            text-align: center;
            font-size: 1.5rem;
            color: #333;
            transition: all 0.3s ease;
        }

        .info-block:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        .info-block i {
            font-size: 3rem;
            color: #4c8bf5;
            margin-bottom: 10px;
        }

        /* Estilo do painel de navegação */
        .sidebar i {
            margin-right: 10px;
        }

        .info-blocks .total {
            background-color: #e0f7fa;
        }

        .info-blocks .admins {
            background-color: #ffe0b2;
        }

        .info-blocks .students {
            background-color: #dcedc8;
        }

    </style>
</head>
<body>

<!-- Painel Lateral -->
<div class="sidebar">
    <div class="logo">
        <img src="logo.png" alt="Logo" class="logo">
    </div>
    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="cadastrar_aluno.php"><i class="fas fa-user-plus"></i> Cadastrar Usuário</a>
    <a href="cadastrar_atividade.php"><i class="fas fa-plus-circle"></i> Cadastrar Atividade</a>
    <a href="cadastrar_ocorrencia.php"><i class="fas fa-exclamation-circle"></i> Cadastrar Ocorrência</a> 
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
</div>

<!-- Conteúdo Principal -->
<div class="main-content">
    <div class="dashboard-header">
        <h1>Olá, <?php echo $user['nome']; ?>!</h1>
        <p>Bem-vindo ao seu painel de controle.</p>
    </div>

    <!-- Blocos de Informações -->
    <div class="info-blocks">
        <div class="info-block total">
            <i class="fas fa-users"></i>
            <h3><?php echo $alunos['total_alunos']; ?></h3>
            <p>Alunos Cadastrados</p>
        </div>
        <div class="info-block admins">
            <i class="fas fa-user-shield"></i>
            <h3><?php echo $admins['total_admins']; ?></h3>
            <p>Admins Logados</p>
        </div>
        <div class="info-block students">
            <i class="fas fa-chalkboard-teacher"></i>
            <h3>0</h3>
            <p>Atividades Cadastradas</p>
        </div>
    </div>
</div>

</body>
</html>
