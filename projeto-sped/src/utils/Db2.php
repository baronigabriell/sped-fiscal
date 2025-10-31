<?php
// Linha 2: A ÚNICA CLASSE neste arquivo é DbBusca.
class DbBusca { 
    // É importante que as credenciais aqui apontem para o banco de dados SPED (se for diferente)
    private static $host = 'localhost';
    private static $dbname = 'sped fiscal'; // Nome do banco de busca
    private static $username = 'root';
    private static $password = '';

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