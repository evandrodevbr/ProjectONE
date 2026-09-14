-- Schema do banco usado pelo ProjectONE.
-- Base: users | Tabela: dados
--
-- No projeto original (WAMP) a base e a tabela eram criadas a mao pelo phpMyAdmin;
-- este arquivo apenas registra o schema que as consultas PHP esperam.
--
--   mysql -u root < sql/schema.sql

CREATE DATABASE IF NOT EXISTS `users` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `users`;

CREATE TABLE IF NOT EXISTS `dados` (
  `id`        INT AUTO_INCREMENT PRIMARY KEY,
  `email`     VARCHAR(255) NOT NULL,
  `senha`     VARCHAR(255) NOT NULL,
  `nome`      VARCHAR(255) DEFAULT NULL,
  `sobrenome` VARCHAR(255) DEFAULT NULL,
  `rg`        VARCHAR(20)  DEFAULT NULL,
  `cpf`       VARCHAR(20)  DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
