-- phpMyAdmin SQL Dump
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 12-08-2016 a las 02:21:44
-- Versión del servidor: 5.7.13-0ubuntu0.16.04.2
-- Versión de PHP: 7.0.8-0ubuntu0.16.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de datos: `ProjectBase`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pb_acl_roles`
--

CREATE TABLE `pb_acl_roles` (
  `role_id` int(11) NOT NULL,
  `level` int(4) NOT NULL,
  `role_group` varchar(11) NOT NULL,
  `role_type` varchar(11) NOT NULL,
  `role_description` text NOT NULL,
  `role_name` varchar(32) NOT NULL,
  `resource` varchar(11) NOT NULL DEFAULT 'ALL'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `pb_acl_roles`
--

INSERT INTO `pb_acl_roles` (`role_id`, `level`, `role_group`, `role_type`, `role_description`, `role_name`, `resource`) VALUES
(1, 1, 'news', 'all', 'admin news role description', 'L_NEWS_ADMIN_ALL', 'ALL'),
(2, 1, 'admin', 'all', 'Admin all description', 'L_ADMIN_ALL', 'ALL'),
(3, 2, 'admin', 'read', 'Admin read desc', 'L_ADMIN_READ', 'ALL'),
(4, 3, 'admin', 'change', 'admin change desc', 'L_ADMIN_CHANGE', 'ALL');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pb_acl_users`
--

CREATE TABLE `pb_acl_users` (
  `urid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `pb_acl_users`
--

INSERT INTO `pb_acl_users` (`urid`, `uid`, `role_id`) VALUES
(1, 1, 1),
(2, 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pb_adv_stats`
--

CREATE TABLE `pb_adv_stats` (
  `advstatid` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `plugin` varchar(11) NOT NULL,
  `lang` varchar(2) DEFAULT NULL,
  `rid` int(11) DEFAULT NULL,
  `ip` varchar(32) DEFAULT NULL,
  `hostname` varchar(32) DEFAULT NULL,
  `referer` varchar(256) DEFAULT NULL,
  `user_agent` varchar(256) DEFAULT NULL,
  `counter` int(11) NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pb_categories`
--

CREATE TABLE `pb_categories` (
  `cid` int(11) NOT NULL,
  `plugin` varchar(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `father` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `pb_categories`
--

INSERT INTO `pb_categories` (`cid`, `plugin`, `lang_id`, `name`, `father`) VALUES
(1, 'Newspage', 1, 'Actualidad', 1),
(1, 'Newspage', 2, 'News', 1),
(2, 'Newspage', 1, 'Tecnologia', 1),
(2, 'Newspage', 2, 'Tech', 1),
(3, 'Newspage', 1, 'Deportes', 1),
(3, 'Newspage', 2, 'Sports', 1),
(4, 'Newspage', 1, 'Ocio', 1),
(4, 'Newspage', 2, 'Enterteiment', 1),
(5, 'Newspage', 1, 'Otros', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pb_comments`
--

CREATE TABLE `pb_comments` (
  `cid` int(11) NOT NULL,
  `plugin` varchar(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `lang_id` tinyint(2) NOT NULL,
  `message` longtext NOT NULL,
  `author` varchar(32) NOT NULL,
  `author_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rating` float NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pb_lang`
--

CREATE TABLE `pb_lang` (
  `lang_id` int(11) NOT NULL,
  `lang_name` varchar(11) NOT NULL,
  `iso_code` varchar(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `pb_lang`
--

INSERT INTO `pb_lang` (`lang_id`, `lang_name`, `iso_code`, `active`) VALUES
(1, 'Español', 'es', 1),
(2, 'English', 'en', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pb_links`
--

CREATE TABLE `pb_links` (
  `link_id` int(11) NOT NULL,
  `plugin` varchar(32) NOT NULL,
  `source_id` int(11) NOT NULL,
  `type` varchar(11) NOT NULL,
  `link` varchar(256) NOT NULL,
  `itsmain` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pb_news`
--

CREATE TABLE `pb_news` (
  `nid` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `page` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `lead` text,
  `text` longtext NOT NULL,
  `acl` varchar(11) NOT NULL,
  `author` varchar(32) NOT NULL,
  `author_id` int(11) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `category` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_edited` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `frontpage` tinyint(1) NOT NULL DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `moderation` tinyint(1) NOT NULL DEFAULT '0',
  `visits` int(32) NOT NULL DEFAULT '0',
  `translator` varchar(32) DEFAULT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `tags` varchar(128) DEFAULT NULL,
  `rating` float NOT NULL DEFAULT '0',
  `rating_closed` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Estructura de tabla para la tabla `pb_news_ads`
--

CREATE TABLE `pb_news_ads` (
  `adid` int(11) NOT NULL,
  `lang` int(2) DEFAULT NULL,
  `ad_code` text NOT NULL,
  `resource_id` int(11) NOT NULL DEFAULT '0',
  `itsmain` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `pb_news_ads`
--

INSERT INTO `pb_news_ads` (`adid`, `lang`, `ad_code`, `resource_id`, `itsmain`) VALUES
(1, NULL, '<img src="https://static06.cminds.com/wp-content/uploads/banner_functionalvisual-design1.png" alt="" />', 0, 1),
(2, NULL, '<img src=\'http://www.2015awmanationals.com/images/your_ad_here.jpg\' alt=\'\' />\r\n', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pb_rating_track`
--

CREATE TABLE `pb_rating_track` (
  `rating_id` int(32) NOT NULL,
  `uid` int(11) NOT NULL,
  `ip` varchar(14) NOT NULL DEFAULT '0',
  `section` varchar(32) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `lang_id` int(6) DEFAULT NULL,
  `vote_value` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pb_sessions`
--

CREATE TABLE `pb_sessions` (
  `session_id` varchar(32) NOT NULL,
  `session_uid` int(11) NOT NULL,
  `session_ip` varchar(11) NOT NULL,
  `session_browser` text NOT NULL,
  `session_expire` int(11) NOT NULL,
  `last_login` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Estructura de tabla para la tabla `pb_users`
--

CREATE TABLE `pb_users` (
  `uid` int(32) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `email` varchar(64) NOT NULL,
  `realname` varchar(64) DEFAULT NULL,
  `regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` int(12) NOT NULL DEFAULT '1',
  `disable` tinyint(4) NOT NULL DEFAULT '0',
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0',
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reset` int(11) DEFAULT NULL,
  `avatar` varchar(256) DEFAULT NULL,
  `tos` tinyint(1) NOT NULL DEFAULT '1',
  `user_rating` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `pb_users`
--

INSERT INTO `pb_users` (`uid`, `username`, `password`, `email`, `realname`, `regdate`, `active`, `disable`, `isAdmin`, `last_login`, `reset`, `avatar`, `tos`, `user_rating`) VALUES
(1, 'diego', 'pass', 'no@no.es', 'Diegargon', '2016-07-25 14:04:38', 0, 0, 0, '2016-08-11 23:56:43', 0, 'http://diego.envigo.net/images/Mifoto_Desktop.png', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pb_user_extra`
--

CREATE TABLE `pb_user_extra` (
  `ueid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `keyword` varchar(16) NOT NULL,
  `value` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pb_acl_roles`
--
ALTER TABLE `pb_acl_roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indices de la tabla `pb_acl_users`
--
ALTER TABLE `pb_acl_users`
  ADD PRIMARY KEY (`urid`);

--
-- Indices de la tabla `pb_adv_stats`
--
ALTER TABLE `pb_adv_stats`
  ADD PRIMARY KEY (`advstatid`);

--
-- Indices de la tabla `pb_categories`
--
ALTER TABLE `pb_categories`
  ADD PRIMARY KEY (`cid`,`plugin`,`lang_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `pb_comments`
--
ALTER TABLE `pb_comments`
  ADD PRIMARY KEY (`cid`),
  ADD UNIQUE KEY `cid` (`cid`);

--
-- Indices de la tabla `pb_lang`
--
ALTER TABLE `pb_lang`
  ADD PRIMARY KEY (`lang_id`),
  ADD UNIQUE KEY `lang_id` (`lang_id`);

--
-- Indices de la tabla `pb_links`
--
ALTER TABLE `pb_links`
  ADD PRIMARY KEY (`link_id`),
  ADD UNIQUE KEY `link_id` (`link_id`);

--
-- Indices de la tabla `pb_news`
--
ALTER TABLE `pb_news`
  ADD PRIMARY KEY (`nid`,`lang_id`,`page`),
  ADD UNIQUE KEY `nid` (`nid`,`lang_id`,`page`);

--
-- Indices de la tabla `pb_news_ads`
--
ALTER TABLE `pb_news_ads`
  ADD PRIMARY KEY (`adid`),
  ADD UNIQUE KEY `adid` (`adid`);

--
-- Indices de la tabla `pb_rating_track`
--
ALTER TABLE `pb_rating_track`
  ADD PRIMARY KEY (`rating_id`),
  ADD UNIQUE KEY `rating_id` (`rating_id`);

--
-- Indices de la tabla `pb_sessions`
--
ALTER TABLE `pb_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD UNIQUE KEY `session_id` (`session_id`);

--
-- Indices de la tabla `pb_users`
--
ALTER TABLE `pb_users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `uid` (`uid`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `pb_user_extra`
--
ALTER TABLE `pb_user_extra`
  ADD PRIMARY KEY (`ueid`),
  ADD UNIQUE KEY `ueid` (`ueid`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pb_acl_roles`
--
ALTER TABLE `pb_acl_roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `pb_acl_users`
--
ALTER TABLE `pb_acl_users`
  MODIFY `urid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `pb_adv_stats`
--
ALTER TABLE `pb_adv_stats`
  MODIFY `advstatid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;
--
-- AUTO_INCREMENT de la tabla `pb_comments`
--
ALTER TABLE `pb_comments`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT de la tabla `pb_lang`
--
ALTER TABLE `pb_lang`
  MODIFY `lang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `pb_links`
--
ALTER TABLE `pb_links`
  MODIFY `link_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;
--
-- AUTO_INCREMENT de la tabla `pb_news_ads`
--
ALTER TABLE `pb_news_ads`
  MODIFY `adid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `pb_rating_track`
--
ALTER TABLE `pb_rating_track`
  MODIFY `rating_id` int(32) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;
--
-- AUTO_INCREMENT de la tabla `pb_users`
--
ALTER TABLE `pb_users`
  MODIFY `uid` int(32) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `pb_user_extra`
--
ALTER TABLE `pb_user_extra`
  MODIFY `ueid` int(11) NOT NULL AUTO_INCREMENT;