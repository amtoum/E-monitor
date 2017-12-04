-- phpMyAdmin SQL Dump
-- version 4.7.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le :  lun. 04 déc. 2017 à 11:02
-- Version du serveur :  5.7.18
-- Version de PHP :  5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `flux_valarnum`
--

-- --------------------------------------------------------

--
-- Structure de la table `flux_acti`
--

CREATE TABLE `flux_acti` (
  `acti_id` int(11) NOT NULL,
  `code` text COLLATE utf8_unicode_ci NOT NULL,
  `desc` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `flux_doc`
--

CREATE TABLE `flux_doc` (
  `doc_id` int(11) NOT NULL,
  `url` text COLLATE utf8_unicode_ci,
  `titre` varchar(5000) COLLATE utf8_unicode_ci NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `tronc` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `poids` int(11) NOT NULL DEFAULT '1',
  `maj` datetime NOT NULL,
  `pubDate` datetime NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `total_posts` int(11) DEFAULT NULL,
  `top_tags` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `data` blob,
  `score` decimal(28,14) DEFAULT NULL,
  `parent` int(11) DEFAULT NULL,
  `niveau` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `flux_doctypes`
--

CREATE TABLE `flux_doctypes` (
  `id_type` bigint(21) NOT NULL,
  `titre` text COLLATE utf8_unicode_ci NOT NULL,
  `descriptif` text COLLATE utf8_unicode_ci NOT NULL,
  `extension` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `mime_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `inclus` enum('non','image','embed') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'non',
  `upload` enum('oui','non') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'oui',
  `maj` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `flux_exi`
--

CREATE TABLE `flux_exi` (
  `exi_id` int(11) NOT NULL,
  `uti_id` int(11) DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prenom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `niveau` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  `poids` int(11) DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `nait` date DEFAULT NULL,
  `mort` date DEFAULT NULL,
  `isni` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `flux_geo`
--

CREATE TABLE `flux_geo` (
  `geo_id` int(11) NOT NULL,
  `lat` decimal(12,8) DEFAULT NULL,
  `lng` decimal(12,8) DEFAULT NULL,
  `alt` decimal(20,10) DEFAULT NULL COMMENT 'en mètre',
  `geo` geometry DEFAULT NULL,
  `zoom_min` int(11) DEFAULT NULL,
  `zoom_max` int(11) DEFAULT NULL,
  `adresse` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `codepostal` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ville` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pays` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `kml` text COLLATE utf8_unicode_ci,
  `type_carte` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `maj` datetime DEFAULT NULL,
  `uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `flux_ieml`
--

CREATE TABLE `flux_ieml` (
  `ieml_id` int(11) NOT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `desc` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `niveau` int(11) NOT NULL,
  `parent` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `maj` date NOT NULL,
  `parse` longtext COLLATE utf8_unicode_ci NOT NULL,
  `binary` longtext COLLATE utf8_unicode_ci NOT NULL,
  `ordre` longtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Cette table est utilisée pour stocker l’ontologie IEML.';

-- --------------------------------------------------------

--
-- Structure de la table `flux_monade`
--

CREATE TABLE `flux_monade` (
  `monade_id` int(11) NOT NULL,
  `titre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `maj` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `flux_rapport`
--

CREATE TABLE `flux_rapport` (
  `rapport_id` int(11) NOT NULL,
  `monade_id` int(11) NOT NULL,
  `src_id` int(11) NOT NULL,
  `src_obj` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `dst_id` int(11) NOT NULL,
  `dst_obj` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `pre_id` int(11) DEFAULT NULL COMMENT 'id prédicat',
  `pre_obj` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'obj prédicat',
  `geo_id` int(11) NOT NULL,
  `maj` datetime NOT NULL,
  `niveau` int(11) DEFAULT NULL,
  `valeur` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `flux_tag`
--

CREATE TABLE `flux_tag` (
  `tag_id` int(11) NOT NULL,
  `code` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `desc` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ns` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `niveau` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `uri` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `flux_uti`
--

CREATE TABLE `flux_uti` (
  `uti_id` int(11) NOT NULL,
  `login` varchar(2000) COLLATE utf8_unicode_ci NOT NULL,
  `maj` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `flux` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mdp` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mdp_sel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ip_inscription` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_inscription` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `flux_acti`
--
ALTER TABLE `flux_acti`
  ADD PRIMARY KEY (`acti_id`);

--
-- Index pour la table `flux_doc`
--
ALTER TABLE `flux_doc`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `type` (`type`),
  ADD KEY `score` (`score`);

--
-- Index pour la table `flux_doctypes`
--
ALTER TABLE `flux_doctypes`
  ADD PRIMARY KEY (`id_type`),
  ADD UNIQUE KEY `extension` (`extension`),
  ADD KEY `inclus` (`inclus`);

--
-- Index pour la table `flux_exi`
--
ALTER TABLE `flux_exi`
  ADD PRIMARY KEY (`exi_id`);

--
-- Index pour la table `flux_geo`
--
ALTER TABLE `flux_geo`
  ADD PRIMARY KEY (`geo_id`);

--
-- Index pour la table `flux_ieml`
--
ALTER TABLE `flux_ieml`
  ADD PRIMARY KEY (`ieml_id`);

--
-- Index pour la table `flux_monade`
--
ALTER TABLE `flux_monade`
  ADD PRIMARY KEY (`monade_id`);

--
-- Index pour la table `flux_rapport`
--
ALTER TABLE `flux_rapport`
  ADD PRIMARY KEY (`rapport_id`),
  ADD KEY `idxMonade` (`monade_id`),
  ADD KEY `idxSrc` (`src_obj`),
  ADD KEY `idxDst` (`dst_obj`),
  ADD KEY `idxPre` (`pre_obj`),
  ADD KEY `idxSrcId` (`src_id`),
  ADD KEY `idxDstId` (`dst_id`),
  ADD KEY `idxPreId` (`pre_id`);

--
-- Index pour la table `flux_tag`
--
ALTER TABLE `flux_tag`
  ADD PRIMARY KEY (`tag_id`),
  ADD KEY `code` (`code`),
  ADD KEY `lft_rgt` (`lft`,`rgt`);

--
-- Index pour la table `flux_uti`
--
ALTER TABLE `flux_uti`
  ADD PRIMARY KEY (`uti_id`),
  ADD KEY `login` (`login`(333));

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `flux_acti`
--
ALTER TABLE `flux_acti`
  MODIFY `acti_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `flux_doc`
--
ALTER TABLE `flux_doc`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20899;
--
-- AUTO_INCREMENT pour la table `flux_doctypes`
--
ALTER TABLE `flux_doctypes`
  MODIFY `id_type` bigint(21) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `flux_exi`
--
ALTER TABLE `flux_exi`
  MODIFY `exi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;
--
-- AUTO_INCREMENT pour la table `flux_geo`
--
ALTER TABLE `flux_geo`
  MODIFY `geo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;
--
-- AUTO_INCREMENT pour la table `flux_ieml`
--
ALTER TABLE `flux_ieml`
  MODIFY `ieml_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `flux_monade`
--
ALTER TABLE `flux_monade`
  MODIFY `monade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `flux_rapport`
--
ALTER TABLE `flux_rapport`
  MODIFY `rapport_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21384;
--
-- AUTO_INCREMENT pour la table `flux_tag`
--
ALTER TABLE `flux_tag`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `flux_uti`
--
ALTER TABLE `flux_uti`
  MODIFY `uti_id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
