<?php
declare(strict_types=1);

$host = 'localhost';
$banco = 'supermercado';
$usuario = 'root';
$senha = '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$banco};charset=utf8mb4",
        $usuario,
        $senha,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    exit('Não foi possível conectar ao banco. Confira se o MySQL está ligado e se importou database.sql. Detalhe: ' . htmlspecialchars($e->getMessage()));
}
