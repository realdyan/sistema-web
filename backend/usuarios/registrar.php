<?php
// backend/usuarios/registrar.php
// (VERSÃO CORRIGIDA PARA A ARQUITETURA 'public/')

// 1. CONFIGURAR CABEÇALHOS (API)
header('Content-Type: application/json');

// O Roteador (public/index.php) já iniciou a sessão (session_start()),
// então NÃO precisamos fazer isso aqui.

try {
    // 2. INCLUIR A CONEXÃO
    // ***** MUDANÇA AQUI: USAR A CONSTANTE ROOT_PATH *****
    // (Esta constante foi definida no public/index.php)
    require_once ROOT_PATH . '/backend/conexao.php';

    // 3. OBTER DADOS DA REQUISIÇÃO
    $dados = json_decode(file_get_contents('php://input'), true);

    // 4. VALIDAÇÃO BÁSICA
    if (
        !isset($dados['nome_usuario']) ||
        !isset($dados['email']) ||
        !isset($dados['senha']) ||
        !isset($dados['confirmar_senha'])
    ) {
        throw new Exception('Todos os campos são obrigatórios.');
    }

    // 5. ATRIBUIR DADOS A VARIÁVEIS
    $nome_usuario = $dados['nome_usuario'];
    $email = $dados['email'];
    $senha = $dados['senha'];
    $confirmar_senha = $dados['confirmar_senha'];

    // 6. MAIS VALIDAÇÕES
    if ($senha !== $confirmar_senha) {
        throw new Exception('As senhas não coincidem.');
    }
    if (strlen($senha) < 6) {
        throw new Exception('A senha deve ter pelo menos 6 caracteres.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Formato de e-mail inválido.');
    }

    // 7. PROCESSAMENTO NO BANCO DE DADOS
    
    // 7.1. VERIFICAR SE USUÁRIO OU E-MAIL JÁ EXISTEM
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nome_usuario = ? OR email = ?");
    $stmt->execute([$nome_usuario, $email]);
    
    if ($stmt->rowCount() > 0) {
        throw new Exception('Nome de usuário ou e-mail já cadastrado.');
    }

    // 7.2. CRIPTOGRAFAR A SENHA (HASH)
    $senha_hash = password_hash($senha, PASSWORD_ARGON2ID);

    // 7.3. INSERIR O NOVO USUÁRIO
    // Note que não estamos inserindo o 'tipo' ou 'data_modificacao'
    // pois eles têm valores 'DEFAULT' no banco de dados. O SQL cuida disso.
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome_usuario, email, senha_hash) VALUES (?, ?, ?)");
    $stmt->execute([$nome_usuario, $email, $senha_hash]);

    // 7.4. RETORNAR SUCESSO
    echo json_encode(['status' => 'sucesso', 'mensagem' => 'Usuário cadastrado com sucesso!']);

} catch (PDOException $e) {
    // 8. TRATAMENTO DE ERRO DO BANCO (PDOException)
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro de Banco de Dados: ' . $e->getMessage()]);

} catch (Exception $e) {
    // 9. TRATAMENTO DE ERROS GERAIS (Exception)
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
}

?>