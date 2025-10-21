// public/js/pages/registro.js (VERSÃO CORRIGIDA PARA O ROTEADOR)

document.addEventListener("DOMContentLoaded", function() {

    const formRegistro = document.getElementById("form-registro");
    const mensagem = document.getElementById("mensagem");

    formRegistro.addEventListener("submit", function(event) {
        event.preventDefault(); 
        mensagem.textContent = "";
        mensagem.className = "";

        const formData = new FormData(formRegistro);
        const dados = Object.fromEntries(formData.entries());

        if (dados.senha !== dados.confirmar_senha) {
            mensagem.textContent = "As senhas não coincidem.";
            mensagem.className = "erro";
            return;
        }
        
        const enviarDados = async () => {
            try {
                // ***** MUDANÇA AQUI *****
                // Trocamos o caminho direto do backend pelo nosso Roteador (index.php)
                const response = await fetch('index.php?api=registrar', { 
                // ***** FIM DA MUDANÇA *****

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

                if (data.status === 'sucesso') {
                    mensagem.textContent = data.mensagem;
                    mensagem.className = "sucesso";
                    formRegistro.reset();
                    setTimeout(() => {
                        // Este redirecionamento está CORRETO,
                        // pois 'login.html' está na mesma pasta 'public/'
                        window.location.href = 'login.html';
                    }, 2000);
                } else {
                    mensagem.textContent = data.mensagem;
                    mensagem.className = "erro";
                }

            } catch (error) {
                console.error('Erro na requisição:', error);
                let erroMsg = error.message;
                try {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = erroMsg;
                    erroMsg = tempDiv.textContent || tempDiv.innerText || error.message;
                } catch (e) {
                    // se falhar, só mostra a mensagem de erro original
                }
                mensagem.textContent = "Erro do Servidor: " + erroMsg;
                mensagem.className = 'erro';
            }
        };

        enviarDados();
    });
});