<?php
session_start();
require_once __DIR__ . '/../src/utils/Db.php';


if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['usuario_id'];
$conn = Db::getConnection();

// Atualizar dados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $foto = null;

    // Diretório de uploads (absoluto)
    $diretorio = __DIR__ . "/uploads/";
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0777, true);
    }

    // Upload de imagem
    if (!empty($_FILES['foto']['name'])) {
        $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $nomeArquivo = uniqid() . "." . $extensao;
        $caminho = $diretorio . $nomeArquivo;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho)) {
            $foto = $nomeArquivo;
        }
    }

    if (!empty($_POST['senha'])) {
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

        if ($foto) {
            $sql = "UPDATE usuarios SET nome=:nome, email=:email, senha=:senha, foto=:foto WHERE id=:id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":foto", $foto);
        } else {
            $sql = "UPDATE usuarios SET nome=:nome, email=:email, senha=:senha WHERE id=:id";
            $stmt = $conn->prepare($sql);
        }

        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":senha", $senha);
        $stmt->bindParam(":id", $id);
    } else {
        if ($foto) {
            $sql = "UPDATE usuarios SET nome=:nome, email=:email, foto=:foto WHERE id=:id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":foto", $foto);
        } else {
            $sql = "UPDATE usuarios SET nome=:nome, email=:email WHERE id=:id";
            $stmt = $conn->prepare($sql);
        }

        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":id", $id);
    }

    if ($stmt->execute()) {
        echo "Perfil atualizado com sucesso!";
    } else {
        echo "Erro ao atualizar perfil.";
    }
}

// Buscar dados atuais
$sql = "SELECT nome, email, foto FROM usuarios WHERE id=:id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":id", $id);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
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
            justify-content: center;
        }
        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(2px) saturate(180%);
            border: 0.5px solid rgba(255, 255, 255, 0.8);
            border-radius: 2rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2),
                inset 0 4px 20px rgba(255, 255, 255, 0.3);
            outline: none;
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
        #olho1f, #olho2f{
            display: none;
        }

        .senha-container {
            position: relative;
        }

        .toggle-senha {
            position: absolute;
            right: 20px;
            top: 30%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            width: 10px;
            height: 10px;
            background-color: transparent;
        }

        .toggle-senha svg {
            width: 20px;
            height: 20px;
        }

        form {
            background-color: #f5f5f5ff;
            padding: 40px;
            border-radius: 30px;
        }

        input {
            outline: none;
            border: none;
            background-color: #e5e5e5ff;
            border-radius: 20px;
            width: 430px;
            height: 45px;
            padding-left: 15px;
            font-family: poppins;
            font-size: 13px;
            margin-bottom: 15px;
        }


        label {
            font-size: 13px;
        }

        button:not(.toggle-senha) {
            width: 100%;
            height: 45px;
            border-radius: 999px;
            border: none;
            outline: none;
            background-color: #e5e5e5ff;
            font-size: 13px;
            font-family: poppins;
            background-color: #94b9ff;
        }

        button:not(.toggle-senha):hover {
            background-color: #cdffd8;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        button:not(.toggle-senha):not(:hover) {
            background-color: #94b9ff;
            transition: all 0.2s ease-in-out;
        }
    </style>
</head>
<body>
    <h2>Editar Perfil</h2>

    <!-- Mostra a foto atual -->
    <?php if (!empty($usuario['foto'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($usuario['foto']); ?>" 
             width="120" height="120" style="border-radius:50%;">
    <?php else: ?>
        <img src="uploads/default.png" 
             width="120" height="120" style="border-radius:50%;">
    <?php endif; ?>

    <form class="glass" method="post" enctype="multipart/form-data">
        Nome <input class="glass" type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>"><br>
        Email <input class="glass" type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>"><br>
        Nova senha <input class="glass" type="password" name="senha" placeholder="Deixe em branco para não alterar"><br>
        Foto <input style="padding-top: 20px;" class="glass" type="file" name="foto" accept="image/*"><br>
        <button type="submit">Salvar</button>
    </form>
</body>
</html>