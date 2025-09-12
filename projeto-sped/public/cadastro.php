<?php
// Inicia a sessão
session_start();

// Inclui o arquivo de conexão com o banco de dados
require_once __DIR__ . '/../src/utils/Db.php';

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verifica se o email e a senha foram preenchidos
    if (isset($_POST['email']) && isset($_POST['senha']) && !empty($_POST['email']) && !empty($_POST['senha'])) {

        // Obtém os dados do formulário
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
                $stmt = $pdo->prepare("INSERT INTO usuarios (email, senha) VALUES (:email, :senha)");
                $stmt->execute([
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
?>

<!-- Formulário de cadastro -->
<form method="POST" action="cadastro.php">
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>

    <label for="senha">Senha:</label>
    <input type="password" name="senha" id="senha" required>

    <button type="submit">Cadastrar</button>
</form>
