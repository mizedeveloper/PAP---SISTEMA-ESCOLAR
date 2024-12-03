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

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura os dados do formulário
    $nome_aluno = $_POST['nome_aluno'];
    $motivo_ocorrencia = $_POST['motivo_ocorrencia'];
    $data_ocorrencia = $_POST['data_ocorrencia'];

    // Inserir dados no banco
    $query = "INSERT INTO ocorrencias (nome_aluno, motivo_ocorrencia, data_ocorrencia) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $nome_aluno, $motivo_ocorrencia, $data_ocorrencia);

    if ($stmt->execute()) {
        echo "Ocorrência cadastrada com sucesso!";
    } else {
        echo "Erro ao cadastrar ocorrência: " . $stmt->error;
    }
}

// Função para buscar alunos pelo nome
if (isset($_GET['search'])) {
    $search_term = "%" . $_GET['search'] . "%";
    $query = "SELECT * FROM usuarios WHERE nome LIKE ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    $alunos = [];
    while ($row = $result->fetch_assoc()) {
        $alunos[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Ocorrência</title>
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

        /* Formulário de Cadastro de Ocorrência */
        .form-container {
            background-color: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 60%;
            margin: 0 auto;
            transition: transform 0.3s ease;
        }

        .form-container:hover {
            transform: translateY(-10px);
        }

        .form-container h2 {
            font-size: 1.7rem;
            color: #333;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-container input,
        .form-container textarea {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 1rem;
            background-color: #f9f9f9;
            transition: border-color 0.3s ease;
        }

        .form-container input:focus,
        .form-container textarea:focus {
            border-color: #4c8bf5;
            outline: none;
        }

        .form-container textarea {
            min-height: 150px;
            resize: vertical;
        }

        .form-container button {
            background-color: #4c8bf5;
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 10px;
            font-size: 1.2rem;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #3578e5;
        }

        /* Estilos para a pesquisa de alunos */
        .search-container {
            margin-bottom: 30px;
            text-align: center;
        }

        .search-container input {
            width: 80%;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 1rem;
            background-color: #f9f9f9;
            transition: border-color 0.3s ease;
        }

        .search-container input:focus {
            border-color: #4c8bf5;
            outline: none;
        }

        .search-results {
            margin-top: 20px;
            text-align: center;
        }

        .search-results li {
            list-style: none;
            padding: 12px;
            background-color: #f9f9f9;
            margin-bottom: 10px;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-results li:hover {
            background-color: #e0e0e0;
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

    <!-- Pesquisa de Aluno -->
    <div class="search-container">
        <h2>Pesquisar Aluno</h2>
        <form method="GET" action="cadastrar_ocorrencia.php">
            <input type="text" name="search" placeholder="Digite o nome do aluno" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" required>
        </form>

        <?php if (isset($alunos) && count($alunos) > 0): ?>
            <div class="search-results">
                <ul>
                    <?php foreach ($alunos as $aluno): ?>
                        <li onclick="document.getElementById('nome_aluno').value = '<?php echo $aluno['nome']; ?>'"><?php echo $aluno['nome']; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif (isset($_GET['search'])): ?>
            <p>Nenhum aluno encontrado com esse nome.</p>
        <?php endif; ?>
    </div>

    <!-- Formulário de Cadastro de Ocorrência -->
    <div class="form-container">
        <h2>Cadastrar Ocorrência</h2>
        <form method="POST" action="cadastrar_ocorrencia.php">
            <input type="text" name="nome_aluno" id="nome_aluno" placeholder="Nome do Aluno" required>
            <textarea name="motivo_ocorrencia" placeholder="Motivo da Ocorrência" required></textarea>
            <input type="date" name="data_ocorrencia" required>
            <button type="submit">Cadastrar Ocorrência</button>
        </form>
    </div>
</div>

</body>
</html>
