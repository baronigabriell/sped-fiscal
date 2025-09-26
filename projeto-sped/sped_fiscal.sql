-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 26-Set-2025 às 20:13
-- Versão do servidor: 8.0.31
-- versão do PHP: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sped_fiscal`
--
CREATE DATABASE IF NOT EXISTS `sped_fiscal` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sped_fiscal`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

DROP TABLE IF EXISTS `produtos`;
CREATE TABLE IF NOT EXISTS `produtos` (
  `nome` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `preco` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `criado_em`) VALUES
(1, '', 'gabriel@gmail.com', '$2y$10$xLQyeQAY1TL9pDYHKKPBB.PJ8g/MHH6I5qg6hdoBfhStjKrX0BnB6', '2025-09-19 11:57:56'),
(2, '', 'amanda@gmail.com', '$2y$10$uvROOP6KV8p5Ze6gO1sfLOFxe39yPSOrJLtC8k3gswDtJr/WsF1pW', '2025-09-19 12:40:52'),
(3, '', 'rafael@gmail.com', '$2y$10$.pdP/UTJ.bI/UhrajSw7cernC8p1ozVg/ug8p5FngogXiLyYqfUR2', '2025-09-19 12:42:45'),
(4, '', 'gaybriel@gmail.com', '$2y$10$8xlwUPKQH1NFG7ffsIIfLuD6g80lgMVKV2vHabLtGzm9.fm6ezOi6', '2025-09-19 12:46:37'),
(5, '', 'wina@gmail.com', '$2y$10$j882P4UzDWydYWtWaAS5KObliwX7yIeEREkjhCvLa3qYd1F9ChuV.', '2025-09-19 13:15:19'),
(6, '', 'julinha@gmail.com', '$2y$10$DcUAW4UdeUXmoJdFNMPWXuDmGgvwI.6FcyKCdNKItRqWivTXpj9b2', '2025-09-19 13:16:56'),
(7, '', 'julia@gmail.com', '$2y$10$45oMIeNq37oD70bukpSMXOZCBie9om6u5t/snCfWMMGBDcoid080S', '2025-09-19 13:23:02'),
(8, '', 'gezota@gmail.com', '$2y$10$tPxdo81LfbZp89xmIDSyIO0ttUvDgdgBOexTrLWiMIEnkfSLAORf.', '2025-09-19 13:27:58'),
(9, 'lelo', 'lelo@gmail.com', '$2y$10$mHfU2kP9w2q1ukH28VLuP.a5Z53VTEclDBBNtDQBKQXRR.2UTXd0S', '2025-09-19 13:34:54'),
(10, 'henrique', 'henrique@gmail.com', '$2y$10$6v1aDVLUbWq/djvhCAttfuLUH.ZKzq38s8u5ktG9w/PjQV9YxHAlW', '2025-09-19 13:35:15'),
(11, 'pietro', 'pietro@gmail.com', '$2y$10$mWXiCJTmRTaZBe6hZBn6ReefIiFdW9FmjkQ0trakcPmmf/VIDKrAq', '2025-09-19 19:43:45'),
(12, 'gabriel', 'gabrielbaroni8@gmail.com', '$2y$10$avcCVMaOYzE85lEwAwXSYuhU2crkgmpYUsL0qXIMPJGKY8ZhfFOHa', '2025-09-26 13:29:27');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
