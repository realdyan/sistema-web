<?php
// backend/usuarios/login.php
// (VERSÃO CORRIGIDA PARA A ARQUITETURA 'public/')

// ***** MUDANÇA 1: REMOVER O 'session_start()' *****
// O Roteador (public/index.php) já fez isso por nós.
// session_start(); 

// 2. INCLUIR A CONEXÃO
// ***** MUDANÇA 2: USAR A CONSTANTE ROOT_PATH *****
// (Esta constante foi definida no public/index.php)
require_once ROOT_PATH . '/backend/conexao.php';

// 3. CONFIGURAR CABEÇALHOS (API)
header('Content-Type: application/json');

// 4. OBTER DADOS DA REQUISIÇÃO
$dados = json_decode(file_get_contents('php://input'), true);

// 5. VALIDAÇÃO BÁSICA
if (!isset($dados['nome_usuario']) || !isset($dados['senha'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Usuário e senha são obrigatórios.']);
    exit;
}

// 6. ATRIBUIR DADOS A VARIÁVEIS
$nome_usuario = $dados['nome_usuario'];
$senha_digitada = $dados['senha'];

// 7. PROCESSAMENTO NO BANCO DE DADOS (TRY-CATCH)
try {
    // 7.1. BUSCAR O USUÁRIO NO BANCO
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nome_usuario = ?");
    $stmt->execute([$nome_usuario]);
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // 7.2. VERIFICAR O USUÁRIO E A SENHA
    if ($usuario) {
        
        // 7.3. A "MÁGICA" DA VERIFICAÇÃO
        if (password_verify($senha_digitada, $usuario['senha_hash'])) {
            
            // 7.4. SUCESSO! SENHA CORRETA!
            // (Seu código aqui está PERFEITO!)
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nome_usuario'] = $usuario['nome_usuario'];
            $_SESSION['tipo_usuario'] = $usuario['tipo']; // Salva a 'role' do usuário
            
            echo json_encode([
                'status' => 'sucesso', 
                'mensagem' => 'Login realizado com sucesso!'
            ]);
            exit;

        }
    }

    // 7.5. FALHA NO LOGIN
    echo json_encode([
        'status' => 'erro', 
        'mensagem' => 'Usuário ou senha inválidos.'
    ]);

} catch (PDOException $e) {
    // 8. TRATAMENTO DE ERRO DO BANCO
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro interno do servidor.']);
}

?>