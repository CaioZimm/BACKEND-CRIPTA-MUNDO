<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Senha Redefinida</title>
    <style>
        body{
            margin: 1vh; 
            font-family: Tahoma, sans-serif;
        }

        table{
            align-content: center;
            border: 0;
            width: 100%;
            max-width: 600px; 
            margin: 0 auto;
        }

        table td{
            background-color: rgba(241, 229, 213, 0.8);
            border: 1px solid black;
            padding: 10px 40px 20px;
            border-radius: 5px;
            text-align: left;
            box-shadow:#47463e 2px 1px 10px 0.5px;
        }

        p{
            font-size: 18px;
        }

        h3{
            font-size: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td>
                <h3>Olá {{ $name }}</h3>
                <p>Gostaríamos de informar que sua senha do Blog Cripta do Mundo foi alterada com sucesso.</p>
                <p>Obrigado.</p>
            </td>
        </tr>
    </table>
</body>
</html>