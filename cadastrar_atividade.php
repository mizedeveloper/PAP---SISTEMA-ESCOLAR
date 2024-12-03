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

// Lidar com o cadastro de atividade
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $serie = $_POST['serie'];
    $data_postagem = date('Y-m-d');  // Data de postagem é a data atual

    // Inserir a atividade no banco de dados
    $query_insert = "INSERT INTO atividades (titulo, descricao, serie, data_postagem) 
                     VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($query_insert);
    $stmt_insert->bind_param('ssss', $titulo, $descricao, $serie, $data_postagem);

    if ($stmt_insert->execute()) {
        $success_msg = "Atividade cadastrada com sucesso!";
    } else {
        $error_msg = "Erro ao cadastrar atividade!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Atividade</title>
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

        /* Estilo do Formulário */
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        .form-container h2 {
            margin-bottom: 20px;
        }

        .form-container input, .form-container select, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: #4c8bf5;
            border: none;
            color: white;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .form-container button:hover {
            background-color: #3578e5;
        }

        .form-container .error {
            color: red;
        }

        .form-container .success {
            color: green;
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

    <!-- Formulário para Cadastrar Atividade -->
    <div class="form-container">
        <h2>Cadastrar Nova Atividade</h2>

        <!-- Exibe mensagens de sucesso ou erro -->
        <?php if (isset($success_msg)) { echo "<p class='success'>$success_msg</p>"; } ?>
        <?php if (isset($error_msg)) { echo "<p class='error'>$error_msg</p>"; } ?>

        <form method="POST" action="cadastrar_atividade.php">
            <input type="text" name="titulo" placeholder="Título da atividade" required>
            <textarea name="descricao" placeholder="Descrição da atividade" required></textarea>

            <!-- Caixa de Seleção para Série -->
            <select name="serie" required>
                <option value="1° Ano - Ensino Fundamental I">1° Ano - Ensino Fundamental I</option>
                <option value="2° Ano - Ensino Fundamental I">2° Ano - Ensino Fundamental I</option>
                <option value="3° Ano - Ensino Fundamental I">3° Ano - Ensino Fundamental I</option>
                <option value="4° Ano - Ensino Fundamental I">4° Ano - Ensino Fundamental I</option>
                <option value="5° Ano - Ensino Fundamental I">5° Ano - Ensino Fundamental I</option>
                <option value="6° Ano - Ensino Fundamental II">6° Ano - Ensino Fundamental II</option>
                <option value="7° Ano - Ensino Fundamental II">7° Ano - Ensino Fundamental II</option>
                <option value="8° Ano - Ensino Fundamental II">8° Ano - Ensino Fundamental II</option>
                <option value="9° Ano - Ensino Fundamental II">9° Ano - Ensino Fundamental II</option>
                <option value="1° Ano - Ensino Médio">1° Ano - Ensino Médio</option>
                <option value="2° Ano - Ensino Médio">2° Ano - Ensino Médio</option>
                <option value="3° Ano - Ensino Médio">3° Ano - Ensino Médio</option>
            </select>

            <button type="submit">Cadastrar Atividade</button>
        </form>
    </div>

</div>

</body>
</html>