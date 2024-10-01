-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 01. Okt 2024 um 09:42
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `fahrzeugpflege`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auftraege`
--

CREATE TABLE `auftraege` (
  `AuftragsID` int(11) NOT NULL,
  `KundenID` int(11) NOT NULL,
  `FahrzeugsID` int(11) NOT NULL,
  `LeistungsID` int(11) NOT NULL,
  `auftrag_eingegangen` date NOT NULL,
  `auftrag_abgeschlossen` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auftrag_leistung`
--

CREATE TABLE `auftrag_leistung` (
  `auftraege_leistungenID` int(11) NOT NULL,
  `AuftragsID` int(11) NOT NULL,
  `LeistungsID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `counters`
--

CREATE TABLE `counters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fahrzeuge`
--

CREATE TABLE `fahrzeuge` (
  `FahrzeugID` int(11) NOT NULL,
  `KundenID` int(11) NOT NULL,
  `Definition` text NOT NULL,
  `Kennzeichen` text NOT NULL,
  `Fahrzeugklasse` text NOT NULL,
  `Automarke` text NOT NULL,
  `Typ` text NOT NULL,
  `Farbe` text NOT NULL,
  `Foto` blob NOT NULL,
  `Sonstiges` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `grosse_innenraumreinigung`
--

CREATE TABLE `grosse_innenraumreinigung` (
  `LeistungsID` int(11) NOT NULL,
  `sitzreinigung_VL` tinyint(1) NOT NULL,
  `sitzreinigung_VR` tinyint(1) NOT NULL,
  `sitzreiningung_HL` tinyint(1) NOT NULL,
  `sitzreinigung_HM` tinyint(1) NOT NULL,
  `sitzreinigung_HR` tinyint(1) NOT NULL,
  `himmelreinigung` tinyint(1) NOT NULL,
  `teppich_shampoonierung` tinyint(1) NOT NULL,
  `kunststoffpflege` tinyint(1) NOT NULL,
  `lederpflege` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kunden`
--

CREATE TABLE `kunden` (
  `KundenID` int(11) NOT NULL,
  `Benutzername` text NOT NULL,
  `Vorname` text NOT NULL,
  `Nachname` text NOT NULL,
  `PLZ` text NOT NULL,
  `Straße` text NOT NULL,
  `Ort` text NOT NULL,
  `Telefonnummer` text NOT NULL,
  `E-Mail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `leistungen`
--

CREATE TABLE `leistungen` (
  `LeistungsID` int(11) NOT NULL,
  `Name` text NOT NULL,
  `Definition` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_09_17_134426_create_counters_table', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mitarbeiter`
--

CREATE TABLE `mitarbeiter` (
  `MitarbeiterID` int(11) NOT NULL,
  `AuftragsID` int(11) NOT NULL,
  `Vorname` text NOT NULL,
  `Nachname` int(255) NOT NULL,
  `Telefonnummer` text NOT NULL,
  `E-Mail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `schnellwaesche_buerste`
--

CREATE TABLE `schnellwaesche_buerste` (
  `LeistungsID` int(11) NOT NULL,
  `felgenreinigung` tinyint(1) NOT NULL,
  `scheibenreinigung` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `schonwaesche_hand`
--

CREATE TABLE `schonwaesche_hand` (
  `LeistungsID` int(11) NOT NULL,
  `felgenreinigung` tinyint(1) NOT NULL,
  `scheibenreinigung` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('sZstoi7IkDemCzSzsrxLXA2l6WvvbdkvBlUe1Vyp', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:130.0) Gecko/20100101 Firefox/130.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWW42UURrSkRselBGV3FIbWRSanVXQTJGNVdlRUNUSzU0akpoaGI5ZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1727702586);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `auftraege`
--
ALTER TABLE `auftraege`
  ADD PRIMARY KEY (`AuftragsID`),
  ADD KEY `KundenID` (`KundenID`),
  ADD KEY `FahrzeugsID` (`FahrzeugsID`);

--
-- Indizes für die Tabelle `auftrag_leistung`
--
ALTER TABLE `auftrag_leistung`
  ADD PRIMARY KEY (`auftraege_leistungenID`),
  ADD KEY `LeistungsID` (`LeistungsID`),
  ADD KEY `AuftragsID` (`AuftragsID`);

--
-- Indizes für die Tabelle `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indizes für die Tabelle `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indizes für die Tabelle `counters`
--
ALTER TABLE `counters`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `fahrzeuge`
--
ALTER TABLE `fahrzeuge`
  ADD PRIMARY KEY (`FahrzeugID`),
  ADD KEY `KundenID` (`KundenID`);

--
-- Indizes für die Tabelle `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indizes für die Tabelle `grosse_innenraumreinigung`
--
ALTER TABLE `grosse_innenraumreinigung`
  ADD PRIMARY KEY (`LeistungsID`);

--
-- Indizes für die Tabelle `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indizes für die Tabelle `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `kunden`
--
ALTER TABLE `kunden`
  ADD PRIMARY KEY (`KundenID`);

--
-- Indizes für die Tabelle `leistungen`
--
ALTER TABLE `leistungen`
  ADD PRIMARY KEY (`LeistungsID`);

--
-- Indizes für die Tabelle `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `mitarbeiter`
--
ALTER TABLE `mitarbeiter`
  ADD PRIMARY KEY (`MitarbeiterID`),
  ADD KEY `AuftragsID` (`AuftragsID`);

--
-- Indizes für die Tabelle `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indizes für die Tabelle `schnellwaesche_buerste`
--
ALTER TABLE `schnellwaesche_buerste`
  ADD PRIMARY KEY (`LeistungsID`);

--
-- Indizes für die Tabelle `schonwaesche_hand`
--
ALTER TABLE `schonwaesche_hand`
  ADD PRIMARY KEY (`LeistungsID`);

--
-- Indizes für die Tabelle `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `auftraege`
--
ALTER TABLE `auftraege`
  MODIFY `AuftragsID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `auftrag_leistung`
--
ALTER TABLE `auftrag_leistung`
  MODIFY `auftraege_leistungenID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `counters`
--
ALTER TABLE `counters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `fahrzeuge`
--
ALTER TABLE `fahrzeuge`
  MODIFY `FahrzeugID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `grosse_innenraumreinigung`
--
ALTER TABLE `grosse_innenraumreinigung`
  MODIFY `LeistungsID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kunden`
--
ALTER TABLE `kunden`
  MODIFY `KundenID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `leistungen`
--
ALTER TABLE `leistungen`
  MODIFY `LeistungsID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `mitarbeiter`
--
ALTER TABLE `mitarbeiter`
  MODIFY `MitarbeiterID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `schnellwaesche_buerste`
--
ALTER TABLE `schnellwaesche_buerste`
  MODIFY `LeistungsID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `schonwaesche_hand`
--
ALTER TABLE `schonwaesche_hand`
  MODIFY `LeistungsID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `auftraege`
--
ALTER TABLE `auftraege`
  ADD CONSTRAINT `auftraege_ibfk_1` FOREIGN KEY (`KundenID`) REFERENCES `auftraege` (`AuftragsID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `auftraege_ibfk_2` FOREIGN KEY (`FahrzeugsID`) REFERENCES `auftraege` (`AuftragsID`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `auftrag_leistung`
--
ALTER TABLE `auftrag_leistung`
  ADD CONSTRAINT `auftrag_leistung_ibfk_1` FOREIGN KEY (`AuftragsID`) REFERENCES `auftrag_leistung` (`auftraege_leistungenID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `auftrag_leistung_ibfk_2` FOREIGN KEY (`LeistungsID`) REFERENCES `auftrag_leistung` (`auftraege_leistungenID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `auftrag_leistung_ibfk_3` FOREIGN KEY (`AuftragsID`) REFERENCES `kunden` (`KundenID`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `fahrzeuge`
--
ALTER TABLE `fahrzeuge`
  ADD CONSTRAINT `fahrzeuge_ibfk_1` FOREIGN KEY (`KundenID`) REFERENCES `kunden` (`KundenID`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `grosse_innenraumreinigung`
--
ALTER TABLE `grosse_innenraumreinigung`
  ADD CONSTRAINT `grosse_innenraumreinigung_ibfk_1` FOREIGN KEY (`LeistungsID`) REFERENCES `leistungen` (`LeistungsID`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `kunden`
--
ALTER TABLE `kunden`
  ADD CONSTRAINT `kunden_ibfk_1` FOREIGN KEY (`KundenID`) REFERENCES `auftraege` (`AuftragsID`);

--
-- Constraints der Tabelle `leistungen`
--
ALTER TABLE `leistungen`
  ADD CONSTRAINT `leistungen_ibfk_1` FOREIGN KEY (`LeistungsID`) REFERENCES `auftrag_leistung` (`LeistungsID`);

--
-- Constraints der Tabelle `mitarbeiter`
--
ALTER TABLE `mitarbeiter`
  ADD CONSTRAINT `mitarbeiter_ibfk_1` FOREIGN KEY (`AuftragsID`) REFERENCES `auftraege` (`AuftragsID`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `schnellwaesche_buerste`
--
ALTER TABLE `schnellwaesche_buerste`
  ADD CONSTRAINT `schnellwaesche_buerste_ibfk_1` FOREIGN KEY (`LeistungsID`) REFERENCES `leistungen` (`LeistungsID`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `schonwaesche_hand`
--
ALTER TABLE `schonwaesche_hand`
  ADD CONSTRAINT `schonwaesche_hand_ibfk_1` FOREIGN KEY (`LeistungsID`) REFERENCES `leistungen` (`LeistungsID`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
