<?php
session_start();
require_once __DIR__ . '/../src/utils/Db.php';  // Ajuste o caminho se necessário

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['usuario_id'];
$conn = Db::getConnection();

// Buscar histórico recente (últimas 10 buscas)
$sql = "SELECT termo_busca, data_busca, resultados_encontrados 
        FROM historico_buscas 
        WHERE usuario_id = :id 
        ORDER BY data_busca DESC 
        LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();
$historico = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar total de buscas (opcional, para stats)
$sql_total = "SELECT COUNT(*) as total FROM historico_buscas WHERE usuario_id = :id";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bindParam(':id', $id);
$stmt_total->execute();
$total_buscas = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Buscas - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-image: url('../public/Design sem nome.png'); /* Mesmo fundo da dashboard, remova se quiser clean */
            background-repeat: no-repeat;
            background-size: cover;
            min-height: 90vh;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #container {
            max-width: 800px;
            width: 100%;
            border-radius: 40px;
            padding: 30px;
            margin-top: 20px;
        }

        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(2px) saturate(180%);
            border: 0.5px solid rgba(255, 255, 255, 0.8);
            border-radius: 2rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2),
                inset 0 4px 20px rgba(255, 255, 255, 0.3);
            outline: none;
            padding-left: 20px;
            font-family: poppins;
            border: none;
            margin-bottom: 10px;
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

        #container p {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-voltar {
            display: inline-block;
            padding: 12px 24px;
            background: #94b9ff;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            margin-bottom: 20px;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            transition: background 0.3s;
        }

        .btn-voltar:hover {
            background: #cdffd8;
        }

        .stats {
            text-align: center;
            margin-bottom: 30px;
            color: #94b9ff;
            font-size: 16px;
        }

        .historico-lista {
            max-height: 400px;
            overflow-y: auto;
        }

        .historico-item {
            background: rgba(255, 255, 255, 0.2);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
        }

        .historico-item:last-child {
            margin-bottom: 0;
        }

        .detalhes {
            flex: 1;
            margin-right: 20px;
        }

        .termo {
            font-weight: bold;
            color: #333;
            font-size: 16px;
            display: block;
            margin-bottom: 5px;
        }

        .info {
            font-size: 14px;
            color: #666;
        }

        .btn-buscar-novamente {
            background: #94b9ff;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 20px;
            font-size: 14px;
            transition: background 0.3s;
        }

        .btn-buscar-novamente:hover {
            background: #cdffd8;
        }

        .no-historico {
            text-align: center;
            color: #999;
            font-style: italic;
            font-size: 16px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
        }

        .btn-limpar {
            display: block;
            margin: 30px auto 0;
            padding: 12px 24px;
            background: #ff6b6b;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            transition: background 0.3s;
        }

        .btn-limpar:hover {
            background: #ff5252;
        }
        header {
            height: 100px;
            width: 100%;
            background-image: linear-gradient(to bottom, #d1d1d1, rgba(255, 0, 0, 0));
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            box-sizing: border-box;
            flex-shrink: 0;
        }
        header svg{
            height: 20px;
        }
    </style>
</head>
<header>
    <a href="dashboard.php">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
        </svg>
    </a>
</header>
<body>
    <div class="glass" id="container">
        
        <p>Histórico de buscas</p>
        
        <div class="stats">
            Você fez <strong><?php echo $total_buscas; ?></strong> buscas no total.
        </div>
        
        <?php if (!empty($historico)): ?>
            <div class="historico-lista">
                <?php foreach ($historico as $busca): ?>
                    <div class="historico-item">
                        <div class="detalhes">
                            <span class="termo"><?php echo htmlspecialchars(substr($busca['termo_busca'], 0, 50)) . (strlen($busca['termo_busca']) > 50 ? '...' : ''); ?></span>
                            <span class="info">
                                <?php echo $busca['resultados_encontrados']; ?> resultado(s) • 
                                <?php echo date('d/m/Y H:i', strtotime($busca['data_busca'])); ?>
                            </span>
                        </div>
                        <!-- Link para buscar novamente: Reconstrói os filtros do termo (simplificado; ajuste se necessário) -->
                        <a href="dashboard.php?<?php 
                            // Exemplo simples: parse o termo para GET params. Para simplicidade, use o termo como blocos
                            $termo = urlencode($busca['termo_busca']); 
                            echo "blocos=" . $termo; 
                        ?>" class="btn-buscar-novamente" title="Buscar novamente">Buscar novamente</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-historico">
                Nenhuma busca recente. Comece a buscar algo no dashboard!
            </div>
        <?php endif; ?>
        
        <!-- Botão para limpar histórico -->
        <?php if (!empty($historico)): ?>
            <button onclick="limparHistorico()" class="btn-limpar">Limpar Todo o Histórico</button>
        <?php endif; ?>
    </div>

    <script>
        function limparHistorico() {
            if (confirm('Tem certeza? Isso apagará todo o seu histórico de buscas.')) {
                fetch('limpar_historico.php', { 
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Histórico limpo com sucesso!');
                        location.reload();  // Recarrega a página para atualizar
                    } else {
                        alert('Erro ao limpar: ' + (data.message || 'Tente novamente'));
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro de conexão. Tente novamente.');
                });
            }
        }
    </script>
</body>
</html>
