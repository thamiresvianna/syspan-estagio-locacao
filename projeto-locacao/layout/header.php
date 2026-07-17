<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Mini Locação Syspan</title>

        <style>
            body {
                font-family: "Gill Sans Extrabold", Helvetica, sans-serif;
                background: #f8f8f8;
                margin: 20px;
            }

            h1 {
                text-align: center;
            }

            nav {
                text-align: center;
                margin-bottom: 20px;
                background-color: #5eafe5;
                padding: 12px;
                border-radius: 10px;
            }

            nav a {
                padding: 0 15px;
                text-decoration: none;
                color: #ffffff;
                font-weight: bold;
                border-radius: 10px;
            }

            nav a:hover {
                opacity: 0.8;
            }

            hr {
                margin-top: 18px; 
                border: 0; 
                border-top: 1px solid #767676;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
                overflow: hidden;
                border: 1px solid #c9c9c9;
            }

            th, td {
                padding: 8px;
                text-align: left;
            }

            th {
                background: #d8d8d9;
            }

            td a {
                padding: 5px 8px;
                border-radius: 8px;
                text-decoration: none;
                display: inline-block;
                margin: 2px;
            }

            .botao-editar {
                background: #23a10a;
                color: white;
            }

            .botao-excluir {
                background: #a10a0a;
                color: white;
            }

            .botao-ver {
                background: #0a1ca1;
                color: white;
            }

            .links {
                padding: 10px 15px;
                border-radius: 8px;
                text-decoration: none;
                background-color: #6bc1fa;
                color: white;
                font-size: 15px;
                font-weight: bold;
                display: inline-block;
            }

            .links:hover, td a:hover {
                opacity: 0.8;
            }

            label {
                font-weight: bold;
            }

            input, select, textarea {
                width: 45%;
                padding: 8px;
                margin: 8px 0;
                box-sizing: border-box;
                border: 1px solid #c9c9c9;
                border-radius: 4px;
            }

            input[type="checkbox"] {
                width: auto;
            }

            button {
                padding: 10px 15px;
                border: none;
                border-radius: 8px;
                background-color: #5eafe5;
                color: white;
                font-size: 15px;
                font-weight: bold;
                cursor: pointer;
            }

            .botao-cancelar {
                padding: 10px 15px;
                border-radius: 8px;
                text-decoration: none;
                background-color: #ff0000;
                color: white;
                font-size: 15px;
                font-weight: bold;
                display: inline-block;
            }

            .erro {
                color: red;
                font-weight: bold;
                margin: 5px 0;
            }

            .paginacao {
                margin-top: 20px;
                text-align: center;
            }

            .paginacao a {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 10px 15px;
                border-radius: 8px;
                text-decoration: none;
                background-color: #6bc1fa;
                color: white;
                font-size: 15px;
                font-weight: bold;
                transition: 0.3s;
            }

            .paginacao a:hover {
                background-color: #8bcbf6;
            }

            .paginacao a.ativa {
                background-color: #3ca8f0;
            }

            footer {
                text-align: center;
                font-size: 14px;
                margin-top: 20px;
                color: #777;
            }
        </style>
    </head>

    <body>
        <h1>Mini Locação Syspan</h1>

        <nav>
            <a href="/syspan-estagio/projeto-locacao/clientes/listar.php">Clientes</a>
            <a href="/syspan-estagio/projeto-locacao/equipamentos/listar.php">Equipamentos</a>
            <a href="/syspan-estagio/projeto-locacao/contratos/listar.php">Contratos</a>
        </nav>

        <hr>