<?php
session_start();
require_once __DIR__ . '/../src/utils/Db.php'; // Inclui a conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se os campos de email e senha foram enviados
    if (isset($_POST['email']) && isset($_POST['senha']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
        $email = $_POST['email'];
        $senha = $_POST['senha'];  // A senha fornecida pelo usuário

        // Conecta ao banco de dados
        $pdo = Db::getConnection();
        // Consulta para verificar o email no banco
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch(); // Retorna os dados do usuário encontrado

        if ($usuario) {
            // Verifica se a senha fornecida bate com o hash no banco
            if (password_verify($senha, $usuario['senha'])) {
                // Senha correta, inicia a sessão
                $_SESSION['usuario_id'] = $usuario['id'];
                header("Location: upload.php");  // Redireciona para a página de upload
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

<!-- Formulário de login -->
<form method="POST" action="login.php">
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>

    <label for="senha">Senha:</label>
    <input type="password" name="senha" id="senha" required>

    <button type="submit">Entrar</button>
</form>
