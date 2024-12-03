<?php
$host = '127.0.0.1';
$user = 'root';
$password = '';
$dbname = 'sistema_escolar';

// Conexão com o banco de dados
$conn = new mysqli($host, $user, $password, $dbname);

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
} else {
    echo "";
}
?>
