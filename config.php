<?php
// config.php - Configuração da conexão à base de dados
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'quizportugues');

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Função para verificar se o utilizador está logado
function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

// Função para redirecionar
function redirect($page) {
    header("Location: $page");
    exit();
}
?>