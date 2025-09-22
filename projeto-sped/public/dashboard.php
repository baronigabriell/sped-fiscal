<?php
session_start();

// Verificando se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Conexão com o banco de dados
require_once __DIR__ . '/../src/utils/Db.php';
$pdo = Db::getConnection(); // Certifique-se de que a conexão está sendo realizada corretamente

// Definindo os filtros com valores padrão
$nome = isset($_GET['nome']) ? '%' . $_GET['nome'] . '%' : '%';
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : [];
$preco_min = isset($_GET['preco_min']) ? $_GET['preco_min'] : 0;
$preco_max = isset($_GET['preco_max']) ? $_GET['preco_max'] : 10000;

// Montar a parte da consulta SQL para categorias
if (count($categoria) > 0) {
    // Criar placeholders para cada categoria selecionada
    $placeholders = implode(',', array_fill(0, count($categoria), '?'));
    $sql = "SELECT * FROM produtos WHERE 
                nome LIKE ? AND 
                categoria IN ($placeholders) AND 
                preco BETWEEN ? AND ?";
} else {
    // Sem filtro de categoria
    $sql = "SELECT * FROM produtos WHERE 
                nome LIKE ? AND 
                preco BETWEEN ? AND ?";
}

// Preparando a consulta
$stmt = $pdo->prepare($sql);

// Bind dos parâmetros conforme a query
if (count($categoria) > 0) {
    // Se categorias foram selecionadas, fazemos o bind dos parâmetros
    $params = array_merge([$nome], $categoria, [$preco_min, $preco_max]);
} else {
    // Se nenhuma categoria foi selecionada, fazemos o bind sem o parâmetro de categoria
    $params = [$nome, $preco_min, $preco_max];
}

// Executar a consulta com os parâmetros
$stmt->execute($params);

// Buscar os resultados
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Exibindo os resultados
foreach ($produtos as $produto) {
    echo "<div>";
    echo "<h3>" . htmlspecialchars($produto['nome']) . "</h3>";
    echo "<p>Categoria: " . htmlspecialchars($produto['categoria']) . "</p>";
    echo "<p>Preço: R$ " . number_format($produto['preco'], 2, ',', '.') . "</p>";
    echo "<p>" . htmlspecialchars($produto['descricao']) . "</p>";
    echo "</div>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Painel do Usuário</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['nome']) ?>!</h1>
    <a href="logout.php">Sair</a>

    <h2>🔍 Buscar Registros/Blocos</h2>
    <form method="GET" action="pesquisa.php">
        <label for="nome">Nome do Produto:</label>
        <input type="text" name="nome" id="nome">
        
        <label for="categoria">Categoria:</label>
        <select name="categoria[]" id="categoria" multiple>
            <option value="eletronicos">Eletrônicos</option>
            <option value="roupas">Roupas</option>
            <option value="brinquedos">Brinquedos</option>
        </select>

        <label for="preco_min">Preço Mínimo:</label>
        <input type="number" step="0.01" name="preco_min" id="preco_min">
        
        <label for="preco_max">Preço Máximo:</label>
        <input type="number" step="0.01" name="preco_max" id="preco_max">
        
        <button type="submit">Buscar</button>
    </form>

    <h2>📤 Enviar Arquivo SPED</h2>
    <form method="POST" action="upload.php" enctype="multipart/form-data">
        <input type="file" name="arquivo" accept=".txt" required>
        <button type="submit">Enviar</button>
    </form>
</body>
<style>
    body{
        background-image: url('../public/Design sem nome.png');
        background-repeat: no-repeat;
        background-size: cover;
        justify-content: center; 
        height: 98.3vh;
        font-family: poppins, Segoe UI;
    }
</style>
</html>
