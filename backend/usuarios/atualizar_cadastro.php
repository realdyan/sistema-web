<?php
// backend/usuarios/atualizar_cadastro.php
// VERSÃO FINAL CORRIGIDA: Resolve o bug de ROOT_PATH e garante a sessão.

// 1. GARANTIR QUE A SESSÃO ESTÁ ATIVA
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ***** FALLBACK PARA ROOT_PATH *****
// 2. ADICIONAR FALLBACK PARA ROOT_PATH
// Esta checagem é essencial, caso o Roteador não consiga definir ROOT_PATH.
if (!defined('ROOT_PATH')) {
    // Define ROOT_PATH como a raiz do projeto (subindo dois níveis de backend/usuarios/)
    define('ROOT_PATH', dirname(dirname(__DIR__)));
}


// 3. INCLUIR CONEXÃO
require_once ROOT_PATH . '/backend/conexao.php';

// 4. VERIFICAR AUTENTICAÇÃO (Otimizado)
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

// 5. CONFIGURAR RESPOSTA JSON
header('Content-Type: application/json');
$dados = json_decode(file_get_contents('php://input'), true);

try {
    // 6. VALIDAÇÃO DOS CAMPOS OBRIGATÓRIOS
    if (empty($dados['cpf_cnpj']) || empty($dados['endereco'])) {
        throw new Exception('CPF/CNPJ e Endereço são campos obrigatórios.');
    }

    // 7. VALIDAÇÃO E LIMPEZA DO CPF/CNPJ
    $cpf_cnpj = preg_replace('/[^0-9]/', '', $dados['cpf_cnpj']);
    
    if (strlen($cpf_cnpj) !== 11 && strlen($cpf_cnpj) !== 14) {
        throw new Exception('CPF/CNPJ inválido. Deve ter 11 ou 14 dígitos.');
    }

    // 8. VERIFICAR SE CPF/CNPJ JÁ EXISTE (para outro usuário)
    $stmt = $pdo->prepare("
        SELECT id FROM usuarios 
        WHERE cpf_cnpj = :cpf_cnpj AND id != :id_usuario
    ");
    $stmt->execute([
        ':cpf_cnpj' => $cpf_cnpj,
        ':id_usuario' => $id_usuario
    ]);
    
    if ($stmt->fetch()) {
        throw new Exception('Este CPF/CNPJ já está cadastrado para outro usuário.');
    }

    // 9. ATUALIZAR O CADASTRO (USANDO PARAMETROS NOMEADOS)
    $sql = "
        UPDATE usuarios 
        SET cpf_cnpj = :cpf_cnpj,
            endereco = :endereco,
            cidade = :cidade,
            estado = :estado,
            pais = :pais,
            data_modificacao = CURRENT_TIMESTAMP
        WHERE id = :id_usuario
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':cpf_cnpj' => $cpf_cnpj,
        ':endereco' => trim($dados['endereco']),
        ':cidade' => trim($dados['cidade'] ?? ''),
        ':estado' => trim($dados['estado'] ?? ''),
        ':pais' => trim($dados['pais'] ?? ''),
        ':id_usuario' => $id_usuario
    ]);

    // 10. ATUALIZAR A SESSÃO E VERIFICAR SUCESSO
    $_SESSION['cadastro_completo'] = true;

    // Se a contagem de linhas for 0, mas não houve erro, ainda é sucesso
    if ($stmt->rowCount() === 0) {
         echo json_encode([
            'status' => 'info',
            'mensagem' => 'Nenhuma alteração foi realizada.'
        ]);
        exit;
    }

    // 11. SUCESSO
    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => 'Cadastro atualizado com sucesso!',
        // Redirecionamento que o JS deve usar
        'redirect' => 'index.php?pagina=painel' 
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Erro ao atualizar cadastro. Detalhes técnicos: Ocorreu um erro no servidor SQL.'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'erro',
        'mensagem' => $e->getMessage()
    ]);
}
?>