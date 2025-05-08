-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Apr 18, 2025 alle 21:32
-- Versione del server: 10.4.28-MariaDB
-- Versione PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `carpooling`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `autista`
--

CREATE TABLE `autista` (
  `id_autista` int(11) NOT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `cognome` varchar(50) DEFAULT NULL,
  `sesso` varchar(10) DEFAULT NULL,
  `recapito_telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `foto` text DEFAULT NULL,
  `dati_auto` varchar(1000) DEFAULT NULL,
  `numero_patente` varchar(30) DEFAULT NULL,
  `scadenza_patente` date DEFAULT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `autista`
--

INSERT INTO `autista` (`id_autista`, `nome`, `cognome`, `sesso`, `recapito_telefono`, `email`, `foto`, `dati_auto`, `numero_patente`, `scadenza_patente`, `password`) VALUES
(1, 'Marco', 'Rossi', 'M', '3331112222', 'marco.rossi@email.it', 'foto1.jpg', 'Toyota Yaris, blu', 'A1234567', '2027-12-31', '12345'),
(2, 'Laura', 'Bianchi', 'F', '3334445555', 'laura.bianchi@email.it', 'foto2.jpg', 'Peugeot 208, rossa', 'B7654321', '2026-06-15', '54321'),
(3, 'alberto', 'conti', 'maschio', '435636', 'fsgrsr', NULL, 'gsrsdger', 'ID324', '2006-12-02', '12345'),
(4, 'alberto', 'conti', 'maschio', '435636', 'fsgrsr', NULL, 'gsrsdger', 'ID324', '2006-12-02', '$2y$10$lj6oujVuOWGlM97/ZOgTc.JFg5J4iajcV4OlkaVbc9/rhNGYNDGDO'),
(7, 'f', 'f', 'Femmina', '3703762231', 'autista@gmail.com', NULL, 'fsd', 'sdf', '2025-05-03', '$2y$10$94kmvsUqZfa7S6nvs0.JPe1wdNTjrj7M3rX9Uri2gu4ofcT2OThZC'),
(8, 't', 't', 'Femmina', '3703762231', 'q@q', NULL, 'sdffsd', 'sdf', '2025-05-03', '$2y$10$5FuQbXfuRIGSNpFr75ZmEeALrJkwDUDutB8k.FPWT3I0fu6OduaZC');

-- --------------------------------------------------------

--
-- Struttura della tabella `feedback`
--

CREATE TABLE `feedback` (
  `id_feedback` int(11) NOT NULL,
  `recensione` varchar(100) DEFAULT NULL,
  `voto` int(11) DEFAULT NULL,
  `id_viaggio` int(11) DEFAULT NULL,
  `id_autista` int(11) DEFAULT NULL,
  `id_utente` int(11) DEFAULT NULL,
  `recensitore` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `feedback`
--

INSERT INTO `feedback` (`id_feedback`, `recensione`, `voto`, `id_viaggio`, `id_autista`, `id_utente`, `recensitore`) VALUES
(1, 'Ottimo viaggio, puntuale e simpatico!', 10, 1, 1, 1, 'passeggero'),
(2, 'Macchina comoda, ma partiti in ritardo.', 9, 2, 2, 3, 'passeggero'),
(3, 'Passeggero maleducato', 10, 3, 1, 1, 'autista');

-- --------------------------------------------------------

--
-- Struttura della tabella `partecipa`
--

CREATE TABLE `partecipa` (
  `id_partecipa` int(11) NOT NULL,
  `id_viaggio` int(11) DEFAULT NULL,
  `id_passeggero` int(11) DEFAULT NULL,
  `stato` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `partecipa`
--

INSERT INTO `partecipa` (`id_partecipa`, `id_viaggio`, `id_passeggero`, `stato`) VALUES
(5, 4, 19, 'sospesa'),
(6, 5, 19, 'accettata'),
(7, 3, 19, 'sospesa');

-- --------------------------------------------------------

--
-- Struttura della tabella `passeggero`
--

CREATE TABLE `passeggero` (
  `id_passeggero` int(11) NOT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `cognome` varchar(50) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `documento` varchar(50) DEFAULT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `passeggero`
--

INSERT INTO `passeggero` (`id_passeggero`, `nome`, `cognome`, `telefono`, `email`, `documento`, `password`) VALUES
(1, 'Giulia', 'Verdi', '3310001111', 'giulia.verdi@email.it', 'ID12345', '12345'),
(2, 'Alessandro', 'Neri', '3312223333', 'alessandro.neri@email.it', 'ID23456', '54321'),
(3, 'Elena', 'Moretti', '3314445555', 'elena.moretti@email.it', 'ID34567', '67890'),
(19, 'd', 'd', '12345678990', 'passeggero@gmail.com', 'sad', '$2y$10$MArtbX1VA5N0XRDfvqR.sOoiHbGGae2y3y1ejxFF6iAHbw5r9DloS'),
(20, 'd', 'd', '12345678990', '2@2', 'dq', '$2y$10$UllxXAa24hUTMHQpewXXnuwCyghtvDQBMY8xmMdI6LSPmwy5hYF26'),
(21, 'l', 'l', '12345678990', 'l@l', 'l', '$2y$10$8G0zSIx44MytDi4WeSRQBOVHtfhV9aVn.5XsR0ukEzsJiOiCC.FUW');

-- --------------------------------------------------------

--
-- Struttura della tabella `viaggio`
--

CREATE TABLE `viaggio` (
  `id_viaggio` int(11) NOT NULL,
  `citta_partenza` varchar(100) DEFAULT NULL,
  `citta_arrivo` varchar(100) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `tempo` int(11) DEFAULT NULL,
  `orario` time DEFAULT NULL,
  `numero_passeggeri` int(11) DEFAULT NULL,
  `soste` int(11) DEFAULT NULL,
  `costo` int(11) DEFAULT NULL,
  `id_autista` int(11) DEFAULT NULL,
  `stato` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `viaggio`
--

INSERT INTO `viaggio` (`id_viaggio`, `citta_partenza`, `citta_arrivo`, `data`, `tempo`, `orario`, `numero_passeggeri`, `soste`, `costo`, `id_autista`, `stato`) VALUES
(1, 'Milano', 'Firenze', '2025-04-12', 3, '08:00:00', 3, 0, 30, 1, 'chiuse'),
(2, 'Roma', 'Napoli', '2025-04-15', 2, '10:00:00', 2, 0, 20, 2, 'aperte'),
(3, 'Torino', 'Genova', '2025-04-12', 1, '09:00:00', 1, 0, 15, 1, 'chiuse'),
(4, 'milano', 'roma', '2025-05-02', 2, '01:40:00', 45, 45, 54, 7, 'chiuse'),
(5, 'milano', 'roma', '2025-05-02', 3, '02:52:00', 4, 4, 3, 7, 'chiuse'),
(6, 'milano', 'roma', '2025-05-02', 5, '03:04:00', 4, 3, 2, 8, 'aperte'),
(7, 'f', 'f', '2025-04-25', 5, '13:04:00', 4, 3, 43, 7, 'aperte');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `autista`
--
ALTER TABLE `autista`
  ADD PRIMARY KEY (`id_autista`);

--
-- Indici per le tabelle `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id_feedback`),
  ADD KEY `id_viaggio` (`id_viaggio`),
  ADD KEY `id_autista` (`id_autista`),
  ADD KEY `id_utente` (`id_utente`);

--
-- Indici per le tabelle `partecipa`
--
ALTER TABLE `partecipa`
  ADD PRIMARY KEY (`id_partecipa`),
  ADD KEY `id_viaggio` (`id_viaggio`),
  ADD KEY `id_passeggero` (`id_passeggero`);

--
-- Indici per le tabelle `passeggero`
--
ALTER TABLE `passeggero`
  ADD PRIMARY KEY (`id_passeggero`);

--
-- Indici per le tabelle `viaggio`
--
ALTER TABLE `viaggio`
  ADD PRIMARY KEY (`id_viaggio`),
  ADD KEY `id_autista` (`id_autista`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `autista`
--
ALTER TABLE `autista`
  MODIFY `id_autista` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT per la tabella `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id_feedback` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT per la tabella `partecipa`
--
ALTER TABLE `partecipa`
  MODIFY `id_partecipa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT per la tabella `passeggero`
--
ALTER TABLE `passeggero`
  MODIFY `id_passeggero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT per la tabella `viaggio`
--
ALTER TABLE `viaggio`
  MODIFY `id_viaggio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`id_viaggio`) REFERENCES `viaggio` (`id_viaggio`),
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`id_autista`) REFERENCES `autista` (`id_autista`),
  ADD CONSTRAINT `feedback_ibfk_3` FOREIGN KEY (`id_utente`) REFERENCES `passeggero` (`id_passeggero`);

--
-- Limiti per la tabella `partecipa`
--
ALTER TABLE `partecipa`
  ADD CONSTRAINT `partecipa_ibfk_1` FOREIGN KEY (`id_viaggio`) REFERENCES `viaggio` (`id_viaggio`),
  ADD CONSTRAINT `partecipa_ibfk_2` FOREIGN KEY (`id_passeggero`) REFERENCES `passeggero` (`id_passeggero`);

--
-- Limiti per la tabella `viaggio`
--
ALTER TABLE `viaggio`
  ADD CONSTRAINT `viaggio_ibfk_1` FOREIGN KEY (`id_autista`) REFERENCES `autista` (`id_autista`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
