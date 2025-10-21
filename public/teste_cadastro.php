<?php
// Coloque este arquivo temporariamente na raiz para testar
session_start();
echo "<h1>DEBUG SESSÃO</h1>";
echo "Usuario ID: " . ($_SESSION['usuario_id'] ?? 'NÃO LOGADO');
echo "<br>CPF/CNPJ: " . ($_SESSION['cpf_cnpj'] ?? 'NÃO PREENCHIDO');
echo "<br><a href='/index.php/api/usuarios/logout'>TESTAR LOGOUT</a>";
?>