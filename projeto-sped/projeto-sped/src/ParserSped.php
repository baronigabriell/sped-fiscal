<?php
// src/utils/ParserSped.php
class ParserSped {
    private $filepath;

    public function __construct($filepath) {
        $this->filepath = $filepath;
    }

    public function analyze() {
        $handle = fopen($this->filepath, "r");
        $total_blocos = 0;
        $total_registros = 0;
        $total_caracteres = 0;
        $blocos_presentes = [];
        $erros = [];

        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $total_registros++;
                $total_caracteres += strlen($line);

                // Exemplo: identificar o bloco pelo primeiro campo
                $fields = explode("|", trim($line));
                $bloco = $fields[1] ?? null;

                if ($bloco) {
                    if (!isset($blocos_presentes[$bloco])) {
                        $blocos_presentes[$bloco] = 0;
                        $total_blocos++;
                    }
                    $blocos_presentes[$bloco]++;
                } else {
                    $erros[] = "Linha {$total_registros} com formato inválido";
                }
            }
            fclose($handle);
        } else {
            $erros[] = "Erro ao abrir o arquivo";
        }

        return [
            "total_blocos" => $total_blocos,
            "total_registros" => $total_registros,
            "total_caracteres" => $total_caracteres,
            "blocos_presentes" => $blocos_presentes,
            "erros" => $erros
        ];
    }
    public function buscarBloco($codigo) {
    $handle = fopen($this->filepath, "r");
    $linhas = [];

    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $fields = explode("|", trim($line));
            if (($fields[1] ?? null) === $codigo) {
                $linhas[] = $line;
            }
        }
        fclose($handle);
    }

    return $linhas;
}

}
?>
