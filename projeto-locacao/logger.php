<?php
    date_default_timezone_set('America/Sao_Paulo');

    function registrarLog(string $mensagem): void {
        $arquivoLog = __DIR__ . '/logger.log';
        $dataHora = date('Y-m-d H:i:s');
        file_put_contents($arquivoLog, "[$dataHora] $mensagem\n", FILE_APPEND | LOCK_EX);
    }
?>