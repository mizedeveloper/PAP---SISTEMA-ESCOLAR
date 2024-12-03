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

// Buscar as ocorrências relacionadas ao nome do usuário
$query_ocorrencias = "SELECT * FROM ocorrencias WHERE nome_aluno = ?";
$stmt_ocorrencias = $conn->prepare($query_ocorrencias);
$stmt_ocorrencias->bind_param('s', $user['nome']);
$stmt_ocorrencias->execute();
$result_ocorrencias = $stmt_ocorrencias->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Aluno</title>
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

        /* Estilo das ocorrências */
        .ocorrencias-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .ocorrencia {
            background-color: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .ocorrencia h3 {
            margin-bottom: 10px;
            font-size: 1.2rem;
            color: #333;
        }

        .ocorrencia p {
            font-size: 1rem;
            color: #666;
        }

        .ocorrencia .data {
            font-weight: bold;
            color: #4c8bf5;
        }
    </style>
</head>
<body>

<!-- Painel Lateral -->
<div class="sidebar">
    <div class="logo">
        <img src="logo.png" alt="Logo" class="logo">
        </div>
    <a href="sistema.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="ver_atividades.php"><i class="fas fa-list"></i> Ver Atividades</a>
    <a href="ocorrencias_registradas.php"><i class="fas fa-clipboard-list"></i> Ocorrências Registradas</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
</div>
<!-- Conteúdo Principal -->
<div class="main-content">
    <div class="dashboard-header">
        <h1>Olá, <?php echo $user['nome']; ?>!</h1>
        <p>Bem-vindo ao seu painel de ocorrências.</p>
    </div>

    <!-- Ocorrências -->
    <div class="ocorrencias-container">
        <h2>Minhas Ocorrências</h2>
        <?php if ($result_ocorrencias->num_rows > 0): ?>
            <?php while ($ocorrencia = $result_ocorrencias->fetch_assoc()): ?>
                <div class="ocorrencia">
                    <h3><?php echo $ocorrencia['motivo_ocorrencia']; ?></h3>
                    <p class="data"><?php echo date('d/m/Y', strtotime($ocorrencia['data_ocorrencia'])); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Você não tem ocorrências registradas.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
