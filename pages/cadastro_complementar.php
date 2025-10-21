<?php
// pages/cadastro_complementar.php - A VIEW (FORMULÁRIO HTML)

// A variável $nome_usuario_logado está disponível graças ao Roteador (index.php) 
// que chamou verificar_sessao.php.
$nome_usuario_logado = $_SESSION['nome_usuario'] ?? 'Usuário'; 

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complementar Cadastro</title>
    
    <link rel="stylesheet" href="/css/style.css">
    
    <style>
        body { justify-content: center; align-items: center; min-height: 100vh; }
        .form-container { max-width: 600px; padding: 2rem; }
        h2 { text-align: center; color: #dc3545; }
        .form-cadastro { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-cadastro .full-width { grid-column: 1 / 3; }
        .aviso { background-color: #fff3cd; color: #856404; padding: 10px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #ffeeba; }
        /* Garante que o botão Salvar não fique quebrado */
        .btn { width: 100%; } 
    </style>
</head>
<body>

    <div class="form-container">
        <div class="aviso">
            <strong>Cadastro Incompleto:</strong> Olá, <?php echo htmlspecialchars($nome_usuario_logado); ?>. Por favor, complete seu cadastro para acessar o sistema.
        </div>
        
        <div class="hub-header" style="border: none;">
            <a href="/index.php?api=logout" class="logout-link">Sair</a>
        </div>
        
        <form id="form-complementar" class="form-cadastro">
            
            <div class="form-group full-width">
                <label for="cpf_cnpj">CPF ou CNPJ</label>
                <input type="text" id="cpf_cnpj" name="cpf_cnpj" required>
            </div>
            
            <div class="form-group full-width">
                <label for="endereco">Endereço</label>
                <input type="text" id="endereco" name="endereco" required>
            </div>
            
            <div class="form-group">
                <label for="cidade">Cidade</label>
                <input type="text" id="cidade" name="cidade" required>
            </div>
            
            <div class="form-group">
                <label for="estado">Estado</label>
                <input type="text" id="estado" name="estado" required>
            </div>

            <div class="form-group">
                <label for="pais">País</label>
                <input type="text" id="pais" name="pais" required value="Brasil">
            </div>

            <div class="form-group full-width">
                <button type="submit" class="btn">Concluir Cadastro</button>
            </div>
            
            <p id="mensagem" class="full-width"></p>
        </form>
    </div>

    <script src="/js/pages/cadastro_complementar.js"></script>
</body>
</html>