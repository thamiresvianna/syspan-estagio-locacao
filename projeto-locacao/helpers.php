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

    function validarCliente(string $tipo_pessoa, string $nome, string $cpf_cnpj, string $email, string $telefone, string $cep, string $endereco, 
                            string $numero, string $complemento, string $bairro, string $cidade, string $estado, string $observacao): array {
        $erros = [];

        $tipo_pessoa = trim($tipo_pessoa);
        $nome = trim($nome);
        $cpf_cnpj = preg_replace('/\D/', '', trim($cpf_cnpj));
        $email = trim($email);
        $telefone = preg_replace('/\D/', '', trim($telefone));
        $cep = preg_replace('/\D/', '', trim($cep));
        $endereco = trim($endereco);
        $numero = trim($numero);
        $complemento = trim($complemento);
        $bairro = trim($bairro);
        $cidade = trim($cidade);
        $estado = trim($estado);
        $observacao = trim($observacao);

        if(!in_array($tipo_pessoa, ['F','J'])){
            $erros[] = "Tipo de pessoa inválido.";
        }
        if(strlen($nome) < 3 || strlen($nome) > 120){
            $erros[] = "Nome deve conter entre 3 e 120 caracteres.";
        }
        if(empty($cpf_cnpj)){
            $erros[] = "CPF/CNPJ é obrigatório.";
        } else {
            if($tipo_pessoa == 'F' && strlen($cpf_cnpj) != 11){
                $erros[] = "CPF inválido.";
            }
            if($tipo_pessoa == 'J' && strlen($cpf_cnpj) != 14){
                $erros[] = "CNPJ inválido.";
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