/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `disponibilidade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disponibilidade` (
  `id_disponibilidade` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_van` bigint unsigned NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ex: Manhã ida, Tarde volta, Treino ter/qui',
  `turno` enum('manha','tarde','integral') COLLATE utf8mb4_unicode_ci NOT NULL,
  `preco_mensal` decimal(8,2) DEFAULT NULL,
  `capacidade_total` int unsigned NOT NULL,
  `regioes_atendidas` json DEFAULT NULL,
  `escolas_atendidas` json DEFAULT NULL,
  `ativa` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_disponibilidade`),
  KEY `disponibilidade_id_van_ativa_index` (`id_van`,`ativa`),
  CONSTRAINT `disponibilidade_id_van_foreign` FOREIGN KEY (`id_van`) REFERENCES `van` (`id_van`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `disponibilidade_dia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disponibilidade_dia` (
  `id_disponibilidade_dia` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_disponibilidade` bigint unsigned NOT NULL,
  `dia_semana` enum('seg','ter','qua','qui','sex','sab','dom') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_disponibilidade_dia`),
  UNIQUE KEY `uk_disponibilidade_dia` (`id_disponibilidade`,`dia_semana`),
  KEY `disponibilidade_dia_dia_semana_index` (`dia_semana`),
  CONSTRAINT `disponibilidade_dia_id_disponibilidade_foreign` FOREIGN KEY (`id_disponibilidade`) REFERENCES `disponibilidade` (`id_disponibilidade`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `disponibilidade_parada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disponibilidade_parada` (
  `id_disponibilidade_parada` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_disponibilidade` bigint unsigned NOT NULL,
  `id_endereco` bigint unsigned NOT NULL,
  `ordem` int unsigned NOT NULL,
  `tipo` enum('embarque','desembarque') COLLATE utf8mb4_unicode_ci NOT NULL,
  `horario_previsto` time NOT NULL,
  `ativa` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_disponibilidade_parada`),
  UNIQUE KEY `disponibilidade_parada_id_disponibilidade_ordem_unique` (`id_disponibilidade`,`ordem`),
  KEY `disponibilidade_parada_id_endereco_foreign` (`id_endereco`),
  KEY `disponibilidade_parada_id_disponibilidade_ordem_index` (`id_disponibilidade`,`ordem`),
  CONSTRAINT `disponibilidade_parada_id_disponibilidade_foreign` FOREIGN KEY (`id_disponibilidade`) REFERENCES `disponibilidade` (`id_disponibilidade`) ON DELETE CASCADE,
  CONSTRAINT `disponibilidade_parada_id_endereco_foreign` FOREIGN KEY (`id_endereco`) REFERENCES `endereco` (`id_endereco`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `disponibilidade_passageiro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disponibilidade_passageiro` (
  `id_disponibilidade_passageiro` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_passageiro` bigint unsigned NOT NULL,
  `id_vinculo` bigint unsigned NOT NULL,
  `data` date NOT NULL,
  `vai` tinyint(1) NOT NULL DEFAULT '1',
  `motivo_falta` enum('doenca','feriado','viagem','evento','outro') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacoes` text COLLATE utf8mb4_unicode_ci,
  `marcado_por` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_disponibilidade_passageiro`),
  UNIQUE KEY `dp_passageiro_vinculo_data_unique` (`id_passageiro`,`id_vinculo`,`data`),
  KEY `disponibilidade_passageiro_marcado_por_foreign` (`marcado_por`),
  KEY `disponibilidade_passageiro_id_passageiro_data_index` (`id_passageiro`,`data`),
  KEY `disponibilidade_passageiro_id_vinculo_vai_index` (`id_vinculo`,`vai`),
  CONSTRAINT `disponibilidade_passageiro_id_passageiro_foreign` FOREIGN KEY (`id_passageiro`) REFERENCES `passageiro` (`id_passageiro`) ON DELETE CASCADE,
  CONSTRAINT `disponibilidade_passageiro_id_vinculo_foreign` FOREIGN KEY (`id_vinculo`) REFERENCES `vinculo` (`id_vinculo`) ON DELETE RESTRICT,
  CONSTRAINT `disponibilidade_passageiro_marcado_por_foreign` FOREIGN KEY (`marcado_por`) REFERENCES `usuario` (`id_usuario`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `endereco`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `endereco` (
  `id_endereco` bigint unsigned NOT NULL AUTO_INCREMENT,
  `logradouro` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complemento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cidade` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` char(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cep` char(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_endereco`),
  KEY `endereco_cidade_estado_index` (`cidade`,`estado`),
  KEY `endereco_cep_index` (`cep`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `localizacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `localizacao` (
  `id_localizacao` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_rota` bigint unsigned NOT NULL,
  `id_van` bigint unsigned NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `altitude` decimal(8,2) DEFAULT NULL,
  `precisao_metros` decimal(6,2) DEFAULT NULL,
  `fonte_localizacao` enum('gps','rede','fusao') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gps',
  `numero_satelites` int unsigned DEFAULT NULL,
  `timestamp_captura` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_localizacao`),
  KEY `localizacao_id_rota_timestamp_captura_index` (`id_rota`,`timestamp_captura`),
  KEY `localizacao_id_van_timestamp_captura_index` (`id_van`,`timestamp_captura`),
  KEY `localizacao_created_at_index` (`created_at`),
  CONSTRAINT `localizacao_id_rota_foreign` FOREIGN KEY (`id_rota`) REFERENCES `rota` (`id_rota`) ON DELETE CASCADE,
  CONSTRAINT `localizacao_id_van_foreign` FOREIGN KEY (`id_van`) REFERENCES `van` (`id_van`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `motorista`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `motorista` (
  `id_motorista` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` bigint unsigned NOT NULL,
  `cnh_numero` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cnh_categoria` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cnh_validade` date NOT NULL,
  `cnh_foto_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certidao_antecedentes_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `curso_transporte_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `renach_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_aprovacao` enum('pendente','aprovado','rejeitado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente',
  `motivo_rejeicao` text COLLATE utf8mb4_unicode_ci,
  `data_avaliacao_documento` timestamp NULL DEFAULT NULL,
  `id_usuario_avaliador` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_motorista`),
  UNIQUE KEY `motorista_id_usuario_unique` (`id_usuario`),
  UNIQUE KEY `motorista_cnh_numero_unique` (`cnh_numero`),
  KEY `motorista_id_usuario_avaliador_foreign` (`id_usuario_avaliador`),
  KEY `motorista_status_aprovacao_index` (`status_aprovacao`),
  KEY `motorista_cnh_validade_index` (`cnh_validade`),
  CONSTRAINT `motorista_id_usuario_avaliador_foreign` FOREIGN KEY (`id_usuario_avaliador`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL,
  CONSTRAINT `motorista_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_motorista_verificar_role` BEFORE INSERT ON `motorista` FOR EACH ROW BEGIN
                DECLARE u_role VARCHAR(50);
                SELECT role INTO u_role FROM usuario WHERE id_usuario = NEW.id_usuario;
                IF u_role != 'motorista' THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Usuário deve ter role motorista';
                END IF;
            END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `parada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parada` (
  `id_parada` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_rota` bigint unsigned NOT NULL,
  `id_endereco` bigint unsigned NOT NULL,
  `ordem` int unsigned NOT NULL,
  `tipo` enum('embarque','desembarque') COLLATE utf8mb4_unicode_ci NOT NULL,
  `horario_previsto` time DEFAULT NULL,
  `horario_real` time DEFAULT NULL,
  `observacoes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_parada`),
  UNIQUE KEY `parada_id_rota_ordem_unique` (`id_rota`,`ordem`),
  KEY `parada_id_endereco_foreign` (`id_endereco`),
  KEY `parada_id_rota_index` (`id_rota`),
  CONSTRAINT `parada_id_endereco_foreign` FOREIGN KEY (`id_endereco`) REFERENCES `endereco` (`id_endereco`) ON DELETE RESTRICT,
  CONSTRAINT `parada_id_rota_foreign` FOREIGN KEY (`id_rota`) REFERENCES `rota` (`id_rota`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `parada_passageiro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parada_passageiro` (
  `id_parada_passageiro` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_parada` bigint unsigned NOT NULL,
  `id_passageiro` bigint unsigned NOT NULL,
  `embarque_em` timestamp NULL DEFAULT NULL,
  `desembarque_em` timestamp NULL DEFAULT NULL,
  `marcado_por` enum('motorista','sistema','responsavel') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metodo_confirmacao` enum('manual','geo','qrcode') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_parada_passageiro`),
  UNIQUE KEY `parada_passageiro_id_parada_id_passageiro_unique` (`id_parada`,`id_passageiro`),
  KEY `parada_passageiro_id_passageiro_index` (`id_passageiro`),
  CONSTRAINT `parada_passageiro_id_parada_foreign` FOREIGN KEY (`id_parada`) REFERENCES `parada` (`id_parada`) ON DELETE CASCADE,
  CONSTRAINT `parada_passageiro_id_passageiro_foreign` FOREIGN KEY (`id_passageiro`) REFERENCES `passageiro` (`id_passageiro`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `passageiro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `passageiro` (
  `id_passageiro` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pessoa` bigint unsigned NOT NULL,
  `id_usuario` bigint unsigned DEFAULT NULL,
  `observacoes_medicas` text COLLATE utf8mb4_unicode_ci,
  `foto_consentimento_lgpd` tinyint(1) NOT NULL DEFAULT '0',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `data_inscricao` date NOT NULL DEFAULT '2026-05-20',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_passageiro`),
  UNIQUE KEY `passageiro_id_pessoa_unique` (`id_pessoa`),
  UNIQUE KEY `passageiro_id_usuario_unique` (`id_usuario`),
  KEY `passageiro_ativo_index` (`ativo`),
  CONSTRAINT `passageiro_id_pessoa_foreign` FOREIGN KEY (`id_pessoa`) REFERENCES `pessoa` (`id_pessoa`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `passageiro_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `passageiro_endereco`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `passageiro_endereco` (
  `id_passageiro_endereco` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_passageiro` bigint unsigned NOT NULL,
  `id_endereco` bigint unsigned NOT NULL,
  `tipo` enum('embarque','desembarque','residencia') COLLATE utf8mb4_unicode_ci NOT NULL,
  `principal` tinyint(1) NOT NULL DEFAULT '0',
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome do local (ex: Escola Municipal ABC)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_passageiro_endereco`),
  UNIQUE KEY `passageiro_endereco_id_passageiro_id_endereco_tipo_unique` (`id_passageiro`,`id_endereco`,`tipo`),
  KEY `passageiro_endereco_id_endereco_foreign` (`id_endereco`),
  KEY `passageiro_endereco_id_passageiro_principal_index` (`id_passageiro`,`principal`),
  CONSTRAINT `passageiro_endereco_id_endereco_foreign` FOREIGN KEY (`id_endereco`) REFERENCES `endereco` (`id_endereco`) ON DELETE RESTRICT,
  CONSTRAINT `passageiro_endereco_id_passageiro_foreign` FOREIGN KEY (`id_passageiro`) REFERENCES `passageiro` (`id_passageiro`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pessoa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pessoa` (
  `id_pessoa` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpf` char(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_nascimento` date NOT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_upload_data` timestamp NULL DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pessoa`),
  UNIQUE KEY `pessoa_cpf_unique` (`cpf`),
  KEY `pessoa_cpf_index` (`cpf`),
  KEY `pessoa_ativo_index` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `responsavel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `responsavel` (
  `id_responsavel` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` bigint unsigned NOT NULL,
  `tipo_responsavel` enum('pai','mae','tutor','representante_legal','autoresponsavel') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pai',
  `telefone_emergencia` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_responsavel_ate` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_responsavel`),
  UNIQUE KEY `responsavel_id_usuario_unique` (`id_usuario`),
  KEY `responsavel_tipo_responsavel_index` (`tipo_responsavel`),
  CONSTRAINT `responsavel_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_responsavel_verificar_role` BEFORE INSERT ON `responsavel` FOR EACH ROW BEGIN
                DECLARE u_role VARCHAR(50);
                SELECT role INTO u_role FROM usuario WHERE id_usuario = NEW.id_usuario;
                IF u_role != 'responsavel' THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Usuário deve ter role responsavel';
                END IF;
            END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `responsavel_passageiro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `responsavel_passageiro` (
  `id_responsavel_passageiro` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_responsavel` bigint unsigned NOT NULL,
  `id_passageiro` bigint unsigned NOT NULL,
  `data_inicio` date NOT NULL DEFAULT '2026-05-20',
  `data_fim` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_responsavel_passageiro`),
  KEY `responsavel_passageiro_id_responsavel_foreign` (`id_responsavel`),
  KEY `responsavel_passageiro_id_passageiro_data_fim_index` (`id_passageiro`,`data_fim`),
  CONSTRAINT `responsavel_passageiro_id_passageiro_foreign` FOREIGN KEY (`id_passageiro`) REFERENCES `passageiro` (`id_passageiro`) ON DELETE CASCADE,
  CONSTRAINT `responsavel_passageiro_id_responsavel_foreign` FOREIGN KEY (`id_responsavel`) REFERENCES `responsavel` (`id_responsavel`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rota`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rota` (
  `id_rota` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_van` bigint unsigned NOT NULL,
  `id_disponibilidade` bigint unsigned NOT NULL,
  `data` date NOT NULL,
  `status` enum('planejada','em_andamento','concluida','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planejada',
  `horario_inicio_previsto` datetime DEFAULT NULL,
  `horario_inicio_real` datetime DEFAULT NULL,
  `horario_fim_previsto` datetime DEFAULT NULL,
  `horario_fim_real` datetime DEFAULT NULL,
  `distancia_km` decimal(10,2) DEFAULT NULL,
  `tempo_decorrido_minutos` int unsigned DEFAULT NULL,
  `observacoes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_rota`),
  KEY `rota_id_van_data_index` (`id_van`,`data`),
  KEY `rota_id_disponibilidade_data_index` (`id_disponibilidade`,`data`),
  KEY `rota_status_index` (`status`),
  CONSTRAINT `rota_id_disponibilidade_foreign` FOREIGN KEY (`id_disponibilidade`) REFERENCES `disponibilidade` (`id_disponibilidade`) ON DELETE RESTRICT,
  CONSTRAINT `rota_id_van_foreign` FOREIGN KEY (`id_van`) REFERENCES `van` (`id_van`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `solicitacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitacao` (
  `id_solicitacao` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_van` bigint unsigned NOT NULL,
  `id_passageiro` bigint unsigned NOT NULL,
  `id_responsavel` bigint unsigned DEFAULT NULL,
  `id_usuario_solicitante` bigint unsigned NOT NULL,
  `tipo_solicitante` enum('responsavel','passageiro','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'responsavel',
  `tipo` enum('nova','alteracao') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nova',
  `id_vinculo_alterado` bigint unsigned DEFAULT NULL,
  `status` enum('pendente','aceita','recusada','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente',
  `mensagem` text COLLATE utf8mb4_unicode_ci,
  `motivo_recusa` text COLLATE utf8mb4_unicode_ci,
  `data_solicitacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_resposta` datetime DEFAULT NULL,
  `cancelado_em` datetime DEFAULT NULL,
  `cancelado_por` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_solicitacao`),
  KEY `solicitacao_id_passageiro_foreign` (`id_passageiro`),
  KEY `solicitacao_id_usuario_solicitante_foreign` (`id_usuario_solicitante`),
  KEY `solicitacao_cancelado_por_foreign` (`cancelado_por`),
  KEY `solicitacao_id_van_status_index` (`id_van`,`status`),
  KEY `solicitacao_id_responsavel_status_index` (`id_responsavel`,`status`),
  KEY `solicitacao_data_solicitacao_index` (`data_solicitacao`),
  KEY `solicitacao_id_vinculo_alterado_foreign` (`id_vinculo_alterado`),
  CONSTRAINT `solicitacao_cancelado_por_foreign` FOREIGN KEY (`cancelado_por`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL,
  CONSTRAINT `solicitacao_id_passageiro_foreign` FOREIGN KEY (`id_passageiro`) REFERENCES `passageiro` (`id_passageiro`) ON DELETE RESTRICT,
  CONSTRAINT `solicitacao_id_responsavel_foreign` FOREIGN KEY (`id_responsavel`) REFERENCES `responsavel` (`id_responsavel`) ON DELETE SET NULL,
  CONSTRAINT `solicitacao_id_usuario_solicitante_foreign` FOREIGN KEY (`id_usuario_solicitante`) REFERENCES `usuario` (`id_usuario`) ON DELETE RESTRICT,
  CONSTRAINT `solicitacao_id_van_foreign` FOREIGN KEY (`id_van`) REFERENCES `van` (`id_van`) ON DELETE RESTRICT,
  CONSTRAINT `solicitacao_id_vinculo_alterado_foreign` FOREIGN KEY (`id_vinculo_alterado`) REFERENCES `vinculo` (`id_vinculo`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `solicitacao_disponibilidade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitacao_disponibilidade` (
  `id_solicitacao_disponibilidade` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_solicitacao` bigint unsigned NOT NULL,
  `id_disponibilidade` bigint unsigned NOT NULL,
  `preco_mensal` decimal(8,2) NOT NULL COMMENT 'Preço do trajeto no momento da solicitação',
  `dias_contratados` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_solicitacao_disponibilidade`),
  UNIQUE KEY `uk_solicitacao_disponibilidade` (`id_solicitacao`,`id_disponibilidade`),
  KEY `solicitacao_disponibilidade_id_disponibilidade_index` (`id_disponibilidade`),
  CONSTRAINT `solicitacao_disponibilidade_id_disponibilidade_foreign` FOREIGN KEY (`id_disponibilidade`) REFERENCES `disponibilidade` (`id_disponibilidade`) ON DELETE RESTRICT,
  CONSTRAINT `solicitacao_disponibilidade_id_solicitacao_foreign` FOREIGN KEY (`id_solicitacao`) REFERENCES `solicitacao` (`id_solicitacao`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `id_usuario` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_pessoa` bigint unsigned NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `tour_visto` tinyint(1) NOT NULL DEFAULT '0',
  `senha_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','motorista','responsavel','passageiro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'passageiro',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `ultimo_login` timestamp NULL DEFAULT NULL,
  `tentativas_falhas` int unsigned NOT NULL DEFAULT '0',
  `bloqueado_ate` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `usuario_id_pessoa_unique` (`id_pessoa`),
  UNIQUE KEY `usuario_email_unique` (`email`),
  KEY `usuario_ativo_index` (`ativo`),
  KEY `usuario_role_index` (`role`),
  KEY `usuario_email_ativo_index` (`email`,`ativo`),
  CONSTRAINT `usuario_id_pessoa_foreign` FOREIGN KEY (`id_pessoa`) REFERENCES `pessoa` (`id_pessoa`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `van`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `van` (
  `id_van` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_motorista` bigint unsigned NOT NULL,
  `placa` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome_servico` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marca` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ano_fabricacao` year DEFAULT NULL,
  `cor` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacidade_passageiros` int unsigned NOT NULL,
  `status_aprovacao` enum('pendente','aprovado','rejeitado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente',
  `status_operacional` enum('ativa','manutencao','inativa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativa',
  `motivo_rejeicao` text COLLATE utf8mb4_unicode_ci,
  `id_usuario_avaliador` bigint unsigned DEFAULT NULL,
  `data_avaliacao` timestamp NULL DEFAULT NULL,
  `foto_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_verso_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_interior_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_lateral_esq_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_lateral_dir_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crlv_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crlv_validade` date DEFAULT NULL,
  `seguro_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seguro_validade` date DEFAULT NULL,
  `autorizacao_municipal_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `autorizacao_municipal_validade` date DEFAULT NULL,
  `prefixo_municipal` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ipva_comprovante_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ipva_comprovante_data` date DEFAULT NULL,
  `documentacao_completa` tinyint(1) NOT NULL DEFAULT '0',
  `data_ultima_inspecao` date DEFAULT NULL,
  `proxima_inspecao_prevista` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_van`),
  UNIQUE KEY `van_placa_unique` (`placa`),
  KEY `van_id_motorista_foreign` (`id_motorista`),
  KEY `van_id_usuario_avaliador_foreign` (`id_usuario_avaliador`),
  KEY `van_status_aprovacao_index` (`status_aprovacao`),
  KEY `van_status_operacional_index` (`status_operacional`),
  KEY `van_crlv_validade_index` (`crlv_validade`),
  KEY `van_documentacao_completa_index` (`documentacao_completa`),
  CONSTRAINT `van_id_motorista_foreign` FOREIGN KEY (`id_motorista`) REFERENCES `motorista` (`id_motorista`) ON DELETE RESTRICT,
  CONSTRAINT `van_id_usuario_avaliador_foreign` FOREIGN KEY (`id_usuario_avaliador`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_van_calcular_documentacao_completa` BEFORE UPDATE ON `van` FOR EACH ROW BEGIN
                SET NEW.documentacao_completa = (
                    NEW.crlv_url IS NOT NULL
                    AND NEW.crlv_validade IS NOT NULL
                    AND NEW.crlv_validade > CURDATE()
                );
            END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `vinculo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vinculo` (
  `id_vinculo` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_van` bigint unsigned NOT NULL,
  `id_passageiro` bigint unsigned NOT NULL,
  `id_solicitacao` bigint unsigned NOT NULL,
  `preco_total` decimal(8,2) NOT NULL COMMENT 'Soma dos preços dos trajetos aceitos',
  `status` enum('ativo','suspenso','encerrado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativo',
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL,
  `observacoes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_vinculo`),
  UNIQUE KEY `vinculo_id_solicitacao_unique` (`id_solicitacao`),
  KEY `vinculo_id_van_foreign` (`id_van`),
  KEY `vinculo_id_passageiro_status_index` (`id_passageiro`,`status`),
  KEY `vinculo_status_index` (`status`),
  CONSTRAINT `vinculo_id_passageiro_foreign` FOREIGN KEY (`id_passageiro`) REFERENCES `passageiro` (`id_passageiro`) ON DELETE RESTRICT,
  CONSTRAINT `vinculo_id_solicitacao_foreign` FOREIGN KEY (`id_solicitacao`) REFERENCES `solicitacao` (`id_solicitacao`) ON DELETE RESTRICT,
  CONSTRAINT `vinculo_id_van_foreign` FOREIGN KEY (`id_van`) REFERENCES `van` (`id_van`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_vinculo_verificar_ativo_duplicado` BEFORE INSERT ON `vinculo` FOR EACH ROW BEGIN
                DECLARE contador INT;
                IF NEW.status = 'ativo' THEN
                    SELECT COUNT(*) INTO contador
                    FROM vinculo
                    WHERE id_passageiro = NEW.id_passageiro
                    AND status = 'ativo';
                    IF contador > 0 THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Passageiro já possui vínculo ativo';
                    END IF;
                END IF;
            END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `vinculo_disponibilidade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vinculo_disponibilidade` (
  `id_vinculo_disponibilidade` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_vinculo` bigint unsigned NOT NULL,
  `id_disponibilidade` bigint unsigned NOT NULL,
  `preco_mensal` decimal(8,2) NOT NULL COMMENT 'Preço do trajeto acordado no vínculo',
  `dias_contratados` json DEFAULT NULL,
  `ordem` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_vinculo_disponibilidade`),
  UNIQUE KEY `uk_vinculo_disponibilidade` (`id_vinculo`,`id_disponibilidade`),
  KEY `vinculo_disponibilidade_id_disponibilidade_index` (`id_disponibilidade`),
  CONSTRAINT `vinculo_disponibilidade_id_disponibilidade_foreign` FOREIGN KEY (`id_disponibilidade`) REFERENCES `disponibilidade` (`id_disponibilidade`) ON DELETE RESTRICT,
  CONSTRAINT `vinculo_disponibilidade_id_vinculo_foreign` FOREIGN KEY (`id_vinculo`) REFERENCES `vinculo` (`id_vinculo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vw_responsavel_passageiros`;
/*!50001 DROP VIEW IF EXISTS `vw_responsavel_passageiros`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_responsavel_passageiros` AS SELECT 
 1 AS `id_responsavel`,
 1 AS `id_usuario`,
 1 AS `nome_responsavel`,
 1 AS `email`,
 1 AS `id_passageiro`,
 1 AS `nome_passageiro`,
 1 AS `data_nascimento`,
 1 AS `data_inicio`,
 1 AS `data_fim`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `vw_vans_disponiveis`;
/*!50001 DROP VIEW IF EXISTS `vw_vans_disponiveis`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_vans_disponiveis` AS SELECT 
 1 AS `id_van`,
 1 AS `placa`,
 1 AS `id_motorista`,
 1 AS `nome_motorista`,
 1 AS `foto_motorista`,
 1 AS `modelo`,
 1 AS `capacidade_passageiros`,
 1 AS `id_disponibilidade`,
 1 AS `nome_trajeto`,
 1 AS `turno`,
 1 AS `preco_mensal`,
 1 AS `capacidade_total`,
 1 AS `dias_semana`,
 1 AS `passageiros_confirmados`,
 1 AS `vagas_disponiveis`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `vw_vinculo_detalhes`;
/*!50001 DROP VIEW IF EXISTS `vw_vinculo_detalhes`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_vinculo_detalhes` AS SELECT 
 1 AS `id_vinculo`,
 1 AS `id_van`,
 1 AS `placa`,
 1 AS `id_passageiro`,
 1 AS `nome_passageiro`,
 1 AS `telefone`,
 1 AS `id_motorista`,
 1 AS `nome_motorista`,
 1 AS `telefone_motorista`,
 1 AS `foto_motorista`,
 1 AS `status`,
 1 AS `data_inicio`,
 1 AS `data_fim`*/;
SET character_set_client = @saved_cs_client;
/*!50001 DROP VIEW IF EXISTS `vw_responsavel_passageiros`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_responsavel_passageiros` AS select `r`.`id_responsavel` AS `id_responsavel`,`u`.`id_usuario` AS `id_usuario`,`pr`.`nome` AS `nome_responsavel`,`u`.`email` AS `email`,`pas`.`id_passageiro` AS `id_passageiro`,`pp`.`nome` AS `nome_passageiro`,`pp`.`data_nascimento` AS `data_nascimento`,`rp`.`data_inicio` AS `data_inicio`,`rp`.`data_fim` AS `data_fim` from (((((`responsavel` `r` join `usuario` `u` on((`r`.`id_usuario` = `u`.`id_usuario`))) join `pessoa` `pr` on((`u`.`id_pessoa` = `pr`.`id_pessoa`))) join `responsavel_passageiro` `rp` on((`r`.`id_responsavel` = `rp`.`id_responsavel`))) join `passageiro` `pas` on((`rp`.`id_passageiro` = `pas`.`id_passageiro`))) join `pessoa` `pp` on((`pas`.`id_pessoa` = `pp`.`id_pessoa`))) where ((`rp`.`data_fim` is null) or (`rp`.`data_fim` >= curdate())) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `vw_vans_disponiveis`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_vans_disponiveis` AS select `v`.`id_van` AS `id_van`,`v`.`placa` AS `placa`,`m`.`id_usuario` AS `id_motorista`,`p`.`nome` AS `nome_motorista`,`p`.`foto_url` AS `foto_motorista`,`v`.`modelo` AS `modelo`,`v`.`capacidade_passageiros` AS `capacidade_passageiros`,`d`.`id_disponibilidade` AS `id_disponibilidade`,`d`.`nome` AS `nome_trajeto`,`d`.`turno` AS `turno`,`d`.`preco_mensal` AS `preco_mensal`,`d`.`capacidade_total` AS `capacidade_total`,group_concat(distinct `dd`.`dia_semana` order by field(`dd`.`dia_semana`,'seg','ter','qua','qui','sex','sab','dom') ASC separator ',') AS `dias_semana`,count(distinct `vi`.`id_vinculo`) AS `passageiros_confirmados`,(`d`.`capacidade_total` - count(distinct `vi`.`id_vinculo`)) AS `vagas_disponiveis` from (((((((`van` `v` join `motorista` `m` on((`v`.`id_motorista` = `m`.`id_motorista`))) join `usuario` `u` on((`m`.`id_usuario` = `u`.`id_usuario`))) join `pessoa` `p` on((`u`.`id_pessoa` = `p`.`id_pessoa`))) join `disponibilidade` `d` on((`v`.`id_van` = `d`.`id_van`))) join `disponibilidade_dia` `dd` on((`d`.`id_disponibilidade` = `dd`.`id_disponibilidade`))) left join `vinculo_disponibilidade` `vd` on((`d`.`id_disponibilidade` = `vd`.`id_disponibilidade`))) left join `vinculo` `vi` on(((`vd`.`id_vinculo` = `vi`.`id_vinculo`) and (`vi`.`status` = 'ativo')))) where ((`v`.`status_operacional` = 'ativa') and (`v`.`documentacao_completa` = true) and (`d`.`ativa` = true)) group by `v`.`id_van`,`d`.`id_disponibilidade` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `vw_vinculo_detalhes`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_vinculo_detalhes` AS select `vi`.`id_vinculo` AS `id_vinculo`,`vi`.`id_van` AS `id_van`,`van`.`placa` AS `placa`,`vi`.`id_passageiro` AS `id_passageiro`,`p`.`nome` AS `nome_passageiro`,`p`.`telefone` AS `telefone`,`m`.`id_usuario` AS `id_motorista`,`pm`.`nome` AS `nome_motorista`,`pm`.`telefone` AS `telefone_motorista`,`pm`.`foto_url` AS `foto_motorista`,`vi`.`status` AS `status`,`vi`.`data_inicio` AS `data_inicio`,`vi`.`data_fim` AS `data_fim` from ((((((`vinculo` `vi` join `van` on((`vi`.`id_van` = `van`.`id_van`))) join `motorista` `m` on((`van`.`id_motorista` = `m`.`id_motorista`))) join `usuario` `u` on((`m`.`id_usuario` = `u`.`id_usuario`))) join `pessoa` `pm` on((`u`.`id_pessoa` = `pm`.`id_pessoa`))) join `passageiro` `pas` on((`vi`.`id_passageiro` = `pas`.`id_passageiro`))) join `pessoa` `p` on((`pas`.`id_pessoa` = `p`.`id_pessoa`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2026_04_01_000001_create_pessoa_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2026_04_01_000002_create_usuario_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2026_04_01_000003_create_responsavel_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2026_04_01_000004_create_motorista_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2026_04_01_000005_create_endereco_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2026_04_01_000006_create_passageiro_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2026_04_01_000007_create_passageiro_endereco_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2026_04_01_000008_create_destino_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2026_04_01_000009_create_van_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2026_04_01_000010_create_responsavel_passageiro_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2026_04_01_000011_create_disponibilidade_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2026_04_01_000011b_create_disponibilidade_dia_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2026_04_01_000012_create_disponibilidade_parada_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2026_04_01_000013_create_solicitacao_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2026_04_01_000013b_create_solicitacao_disponibilidade_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2026_04_01_000014_create_vinculo_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2026_04_01_000014b_create_vinculo_disponibilidade_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2026_04_01_000015_create_disponibilidade_passageiro_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2026_04_01_000016_create_rota_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2026_04_01_000017_create_parada_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2026_04_01_000018_create_parada_passageiro_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2026_04_01_000019_create_localizacao_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2026_04_01_000020_create_triggers',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2026_04_01_000021_create_events_and_views',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2026_04_04_201828_add_remember_token_to_usuario',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2026_04_04_235831_create_sessions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2026_06_23_000001_fix_trigger_passageiro_role',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2026_06_23_000002_create_password_reset_tokens_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2026_06_26_013059_add_bairros_escolas_to_disponibilidade_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2026_07_01_000001_add_dias_contratados_to_pivot_tables',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2026_07_02_130105_add_ordem_to_vinculo_disponibilidade',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2026_07_02_134354_add_nome_to_passageiro_endereco_and_drop_destino',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2026_07_02_143843_add_confirmacao_to_parada_passageiro',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2026_07_07_140000_add_nome_servico_to_van_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2026_07_11_000000_add_tipo_and_id_vinculo_alterado_to_solicitacao',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2026_07_21_020649_add_email_verified_at_to_usuario_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2026_07_08_120000_replace_bairros_atendidos_with_regioes_atendidas',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2026_07_23_120000_add_foto_verso_interior_to_van_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2026_08_31_000001_add_fotos_documentos_to_van_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2026_08_31_000002_add_prefixo_municipal_to_van',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2026_09_02_000001_add_certidao_url_to_motorista',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2026_09_02_000002_add_foto_consentimento_to_passageiro',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2026_09_08_000001_add_novos_docs_to_motorista',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2026_09_15_184648_make_preco_mensal_nullable_in_disponibilidade',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2026_09_17_000001_add_tour_visto_to_usuario_table',18);
