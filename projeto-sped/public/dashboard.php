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
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Usuário</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-image: url('../public/Design sem nome.png');
            background-repeat: no-repeat;
            background-size: cover;
            height: 100vh;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            flex-direction: column;
            /* Coloca os itens em coluna */
        }

        svg {
            margin-bottom: 30px;
            /* Espaçamento entre o SVG e o formulário */
        }

        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(2px) saturate(180%);
            border: 0.5px solid rgba(255, 255, 255, 0.8);
            border-radius: 2rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2),
                inset 0 4px 20px rgba(255, 255, 255, 0.3);
            outline: none;
            height: 50px;
            width: 430px;
            padding-left: 20px;
            font-family: poppins;
        }

        .glass::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 2rem;
            backdrop-filter: blur(1px);
            box-shadow: inset -10px -8px 0px -11px rgba(255, 255, 255, 1),
                inset 0px -9px 0px -8px rgba(255, 255, 255, 1);
            opacity: 0.6;
            z-index: -1;
            filter: blur(10px);
        }


        .produto {
            background: rgba(255, 255, 255, 0.2);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .produto h3 {
            font-size: 24px;
            color: #333;
        }

        .produto p {
            font-size: 16px;
            color: #666;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Centraliza os itens do formulário */
            width: 100%;
            max-width: 450px;
            /* Limita a largura do formulário */
        }

        label,
        input,
        select,
        button {
            margin: 5px 0;
            width: 100%;
        }

        button {
            padding: 10px;
            background-color: #4B4B4B;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #333;
        }

        header {
            height: 100px;
            width: 100%;
            background-image: linear-gradient(to bottom, #d1d1d1, rgba(255, 0, 0, 0));
            /* Gradiente de vermelho opaco para vermelho translúcido */
            display: flex;
            align-items: center;
        }
    </style>
</head>

<header class="header">
    <svg style="width: 20px; stroke: #4B4B4B;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
    </svg>
    
</header>

<body>
    <svg width="261" x height="62" viewBox="0 0 581 127" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M25.979 33.432C32.635 33.432 41.339 37.656 46.459 41.624L43.899 53.4H43.259C39.291 42.52 31.099 34.584 23.803 34.584C15.099 34.584 10.235 39.448 9.595 44.696C7.803 61.848 50.939 57.24 49.531 78.104C48.763 88.216 39.291 95.768 24.955 95.768C14.587 95.768 6.907 92.312 -0.00499964 87.192L2.683 74.392H3.323C7.675 83.352 17.275 93.976 28.667 93.976C37.883 93.976 44.795 89.24 45.563 83.736C47.995 66.968 3.323 74.392 6.395 49.304C7.419 40.984 12.027 33.432 25.979 33.432ZM98.3 33.432C108.796 33.432 121.596 42.392 122.108 58.648C122.876 90.392 93.436 95.768 79.1 95.768C76.284 95.768 73.724 95.256 71.548 94.616V116.632C71.548 121.112 71.932 122.392 74.364 122.776C74.364 123.032 74.364 123.672 74.364 123.672L62.332 126.488H61.436V43.288C61.436 38.808 61.052 37.528 58.62 37.144C58.62 36.888 58.62 36.248 58.62 36.248L70.652 33.432H71.548V68.504C73.852 53.656 80.38 33.432 98.3 33.432ZM79.228 95C91.516 94.872 113.916 89.496 112.124 56.856C111.484 45.72 103.676 33.56 92.668 35.736C78.332 38.552 71.548 65.304 71.548 76.056V93.848C72.188 94.104 75.132 95.128 79.228 95ZM180.653 53.656C187.949 55.576 194.605 63 193.581 74.648C192.813 83.736 184.749 95.768 166.573 95.768C148.909 95.768 131.885 87.064 131.373 65.944C130.733 43.8 150.829 34.2 164.909 33.432C175.789 32.92 190.765 38.168 192.557 53.656C191.405 53.656 183.469 53.656 183.469 53.656H180.653ZM159.149 34.968C150.445 36.632 144.813 43.928 142.509 52.632H183.341C181.677 41.496 171.693 32.536 159.149 34.968ZM192.429 74.904C193.965 61.592 185.133 53.656 175.405 53.656H142.253C140.589 60.44 140.973 67.992 143.149 74.136C147.885 87.576 157.485 93.464 170.541 92.952C181.549 92.44 191.149 84.888 192.429 74.904ZM266.236 94.104C266.236 94.36 266.236 95 266.236 95H253.308V60.696C250.876 75.544 244.348 95.768 226.428 95.768C216.06 95.768 203.26 86.808 202.748 70.424C201.98 38.808 231.42 33.432 245.628 33.432C248.572 33.432 251.132 33.944 253.308 34.584V12.568C253.308 8.088 252.924 6.808 250.364 6.424C250.364 6.168 250.364 5.39999 250.364 5.39999L262.524 2.712H263.42V86.552C263.42 91.032 264.316 92.952 266.236 94.104ZM232.188 93.464C246.524 90.648 253.308 63.896 253.308 53.144V35.224C252.668 35.096 249.724 34.072 245.628 34.072C233.212 34.328 210.94 39.704 212.732 72.344C213.372 83.48 221.18 95.512 232.188 93.464ZM347.113 34.456H322.409V86.552C322.409 91.032 323.305 92.952 325.225 94.104C325.225 94.36 325.225 95 325.225 95H309.481C309.481 95 309.481 94.36 309.481 94.104C311.401 92.952 312.297 91.032 312.297 86.552V44.696C312.297 37.784 309.737 34.456 304.745 34.456V33.432H323.561C315.881 33.048 305.257 26.776 305.257 18.2C305.257 9.49599 312.297 2.712 324.969 2.712C330.473 2.712 337.001 4.12 341.609 7.448L339.689 20.888H338.793C335.721 13.08 327.401 0.535995 318.825 4.504C314.473 6.424 312.937 13.208 314.089 20.12C314.985 25.624 319.081 33.432 329.449 33.432H347.113V34.456ZM360.048 86.552C360.048 91.032 360.944 92.952 362.992 94.104C362.992 94.36 362.992 95 362.992 95H347.12C347.12 95 347.12 94.36 347.12 94.104C349.04 92.952 349.936 91.032 349.936 86.552V43.288C349.936 38.808 349.552 37.528 347.12 37.144C347.12 36.888 347.12 36.248 347.12 36.248L359.152 33.432H360.048V86.552ZM354.672 23.064C351.344 23.064 348.656 20.376 348.656 17.048C348.656 13.72 351.344 11.032 354.672 11.032C358 11.032 360.688 13.72 360.688 17.048C360.688 20.376 358 23.064 354.672 23.064ZM396.729 33.432C403.385 33.432 412.089 37.656 417.209 41.624L414.649 53.4H414.009C410.041 42.52 401.849 34.584 394.553 34.584C385.849 34.584 380.985 39.448 380.345 44.696C378.553 61.848 421.689 57.24 420.281 78.104C419.513 88.216 410.041 95.768 395.705 95.768C385.337 95.768 377.657 92.312 370.745 87.192L373.433 74.392H374.073C378.425 83.352 388.025 93.976 399.417 93.976C408.633 93.976 415.545 89.24 416.313 83.736C418.745 66.968 374.073 74.392 377.145 49.304C378.169 40.984 382.777 33.432 396.729 33.432ZM439.896 74.136C444.632 87.576 455.512 94.744 468.44 93.464C481.112 92.184 488.024 80.92 490.072 77.336L490.84 77.72C486.872 84.888 479.32 94.872 464.088 95.768C446.424 96.664 428.76 87.064 428.248 65.944C427.608 43.8 447.704 33.432 461.784 33.432C469.08 33.432 479.32 36.76 485.976 40.216L484.056 50.84H483.288C480.216 43.16 468.056 32.152 456.024 34.968C440.28 38.552 434.52 58.776 439.896 74.136ZM555.583 94.104C555.583 94.36 555.583 95 555.583 95H542.655V77.08C539.327 88.344 532.287 95.768 519.999 95.768C509.503 95.768 499.263 90.776 497.599 81.56C495.423 68.504 503.231 60.824 513.087 56.728C509.759 49.304 513.727 33.432 530.879 33.432C541.503 33.432 552.767 39.064 552.767 54.808V86.552C552.767 91.032 553.663 92.952 555.583 94.104ZM529.727 34.712C518.207 34.712 510.527 45.848 514.367 56.216C521.279 53.656 528.959 52.76 534.975 52.76C537.919 52.76 540.479 53.272 542.655 53.912V53.016C542.655 43.288 538.815 34.712 529.727 34.712ZM523.071 94.104C534.207 93.464 540.095 83.992 542.655 74.776V55.064C542.015 54.808 538.943 53.912 534.975 53.912C521.023 53.912 502.079 60.824 507.583 83.352C509.247 90.264 516.671 94.488 523.071 94.104ZM577.423 86.552C577.423 91.032 578.319 92.952 580.239 94.104C580.239 94.36 580.239 95 580.239 95H564.495C564.495 95 564.495 94.36 564.495 94.104C566.415 92.952 567.311 91.032 567.311 86.552V12.568C567.311 8.088 566.927 6.808 564.495 6.424C564.495 6.168 564.495 5.39999 564.495 5.39999L576.527 2.712H577.423V86.552Z" fill="#4B4B4B" />
    </svg>
    <form method="GET" action="">
        <label for="nome">Nome do produto</label>
        <input type="text" name="nome" id="nome" class="glass" value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>">

        <label for="preco_min">Preço Mínimo:</label>
        <input type="number" step="0.01" name="preco_min" id="preco_min" class="glass" value="<?= htmlspecialchars($_GET['preco_min'] ?? '') ?>">

        <label for="preco_max">Preço Máximo:</label>
        <input type="number" step="0.01" name="preco_max" id="preco_max" class="glass" value="<?= htmlspecialchars($_GET['preco_max'] ?? '') ?>">

        <label for="categoria">Categoria:</label>
        <select name="categoria[]" id="categoria" class="glass" multiple size="4">
            <option value="eletronicos" <?= in_array('eletronicos', $_GET['categoria'] ?? []) ? 'selected' : '' ?>>Eletrônicos</option>
            <option value="roupas" <?= in_array('roupas', $_GET['categoria'] ?? []) ? 'selected' : '' ?>>Roupas</option>
            <option value="brinquedos" <?= in_array('brinquedos', $_GET['categoria'] ?? []) ? 'selected' : '' ?>>Brinquedos</option>
        </select>
    </form>

</body>

</html>