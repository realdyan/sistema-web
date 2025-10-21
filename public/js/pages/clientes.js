// public/js/pages/clientes.js
// Gerencia a listagem e o CRUD (Create, Read, Update, Delete) de clientes.

document.addEventListener("DOMContentLoaded", function() {
    const tabelaCorpo = document.getElementById('tabela-corpo-clientes');
    const crudMensagem = document.getElementById('crud-mensagem');
    const formCliente = document.getElementById('form-cliente');
    const idClienteInput = document.getElementById('id_cliente');
    const btnSalvar = formCliente.querySelector('button[type="submit"]');

    // Função para exibir a mensagem na tela
    function exibirMensagem(texto, tipo = 'info') {
        crudMensagem.textContent = texto;
        crudMensagem.className = tipo; // Use classes CSS para estilizar (sucesso, erro, info)
        setTimeout(() => {
            crudMensagem.textContent = '';
            crudMensagem.className = '';
        }, 5000);
    }

    // Função auxiliar para resetar o formulário
    function resetarFormulario() {
        formCliente.reset();
        idClienteInput.value = '';
        btnSalvar.textContent = 'Salvar Cliente';
        document.querySelector('h3').textContent = 'Adicionar Novo Cliente';
    }


    // ===================================================
    // 1. FUNÇÃO DE LISTAGEM (READ) - (Mantida)
    // ===================================================
    async function listarClientes() {
        tabelaCorpo.innerHTML = '<tr><td colspan="4">Carregando clientes...</td></tr>';
        
        try {
            const response = await fetch('index.php?api=clientes_listar');
            if (response.status === 401) {
                const data = await response.json();
                exibirMensagem(data.mensagem, 'erro');
                if (data.redirect) window.location.href = data.redirect;
                return;
            }

            const data = await response.json();

            if (data.status === 'sucesso') {
                preencherTabela(data.dados);
            } else {
                exibirMensagem('Erro ao carregar clientes: ' + data.mensagem, 'erro');
                tabelaCorpo.innerHTML = '<tr><td colspan="4">Falha ao carregar dados.</td></tr>';
            }
        } catch (error) {
            console.error('Erro de conexão ao listar clientes:', error);
            exibirMensagem('Erro de rede ou servidor. Tente recarregar a página.', 'erro');
            tabelaCorpo.innerHTML = '<tr><td colspan="4">Erro de conexão.</td></tr>';
        }
    }

    // Função para preencher o corpo da tabela com os dados - (Mantida)
    function preencherTabela(clientes) {
        tabelaCorpo.innerHTML = ''; 
        
        if (clientes.length === 0) {
            tabelaCorpo.innerHTML = '<tr><td colspan="4">Nenhum cliente cadastrado.</td></tr>';
            return;
        }

        clientes.forEach(cliente => {
            const row = tabelaCorpo.insertRow();
            
            row.insertCell().textContent = cliente.nome;
            row.insertCell().textContent = cliente.email;
            row.insertCell().textContent = cliente.telefone;

            const acoesCell = row.insertCell();
            acoesCell.className = 'acoes-botoes';
            
            const btnEditar = document.createElement('button');
            btnEditar.textContent = 'Editar';
            btnEditar.className = 'btn-editar';
            btnEditar.onclick = () => carregarClienteParaEdicao(cliente); 
            
            const btnExcluir = document.createElement('button');
            btnExcluir.textContent = 'Excluir';
            btnExcluir.className = 'btn-excluir';
            btnExcluir.onclick = () => deletarCliente(cliente.id, cliente.nome); 
            
            acoesCell.appendChild(btnEditar);
            acoesCell.appendChild(btnExcluir);
        });
    }
    
    // ===================================================
    // 2. FUNÇÃO DE CARREGAR CLIENTE PARA EDIÇÃO (UPDATE)
    // ===================================================
    function carregarClienteParaEdicao(cliente) {
        // Preenche os campos do formulário
        idClienteInput.value = cliente.id;
        document.getElementById('nome_cliente').value = cliente.nome;
        document.getElementById('email_cliente').value = cliente.email;
        document.getElementById('telefone_cliente').value = cliente.telefone;
        document.getElementById('cpf_cnpj_cliente').value = cliente.cpf_cnpj;
        
        // ***** CORREÇÃO: INCLUIR CAMPOS DE ENDEREÇO *****
        document.getElementById('endereco_cliente').value = cliente.endereco;
        document.getElementById('cidade_cliente').value = cliente.cidade;
        document.getElementById('estado_cliente').value = cliente.estado;
        document.getElementById('pais_cliente').value = cliente.pais;
        // **********************************************
        
        // Atualiza o texto do botão e do título
        btnSalvar.textContent = 'Atualizar Cliente';
        document.querySelector('h3').textContent = 'Editar Cliente ID: ' + cliente.id;
    }

    // ===================================================
    // 3. FUNÇÃO DE DELETAR CLIENTE (DELETE) - (Mantida)
    // ===================================================
    async function deletarCliente(id, nome) {
        if (!confirm(`Tem certeza que deseja excluir o cliente ${nome} (ID: ${id})?`)) {
            return;
        }
        
        exibirMensagem('Excluindo cliente...', 'info');

        try {
            const response = await fetch('index.php?api=clientes_deletar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_cliente: id })
            });
            const data = await response.json();

            if (data.status === 'sucesso') {
                exibirMensagem(data.mensagem, 'sucesso');
                listarClientes(); 
                resetarFormulario();
            } else {
                exibirMensagem(data.mensagem, 'erro');
            }
        } catch (error) {
            console.error('Erro de conexão ao deletar:', error);
            exibirMensagem('Erro de rede ou servidor ao tentar excluir.', 'erro');
        }
    }


    // ===================================================
    // 4. EVENTO DE ENVIO DO FORMULÁRIO (CREATE/UPDATE) - (Mantida)
    // ===================================================
    formCliente.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const dados = Object.fromEntries(formData.entries());
        
        // Limpar CPF/CNPJ (boa prática)
        dados.cpf_cnpj = dados.cpf_cnpj.replace(/[^\d]/g, '');

        const isUpdate = !!dados.id_cliente; 
        
        btnSalvar.disabled = true;
        exibirMensagem(isUpdate ? 'Atualizando cliente...' : 'Cadastrando novo cliente...', 'info');

        try {
            const response = await fetch('index.php?api=clientes_salvar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dados)
            });
            
            const data = await response.json();

            if (data.status === 'sucesso') {
                exibirMensagem(data.mensagem, 'sucesso');
                listarClientes(); 
                resetarFormulario();
            } else {
                exibirMensagem(data.mensagem, 'erro');
            }
        } catch (error) {
            console.error('Erro de conexão ao salvar:', error);
            exibirMensagem('Erro de rede ou servidor ao salvar cliente.', 'erro');
        } finally {
            btnSalvar.disabled = false;
        }
    });
    
    // ===================================================
    // INICIALIZAÇÃO
    // ===================================================
    listarClientes(); 
});