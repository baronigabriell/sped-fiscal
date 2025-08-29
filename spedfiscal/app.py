from flask import Flask, render_template, request, redirect, url_for
import os

app = Flask(__name__)
app.config['UPLOAD_FOLDER'] = 'uploads'

# Criar pasta uploads caso não exista
if not os.path.exists(app.config['UPLOAD_FOLDER']):
    os.makedirs(app.config['UPLOAD_FOLDER'])

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/upload', methods=['POST'])
def upload_file():
    if 'arquivo' not in request.files:
        return "Nenhum arquivo enviado", 400
    
    arquivo = request.files['arquivo']
    if arquivo.filename == '':
        return "Nome de arquivo inválido", 400
    
    caminho = os.path.join(app.config['UPLOAD_FOLDER'], arquivo.filename)
    arquivo.save(caminho)
    
    # Ler todas as linhas do arquivo SPED
    with open(caminho, 'r') as file:
        linhas = file.readlines()
    
    # Separar por blocos
    blocos = {}
    for linha in linhas:
        linha = linha.strip()
        if linha == "":
            continue
        partes = linha.split("|")
        bloco = partes[0]  # primeira coluna é o bloco (0, C, D...)
        if bloco not in blocos:
            blocos[bloco] = []
        blocos[bloco].append(partes)
    
    return render_template('resultado.html', nome_arquivo=arquivo.filename, blocos=blocos)


if __name__ == '__main__':
    app.run(debug=True)
