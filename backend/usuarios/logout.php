<?php
// backend/usuarios/logout.php - VERSÃO SIMPLES E ROBUSTA

// A SESSÃO JÁ ESTÁ INICIADA PELO ROTEADOR.

// 1. Limpar TODOS os dados da sessão
session_unset();

// 2. Destruir a sessão
session_destroy();

// 3. Redirecionamento Final para o login (Usando a raiz '/')
header("Location: /login.html"); 
exit;
?>