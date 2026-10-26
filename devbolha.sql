-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/10/2026 às 01:04
-- Versão do servidor: 10.11.16-MariaDB
-- Versão do PHP: 8.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `devbolha`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `bloqueios`
--

CREATE TABLE `bloqueios` (
  `id_usuario_bloqueador` int(11) NOT NULL,
  `id_usuario_bloqueado` int(11) NOT NULL,
  `date_bloqueio` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `candidatura_projetos`
--

CREATE TABLE `candidatura_projetos` (
  `id_candidatura` int(11) NOT NULL,
  `id_projeto` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `mensagem` text NOT NULL,
  `status_candidatura` enum('pendente','aprovada','recusada','cancelada') NOT NULL DEFAULT 'pendente',
  `data_candidatura` datetime NOT NULL,
  `data_resposta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `comentarios`
--

CREATE TABLE `comentarios` (
  `id_comentario` int(11) NOT NULL,
  `id_publicacao` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `texto` text NOT NULL,
  `data_criacao` datetime NOT NULL,
  `data_atualizacao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `conexoes`
--

CREATE TABLE `conexoes` (
  `id_conexao` int(11) NOT NULL,
  `id_usuario_solicitante` int(11) NOT NULL,
  `id_usuario_destinatario` int(11) NOT NULL,
  `status_conexão` enum('pendente','aceita','recusada') NOT NULL DEFAULT 'pendente',
  `data_solicitacao` datetime NOT NULL,
  `data_resposta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `convites`
--

CREATE TABLE `convites` (
  `id_convite` int(1) NOT NULL,
  `id_usuario_criador` int(11) NOT NULL,
  `codigo_convite` int(11) NOT NULL,
  `status_convite` enum('disponivel','utilizado','expirado','cancelado') NOT NULL,
  `id_usuario_convidado` int(11) NOT NULL,
  `data_criação` date NOT NULL,
  `data_expiracao` datetime NOT NULL,
  `data_utilizacao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `curtidas`
--

CREATE TABLE `curtidas` (
  `id_usuario` int(11) NOT NULL,
  `id_publicacao` int(11) NOT NULL,
  `data_criacao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `interesses_profissionais`
--

CREATE TABLE `interesses_profissionais` (
  `id_interesse` int(11) NOT NULL,
  `nome_interesse` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id_mensagem` bigint(20) NOT NULL,
  `id_remetente` int(11) NOT NULL,
  `id_destinatario` int(11) NOT NULL,
  `texto` text NOT NULL,
  `data_envio` datetime NOT NULL,
  `data_leitura` datetime NOT NULL,
  `data_exclusao_remetente` datetime NOT NULL,
  `data_exclusao_destinatario` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfis`
--

CREATE TABLE `perfis` (
  `id_perfil` int(1) NOT NULL,
  `id_usuario` int(100) DEFAULT NULL,
  `foto` varchar(500) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `nivel_experiencia` enum('iniciante','estudante','intermediario','avançado','outro') NOT NULL,
  `disponivel_projetos` tinyint(1) DEFAULT NULL,
  `data_criação` date NOT NULL,
  `data_atualização` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `projetos`
--

CREATE TABLE `projetos` (
  `id_projeto` int(11) NOT NULL,
  `id_usuario_criador` int(11) NOT NULL,
  `nome_projeto` varchar(150) NOT NULL,
  `descricao` text NOT NULL,
  `objetivos` text NOT NULL,
  `status_projeto` enum('planejamento','em_andamento','concluido','cancelado') NOT NULL DEFAULT 'planejamento',
  `link_repositorio` varchar(2048) NOT NULL,
  `link_demonstracao` varchar(2048) NOT NULL,
  `data_criacao` datetime NOT NULL,
  `data_atualizacao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `publicacoes`
--

CREATE TABLE `publicacoes` (
  `id_publicacao` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `texto` text NOT NULL,
  `link_externo` varchar(2048) NOT NULL,
  `data_criacao` datetime NOT NULL,
  `date_atualizacao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tecnologias`
--

CREATE TABLE `tecnologias` (
  `id_tecnologia` int(11) NOT NULL,
  `nome_tecnologia` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `Nome` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel` enum('admin','user') NOT NULL DEFAULT 'user',
  `status` enum('ativa','desativada','excluida') NOT NULL DEFAULT 'ativa',
  `provedor_login` enum('local','google','github') NOT NULL DEFAULT 'local',
  `id_google` varchar(255) DEFAULT NULL,
  `id_github` varchar(255) DEFAULT NULL,
  `dcriação` date NOT NULL,
  `datualização` date NOT NULL,
  `ultimo_acesso` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `Nome`, `email`, `senha`, `nivel`, `status`, `provedor_login`, `id_google`, `id_github`, `dcriação`, `datualização`, `ultimo_acesso`) VALUES
(1, 'guilherme pereira da silva', 'guilherme.pereira.info@gmail.com', '12345678', 'admin', 'ativa', 'local', NULL, NULL, '2026-10-25', '2026-10-25', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario_interesses`
--

CREATE TABLE `usuario_interesses` (
  `id_usuario` int(11) NOT NULL,
  `id_interesse` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario_tecnologias`
--

CREATE TABLE `usuario_tecnologias` (
  `id_usuario` int(11) NOT NULL,
  `id_tecnologia` int(11) NOT NULL,
  `nivel_conhecimento` enum('basico','intermediario','avancado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id_comentario`);

--
-- Índices de tabela `conexoes`
--
ALTER TABLE `conexoes`
  ADD PRIMARY KEY (`id_conexao`);

--
-- Índices de tabela `convites`
--
ALTER TABLE `convites`
  ADD PRIMARY KEY (`id_convite`);

--
-- Índices de tabela `interesses_profissionais`
--
ALTER TABLE `interesses_profissionais`
  ADD PRIMARY KEY (`id_interesse`);

--
-- Índices de tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id_mensagem`);

--
-- Índices de tabela `perfis`
--
ALTER TABLE `perfis`
  ADD PRIMARY KEY (`id_perfil`);

--
-- Índices de tabela `publicacoes`
--
ALTER TABLE `publicacoes`
  ADD PRIMARY KEY (`id_publicacao`);

--
-- Índices de tabela `tecnologias`
--
ALTER TABLE `tecnologias`
  ADD PRIMARY KEY (`id_tecnologia`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `usuario_tecnologias`
--
ALTER TABLE `usuario_tecnologias`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `conexoes`
--
ALTER TABLE `conexoes`
  MODIFY `id_conexao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `convites`
--
ALTER TABLE `convites`
  MODIFY `id_convite` int(1) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `interesses_profissionais`
--
ALTER TABLE `interesses_profissionais`
  MODIFY `id_interesse` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id_mensagem` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `perfis`
--
ALTER TABLE `perfis`
  MODIFY `id_perfil` int(1) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `publicacoes`
--
ALTER TABLE `publicacoes`
  MODIFY `id_publicacao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tecnologias`
--
ALTER TABLE `tecnologias`
  MODIFY `id_tecnologia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuario_tecnologias`
--
ALTER TABLE `usuario_tecnologias`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
