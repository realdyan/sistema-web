// public/js/pages/login.js (VERSÃO CORRIGIDA PARA O ROTEADOR)

document.addEventListener("DOMContentLoaded", function() {

    const formLogin = document.getElementById("form-login");
    const mensagem = document.getElementById("mensagem");

    formLogin.addEventListener("submit", function(event) {
        
        event.preventDefault(); 
        mensagem.textContent = "";
        mensagem.className = "";

        const formData = new FormData(formLogin);
        const dados = Object.fromEntries(formData.entries());

        // --- INÍCIO DA MUDANÇA (fetch e try/catch) ---
        const enviarDados = async () => {
            try {
                // ***** MUDANÇA 1: O CAMINHO DO FETCH *****
                // Trocamos o caminho direto pelo nosso Roteador (index.php)
                const response = await fetch('index.php?api=login', { 
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dados) 
                });

                // Se não for 'OK', joga o erro do PHP
                if (!response.ok) {
                    const erroTexto = await response.text();
                    throw new Error(erroTexto);
                }

                // Se for 'OK', lê o JSON
                const data = await response.json();

                if (data.status === 'sucesso') {
                    mensagem.textContent = data.mensagem;
                    mensagem.className = "sucesso";
                    
                    setTimeout(() => {
                        // ***** MUDANÇA 2: O REDIRECIONAMENTO *****
                        // Pedimos ao Roteador para nos mostrar a página do painel
                        window.location.href = 'index.php?pagina=painel';
                    }, 1000); 

                } else {
                    mensagem.textContent = data.mensagem;
                    mensagem.className = "erro";
                }

            } catch (error) {
                // ***** MUDANÇA 3: O CATCH INTELIGENTE *****
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

        enviarDados(); // Chama a função que acabamos de criar
        // --- FIM DA MUDANÇA ---
    });
});