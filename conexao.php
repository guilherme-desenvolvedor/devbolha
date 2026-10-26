<?php
declare(strict_types=1);
/** Conexão central do DevBolha. Configure DB_* no .env. */
if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params(['httponly'=>true,'secure'=>$secure,'samesite'=>'Lax']);
    session_start();
}

function carregar_configuracao(): void {
    $arquivo = __DIR__ . '/.env';
    if (!is_file($arquivo)) return;
    foreach (file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {
        $linha = trim($linha);
        if ($linha === '' || str_starts_with($linha, '#') || !str_contains($linha, '=')) continue;
        [$chave, $valor] = explode('=', $linha, 2);
        $chave = trim($chave); $valor = trim(trim($valor), "\"'");
        if (getenv($chave) === false) putenv($chave . '=' . $valor);
    }
}
carregar_configuracao();

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $nome = getenv('DB_NAME') ?: 'devbolha';
    $porta = getenv('DB_PORT') ?: '3306';
    $usuario = getenv('DB_USER') ?: 'root';
    $senha = getenv('DB_PASS') ?: '';
    $dsn = "mysql:host={$host};port={$porta};dbname={$nome};charset=utf8mb4";
    try {
        $pdo = new PDO($dsn, $usuario, $senha, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        error_log('DevBolha DB connection: ' . $e->getMessage());
        throw new RuntimeException('Não foi possível conectar ao banco. Confira o arquivo .env e se o MySQL está ativo no XAMPP.');
    }
}
function h(mixed $valor): string { return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function csrf_token(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function validar_csrf(): void { if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) throw new RuntimeException('Formulário expirado. Atualize a página e tente novamente.'); }
function redirecionar(string $destino): never { header('Location: ' . $destino); exit; }
function usuario_atual(): ?array {
    if (empty($_SESSION['usuario_id'])) return null;
    $stmt = db()->prepare("SELECT u.id_usuario, u.nome, u.sobrenome, u.email, u.tipo_usuario, u.status_conta, p.foto_perfil, p.biografia, p.nivel_experiencia, p.disponivel_projetos FROM usuarios u LEFT JOIN perfis p ON p.id_usuario=u.id_usuario WHERE u.id_usuario=? AND u.status_conta='ativa' LIMIT 1");
    $stmt->execute([(int)$_SESSION['usuario_id']]); $u = $stmt->fetch();
    if (!$u) { unset($_SESSION['usuario_id']); return null; }
    return $u;
}
function exigir_login(): array { $u = usuario_atual(); if (!$u) redirecionar('login.php'); return $u; }
function flash(string $mensagem, string $tipo='ok'): void { $_SESSION['flash'] = ['mensagem'=>$mensagem,'tipo'=>$tipo]; }
function obter_flash(): ?array { $f=$_SESSION['flash']??null; unset($_SESSION['flash']); return $f; }
function e_data(?string $data): string { if (!$data) return ''; $t=strtotime($data); return $t ? date('d/m/Y H:i', $t) : ''; }
