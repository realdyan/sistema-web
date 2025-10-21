<?php
// pages/clientes.php - VERSÃO FINAL CORRIGIDA (Visual e Funcional)

// O Roteador (public/index.php) já faz a verificação de sessão.
$nome_usuario_logado = $_SESSION['nome_usuario'] ?? 'Usuário';
$id_usuario_logado = $_SESSION['usuario_id'] ?? 0;

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Clientes</title>
    
    <link rel="stylesheet" href="/css/style.css"> 
    
    <style>
        /* CSS BÁSICO */
        body { align-items: flex-start; }
        .painel-container { background-color: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); width: 90%; max-width: 900px; margin: 2rem auto; }
        .painel-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 1rem; margin-bottom: 1.5rem; }
        .logout-link { display: inline-block; padding: 0.5rem 1rem; background-color: #dc3545; color: white; text-decoration: none; border-radius: 4px; font-weight: bold; }
        .logout-link:hover { background-color: #c82333; }
        .crud-container { display: flex; gap: 2rem; flex-wrap: wrap; }
        .form-cadastro-cliente { flex: 1; min-width: 280px; }
        .lista-clientes { flex: 2; min-width: 400px; }
        h3 { border-bottom: 2px solid #007bff; padding-bottom: 5px; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .acoes-botoes { display: flex; gap: 5px; }
        .btn-editar, .btn-excluir { padding: 5px 8px; border: none; border-radius: 4px; color: white; cursor: pointer; }
        .btn-editar { background-color: #ffc107; }
        .btn-excluir { background-color: #dc3545; }
        #crud-mensagem { margin-top: 1rem; font-weight: bold; }
        /* Adicionando responsividade básica */
        @media (max-width: 768px) {
            .crud-container { flex-direction: column; }
        }
        
        /* ***** CORREÇÃO VISUAL PARA O FORMULÁRIO ***** */
        /* Garante que o rótulo (label) e o campo (input) fiquem em bloco dentro do form-group */
        .form-group {
            margin-bottom: 15px; /* Espaço entre os campos */
        }
        .form-cadastro-cliente label {
            display: block; /* Garante que o label ocupe a linha inteira */
            margin-bottom: 5px; /* Espaço abaixo do label */
            font-weight: bold;
        }
        .form-cadastro-cliente input[type="text"],
        .form-cadastro-cliente input[type="email"] {
            width: 100%; /* Garante que o campo de entrada ocupe o espaço restante */
            padding: 8px;
            box-sizing: border-box; /* Inclui padding e borda no width */
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <div class="painel-container">
        
        <div class="painel-header">
            <h2>Olá, <?php echo htmlspecialchars($nome_usuario_logado); ?>!</h2>
            <a href="index.php?api=logout" class="logout-link">Sair</a> 
        </div>
        
        <div class="painel-conteudo">
            
            <h1>Gerenciador de Clientes</h1>
            <p>Adicione, edite ou remova clientes.</p>
            <p id="crud-mensagem"></p>

            <div class="crud-container">
                
                <div class="form-cadastro-cliente">
                    <h3>Adicionar Novo Cliente</h3>
                    <form id="form-cliente">
                        <input type="hidden" name="id_usuario" value="<?php echo $id_usuario_logado; ?>">
                        <input type="hidden" id="id_cliente" name="id_cliente">
                        
                        <div class="form-group">
                            <label for="nome_cliente">Nome</label>
                            <input type="text" id="nome_cliente" name="nome" required>
                        </div>
                        <div class="form-group">
                            <label for="email_cliente">Email</label>
                            <input type="email" id="email_cliente" name="email">
                        </div>
                        <div class="form-group">
                            <label for="telefone_cliente">Telefone</label>
                            <input type="text" id="telefone_cliente" name="telefone">
                        </div>
                        <div class="form-group">
                            <label for="cpf_cnpj_cliente">CPF/CNPJ</label>
                            <input type="text" id="cpf_cnpj_cliente" name="cpf_cnpj">
                        </div>
                        
                        <div class="form-group">
                            <label for="endereco_cliente">Endereço</label>
                            <input type="text" id="endereco_cliente" name="endereco">
                        </div>
                        <div class="form-group">
                            <label for="cidade_cliente">Cidade</label>
                            <input type="text" id="cidade_cliente" name="cidade">
                        </div>
                        <div class="form-group">
                            <label for="estado_cliente">Estado</label>
                            <input type="text" id="estado_cliente" name="estado">
                        </div>
                        <div class="form-group">
                            <label for="pais_cliente">País</label>
                            <input type="text" id="pais_cliente" name="pais">
                        </div>
                        
                        <button type="submit" class="btn">Salvar Cliente</button>
                    </form>
                </div>

                <div class="lista-clientes">
                    <h3>Meus Clientes</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-corpo-clientes">
                            </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script src="/js/pages/clientes.js"></script>
</body>
</html>