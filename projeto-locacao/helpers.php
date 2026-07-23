<?php
    declare(strict_types=1);

    function e(string $value): string {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    function obterId(): int {
        $id = (int) ($_GET['id'] ?? 0);

        if($id <= 0){
            die("ID inválido.");
        }

        return $id;
    }

    function calcularStatusContrato(string $data_inicio, string $data_fim, ?string $data_hoje = null): string {
        $hoje = new DateTime($data_hoje ?? date('Y-m-d'));
        $inicio = new DateTime($data_inicio);
        $fim = new DateTime($data_fim);

        if ($hoje < $inicio){
            return 'AGENDADO';
        }
        if ($hoje > $fim){
            return 'ENCERRADO';
        }
        return 'ATIVO';
    }

    function calcularTotalDias(string $inicio, string $fim): int {
        $data_inicio = new DateTime($inicio);
        $data_fim = new DateTime($fim);

        return $data_inicio->diff($data_fim)->days + 1;
    }

    function mostrarErros(array $erros): void {
        foreach ($erros as $erro){
            echo "<p class='erro'>" . e($erro) . "</p>";
        }
    }

    function limparNumeros(string $valor): string {
        return preg_replace('/\D/', '', $valor) ?? '';
    }

    function validarCliente(string $tipo_pessoa, string $nome, string $cpf_cnpj, string $email, 
                            string $telefone, string $cep, string $estado): array {                 
        $erros = [];

        $tipo_pessoa = trim($tipo_pessoa);
        $nome = trim($nome);
        $cpf_cnpj = limparNumeros($cpf_cnpj);
        $email = trim($email);
        $telefone = limparNumeros($telefone);
        $cep = limparNumeros($cep);
        $estado = trim($estado);

        if(!in_array($tipo_pessoa, ['F','J'])){
            $erros[] = "Tipo de pessoa inválido.";
        }
        if(strlen($nome) < 3 || strlen($nome) > 120){
            $erros[] = "Nome deve conter entre 3 e 120 caracteres.";
        }
        if(empty($cpf_cnpj)){
            $erros[] = "CPF/CNPJ é obrigatório.";
        } else {
            if($tipo_pessoa == 'F'){
                if(!validarCPF($cpf_cnpj)){
                    $erros[] = "CPF inválido.";
                }
            }
            if($tipo_pessoa == 'J'){
                if(!validarCNPJ($cpf_cnpj)){
                    $erros[] = "CNPJ inválido.";
                }
            }
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $erros[] = "E-mail inválido.";
        }
        if(strlen($telefone) < 10 || strlen($telefone) > 11){
            $erros[] = "Telefone inválido.";
        }
        if(!empty($cep) && !preg_match('/^\d{8}$/', $cep)){
            $erros[] = "CEP inválido.";
        }
        if(!empty($estado) && !preg_match('/^[A-Z]{2}$/', strtoupper($estado))){
            $erros[] = "Estado inválido.";
        }

        return $erros;
    }

    function validarCPF(string $cpf): bool {
        $cpf = limparNumeros($cpf);

        if(strlen($cpf) != 11){
            return false;
        }
        if(preg_match('/^(\d)\1{10}$/', $cpf)){
            return false;
        }

        $soma_primeiro_digito = 0;
        for($i = 0; $i < 9; $i++){
            $soma_primeiro_digito += $cpf[$i] * (10 - $i);
        }
        $resto = ($soma_primeiro_digito * 10) % 11;
        if($resto == 10){
            $resto = 0;
        }
        if($resto != $cpf[9]){
            return false;
        }

        $soma_segundo_digito = 0;
        for($i = 0; $i < 10; $i++){
            $soma_segundo_digito += $cpf[$i] * (11 - $i);
        }
        $resto = ($soma_segundo_digito * 10) % 11;
        if($resto == 10){
            $resto = 0;
        }
        if($resto != $cpf[10]){
            return false;
        }

        return true;
    }

    function validarCNPJ(string $cnpj): bool {
        $cnpj = limparNumeros($cnpj);

        if(strlen($cnpj) != 14){
            return false;
        }
        if(preg_match('/^(\d)\1{13}$/', $cnpj)){
            return false;
        }

        $peso1 = [5,4,3,2,9,8,7,6,5,4,3,2];
        $soma_peso1 = 0;
        for($i = 0; $i < 12; $i++){
            $soma_peso1 += $cnpj[$i] * $peso1[$i];
        }
        $resto = $soma_peso1 % 11;
        $digito1 = ($resto < 2) ? 0 : 11 - $resto;
        if($digito1 != $cnpj[12]){
            return false;
        }

        $peso2 = [6,5,4,3,2,9,8,7,6,5,4,3,2];
        $soma_peso2 = 0;
        for($i = 0; $i < 13; $i++){
            $soma_peso2 += $cnpj[$i] * $peso2[$i];
        }
        $resto = $soma_peso2 % 11;
        $digito2 = ($resto < 2) ? 0 : 11 - $resto;
        if($digito2 != $cnpj[13]){
            return false;
        }

        return true;
    }

    function validarEquipamento(string $descricao, float $diaria): array {
        $erros = [];

        $descricao = trim($descricao);

        if(strlen($descricao) < 3 || strlen($descricao) > 120){
            $erros[] = "Descrição deve conter entre 3 e 120 caracteres.";
        }
        if ($diaria <= 0){
            $erros[] = "Valor da diária inválido. O valor deve ser maior do que 0.";
        }

        return $erros;
    }
?>