const campo_cep = document.getElementById("cep");
const mensagem = document.getElementById("mensagem-cep");

const cpf_cnpj = document.getElementById("cpf_cnpj");
const tipo_pessoa = document.getElementById("tipo_pessoa");
const label_cpf_cnpj = document.getElementById("label_cpf_cnpj");

const telefone = document.getElementById("telefone");

const endereco = document.getElementById("endereco");
const bairro = document.getElementById("bairro");
const cidade = document.getElementById("cidade");
const estado = document.getElementById("estado");
const numero = document.querySelector('[name="numero"]');

function bloquearEndereco(valor) {
    endereco.readOnly = valor;
    bairro.readOnly = valor;
    cidade.readOnly = valor;
    estado.readOnly = valor;
}

function limparEndereco() {
    endereco.value = "";
    bairro.value = "";
    cidade.value = "";
    estado.value = "";

    bloquearEndereco(false);
}

function carregandoEndereco() {
    campo_cep.readOnly = true;

    endereco.value = "Consultando CEP...";
    bairro.value = "Consultando...";
    cidade.value = "Consultando...";
    estado.value = "...";

    bloquearEndereco(true);
}

function atualizarTipoPessoa(limpar = false) {
    if(tipo_pessoa.value === "F"){
        label_cpf_cnpj.textContent = "CPF:";
        cpf_cnpj.placeholder = "000.000.000-00";
        cpf_cnpj.maxLength = 14;
    }
    else{
        label_cpf_cnpj.textContent = "CNPJ:";
        cpf_cnpj.placeholder = "00.000.000/0000-00";
        cpf_cnpj.maxLength = 18;
    }

    if(limpar){
        cpf_cnpj.value = "";
    }
}

cpf_cnpj.addEventListener("input", function () {
    let valor = this.value.trim().replace(/\D/g, "");

    if(tipo_pessoa.value === "F"){
        valor = valor.substring(0,11);

        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
    }
    else{
        valor = valor.substring(0,14);

        valor = valor.replace(/^(\d{2})(\d)/, "$1.$2");
        valor = valor.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
        valor = valor.replace(/^(\d{2})\.(\d{3})\.(\d{3})(\d)/, "$1.$2.$3/$4");
        valor = valor.replace(/^(\d{2})\.(\d{3})\.(\d{3})\/(\d{4})(\d)/, "$1.$2.$3/$4-$5");
    }
      
    this.value = valor;
});

telefone.addEventListener("input", function () {
    let valor = this.value.trim().replace(/\D/g, "");

    valor = valor.substring(0,11);

    if(valor.length <= 10){
        valor = valor.replace(/(\d{2})(\d)/, "($1) $2");
        valor = valor.replace(/(\d{4})(\d)/, "$1-$2");
    }
    else{
        valor = valor.replace(/(\d{2})(\d)/, "($1) $2");
        valor = valor.replace(/(\d{5})(\d)/, "$1-$2");
    }

    this.value = valor;
});

campo_cep.addEventListener("input", function () {
    let cep = this.value.trim().replace(/\D/g, "");

    cep = cep.substring(0,8);

    if(cep.length > 5){
        cep = cep.slice(0,5) + "-" + cep.slice(5);
    }

    this.value = cep;
});

let ultimo_cep = "";

async function buscarCep(cep) {
    try {
        const resposta = await fetch(`https://viacep.com.br/ws/${encodeURIComponent(cep)}/json/`);

        if(!resposta.ok){
            throw new Error("Erro na consulta.");
        }

        const dados = await resposta.json();

        if (dados.erro){
            ultimo_cep = "";
            limparEndereco();
                    
            mensagem.textContent = "CEP não encontrado.";
            campo_cep.focus();
            return;
        }

        endereco.value = dados.logradouro;
        bairro.value = dados.bairro;
        cidade.value = dados.localidade;
        estado.value = dados.uf;

        numero.focus();
    }
    catch(error) {
        ultimo_cep = "";
        limparEndereco();

        mensagem.textContent = "CEP não localizado.";
        campo_cep.focus();
    }
    finally {
        campo_cep.readOnly = false;
        bloquearEndereco(false);
    }
}

campo_cep.addEventListener("blur", async function () {
    mensagem.textContent = "";

    let cep = this.value.trim().replace(/\D/g, "");

    cep = cep.substring(0,8);

    if(cep.length !== 8){
        ultimo_cep = "";
        limparEndereco();

        mensagem.textContent = "CEP deve conter 8 dígitos.";
        return;
    }

    if(cep === ultimo_cep){
        return;
    }

    ultimo_cep = cep;
    carregandoEndereco();

    await buscarCep(cep);
});

tipo_pessoa.addEventListener("change", () => atualizarTipoPessoa(true));

atualizarTipoPessoa();