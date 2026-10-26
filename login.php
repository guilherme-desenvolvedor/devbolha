<?php
declare(strict_types=1);
require_once __DIR__ . '/conexao.php';
if (!empty($_SESSION['usuario_id']) && usuario_atual()) redirecionar('index.php');
$modo = (($_GET['modo'] ?? $_POST['modo'] ?? 'entrar') === 'cadastro') ? 'cadastro' : 'entrar';
$erro = ''; $sucesso = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 try {
  validar_csrf(); $modo = (($_POST['modo'] ?? 'entrar') === 'cadastro') ? 'cadastro' : 'entrar';
  $email = mb_strtolower(trim((string)($_POST['email'] ?? ''))); $senha = (string)($_POST['senha'] ?? '');
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Informe um e-mail válido.');
  if ($senha === '') throw new RuntimeException('Informe sua senha.');
  if ($modo === 'entrar') {
   $st = db()->prepare("SELECT id_usuario,nome,sobrenome,email,senha,tipo_usuario,status_conta FROM usuarios WHERE email=? LIMIT 1"); $st->execute([$email]); $u=$st->fetch();
   if (!$u || !$u['senha'] || !password_verify($senha, $u['senha'])) throw new RuntimeException('E-mail ou senha incorretos.');
   if ($u['status_conta'] !== 'ativa') throw new RuntimeException('Esta conta não está ativa. Entre em contato com a administração.');
   session_regenerate_id(true); $_SESSION['usuario_id']=(int)$u['id_usuario']; $_SESSION['csrf']=bin2hex(random_bytes(32));
   db()->prepare('UPDATE usuarios SET ultimo_acesso=NOW() WHERE id_usuario=?')->execute([$u['id_usuario']]);
   redirecionar('index.php');
  }
  $nome=trim((string)($_POST['nome']??'')); $sobrenome=trim((string)($_POST['sobrenome']??'')); $codigo=strtoupper(trim((string)($_POST['codigo_convite']??'')));
  if ($nome==='' || $sobrenome==='' || $codigo==='') throw new RuntimeException('Preencha nome, sobrenome e código do convite.');
  if (mb_strlen($senha)<8) throw new RuntimeException('A senha precisa ter pelo menos 8 caracteres.');
  $pdo=db(); $pdo->beginTransaction();
  $st=$pdo->prepare("SELECT * FROM convites WHERE codigo_convite=? AND status_convite='disponivel' AND (data_expiracao IS NULL OR data_expiracao>NOW()) FOR UPDATE"); $st->execute([$codigo]); $convite=$st->fetch();
  if (!$convite) throw new RuntimeException('Convite inválido, expirado ou já utilizado.');
  $st=$pdo->prepare('SELECT id_usuario FROM usuarios WHERE email=?'); $st->execute([$email]); if ($st->fetch()) throw new RuntimeException('Já existe uma conta com esse e-mail.');
  $st=$pdo->prepare("INSERT INTO usuarios (nome,sobrenome,email,senha,tipo_usuario,status_conta,provedor_login) VALUES (?,?,?,?,'membro','ativa','local')");
  $st->execute([$nome,$sobrenome,$email,password_hash($senha,PASSWORD_DEFAULT)]); $id=(int)$pdo->lastInsertId();
  $pdo->prepare('INSERT INTO perfis (id_usuario) VALUES (?)')->execute([$id]);
  $pdo->prepare("UPDATE convites SET status_convite='utilizado',id_usuario_convidado=?,data_utilizacao=NOW() WHERE id_convite=?")->execute([$id,$convite['id_convite']]);
  for($i=0;$i<3;$i++){ $codigoNovo=strtoupper(bin2hex(random_bytes(6))); $pdo->prepare("INSERT INTO convites (id_usuario_criador,codigo_convite,status_convite) VALUES (?,?,'disponivel')")->execute([$id,$codigoNovo]); }
  $pdo->commit(); $sucesso='Conta criada! Agora você já pode entrar com seu e-mail e senha.'; $modo='entrar';
 } catch (Throwable $e) { if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) $pdo->rollBack(); $erro=$e instanceof RuntimeException ? $e->getMessage() : 'Não foi possível concluir a operação. Confira a configuração e tente novamente.'; if (!($e instanceof RuntimeException)) error_log('DevBolha login/cadastro: '.$e->getMessage()); }
}
?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= $modo==='cadastro'?'Criar conta':'Entrar' ?> — DevBolha</title><link rel="stylesheet" href="style.css"><style>
.auth-wrap{min-height:100vh;display:grid;place-items:center;padding:28px;background:radial-gradient(ellipse at top,#123b7a44,transparent 55%),var(--bg,#0b0d12);color:var(--text,#f4f7fb)}.auth-card{width:min(100%,480px);padding:34px;border:1px solid #ffffff18;border-radius:24px;background:#11151def;box-shadow:0 24px 80px #0005}.auth-brand{font-size:28px;font-weight:800;letter-spacing:-1px;margin-bottom:28px}.auth-brand span{color:#00b8d9}.auth-card h1{font-size:28px;margin:0 0 8px}.auth-muted{color:#9ca9bc;margin:0 0 24px;line-height:1.6}.auth-tabs{display:flex;gap:8px;padding:5px;background:#080b10;border-radius:12px;margin-bottom:22px}.auth-tabs a{flex:1;text-align:center;padding:11px;border-radius:9px;text-decoration:none;color:#aab5c6;font-weight:700}.auth-tabs a.active{background:#123b7a;color:white}.auth-card label{display:block;margin:14px 0 6px;font-size:13px;color:#c5cedb}.auth-card input{width:100%;padding:13px 14px;border:1px solid #303948;background:#0b0f16;color:white;border-radius:10px;outline:none}.auth-card input:focus{border-color:#00b8d9}.auth-submit{width:100%;margin-top:22px;border:0;border-radius:10px;padding:14px;background:#00b8d9;color:#04121a;font-weight:800;cursor:pointer}.auth-error,.auth-success{padding:12px 14px;border-radius:10px;margin:0 0 16px;overflow-wrap:anywhere}.auth-error{background:#7f1d1d44;border:1px solid #ef444466;color:#fecaca}.auth-success{background:#064e3b55;border:1px solid #10b98166;color:#a7f3d0}.auth-note{font-size:12px;color:#8c9aaf;line-height:1.5;margin-top:18px}.auth-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}@media(max-width:480px){.auth-card{padding:24px}.auth-grid{grid-template-columns:1fr}}
</style></head><body><main class="auth-wrap"><section class="auth-card"><div class="auth-brand">◉ dev<span>bolha</span></div><h1><?= $modo==='cadastro'?'Entre para a comunidade.':'Bom ter você de volta.' ?></h1><p class="auth-muted"><?= $modo==='cadastro'?'A DevBolha é uma comunidade fechada. Você precisa de um convite válido para criar sua conta.':'Conecte-se à sua rede de desenvolvedores.' ?></p><nav class="auth-tabs"><a class="<?= $modo==='entrar'?'active':'' ?>" href="login.php?modo=entrar">Entrar</a><a class="<?= $modo==='cadastro'?'active':'' ?>" href="login.php?modo=cadastro">Tenho um convite</a></nav>
<?php if($erro): ?><div class="auth-error"><?=h($erro)?></div><?php endif; ?><?php if($sucesso): ?><div class="auth-success"><?=h($sucesso)?></div><?php endif; ?>
<form method="post" autocomplete="on"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="modo" value="<?=h($modo)?>">
<?php if($modo==='cadastro'): ?><div class="auth-grid"><div><label for="nome">Nome</label><input id="nome" name="nome" required maxlength="100" autocomplete="given-name" value="<?=h($_POST['nome']??'')?>"></div><div><label for="sobrenome">Sobrenome</label><input id="sobrenome" name="sobrenome" required maxlength="100" autocomplete="family-name" value="<?=h($_POST['sobrenome']??'')?>"></div><div style="grid-column:1/-1"><label for="codigo_convite">Código do convite</label><input id="codigo_convite" name="codigo_convite" required maxlength="100" autocomplete="off" placeholder="Cole seu convite aqui" value="<?=h($_POST['codigo_convite']??'')?>"></div></div><?php endif; ?>
<label for="email">E-mail</label><input id="email" name="email" type="email" required autocomplete="email" value="<?=h($_POST['email']??'')?>" placeholder="voce@exemplo.com"><label for="senha">Senha</label><input id="senha" name="senha" type="password" required minlength="8" autocomplete="<?= $modo==='entrar'?'current-password':'new-password' ?>" placeholder="Mínimo de 8 caracteres"><button class="auth-submit" type="submit"><?= $modo==='cadastro'?'Criar conta com convite':'Entrar na DevBolha' ?> ↗</button></form><p class="auth-note">O acesso administrativo deve ser configurado pelo administrador do projeto usando o script de instalação local. Nunca compartilhe senhas.</p></section></main></body></html>
