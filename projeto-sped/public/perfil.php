<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../src/utils/Db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['usuario_id'];
$conn = Db::getConnection();

// Função para checar se é requisição AJAX (para fetch/JSON)
function isAjax()
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

// Atualizar dados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = ['success' => false, 'message' => 'Erro desconhecido'];

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $foto = null;

    // Diretório de uploads
    $diretorio = __DIR__ . "/uploads/";
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0777, true);
    }

    // Processar imagem (via $_FILES para fetch ou submit normal)
    if (!empty($_FILES['foto']['name'])) {
        $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($extensao, $extensoesPermitidas)) {
            $response['message'] = 'Formato de imagem inválido!';
            if (isAjax()) {
                echo json_encode($response);
                exit;
            } else {
                echo $response['message'];
                exit;
            }
        }

        if ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
            $response['message'] = 'O arquivo excede o tamanho máximo permitido (5MB).';
            if (isAjax()) {
                echo json_encode($response);
                exit;
            } else {
                echo $response['message'];
                exit;
            }
        }

        $nomeArquivo = uniqid() . "." . $extensao;
        $caminho = $diretorio . $nomeArquivo;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho)) {
            $foto = $nomeArquivo;
            $response['message'] = 'Foto processada com sucesso.';
        } else {
            $response['message'] = 'Erro ao salvar a foto.';
            if (isAjax()) {
                echo json_encode($response);
                exit;
            } else {
                echo $response['message'];
                exit;
            }
        }
    }

    // Preparar SQL baseado em senha e foto
    $sqlBase = "UPDATE usuarios SET nome=:nome, email=:email";
    $params = [':nome' => $nome, ':email' => $email, ':id' => $id];

    if (!empty($_POST['senha'])) {
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $sqlBase .= ", senha=:senha";
        $params[':senha'] = $senha;
    }

    if ($foto) {
        $sqlBase .= ", foto=:foto";
        $params[':foto'] = $foto;
    }

    $sql = $sqlBase . " WHERE id=:id";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    if ($stmt->rowCount() > 0) { // Verifica se algo foi atualizado
        $response['success'] = true;
        $response['message'] = 'Perfil atualizado com sucesso!';
        if ($foto) {
            $response['foto'] = $foto; // Retorna o nome da nova foto para JS atualizar preview
        }
    } else {
        $response['message'] = 'Nenhuma alteração foi feita ou erro ao atualizar.';
    }

    // Se for AJAX, retorne JSON e pare (não renderize HTML)
    if (isAjax()) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } else {
        // Para submit normal, ecoa e continua para HTML
        echo $response['message'];
    }
}

// Buscar dados atuais (só se não for AJAX)
if (!isAjax()) {
    $sql = "SELECT nome, email, foto FROM usuarios WHERE id=:id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">

    <style>
        body {
            background-image: url('../public/Design sem nome.png');
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
            /* fixa o background */
            min-height: 100vh;
            /* permite altura maior que viewport */
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            /* remove align-items e justify-content para não centralizar verticalmente */
        }

        .container {
            
            padding-top: 70px;
            /* espaço para a foto não ser cortada */
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
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

        #olho1f,
        #olho2f {
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
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 450px;
            margin-top: 40px;
        }

        label {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
            align-self: flex-start;
        }

        input,
        select {
            margin: 5px 0;
            width: 100%;
            box-sizing: border-box;
            height: 50px;
            border-radius: 20px;
            padding-left: 15px;
            background: #e5e5e5;
            border: none;
            font-family: 'Poppins', sans-serif;
        }

        /* Estilos para o Cropper */
        #imagemPreview {
            width: 100%;
            height: 300px;
            margin: 10px 0;
            border: 2px dashed #ddd;
            border-radius: 10px;
            overflow: hidden;
            background: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #imagemExibida {
            display: block;
            max-width: 100%;
            height: auto;
            max-height: 100%;
        }

        .cropper-container {
            width: 100% !important;
            height: 100% !important;
        }

        .cropper-view-box {
            border-radius: 50%;
            /* Para crop circular, se quiser */
            border: 2px solid #94b9ff;
        }

        .cropper-placeholder {
            display: none;
            /* Esconde placeholder padrão do cropper */
        }

        button:not(.toggle-senha) {
            padding: 15px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(148, 185, 255, 0.3);
            color: #3d3d3d;
            border-radius: 25px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            margin-top: 10px;
            width: 100%;
            margin-bottom: 80px;
        }

        button:not(.toggle-senha):hover {
            background: rgba(148, 185, 255, 0.3);
            color: white;
            transition: all 0.3s ease-in-out;
        }

        button:not(.toggle-senha):not(:hover) {
            background: rgba(255, 255, 255, 0.2);
            color: #3d3d3d;
            transition: all 0.3s ease-in-out;
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
            position: fixed;
        }
        header svg{
            height: 20px;
        }
        a{
            text-decoration: none;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
</head>

<header>
    <a href="dashboard.php">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
        </svg>
    </a>
</header>

<body>
    <div class="container">
        <?php if (!isAjax()): // Só renderiza HTML se não for AJAX 
        ?>
            <p>Editar perfil</p>
            <?php if (!empty($usuario['foto'])): ?>
                <img id="previewPerfil" src="uploads/<?php echo htmlspecialchars($usuario['foto']) . '?v=' . time(); ?>"
                    width="120" height="120" style="border-radius:50%;">
            <?php else: ?>
                <img id="previewPerfil" src="uploads/default.png?v=<?php echo time(); ?>"
                    width="120" height="120" style="border-radius:50%;">
            <?php endif; ?>

            <!-- Formulário -->
            <form method="post" enctype="multipart/form-data">
                <label for="blocos">Nome</label>
                <input class="glass" type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome'] ?? ''); ?>">

                <label for="blocos">Email</label>
                <input class="glass" type="email" name="email" value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>">

                <label for="blocos">Nova senha</label>
                <input class="glass" type="password" name="senha" placeholder="Deixe em branco para não alterar">

                <!-- Campo para foto -->
                <label for="blocos">Foto</label>
                <input style="padding-top: 15px;" class="glass" type="file" name="foto" accept="image/*" id="inputImagem"><br>

                <!-- Área para Cropper -->
                <div id="imagemPreview">
                    <img id="imagemExibida" src="" alt="Pré-visualização da imagem" style="display: none;" />
                    <p style="color: #666; text-align: center;">Selecione uma imagem para cortar</p>
                </div>

                <!-- Botão para salvar (já salva o crop!) -->
                <button type="submit">Salvar</button>
            </form>
    </div>
<?php endif; // Fim do if (!isAjax()) 
?>

<script>
    let cropper;

    document.addEventListener('DOMContentLoaded', function() {
        const inputImagem = document.getElementById('inputImagem');
        const imagemExibida = document.getElementById('imagemExibida');
        const previewContainer = document.getElementById('imagemPreview');
        const previewPerfil = document.getElementById('previewPerfil');
        const placeholderText = previewContainer ? previewContainer.querySelector('p') : null;

        if (inputImagem) {
            inputImagem.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file && imagemExibida) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagemExibida.src = e.target.result;
                        imagemExibida.style.display = 'block';
                        if (placeholderText) placeholderText.style.display = 'none'; // Esconde texto placeholder

                        // Destrói cropper anterior
                        if (cropper) {
                            cropper.destroy();
                        }

                        // Inicializa Cropper
                        cropper = new Cropper(imagemExibida, {
                            aspectRatio: 1, // Quadrado (para perfil)
                            viewMode: 1,
                            autoCropArea: 0.8,
                            responsive: true,
                            background: true,
                            minContainerWidth: 300,
                            minContainerHeight: 300,
                            dragMode: 'crop',
                            cropBoxResizable: true,
                            cropBoxMovable: true
                        });

                        console.log('Cropper inicializado com sucesso!');
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Submit do form (salva o crop!)
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();

                console.log('Submit iniciado...'); // Debug

                if (cropper) {
                    // Pega o canvas cortado (tamanho 120x120 para preview)
                    const canvas = cropper.getCroppedCanvas({
                        width: 120,
                        height: 120,
                        imageSmoothingEnabled: true,
                        imageSmoothingQuality: 'high'
                    });

                    if (!canvas) {
                        alert('Erro ao cortar a imagem. Tente novamente.');
                        return;
                    }

                    console.log('Canvas gerado, convertendo para blob...'); // Debug

                    // Converte para blob (usa 'image/jpeg' para qualidade melhor, ou detecta tipo)
                    const mimeType = 'image/jpeg'; // Ou 'image/png' se preferir transparência
                    canvas.toBlob(function(blob) {
                        if (!blob) {
                            alert('Erro ao processar a imagem cortada.');
                            return;
                        }

                        console.log('Blob criado:', blob.size + ' bytes, tipo:', blob.type); // Debug

                        const formData = new FormData();
                        const ext = mimeType.split('/')[1] || 'jpeg';
                        const fileName = 'foto_cortada.' + ext;

                        formData.append('foto', blob, fileName);
                        formData.append('nome', document.querySelector('input[name="nome"]').value);
                        formData.append('email', document.querySelector('input[name="email"]').value);
                        formData.append('senha', document.querySelector('input[name="senha"]').value || '');

                        // Envia via fetch (marca como AJAX)
                        fetch('', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest' // Para PHP detectar AJAX
                            }
                        }).then(response => {
                            console.log('Response status:', response.status); // Debug
                            if (!response.ok) {
                                throw new Error('HTTP error! status: ' + response.status);
                            }
                            return response.json(); // Espera JSON do PHP
                        }).then(data => {
                            console.log('Response data:', data); // Debug
                            if (data.success) {
                                alert(data.message);
                                // Atualiza preview da imagem no perfil (sem reload total, se houver nova foto)
                                if (previewPerfil && data.foto) {
                                    previewPerfil.src = 'uploads/' + data.foto + '?v=' + new Date().getTime();
                                } else {
                                    location.reload(true); // Força reload sem cache se não houver foto nova
                                }
                                // Limpa o input e cropper após sucesso
                                inputImagem.value = '';
                                if (cropper) {
                                    cropper.destroy();
                                    cropper = null;
                                }
                                imagemExibida.style.display = 'none';
                                if (placeholderText) placeholderText.style.display = 'block';
                            } else {
                                alert('Erro: ' + data.message);
                            }
                        }).catch(error => {
                            console.error('Erro no fetch:', error);
                            alert('Erro ao enviar dados. Verifique o console. Tentando envio normal...');
                            // Fallback: envia form normal se fetch falhar (sem crop, mas salva outros dados)
                            form.submit();
                        });
                    }, mimeType, 0.9); // Qualidade 90% para JPEG
                } else {
                    // Se não há cropper (sem imagem nova), envia form normal
                    console.log('Sem cropper, enviando form normal...');
                    form.submit();
                }
            });
        }
    });
</script>
</body>

</html>