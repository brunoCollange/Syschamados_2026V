-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 19/03/2026 às 15:02
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `syschamados`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `chamados`
--

CREATE TABLE `chamados` (
  `id` int(255) NOT NULL,
  `problema` text NOT NULL,
  `setor` varchar(255) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `comentario` text NOT NULL,
  `momento_registro` datetime NOT NULL,
  `mes_resolvido` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `resolvidos`
--

CREATE TABLE `resolvidos` (
  `id` int(11) NOT NULL,
  `problema_r` text NOT NULL,
  `setor_r` varchar(255) NOT NULL,
  `nome_r` varchar(255) NOT NULL,
  `comentario_r` text NOT NULL,
  `momento_registro_r` datetime NOT NULL,
  `mes_resolvido_r` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `resolvidos`
--

INSERT INTO `resolvidos` (`id`, `problema_r`, `setor_r`, `nome_r`, `comentario_r`, `momento_registro_r`, `mes_resolvido_r`) VALUES
(1396, 'bom dia bruno, o telefone nao esta realizando chamada', 'RTenf', 'Karol', '', '2026-03-02 11:04:43', 3),
(1397, 'fio impressora', 'agendamentos', 'lari', '', '2026-03-06 10:10:45', 3),
(1398, 'Ainda está cortando a evolução médica na hora de imprimir', 'A.Social', 'Nathália Santos Martinez', '', '2026-03-11 09:38:21', 3),
(1399, 'Registro do médico Robson está incorreto no sistema', 'A.Social', 'Nathália Santos Martinez', '283514 crm correto, 7766 crm incorreto', '2026-03-11 11:26:31', 3),
(1400, 'O TOTEM NAO ESTA SAINDO SENHA ', 'RTenf', 'Rayane Rodrigues', '', '2026-03-13 10:37:03', 3),
(1401, 'Bruno a internet nao esta pegando, ai altomaticamento o telefone tambem nao', 'RTenf', 'Rayane Rodrigues', 'me ajudaa', '2026-03-13 14:40:51', 3),
(1402, 'Bruno, a impressora não quer imprimir dos dois lados. Pode me ajudar por favor?!', 'faturamento', 'CLARA FONTE BASSO', '', '2026-03-16 08:46:59', 3),
(1403, 'Bruno, a impressora fica fazendo um barulho como se fosse emperrar papel, eu nao sei se é falta de oleo, de lubrificaçao kkk', 'RTenf', 'Karol', '', '2026-03-16 13:05:35', 3),
(1404, 'Bruno o telefone da recepçao nao esta recebendo ligaçao ', 'RTenf', 'Rayane Rodrigues', '', '2026-03-17 12:39:36', 3),
(1405, 'ONDE FICA APAC NO SISTEMA', 'const1', 'BONAMIGO', 'ONDE ESTÁ A APAC', '2026-03-17 15:36:09', 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo_permissao` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `login`, `senha`, `tipo_permissao`) VALUES
(22, 'recepção', '$2y$10$S9A7L7ckVrdzz5CX6NWrp.CxUtAxJdDZkBEzF6YV1w9CsPnPT6z1i', 'usuario'),
(23, 'adm', '$2y$10$Th.JX7tG1FEK4Pz9Yh5c9.RUQMGefXl7SqT96kcJE38EGnoqD1kcK', 'usuario'),
(24, 'const1', '$2y$10$jpRhBOE545ABqFZimBm9keKNWc.7SealBdWhA31XiB/tK1saixI72', 'usuario'),
(25, 'const2', '$2y$10$rrYjI/mRt4KpG94drZPgBeeRvQExbXkiC4A6I5T7rtnfeDlm/Lhya', 'usuario'),
(26, 'const3', '$2y$10$ebpYgkMloHyU509ctpO3U.aE6BvNs8D/6fWFZz7fUSNFEspYj.vX2', 'usuario'),
(27, 'gerencia', '$2y$10$86pbvbsUftT4CcB8O6yJ5.q.KBx/mg0oEjaMYxGxGMfW1M7Nl1kFS', 'usuario'),
(28, 'raiox', '$2y$10$TN0c.yUq0WC3LVqw3/XuYOiIFt1zqHJ6tFu9BSKp0LBMhiDaNaVtS', 'usuario'),
(29, 'triagem', '$2y$10$T6PqlRfx6TU8uGBSBhZ0tuRyt6w6nZ/6Aqo8vtIX9S5nsHevGm5zW', 'usuario'),
(30, 'RTenf', '$2y$10$qC0USV00nl7hVyezRpZ48.ZS66X32SutP3tP/aj8N0anvdiTKOj8G', 'usuario'),
(32, 'bruno', '$2y$10$WjTWis8zhoRntW.D7m5FoOMWAdYGgVyWo60XkI/MegowKKF4JoP8q', 'admin'),
(33, 'pediatria ', '$2y$10$ddkAj.a4VsSLEHFeZw/or.nPwev3sNiwYiNJKG7FnOLn8MM/.dzkm', 'usuario'),
(34, 'ortopedia', '$2y$10$GXok8Cd1RqUMw4eR9IyG.efji/ogaD7lHRZ4T0bEEZIN13WId7B7m', 'usuario'),
(35, 'farmacia', '$2y$10$Uz848f//KVsui7kc.Mn9zuwBgwbeLokN5yWxMpwxs8PFH1ZG4bs9u', 'usuario'),
(37, 'faturamento', '$2y$10$iPRVrEEN.Gg.2ntrK1hdUe1GxNPcaG97V/OT/t/5iYoJhQqiimPX6', 'usuario'),
(38, 'agendamentos', '$2y$10$ZekFhxXRSJBNWUVgiOMQkeC9ejqsUBbKK.5Xglb4NB7EA5IXEwn.q', 'usuario'),
(39, 'RH', '$2y$10$0F2bBztq7SUh3b529xDIIenjJPM6yMWou6TlzlQnYOeAFkhazuthK', 'usuario'),
(40, 'internação', '$2y$10$KeBTE3hWuTEjOUa0o4Ep5.nFDwzYJlwFcvzaxBARTvmXBrp0ij90C', 'usuario'),
(42, 'medico', '$2y$10$v4IUzgNNNckLwGtnrcPDqOogE8Ksfm1KOK5vPhH9EGsu5CTOuM0nO', 'usuario'),
(43, 'A.Social', '$2y$10$tzl3QGI94AovV3TeSWEw8ezmNUZXzheSwDSXKbkttGKeNG1bhxckS', 'usuario'),
(44, 'emergencia', '$2y$10$Wg5uGYKwhi7T7kLAm945M.Qaomumxj0SFjLNi4GkP2qLK4N6XjpTa', 'usuario'),
(45, 'Telemedicina', '$2y$10$BvVXC8WRf0TWIyifBo3m3Of2NDbNmb/BDRIPQev6JARfdUNEo2ZsG', 'usuario'),
(50, 'nutricionista', '$2y$10$6KuThVih2JVx6vWuGDrFC.PQgvKPdFsd3F.nndKffQdlTDk7oE2/q', 'usuario');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `chamados`
--
ALTER TABLE `chamados`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `resolvidos`
--
ALTER TABLE `resolvidos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `chamados`
--
ALTER TABLE `chamados`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1406;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
