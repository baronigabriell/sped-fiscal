<?php
session_start();

// Carrega a conexão com o banco de dados
require_once __DIR__ . '/../src/utils/Db.php'; // Corrigir caminho para Db.php
require_once __DIR__ . '/../src/ParserSped.php';  // Corrigir caminho para ParserSped.php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sped_file'])) {
    $file = $_FILES['sped_file'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        // Salva o arquivo no diretório de uploads
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($file['name']);
        
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // Analisando o arquivo
            $parser = new ParserSped($target_file);  // Usa o ParserSped para análise do arquivo
            $relatorio = $parser->analyze(); // Função que retorna o relatório de erros

            // Salva os resultados no banco de dados
            $pdo = Db::getConnection();  // Conecta ao banco de dados
            $stmt = $pdo->prepare("INSERT INTO uploads (usuario_id, nome_arquivo, caminho_arquivo) VALUES (:usuario_id, :nome_arquivo, :caminho_arquivo)");
            $stmt->execute([
                'usuario_id' => $_SESSION['usuario_id'],
                'nome_arquivo' => basename($file['name']),
                'caminho_arquivo' => $target_file
            ]);

            $upload_id = $pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO relatorios (upload_id, total_blocos, total_registros, total_caracteres, erros, blocos_presentes) VALUES (:upload_id, :total_blocos, :total_registros, :total_caracteres, :erros, :blocos_presentes)");
            $stmt->execute([
                'upload_id' => $upload_id,
                'total_blocos' => $relatorio['total_blocos'],
                'total_registros' => $relatorio['total_registros'],
                'total_caracteres' => $relatorio['total_caracteres'],
                'erros' => json_encode($relatorio['erros']),
                'blocos_presentes' => json_encode($relatorio['blocos_presentes'])
            ]);

            echo "Arquivo enviado e analisado com sucesso!";
        } else {
            echo "Erro ao mover o arquivo.";
        }
    } else {
        echo "Erro no upload.";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <label>Escolha o arquivo SPED</label>
    <input type="file" name="sped_file" required>
    <button type="submit">Enviar</button>
</form>
