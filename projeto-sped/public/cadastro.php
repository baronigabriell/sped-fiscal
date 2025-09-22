<?php
// Inicia a sessão
session_start();

// Inclui o arquivo de conexão com o banco de dados
require_once __DIR__ . '/../src/utils/Db.php';

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verifica se o email e a senha foram preenchidos
    if (isset($_POST['nome']) && isset($_POST['email']) && isset($_POST['senha']) && !empty($_POST['nome']) && !empty($_POST['email']) && !empty($_POST['senha'])) {

        // Obtém os dados do formulário
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        // Verifica se o email já está cadastrado
        $pdo = Db::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $usuarioExistente = $stmt->fetch();

        if ($usuarioExistente) {
            // Se o email já estiver cadastrado, exibe uma mensagem de erro
            echo "Este email já está cadastrado!";
        } else {
            // Gera o hash da senha utilizando password_hash
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            try {
                // Insere os dados do usuário no banco de dados
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)");
                $stmt->execute([
                    'nome' => $nome,
                    'email' => $email,
                    'senha' => $senha_hash  // Aqui, armazenamos o hash da senha
                ]);

                // Mensagem de sucesso
                echo "Cadastro realizado com sucesso! Agora você pode <a href='login.php'>entrar</a>.";
            } catch (PDOException $e) {
                // Em caso de erro de duplicação, exibe uma mensagem
                if ($e->getCode() == 23000) {
                    echo "Erro: Este email já está registrado!";
                } else {
                    echo "Erro ao cadastrar usuário: " . $e->getMessage();
                }
            }
        }
    } else {
        echo "Por favor, preencha todos os campos!";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['email'], $_POST['senha'], $_POST['confirmar_senha'], $_POST['nome']) &&
        !empty($_POST['email']) &&
        !empty($_POST['senha']) &&
        !empty($_POST['confirmar_senha']) &&
        !empty($_POST['nome'])
    ) {
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $confirmar_senha = $_POST['confirmar_senha'];
        $nome = $_POST['nome'];

        // 🛑 Verifica se as senhas batem
        if ($senha !== $confirmar_senha) {
            echo "As senhas não coincidem!";
            exit;
        }

        // ... aqui segue o resto do seu código:
        // - conexão com banco
        // - verificação se email já existe
        // - inserção do usuário etc.
    }
}
?>

<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<form method="POST" action="cadastro.php">
    <h1>Faça seu cadastro</h1>
    <label for="nome">Nome</label>
    <br>
    <input type="nome" name="nome" id="nome" required>
    <br>
    <label for="email">Email</label>
    <br>
    <input type="email" name="email" id="email" required>
    <br>
    <label for="senha">Crie uma senha</label>
    <br>
    <div class="senha-container">
        <input type="password" name="senha" id="senha" required pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[$*&@#])(?:([0-9a-zA-Z$*&@#])(?!\1)){8,}$" title="A senha deve ter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma minúscula, um número e um caractere especial ($*&@#). Não pode ter caracteres repetidos seguidos.">
        <button type="button" class="toggle-senha" onclick="toggleSenha('senha', 'olho1a', 'olho1f')">
            <svg id="olho1a" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            <svg id="olho1f" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
            </svg>
        </button>
    </div>
    <label for="confirmar_senha">Repita a senha</label>

    <div class="senha-container" style="margin-bottom: 5%;">
        <input type="password" name="confirmar_senha" id="confirmar_senha" required>
        <button type="button" class="toggle-senha" onclick="toggleSenha('confirmar_senha', 'olho2a', 'olho2f')">
            <svg id="olho2a" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            <svg id="olho2f" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
            </svg>
        </button>
    </div>
    <button type="submit">Cadastrar</button>
    <p style="font-style: italic; font-size: 13px; text-align: center;">Já tem uma conta? Faça o
        <a href="login.php">login</a>
    </p>
</form>
<style>
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
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        const senha = document.getElementById('senha').value;
        const confirmar = document.getElementById('confirmar_senha').value;

        if (senha !== confirmar) {
            alert('As senhas não coincidem!');
            e.preventDefault();
        }
    });

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