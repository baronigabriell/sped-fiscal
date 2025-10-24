<?php
class Db {
    private static $host = 'localhost';  // Endereço do banco de dados
    private static $dbname = 'sped fiscal';  // Nome do banco de dados
    private static $username = 'root';  // Usuário do banco
    private static $password = '';  // Senha do banco (vazio para local)

    private static $connection;

    public static function getConnection() {
        if (self::$connection === null) {
            try {
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$dbname;
                self::$connection = new PDO($dsn, self::$username, self::$password);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erro ao conectar ao banco de dados: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}
?>