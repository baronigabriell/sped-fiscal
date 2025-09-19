<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Painel do Usuário</title>
</head>
<body>
    <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['nome']) ?>!</h1>
    <a href="logout.php">Sair</a>

    <h2>🔍 Buscar Registros/Blocos</h2>
    <form method="GET" action="pesquisa.php">
        <input type="text" name="bloco" placeholder="Ex: C100" required>
        <button type="submit">Buscar</button>
    </form>

    <h2>📤 Enviar Arquivo SPED</h2>
    <form method="POST" action="upload.php" enctype="multipart/form-data">
        <input type="file" name="arquivo" accept=".txt" required>
        <button type="submit">Enviar</button>
    </form>
</body>
</html>
