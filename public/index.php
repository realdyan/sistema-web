<?php
// public/index.php - ROTEADOR SIMPLIFICADO (QUERY STRING)
// VERSÃO FINAL: Cadastro Complementar OPCIONAL.

// 1. Iniciar Sessão
session_start();

// 2. Definir ROOT_PATH
define('ROOT_PATH', dirname(__DIR__));

// --- LÓGICA DE AÇÃO (Usando ?pagina=... ou ?api=...) ---
$acao = key($_GET) ?? 'home';
$valor = $_GET[$acao] ?? null;

// Variáveis de controle para o roteamento
$is_api_call = ($acao === 'api');
// Verifica se estamos chamando a API de atualização (para não sobrescrever a sessão)
$is_atualizar_cadastro_api = ($is_api_call && $valor === 'atualizar_cadastro');

// --- VERIFICAÇÃO DE CADASTRO (Lógica Opcional/Status) ---

if (isset($_SESSION['usuario_id']) && !$is_atualizar_cadastro_api) {
    
    // Apenas consulte o banco se a flag da sessão estiver faltando (otimização)
    if (!isset($_SESSION['cadastro_completo'])) {

        // Conecta ao banco para checar o CPF
        require_once ROOT_PATH . '/backend/conexao.php';
        
        $id_usuario = $_SESSION['usuario_id'];
        $stmt = $pdo->prepare("SELECT cpf_cnpj FROM usuarios WHERE id = ?");
        $stmt->execute([$id_usuario]);
        $usuario_db = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Define a flag de sessão
        $_SESSION['cadastro_completo'] = !empty($usuario_db['cpf_cnpj']);
    }
}


// --- ROTEAMENTO PRINCIPAL ---

if ($acao === 'home') {
    if (isset($_SESSION['usuario_id'])) {
        header('Location: index.php?pagina=painel');
    } else {
        header('Location: login.html');
    }
    exit;
}

if ($acao === 'pagina') {
    $view = $valor;

    // Garante que só usuários logados possam ver estas páginas
    require_once ROOT_PATH . '/backend/usuarios/verificar_sessao.php';

    switch ($view) {
        case 'painel':
            require_once ROOT_PATH . '/pages/painel.php'; // HUB
            break;
        case 'clientes':
            require_once ROOT_PATH . '/pages/clientes.php'; // CRUD
            break;
        case 'complementar':
            require_once ROOT_PATH . '/pages/cadastro_complementar.php'; // Formulário
            break;
        default:
            http_response_code(404);
            echo "Página '$view' não encontrada.";
            break;
    }
} elseif ($is_api_call) {
    // Roteamento para as APIs (BACKEND)
    $api = $valor;

    // Todas as APIs de USUÁRIOS e CLIENTES
    switch ($api) {
        // AÇÕES DE USUÁRIOS
        case 'login':
        case 'registrar':
        case 'logout':
        case 'atualizar_cadastro':
            require_once ROOT_PATH . "/backend/usuarios/{$api}.php";
            exit; 
            
        // AÇÕES DE CLIENTES
        case 'clientes_listar':
            require_once ROOT_PATH . "/backend/clientes/ler.php";
            exit;
        case 'clientes_salvar': // CREATE e UPDATE
            require_once ROOT_PATH . "/backend/clientes/salvar.php";
            exit;
        case 'clientes_deletar':
            require_once ROOT_PATH . "/backend/clientes/deletar.php";
            exit;
            
        default: // Este é o único e CORRETO bloco default
            http_response_code(404);
            // Retorna JSON, pois é uma chamada de API
            echo json_encode(['status' => 'erro', 'mensagem' => "API '$api' não encontrada."]);
            exit; 
    }
} else {
    // Rota não reconhecida (volta para a lógica de home)
    header('Location: index.php?home');
    exit;
}
?>