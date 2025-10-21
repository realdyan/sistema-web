<?php
// backend/clientes/deletar.php
// Exclui um cliente específico, garantindo que ele pertence ao usuário logado.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('ROOT_PATH')) {
    // Define ROOT_PATH, subindo dois níveis de backend/clientes/
    define('ROOT_PATH', dirname(dirname(__DIR__)));
}

require_once ROOT_PATH . '/backend/conexao.php';

// 1. Verificar Autenticação
if (!isset($_SESSION['usuario_id'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Sessão expirada. Faça login novamente.']);
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
header('Content-Type: application/json');
$dados = json_decode(file_get_contents('php://input'), true);

try {
    // 2. Validação do ID
    $id_cliente = filter_var($dados['id_cliente'] ?? null, FILTER_VALIDATE_INT);
    
    if (!$id_cliente) {
        throw new Exception('ID do cliente inválido ou ausente.');
    }

    // 3. Executar a Exclusão
    // A condição (id_usuario = :id_usuario) é a segurança crucial!
    $sql = "
        DELETE FROM clientes 
        WHERE id = :id_cliente AND id_usuario = :id_usuario
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_cliente' => $id_cliente,
        ':id_usuario' => $id_usuario
    ]);

    // 4. Checar se alguma linha foi afetada
    if ($stmt->rowCount() === 0) {
        // Se 0 linhas foram excluídas, o cliente não existia ou não pertencia ao usuário.
        http_response_code(403); // Forbidden
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Cliente não encontrado ou você não tem permissão para excluí-lo.'
        ]);
        exit;
    }

    // 5. Sucesso
    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => 'Cliente excluído com sucesso!'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro interno do banco de dados ao excluir.']);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
}
?>