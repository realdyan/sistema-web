<?php
// backend/usuarios/verificar_sessao.php

// A SESSÃO JÁ ESTÁ INICIADA PELO ROTEADOR (index.php)

// 1. Verifica se o usuário NÃO está logado

if (!isset($_SESSION['usuario_id'])) {
    
    // Destrói a sessão por segurança
session_destroy();
    
    // ***** CORREÇÃO: Redirecionar DIRETAMENTE para o login estático *****
    // Isso encerra o ciclo de chamadas ao roteador.
    header("Location: /login.html"); 
    exit; 
}

// 2. Se o script chegou até aqui, o usuário ESTÁ logado.
$nome_usuario_logado = $_SESSION['nome_usuario'] ?? 'Usuário Desconhecido';
$tipo_usuario_logado = $_SESSION['tipo_usuario'] ?? 'usuario';

// Nota: A flag de cadastro_completo já está definida no index.php
?>