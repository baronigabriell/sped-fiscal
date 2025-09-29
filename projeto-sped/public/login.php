<?php
session_start();
require_once __DIR__ . '/../src/utils/Db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && isset($_POST['senha']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
        $email = trim($_POST['email']);
        $senha = $_POST['senha'];

        // Validação do email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Por favor, insira um email válido.";
            exit();
        }

        $pdo = Db::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            // Verificando a senha
            if (password_verify($senha, $usuario['senha'])) {
                // 👇 Salva as informações do usuário na sessão
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario'] = $usuario['email'];
                $_SESSION['nome'] = $usuario['nome']; // ✅ Nome está correto!

                // Redireciona o usuário para a página anterior ou o dashboard
                $redirectUrl = isset($_SESSION['redirect_to']) ? $_SESSION['redirect_to'] : 'dashboard.php';
                header("Location: $redirectUrl");
                exit();
            } else {
                echo "Usuário ou senha incorretos!";
            }
        } else {
            echo "Usuário não encontrado!";
        }
    } else {
        echo "Por favor, preencha todos os campos!";
    }
}
?>

<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<form method="POST" action="login.php">
    <h1>Faça seu login</h1>
    
    <label for="email">Email:</label>
    <br>
    <input type="email" name="email" id="email" required>
    <br>
    <label for="senha">Senha:</label>
    <br>
    <div class="senha-container" style="margin-bottom: 5%;">
        <input type="password" name="senha" id="senha" required>
        <button type="button" class="toggle-senha" onclick="toggleSenha('senha', 'olhoa', 'olhof')">
            <svg id="olhoa" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            <svg id="olhof" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
            </svg>
        </button>
    </div>
    <button type="submit">Entrar</button>
</form>
<style>
    #olhof{
        display: none;
    }

        .senha-container {
            position: relative;
        }

        .toggle-senha {
            position: absolute;
            right: 20px;
            top: 40%;
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

    #email{
        margin-bottom: 5%;
    }

    body {
        background-image: url('../public/backg.png');
        background-repeat: no-repeat;
        background-size: cover;
        justify-content: center;
        display: flex;
        align-items: center;
        height: 97vh;
        font-family: poppins, Segoe UI;
    }
    form{
        background-color: #f5f5f5ff;
        padding: 40px;
        border-radius: 30px;
    }
    input{
        outline: none;
        border: none;
        background-color: #e5e5e5ff;
        border-radius: 20px;
        width: 430px;
        height: 45px;
        padding-left: 15px;
        font-family: poppins;
        font-size: 13px;
    }
    label{
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
<script>
    function toggleSenha(inputId, olhoAbertoId, olhoFechadoId) {
        const input = document.getElementById(inputId);
        const olhoAberto = document.getElementById(olhoAbertoId);
        const olhoFechado = document.getElementById(olhoFechadoId);

        const isVisible = input.type === "text";
        input.type = isVisible ? "password" : "text";
        olhoAberto.style.display = isVisible ? "inline" : "none";
        olhoFechado.style.display = isVisible ? "none" : "inline";
    }
</script>