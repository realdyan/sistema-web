<?php
// backend/usuarios/verificar_auth.php
// API para checar o status de autenticação e complementação do cadastro.

// 1. CONFIGURAR CABEÇALHOS (API)
header('Content-Type: application/json');
// O Roteador (public/index.php) já chamou session_start() e definiu ROOT_PATH.

// 2. VERIFICAR SE O USUÁRIO ESTÁ LOGADO
if (isset($_SESSION['usuario_id'])) {
    
    try {
        // 3. INCLUIR A CONEXÃO E LER STATUS DO CADASTRO
        // Acessa a conexão usando a constante definida no roteador
        require_once ROOT_PATH . '/backend/conexao.php'; 

        $id_usuario = $_SESSION['usuario_id'];
        
        // 4. BUSCAR DADOS COMPLETOS DO USUÁRIO NO BANCO
        // O campo cpf_cnpj é usado pelo roteador como flag de cadastro completo
        $stmt = $pdo->prepare("SELECT nome_usuario, tipo, cpf_cnpj FROM usuarios WHERE id = ?");
        $stmt->execute([$id_usuario]);
        $usuario_db = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario_db) {
            
            // 5. DETERMINAR STATUS DE CADASTRO
            // Se o cpf_cnpj estiver preenchido, o cadastro está completo
            $cadastro_completo = !empty($usuario_db['cpf_cnpj']); 

            // 6. RETORNAR SUCESSO E DADOS DO USUÁRIO
            echo json_encode([
                'autenticado' => true,
                'usuario' => [
                    'id' => $id_usuario,
                    'nome_usuario' => $usuario_db['nome_usuario'],
                    'tipo' => $usuario_db['tipo'],
                    'cadastro_completo' => $cadastro_completo // Flag para o auth-helper.js
                ]
            ]);
            
        } else {
             // Usuário na sessão, mas não encontrado no banco (erro)
             throw new Exception('Usuário logado não encontrado no banco.');
        }


    } catch (Exception $e) {
        // Tratar erro do banco ou lógica
        error_log("Erro em verificar_auth: " . $e->getMessage());
        // Se houver erro, assume não autenticado para forçar login/revalidação
        echo json_encode(['autenticado' => false, 'usuario' => null]);
    }
    

} else {
    // 7. RETORNAR FALHA (Não está logado)
    echo json_encode([
        'autenticado' => false,
        'usuario' => null
    ]);
}
exit;
?>