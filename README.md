# DevBolha — versão PHP + MySQL/MariaDB (XAMPP)

Esta versão substitui a dependência do Supabase por PHP tradicional, PDO e MySQL/MariaDB. A interface mantém o tema escuro e os arquivos visuais do projeto original. O banco passa a persistir cadastro, perfis, feed, curtidas, comentários, conexões, mensagens, projetos e candidaturas.

## Antes de começar

- Requer XAMPP com PHP 8.1+ e MySQL/MariaDB.
- No phpMyAdmin, exporte/guarde seu banco atual se houver dados que queira preservar.
- O `database.sql` deste pacote **recria as tabelas do banco `devbolha`**. Execute apenas se puder apagar a estrutura antiga. Ele não cria um usuário administrador nem inclui a senha em texto puro que apareceu no SQL enviado.
- O arquivo `.env` é local e não deve ser publicado.

## Instalação no XAMPP

1. Copie a pasta `devbolha` para `C:\xampp\htdocs\devbolha`.
2. Abra o XAMPP Control Panel e inicie **Apache** e **MySQL**.
3. Acesse `http://localhost/phpmyadmin`.
4. Importe o arquivo `database.sql` pela aba **Importar**. O script cria/usa o banco `devbolha` e recria as tabelas.
5. Copie `.env.example` para `.env`. Para uma instalação XAMPP padrão, os valores podem ficar assim:

   ```ini
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=devbolha
   DB_USER=root
   DB_PASS=
   ```

   Se seu usuário root tiver senha, preencha `DB_PASS`.

6. Crie a conta administrativa pelo terminal do Windows:

   ```bat
   cd C:\xampp\htdocs\devbolha
   set ADMIN_PASSWORD=UmaSenhaForteCom12Caracteres!
   set ADMIN_EMAIL=admin@devbolha.local
   C:\xampp\php\php.exe setup_admin.php
   ```

   Troque o exemplo por uma senha forte, exclusiva e que você não usa em outros serviços. O script só pode ser executado via CLI e não altera contas existentes. Ele cria três convites iniciais.

7. Abra `http://localhost/devbolha/login.php` e entre com o e-mail administrativo definido em `ADMIN_EMAIL` e a senha usada no terminal.
8. Para testar cadastro, abra `http://localhost/devbolha/login.php?modo=cadastro` e use um código de convite disponível. Os códigos podem ser consultados na tabela `convites` pelo phpMyAdmin.

## O que está conectado ao banco nesta versão

- Cadastro por convite, hash de senha, login, sessão e logout.
- Criação e edição do perfil, tecnologias e até três interesses profissionais.
- Feed cronológico, publicações com texto/link, curtidas únicas e comentários.
- Solicitações de conexão, aceitar/recusar, bloqueio e encerramento da conexão.
- Mensagens privadas apenas entre conexões aceitas e sem bloqueios.
- Criação de projetos, candidatura, aprovação/recusa e registro de participantes.
- Contagem de notificações e painel básico de administração/denúncias.

## Limitações conhecidas desta versão

- Login social Google/GitHub, recuperação de senha por e-mail e upload de imagens não foram configurados; os campos estão previstos no banco, mas precisam de provedores/armazenamento e fluxos próprios.
- As denúncias podem ser consultadas pelo painel, mas o formulário público de criação de denúncia ainda precisa ser adicionado.
- Não há ferramenta completa de administração para suspender contas ou moderar publicações ainda.
- A regra de limite de três interesses é validada na aplicação; não há trigger de banco para essa regra.
- Antes de publicar na internet, é necessário revisar CSRF em todos os fluxos, limites de tentativas, HTTPS, recuperação de senha, exclusão/anonymização de conta e moderação.

## Segurança

- As senhas são salvas com `password_hash()` e verificadas com `password_verify()`.
- As consultas dinâmicas usam PDO com prepared statements.
- Não publique `.env` nem a senha administrativa.
- Não use o servidor embutido do PHP ou XAMPP como hospedagem pública de produção.
- Criador do projeto pode editar informações e status; membros aprovados podem sair da equipe.
- A área de notificações lista avisos gerados por conexões, curtidas, comentários, candidaturas e mensagens.
