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

// Verificar a série do usuário
$usuario_serie = $user['serie'];

// Buscar as atividades relacionadas à série do usuário
$query_atividades = "SELECT * FROM atividades WHERE serie = ? ORDER BY data_postagem DESC";  // Ordena pela data de postagem mais recente
$stmt_atividades = $conn->prepare($query_atividades);
$stmt_atividades->bind_param('s', $usuario_serie); // Usando 's' para série que é varchar
$stmt_atividades->execute();
$result_atividades = $stmt_atividades->get_result();
$atividades = [];
while ($row = $result_atividades->fetch_assoc()) {
    $atividades[] = $row;
}

// Contagem de atividades postadas
$query_atividades_contagem = "SELECT COUNT(*) AS total_atividades FROM atividades WHERE serie = ?";
$stmt_atividades_contagem = $conn->prepare($query_atividades_contagem);
$stmt_atividades_contagem->bind_param('s', $usuario_serie);
$stmt_atividades_contagem->execute();
$result_atividades_contagem = $stmt_atividades_contagem->get_result();
$atividades_count = $result_atividades_contagem->fetch_assoc()['total_atividades'];

// Contagem de ocorrências registradas (valor fixo 0 por enquanto)
$ocorrencias = 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Atividades</title>
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

        /* Lista de Atividades */
        .atividades-list {
            margin-top: 30px;
        }

        .atividade-item {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .atividade-item h4 {
            margin-bottom: 10px;
            font-size: 1.5rem;
            color: #333;
        }

        .atividade-item p {
            color: #666;
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
        <p>Bem-vindo ao seu painel de controle.</p>
    </div>

    <!-- Lista de Atividades -->
    <div class="atividades-list">
        <h2>Atividades Disponíveis</h2>
        <?php if (empty($atividades)): ?>
            <p>Não há atividades disponíveis para a sua série.</p>
        <?php else: ?>
            <?php foreach ($atividades as $atividade): ?>
                <div class="atividade-item">
                    <h4><?php echo $atividade['titulo']; ?></h4>
                    <p><?php echo $atividade['descricao']; ?></p>
                    <p><strong>Série:</strong> <?php echo $atividade['serie']; ?></p>
                    <p><strong>Data de Postagem:</strong> <?php echo date('d/m/Y H:i', strtotime($atividade['data_postagem'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
