<?php
declare(strict_types=1);

$configLocal = __DIR__ . '/config.local.php';
$config = is_file($configLocal) ? (require $configLocal) : [];

$host = getenv('DB_HOST') ?: ($config['host'] ?? '127.0.0.1');
$dbname = getenv('DB_NAME') ?: ($config['name'] ?? 'crud_produtos');
$user = getenv('DB_USER') ?: ($config['user'] ?? 'root');
$password = getenv('DB_PASSWORD') ?: ($config['password'] ?? '');
$port = getenv('DB_PORT') ?: ($config['port'] ?? '3306');

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    error_log('Falha na conexão com o banco: ' . $e->getMessage());
    http_response_code(500);
    exit('Não foi possível conectar ao banco de dados. Verifique se o MySQL/MariaDB está iniciado, se o banco crud_produtos foi importado e se a configuração em config.local.php ou nas variáveis DB_* está correta.');
}
