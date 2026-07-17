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

    function calcularTotalDias(string $inicio, string $fim): string {
        $data_inicio = new DateTime($inicio);
        $data_fim = new DateTime($fim);

        return $data_inicio->diff($data_fim)->days + 1;
    }

    function mostrarErros(array $erros): void {
        foreach ($erros as $erro){
            echo "<p class='erro'>" . e($erro) . "</p>";
        }
    }
?>