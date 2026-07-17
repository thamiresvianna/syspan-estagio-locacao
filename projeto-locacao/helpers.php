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

    function validarCliente(string $nome, string $email, string $telefone): array {
        $erros = [];

        $nome = trim($nome);
        $email = trim($email);
        $telefone = preg_replace('/\D/', '', trim($telefone));

        if(strlen($nome) < 3 || strlen($nome) > 120){
            $erros[] = "Nome deve conter entre 3 e 120 caracteres.";
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $erros[] = "E-mail inválido.";
        }
        if(strlen($telefone) < 10 || strlen($telefone) > 11){
            $erros[] = "Telefone inválido.";
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