SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos`
--

CREATE TABLE `datos` (
  `id` int(1) NOT NULL,
  `fecha` date NOT NULL,
  `contagiados` int(11) NOT NULL DEFAULT 0,
  `fallecidos` int(11) NOT NULL DEFAULT 0,
  `curados` int(11) NOT NULL DEFAULT 0,
  `hospitalizados` int(11) NOT NULL DEFAULT 0,
  `uci` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `estados_detalle`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `estados_detalle` (
`pais` varchar(100)
,`estado` varchar(100)
,`fecha` date
,`contagiados` decimal(32,0)
,`fallecidos` decimal(32,0)
,`curados` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `estados_total`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `estados_total` (
`pais` varchar(100)
,`estado` varchar(100)
,`contagiados` decimal(32,0)
,`fallecidos` decimal(32,0)
,`curados` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nombres`
--

CREATE TABLE `nombres` (
  `pais` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `estado` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `isla` varchar(100) COLLATE utf8_spanish2_ci NOT NULL,
  `lat` double DEFAULT NULL,
  `lon` double DEFAULT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `paises_detalle`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `paises_detalle` (
`pais` varchar(100)
,`fecha` date
,`contagiados` decimal(32,0)
,`fallecidos` decimal(32,0)
,`curados` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `paises_total`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `paises_total` (
`pais` varchar(100)
,`contagiados` decimal(32,0)
,`fallecidos` decimal(32,0)
,`curados` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `estados_detalle`
--
DROP TABLE IF EXISTS `estados_detalle`;

CREATE VIEW `estados_detalle`  AS  select `n`.`pais` AS `pais`,`n`.`estado` AS `estado`,`d`.`fecha` AS `fecha`,sum(`d`.`contagiados`) AS `contagiados`,sum(`d`.`fallecidos`) AS `fallecidos`,sum(`d`.`curados`) AS `curados` from (`datos` `d` join `nombres` `n` on(`n`.`id` = `d`.`id`)) group by `n`.`pais`,`n`.`estado`,`d`.`fecha` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `estados_total`
--
DROP TABLE IF EXISTS `estados_total`;

CREATE VIEW `estados_total`  AS  select `n`.`pais` AS `pais`,`n`.`estado` AS `estado`,sum(`d`.`contagiados`) AS `contagiados`,sum(`d`.`fallecidos`) AS `fallecidos`,sum(`d`.`curados`) AS `curados` from (`datos` `d` join `nombres` `n` on(`n`.`id` = `d`.`id`)) group by `n`.`pais`,`n`.`estado` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `paises_detalle`
--
DROP TABLE IF EXISTS `paises_detalle`;

CREATE VIEW `paises_detalle`  AS  (select `n`.`pais` AS `pais`,`d`.`fecha` AS `fecha`,sum(`d`.`contagiados`) AS `contagiados`,sum(`d`.`fallecidos`) AS `fallecidos`,sum(`d`.`curados`) AS `curados` from (`datos` `d` join `nombres` `n` on(`n`.`id` = `d`.`id`)) group by `n`.`pais`,`d`.`fecha`) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `paises_total`
--
DROP TABLE IF EXISTS `paises_total`;

CREATE VIEW `paises_total`  AS  (select `n`.`pais` AS `pais`,sum(`d`.`contagiados`) AS `contagiados`,sum(`d`.`fallecidos`) AS `fallecidos`,sum(`d`.`curados`) AS `curados` from (`datos` `d` join `nombres` `n` on(`n`.`id` = `d`.`id`)) group by `n`.`pais`) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `datos`
--
ALTER TABLE `datos`
  ADD PRIMARY KEY (`id`,`fecha`) USING BTREE;

--
-- Indices de la tabla `nombres`
--
ALTER TABLE `nombres`
  ADD PRIMARY KEY (`pais`,`estado`,`isla`),
  ADD UNIQUE KEY `id` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
