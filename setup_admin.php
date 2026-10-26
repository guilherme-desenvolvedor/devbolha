<?php
/** Execute uma vez pelo terminal: php setup_admin.php */
require_once __DIR__ . '/conexao.php';
if (PHP_SAPI !== 'cli') { http_response_code(403); exit("Execute este script somente pelo terminal.\n"); }
$email = getenv('ADMIN_EMAIL') ?: 'admin@devbolha.local';
$senha = getenv('ADMIN_PASSWORD') ?: '';
if (strlen($senha) < 12) { fwrite(STDERR,"Configure ADMIN_PASSWORD no ambiente com pelo menos 12 caracteres antes de executar.\nExemplo no Windows CMD: set ADMIN_PASSWORD=UmaSenhaForte123!\n"); exit(1); }
$pdo=db(); $pdo->beginTransaction();
try {
 $st=$pdo->prepare('SELECT id_usuario FROM usuarios WHERE email=?'); $st->execute([$email]);
 if ($st->fetch()) throw new RuntimeException('Já existe uma conta com esse e-mail. O script não altera senhas existentes.');
 $st=$pdo->prepare("INSERT INTO usuarios (nome,sobrenome,email,senha,tipo_usuario,status_conta,provedor_login) VALUES ('Admin','DevBolha',?,?, 'administrador','ativa','local')"); $st->execute([$email,password_hash($senha,PASSWORD_DEFAULT)]); $id=(int)$pdo->lastInsertId();
 $pdo->prepare('INSERT INTO perfis (id_usuario) VALUES (?)')->execute([$id]);
 for($i=0;$i<3;$i++) $pdo->prepare("INSERT INTO convites (id_usuario_criador,codigo_convite,status_convite) VALUES (?,?,'disponivel')")->execute([$id,strtoupper(bin2hex(random_bytes(6)))]);
 $pdo->commit(); echo "Administrador criado: {$email}\nTrês convites foram gerados. Consulte-os no banco.\n";
} catch(Throwable $e) { if($pdo->inTransaction())$pdo->rollBack(); fwrite(STDERR,$e->getMessage()."\n"); exit(1); }
