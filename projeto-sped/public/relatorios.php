<?php
// Quando o formulário for enviado
$relatorio = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sped_file'])) {
    $arquivo = $_FILES['sped_file']['tmp_name'];

    if (file_exists($arquivo)) {
        $linhas = file($arquivo);
        $erros = [];
        $relatorio .= "<pre>";

        foreach ($linhas as $i => $linha) {
            $linha = trim($linha);
            $campos = explode('|', $linha);
            $relatorio .= "Linha " . ($i + 1) . ": " . htmlspecialchars($linha) . "\n";

            if (isset($campos[0]) && $campos[0] === '0000') {
                $cnpj = $campos[2] ?? '';
                if (strlen($cnpj) !== 14 || !ctype_digit($cnpj)) {
                    $erros[] = "CNPJ inválido na linha " . ($i + 1) . ": $cnpj";
                }
            }
        }

        $relatorio .= "\n";
        if (!empty($erros)) {
            $relatorio .= "Erros encontrados:\n" . implode("\n", $erros);
        } else {
            $relatorio .= "Nenhum erro encontrado.";
        }

        $relatorio .= "</pre>";
    } else {
        $relatorio = "Erro ao ler o arquivo.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Upload de Arquivo SPED</title>
</head>
<body>
    <h1>Upload de Arquivo SPED</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="sped_file" accept=".txt" required>
        <button type="submit">Enviar</button>
    </form>

    <?php
    if ($relatorio) {
        echo "<h2>Relatório:</h2>";
        echo $relatorio;
    }
    ?>
</body>
</html>
