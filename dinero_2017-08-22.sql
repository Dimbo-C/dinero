# ************************************************************
# Sequel Pro SQL dump
# Версия 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Адрес: 127.0.0.1 (MySQL 5.7.17)
# Схема: dinero
# Время создания: 2017-08-22 08:02:16 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Дамп таблицы gross_indicators
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gross_indicators`;

CREATE TABLE `gross_indicators` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `monthly_gross_turnover` decimal(8,2) NOT NULL,
  `daily_volume` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gross_indicators_created_at_index` (`created_at`),
  KEY `gross_indicators_user_id_index` (`user_id`),
  CONSTRAINT `gross_indicators_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `gross_indicators` WRITE;
/*!40000 ALTER TABLE `gross_indicators` DISABLE KEYS */;

INSERT INTO `gross_indicators` (`id`, `user_id`, `monthly_gross_turnover`, `daily_volume`, `created_at`, `updated_at`)
VALUES
	(1,2,1742.00,131.00,'2017-07-24 15:44:33','2017-07-24 15:44:33'),
	(2,2,1816.00,189.00,'2017-07-23 15:44:33','2017-07-23 15:44:33'),
	(3,2,1002.00,133.00,'2017-07-22 15:44:33','2017-07-22 15:44:33'),
	(4,2,1646.00,136.00,'2017-07-21 15:44:33','2017-07-21 15:44:33'),
	(5,2,1322.00,227.00,'2017-07-20 15:44:33','2017-07-20 15:44:33'),
	(6,2,1730.00,300.00,'2017-07-19 15:44:33','2017-07-19 15:44:33'),
	(7,2,1611.00,172.00,'2017-07-18 15:44:33','2017-07-18 15:44:33'),
	(8,2,1713.00,148.00,'2017-07-17 15:44:33','2017-07-17 15:44:33'),
	(9,2,1007.00,151.00,'2017-07-16 15:44:33','2017-07-16 15:44:33'),
	(10,2,1131.00,121.00,'2017-07-15 15:44:33','2017-07-15 15:44:33'),
	(11,2,1884.00,126.00,'2017-07-14 15:44:33','2017-07-14 15:44:33'),
	(12,2,1274.00,127.00,'2017-07-13 15:44:33','2017-07-13 15:44:33'),
	(13,2,1284.00,117.00,'2017-07-12 15:44:33','2017-07-12 15:44:33'),
	(14,2,1790.00,284.00,'2017-07-11 15:44:33','2017-07-11 15:44:33'),
	(15,2,1567.00,204.00,'2017-07-10 15:44:33','2017-07-10 15:44:33'),
	(16,2,1036.00,195.00,'2017-07-09 15:44:33','2017-07-09 15:44:33'),
	(17,2,1190.00,203.00,'2017-07-08 15:44:33','2017-07-08 15:44:33'),
	(18,2,1303.00,292.00,'2017-07-07 15:44:33','2017-07-07 15:44:33'),
	(19,2,1808.00,213.00,'2017-07-06 15:44:33','2017-07-06 15:44:33'),
	(20,2,1495.00,274.00,'2017-07-05 15:44:33','2017-07-05 15:44:33'),
	(21,2,1186.00,225.00,'2017-07-04 15:44:33','2017-07-04 15:44:33'),
	(22,2,1212.00,106.00,'2017-07-03 15:44:33','2017-07-03 15:44:33'),
	(23,2,1422.00,218.00,'2017-07-02 15:44:33','2017-07-02 15:44:33'),
	(24,2,1886.00,104.00,'2017-07-01 15:44:33','2017-07-01 15:44:33'),
	(25,2,1694.00,100.00,'2017-06-30 15:44:34','2017-06-30 15:44:34'),
	(26,2,1739.00,157.00,'2017-06-29 15:44:34','2017-06-29 15:44:34'),
	(27,2,1608.00,164.00,'2017-06-28 15:44:34','2017-06-28 15:44:34'),
	(28,2,1086.00,185.00,'2017-06-27 15:44:34','2017-06-27 15:44:34'),
	(29,2,1841.00,190.00,'2017-06-26 15:44:34','2017-06-26 15:44:34'),
	(30,2,1601.00,205.00,'2017-06-25 15:44:34','2017-06-25 15:44:34'),
	(31,2,1837.00,216.00,'2017-06-24 15:44:34','2017-06-24 15:44:34'),
	(32,2,1674.00,169.00,'2017-06-23 15:44:34','2017-06-23 15:44:34'),
	(33,2,1480.00,143.00,'2017-06-22 15:44:34','2017-06-22 15:44:34'),
	(34,2,1490.00,262.00,'2017-06-21 15:44:34','2017-06-21 15:44:34'),
	(35,2,1765.00,160.00,'2017-06-20 15:44:34','2017-06-20 15:44:34'),
	(36,2,1467.00,194.00,'2017-06-19 15:44:34','2017-06-19 15:44:34'),
	(37,2,1114.00,117.00,'2017-06-18 15:44:34','2017-06-18 15:44:34'),
	(38,2,1830.00,148.00,'2017-06-17 15:44:34','2017-06-17 15:44:34'),
	(39,2,1465.00,127.00,'2017-06-16 15:44:34','2017-06-16 15:44:34'),
	(40,2,1380.00,260.00,'2017-06-15 15:44:34','2017-06-15 15:44:34'),
	(41,2,1451.00,145.00,'2017-06-14 15:44:34','2017-06-14 15:44:34'),
	(42,2,1772.00,232.00,'2017-06-13 15:44:34','2017-06-13 15:44:34'),
	(43,2,1632.00,147.00,'2017-06-12 15:44:34','2017-06-12 15:44:34'),
	(44,2,1624.00,172.00,'2017-06-11 15:44:34','2017-06-11 15:44:34'),
	(45,2,1761.00,145.00,'2017-06-10 15:44:34','2017-06-10 15:44:34'),
	(46,2,1570.00,183.00,'2017-06-09 15:44:34','2017-06-09 15:44:34'),
	(47,2,1513.00,275.00,'2017-06-08 15:44:34','2017-06-08 15:44:34'),
	(48,2,1250.00,148.00,'2017-06-07 15:44:34','2017-06-07 15:44:34'),
	(49,2,1309.00,142.00,'2017-06-06 15:44:34','2017-06-06 15:44:34'),
	(50,2,1227.00,242.00,'2017-06-05 15:44:34','2017-06-05 15:44:34'),
	(51,2,1930.00,190.00,'2017-06-04 15:44:34','2017-06-04 15:44:34'),
	(52,2,1478.00,300.00,'2017-06-03 15:44:34','2017-06-03 15:44:34'),
	(53,2,1092.00,264.00,'2017-06-02 15:44:34','2017-06-02 15:44:34'),
	(54,2,1196.00,291.00,'2017-06-01 15:44:34','2017-06-01 15:44:34'),
	(55,2,1719.00,133.00,'2017-05-31 15:44:34','2017-05-31 15:44:34'),
	(56,2,1567.00,239.00,'2017-05-30 15:44:34','2017-05-30 15:44:34'),
	(57,2,1348.00,258.00,'2017-05-29 15:44:34','2017-05-29 15:44:34'),
	(58,2,1009.00,110.00,'2017-05-28 15:44:34','2017-05-28 15:44:34'),
	(59,2,1565.00,194.00,'2017-05-27 15:44:34','2017-05-27 15:44:34'),
	(60,2,1775.00,124.00,'2017-05-26 15:44:34','2017-05-26 15:44:34'),
	(61,2,1641.00,143.00,'2017-05-25 15:44:34','2017-05-25 15:44:34'),
	(62,2,1705.00,269.00,'2017-05-24 15:44:34','2017-05-24 15:44:34'),
	(63,2,1351.00,210.00,'2017-05-23 15:44:34','2017-05-23 15:44:34'),
	(64,2,1151.00,105.00,'2017-05-22 15:44:34','2017-05-22 15:44:34'),
	(65,2,1497.00,242.00,'2017-05-21 15:44:34','2017-05-21 15:44:34'),
	(66,2,1862.00,190.00,'2017-05-20 15:44:34','2017-05-20 15:44:34'),
	(67,2,1860.00,222.00,'2017-05-19 15:44:34','2017-05-19 15:44:34'),
	(68,2,1327.00,299.00,'2017-05-18 15:44:34','2017-05-18 15:44:34'),
	(69,2,1766.00,140.00,'2017-05-17 15:44:34','2017-05-17 15:44:34'),
	(70,2,1416.00,200.00,'2017-05-16 15:44:34','2017-05-16 15:44:34'),
	(71,2,1071.00,291.00,'2017-05-15 15:44:34','2017-05-15 15:44:34'),
	(72,2,1337.00,195.00,'2017-05-14 15:44:34','2017-05-14 15:44:34'),
	(73,2,1380.00,142.00,'2017-05-13 15:44:34','2017-05-13 15:44:34'),
	(74,2,1506.00,260.00,'2017-05-12 15:44:34','2017-05-12 15:44:34'),
	(75,2,1055.00,229.00,'2017-05-11 15:44:34','2017-05-11 15:44:34'),
	(76,2,1537.00,252.00,'2017-05-10 15:44:34','2017-05-10 15:44:34'),
	(77,2,1024.00,225.00,'2017-05-09 15:44:34','2017-05-09 15:44:34'),
	(78,2,1600.00,297.00,'2017-05-08 15:44:34','2017-05-08 15:44:34'),
	(79,2,1392.00,207.00,'2017-05-07 15:44:34','2017-05-07 15:44:34'),
	(80,2,1413.00,270.00,'2017-05-06 15:44:34','2017-05-06 15:44:34'),
	(81,2,1874.00,118.00,'2017-05-05 15:44:34','2017-05-05 15:44:34'),
	(82,2,1735.00,209.00,'2017-05-04 15:44:34','2017-05-04 15:44:34'),
	(83,2,1978.00,123.00,'2017-05-03 15:44:34','2017-05-03 15:44:34'),
	(84,2,1196.00,141.00,'2017-05-02 15:44:34','2017-05-02 15:44:34'),
	(85,2,1446.00,144.00,'2017-05-01 15:44:34','2017-05-01 15:44:34'),
	(86,2,1563.00,105.00,'2017-04-30 15:44:34','2017-04-30 15:44:34'),
	(87,2,1823.00,139.00,'2017-04-29 15:44:34','2017-04-29 15:44:34'),
	(88,2,1984.00,226.00,'2017-04-28 15:44:34','2017-04-28 15:44:34'),
	(89,2,1539.00,221.00,'2017-04-27 15:44:34','2017-04-27 15:44:34'),
	(90,2,1889.00,149.00,'2017-04-26 15:44:34','2017-04-26 15:44:34'),
	(91,2,1024.00,218.00,'2017-04-25 15:44:34','2017-04-25 15:44:34'),
	(92,2,1954.00,299.00,'2017-04-24 15:44:34','2017-04-24 15:44:34'),
	(93,2,1381.00,247.00,'2017-04-23 15:44:34','2017-04-23 15:44:34'),
	(94,2,1025.00,187.00,'2017-04-22 15:44:34','2017-04-22 15:44:34'),
	(95,2,1931.00,109.00,'2017-04-21 15:44:34','2017-04-21 15:44:34'),
	(96,2,1840.00,194.00,'2017-04-20 15:44:34','2017-04-20 15:44:34'),
	(97,2,1859.00,182.00,'2017-04-19 15:44:34','2017-04-19 15:44:34'),
	(98,2,1372.00,127.00,'2017-04-18 15:44:34','2017-04-18 15:44:34'),
	(99,2,1236.00,132.00,'2017-04-17 15:44:34','2017-04-17 15:44:34'),
	(100,2,1084.00,223.00,'2017-04-16 15:44:34','2017-04-16 15:44:34');

/*!40000 ALTER TABLE `gross_indicators` ENABLE KEYS */;
UNLOCK TABLES;


# Дамп таблицы migrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;

INSERT INTO `migrations` (`id`, `migration`, `batch`)
VALUES
	(1,'2014_10_12_000000_create_users_table',1),
	(2,'2014_10_12_100000_create_password_resets_table',1),
	(3,'2017_07_24_134541_create_permission_tables',2),
	(4,'2017_07_24_145429_add_head_id_to_users_table',3),
	(5,'2017_07_24_153844_create_gross_indicators_table',4),
	(15,'2017_07_28_105345_create_proxies_table',5),
	(16,'2017_07_28_105639_create_qiwi_wallet_types_table',5),
	(17,'2017_07_28_105808_create_qiwi_wallets_table',5),
	(18,'2017_07_31_112847_add_is_active_to_qiwi_wallets_table',6);

/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;


# Дамп таблицы model_has_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `model_has_permissions`;

CREATE TABLE `model_has_permissions` (
  `permission_id` int(10) unsigned NOT NULL,
  `model_id` int(10) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Дамп таблицы model_has_roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `model_has_roles`;

CREATE TABLE `model_has_roles` (
  `role_id` int(10) unsigned NOT NULL,
  `model_id` int(10) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;

INSERT INTO `model_has_roles` (`role_id`, `model_id`, `model_type`)
VALUES
	(1,1,'App\\User'),
	(5,2,'App\\User');

/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;


# Дамп таблицы password_resets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `password_resets`;

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Дамп таблицы permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `permissions`;

CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Дамп таблицы proxies
# ------------------------------------------------------------

DROP TABLE IF EXISTS `proxies`;

CREATE TABLE `proxies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `host` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `port` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `using_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `proxies` WRITE;
/*!40000 ALTER TABLE `proxies` DISABLE KEYS */;

INSERT INTO `proxies` (`id`, `host`, `port`, `login`, `password`, `country`, `status`, `type`, `using_type`, `price`, `created_at`, `updated_at`)
VALUES
	(1,'host1','port1','login1','password1',NULL,NULL,'admin','own',NULL,'2017-07-28 14:05:38',NULL),
	(2,'47.52.5.8','3128',NULL,NULL,NULL,NULL,'admin','own',NULL,'2017-07-28 14:09:36',NULL),
	(3,'host1',NULL,NULL,NULL,NULL,NULL,'system','own',NULL,'2017-07-30 16:20:15',NULL),
	(4,'host2',NULL,NULL,NULL,NULL,NULL,'system','own',NULL,'2017-07-30 16:20:40',NULL);

/*!40000 ALTER TABLE `proxies` ENABLE KEYS */;
UNLOCK TABLES;


# Дамп таблицы qiwi_wallet_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `qiwi_wallet_types`;

CREATE TABLE `qiwi_wallet_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `qiwi_wallet_types` WRITE;
/*!40000 ALTER TABLE `qiwi_wallet_types` DISABLE KEYS */;

INSERT INTO `qiwi_wallet_types` (`id`, `name`, `slug`, `created_at`, `updated_at`)
VALUES
	(1,'Приёмные киви','receive',NULL,NULL),
	(2,'Резервные киви','reserve',NULL,NULL),
	(3,'Выводные киви','output',NULL,NULL),
	(4,'Отработанные киви','spent',NULL,NULL);

/*!40000 ALTER TABLE `qiwi_wallet_types` ENABLE KEYS */;
UNLOCK TABLES;


# Дамп таблицы qiwi_wallets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `qiwi_wallets`;

CREATE TABLE `qiwi_wallets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) unsigned NOT NULL,
  `proxy_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `balance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `month_income` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `qiwi_wallets_type_id_index` (`type_id`),
  KEY `qiwi_wallets_proxy_id_index` (`proxy_id`),
  CONSTRAINT `qiwi_wallets_proxy_id_foreign` FOREIGN KEY (`proxy_id`) REFERENCES `proxies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `qiwi_wallets_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `qiwi_wallet_types` (`id`) ON DELETE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `qiwi_wallets` WRITE;
/*!40000 ALTER TABLE `qiwi_wallets` DISABLE KEYS */;

INSERT INTO `qiwi_wallets` (`id`, `type_id`, `proxy_id`, `name`, `login`, `password`, `balance`, `month_income`, `is_active`, `created_at`, `updated_at`)
VALUES
	(1,2,NULL,'1й кошелек','+79250308492','Miramane8411w4','1200','1200',1,NULL,'2017-07-31 15:24:31');

/*!40000 ALTER TABLE `qiwi_wallets` ENABLE KEYS */;
UNLOCK TABLES;


# Дамп таблицы role_has_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `role_has_permissions`;

CREATE TABLE `role_has_permissions` (
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Дамп таблицы roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`)
VALUES
	(1,'superadmin','web','2017-07-24 12:03:45','2017-07-24 12:03:45'),
	(2,'Региональный менеджер','web','2017-07-24 12:03:45','2017-07-24 12:03:45'),
	(3,'Зам. директора','web','2017-07-24 12:03:45','2017-07-24 12:03:45'),
	(4,'Куратор','web','2017-07-24 12:03:45','2017-07-24 12:03:45'),
	(5,'Менеджер по продажам','web','2017-07-24 12:03:45','2017-07-24 12:03:45');

/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;


# Дамп таблицы users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `head_id` int(10) unsigned DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_head_id_index` (`head_id`),
  CONSTRAINT `users_head_id_foreign` FOREIGN KEY (`head_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `name`, `head_id`, `email`, `password`, `remember_token`, `created_at`, `updated_at`)
VALUES
	(1,'SuperAdmin',NULL,'admin@dinero.com','$2y$10$psunb9pSP3OXBI3zyYyW1.P5YTCd0pF56MJdCPDpP73Wg5CJD25Jm','RpkFnOPS3s5wT22nDqaSeDXYTABp5NZrZ0yM6w3zTeJoi4lu6DkdH4r7i1cw','2017-07-24 12:03:45','2017-07-24 12:03:45'),
	(2,'coo2571',1,'','',NULL,NULL,NULL);

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
