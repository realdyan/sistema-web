// public/js/pages/cadastro_complementar.js (VERSÃO FINAL E CORRIGIDA)

document.addEventListener("DOMContentLoaded", function() {
    const formComplementar = document.getElementById("form-complementar");
    const mensagem = document.getElementById("mensagem");

    // **NOTA:** A lógica de Logout (Sair) foi removida daqui.
    // O botão 'Sair' deve funcionar apenas com o link HTML.

    formComplementar.addEventListener("submit", function(event) {
        event.preventDefault(); // Essencial para o AJAX
        mensagem.textContent = "";
        mensagem.className = "";
        
        const formData = new FormData(formComplementar);
        const dados = Object.fromEntries(formData.entries());
        
        // Limpar CPF/CNPJ
        dados.cpf_cnpj = dados.cpf_cnpj.replace(/[^\d]/g, '');

        const enviarDados = async () => {
            try {
                mensagem.textContent = "Salvando dados...";
                
                // ***** CORREÇÃO: Usar o caminho absoluto do Roteador (/) *****
                const response = await fetch('/index.php?api=atualizar_cadastro', { 
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dados)
                });

                if (!response.ok) {
                    const erroTexto = await response.text();
                    throw new Error(erroTexto);
                }

                const data = await response.json();
                
                console.log('Resposta do servidor:', data);

                if (data.status === 'sucesso') {
                    mensagem.textContent = "✓ " + data.mensagem + " Redirecionando...";
                    mensagem.className = "sucesso";
                    
                    // Redirecionar para o painel
                    setTimeout(() => {
                        // O Roteador enviou a URL de redirecionamento, usamos ela
                        window.location.href = data.redirect || '/index.php?pagina=painel';
                    }, 2000);
                } else {
                    // Se a resposta for 401 (Não Autorizado) a mensagem virá no JSON
                    throw new Error(data.mensagem || 'Erro ao salvar');
                }
            } catch (error) {
                console.error('Erro completo:', error);
                // Tratar erros de rede ou JSON inválido
                let errorMsg = error.message;
                if (errorMsg.includes('Unexpected token')) {
                    errorMsg = 'Erro de Servidor Inesperado (Verifique o PHP).';
                }
                mensagem.textContent = "✗ Erro: " + errorMsg;
                mensagem.className = 'erro';
            }
        };
        
        enviarDados();
    });
});