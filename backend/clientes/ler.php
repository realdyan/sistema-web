<?php
// backend/clientes/ler.php
// Lista todos os clientes vinculados ao ID do usuário logado.

// 1. GARANTIR A SESSÃO
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Definições de Caminho (Fallback)
if (!defined('ROOT_PATH')) {
    // Define ROOT_PATH, subindo dois níveis de backend/clientes/
    define('ROOT_PATH', dirname(dirname(__DIR__))); 
}

// 3. Incluir Conexão
require_once ROOT_PATH . '/backend/conexao.php';

// 4. Verificar Autenticação
if (!isset($_SESSION['usuario_id'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Sessão expirada. Faça login novamente.',
        'redirect' => '/login.html'
    ]);
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
header('Content-Type: application/json');

try {
    // 5. Selecionar Clientes do Usuário Logado
    // Selecionamos todos os campos para o caso de precisarmos deles na edição.
    $stmt = $pdo->prepare("
        SELECT 
            id, 
            nome, 
            email, 
            telefone, 
            cpf_cnpj, 
            endereco, 
            cidade, 
            estado, 
            pais
        FROM clientes 
        WHERE id_usuario = :id_usuario
        ORDER BY nome ASC
    ");
    $stmt->execute([':id_usuario' => $id_usuario]);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. Retornar Sucesso e Dados
    echo json_encode([
        'status' => 'sucesso',
        'dados' => $clientes
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Erro interno do servidor ao listar clientes.'
    ]);
}
?>