<?php
// backend/clientes/salvar.php
// Responsável por INSERIR (CREATE) ou ATUALIZAR (UPDATE) um cliente.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('ROOT_PATH')) {
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

// Determinar se é uma atualização ou inserção
$id_cliente = filter_var($dados['id_cliente'] ?? null, FILTER_VALIDATE_INT);
$is_update = ($id_cliente > 0);

try {
    // 2. Validação Mínima
    if (empty($dados['nome'])) {
        throw new Exception('O campo Nome é obrigatório.');
    }

    // 3. Preparação dos Dados (Limpeza)
    $nome = trim($dados['nome']);
    $email = trim($dados['email'] ?? '');
    $telefone = trim($dados['telefone'] ?? '');
    $cpf_cnpj = preg_replace('/[^0-9]/', '', $dados['cpf_cnpj'] ?? '');
    $endereco = trim($dados['endereco'] ?? '');
    $cidade = trim($dados['cidade'] ?? '');
    $estado = trim($dados['estado'] ?? '');
    $pais = trim($dados['pais'] ?? '');

    // Se for UPDATE, verificamos se o cliente pertence a este usuário
    if ($is_update) {
        $stmt_check = $pdo->prepare("SELECT id FROM clientes WHERE id = :id AND id_usuario = :id_usuario");
        $stmt_check->execute([':id' => $id_cliente, ':id_usuario' => $id_usuario]);
        if (!$stmt_check->fetch()) {
            throw new Exception('Cliente não encontrado ou você não tem permissão para editá-lo.');
        }

        // 4a. SQL para UPDATE
        $sql = "
            UPDATE clientes SET 
                nome = :nome,
                email = :email,
                telefone = :telefone,
                cpf_cnpj = :cpf_cnpj,
                endereco = :endereco,
                cidade = :cidade,
                estado = :estado,
                pais = :pais,
                data_modificacao = CURRENT_TIMESTAMP
            WHERE id = :id_cliente AND id_usuario = :id_usuario
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':telefone' => $telefone,
            ':cpf_cnpj' => $cpf_cnpj,
            ':endereco' => $endereco,
            ':cidade' => $cidade,
            ':estado' => $estado,
            ':pais' => $pais,
            ':id_cliente' => $id_cliente,
            ':id_usuario' => $id_usuario
        ]);
        $mensagem_sucesso = 'Cliente atualizado com sucesso!';
    
    } else {
        // 4b. SQL para INSERT (CREATE)
        $sql = "
            INSERT INTO clientes (id_usuario, nome, email, telefone, cpf_cnpj, endereco, cidade, estado, pais) 
            VALUES (:id_usuario, :nome, :email, :telefone, :cpf_cnpj, :endereco, :cidade, :estado, :pais)
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':nome' => $nome,
            ':email' => $email,
            ':telefone' => $telefone,
            ':cpf_cnpj' => $cpf_cnpj,
            ':endereco' => $endereco,
            ':cidade' => $cidade,
            ':estado' => $estado,
            ':pais' => $pais
        ]);
        $mensagem_sucesso = 'Cliente cadastrado com sucesso!';
        $id_cliente = $pdo->lastInsertId();
    }

    // 5. Sucesso
    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => $mensagem_sucesso,
        'id_cliente' => $id_cliente
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    $msg = 'Erro no banco de dados. Verifique se o CPF/CNPJ ou Email já existem (se configurados como UNIQUE).';
    echo json_encode(['status' => 'erro', 'mensagem' => $msg]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
}
?>