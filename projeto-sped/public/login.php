<?php
session_start();
require_once __DIR__ . '/../src/utils/Db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && isset($_POST['senha']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $pdo = Db::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            if (password_verify($senha, $usuario['senha'])) {
                // 👇 Agora sim, tudo existe e está validado:
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario'] = $usuario['email'];
                $_SESSION['nome'] = $usuario['nome']; // ✅ Aqui está certo!

                header("Location: dashboard.php");
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
    <input type="password" name="senha" id="senha" required>
    <br>
    <button type="submit">Entrar</button>
</form>
<style>
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
        margin-bottom: 5%;
        font-family: poppins;
        font-size: 13px;
    }
    label{
        font-size: 13px;
    }
    button{
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
    button:hover{
        background-color: #cdffd8;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }
    button:not(:hover){
        background-color: #94b9ff;
        transition: all 0.2s ease-in-out;
    }
</style>