<?php
// pages/painel.php - VIEW
// Usa o formato Query String para navegação: ?pagina=X ou ?api=Y

// O Roteador (index.php) já rodou e definiu $_SESSION['cadastro_completo']
$cadastro_completo = $_SESSION['cadastro_completo'] ?? true; 
$nome_usuario = $_SESSION['nome_usuario'] ?? 'Usuário';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Controle</title>
    
    <link rel="stylesheet" href="/css/style.css">

    <style>
        /* Estilos omitidos por brevidade, mas devem estar no seu arquivo */
        .aviso-cadastro-pendente { background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin-bottom: 25px; border: 1px solid #ffeeba; text-align: center; }
        .aviso-cadastro-pendente h2 { color: #856404; margin-top: 0; font-size: 1.5rem; }
        .btn-aviso { display: inline-block; padding: 8px 15px; background-color: #ffc107; color: #212529; text-decoration: none; border-radius: 4px; margin-top: 10px; font-weight: bold; transition: background-color 0.3s; }
        .logout-link { /* Garante que você tenha um estilo para o link de sair */ }
    </style>
</head>
<body>

    <div class="hub-header">
        <h1>Bem-vindo(a), <?php echo htmlspecialchars($nome_usuario); ?>!</h1>
        
        <a href="index.php?api=logout" class="logout-link">Sair</a>
    </div>

    <?php
    // --- AVISO CONDICIONAL PARA CADASTRO INCOMPLETO ---
    if (!$cadastro_completo) {
    ?>
        <div class="aviso-cadastro-pendente">
            <h2>🚨 Cadastro Incompleto</h2>
            <p>Seu cadastro está pendente. Complete seus dados (CPF/CNPJ e Endereço).</p>
            <p><a href="index.php?pagina=complementar" class="btn-aviso">Preencher Cadastro Agora</a></p>
        </div>
    <?php
    }
    // --- FIM DO AVISO ---
    ?>

    <section class="painel-conteudo">
        <h2>Visão Geral do Sistema</h2>
        <p>Esta é a área central. Seu acesso ao sistema está liberado.</p>
        
        <nav class="painel-nav">
            <a href="index.php?pagina=clientes" class="btn">Gerenciar Clientes</a>
            
            <?php if (!$cadastro_completo): ?>
                 <a href="index.php?pagina=complementar" class="btn-secundario">Complementar Cadastro</a>
            <?php endif; ?>
        </nav>
        
    </section>

</body>
</html>