--
-- Base de datos: `<nombre>Estructura`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>bloque`
--

CREATE TABLE IF NOT EXISTS `<nombre>bloque` (
  `id_bloque` int(5) NOT NULL auto_increment,
  `nombre` char(50) collate utf8_unicode_ci NOT NULL,
  `descripcion` char(255) collate utf8_unicode_ci default NULL,
  `grupo` char(20) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id_bloque`),
  KEY `id_bloque` (`id_bloque`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0 COMMENT='Bloques disponibles' ;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>bloque_pagina`
--

CREATE TABLE IF NOT EXISTS `<nombre>bloque_pagina` (

  `idrelacion` int(10) NOT NULL auto_increment,	
  `id_pagina` int(5) NOT NULL default '0',
  `id_bloque` int(5) NOT NULL default '0',
  `seccion` char(1) collate utf8_unicode_ci NOT NULL,
  `posicion` int(2) NOT NULL default '0',
  PRIMARY KEY  (`idrelacion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Estructura de bloques de las paginas en el aplicativo';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>configuracion`
--

CREATE TABLE IF NOT EXISTS `<nombre>configuracion` (
  `id_parametro` int(3) NOT NULL auto_increment,
  `parametro` char(255) collate utf8_unicode_ci NOT NULL,
  `valor` char(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id_parametro`),
  KEY `parametro` (`parametro`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Variables de configuracion'  ;



-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>dbms`
--

CREATE TABLE IF NOT EXISTS `<nombre>dbms` (

  `idconexion` int(10) NOT NULL auto_increment,
  `nombre` char(50) NOT NULL,
  `dbms` char(20) NOT NULL,
  `servidor` char(50) NOT NULL,
  `puerto` int(6) NOT NULL,
  `conexionssh` char(50) NOT NULL,
  `db` char(100) NOT NULL,
  `esquema` char(100) NOT NULL,
  `usuario` char(100) NOT NULL,
  `password` char(200) NOT NULL,
  PRIMARY KEY  (`idconexion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>estilo`
--

CREATE TABLE IF NOT EXISTS `<nombre>estilo` (
  `usuario` char(50) collate utf8_unicode_ci NOT NULL default '0',
  `estilo` char(50) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`usuario`,`estilo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Estilo de pagina en el sitio';

--
-- Volcar la base de datos para la tabla `<nombre>estilo`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>logger`
--

CREATE TABLE IF NOT EXISTS `<nombre>logger` (
  `id` int(10) NOT NULL auto_increment,
  `evento` char(255) collate utf8_unicode_ci NOT NULL,
  `fecha` char(50) collate utf8_unicode_ci NOT NULL,
   PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Registro de acceso de los usuarios';

--
-- Volcar la base de datos para la tabla `<nombre>logger`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>pagina`
--

CREATE TABLE IF NOT EXISTS `<nombre>pagina` (
  `id_pagina` int(5) NOT NULL auto_increment,
  `nombre` char(50) collate utf8_unicode_ci NOT NULL default '',
  `descripcion` char(250) collate utf8_unicode_ci NOT NULL default '',
  `modulo` char(50) collate utf8_unicode_ci NOT NULL default '',
  `nivel` int(2) NOT NULL default '0',
  `parametro` char(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id_pagina`),
  UNIQUE KEY `id_pagina` (`id_pagina`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>registrado`
--

CREATE TABLE IF NOT EXISTS `<nombre>usuario` (
  `id_usuario` int(4) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL DEFAULT '',
  `apellido` varchar(50) NOT NULL DEFAULT '',
  `correo` varchar(100) NOT NULL DEFAULT '',
  `telefono` varchar(50) NOT NULL DEFAULT '',
  `imagen` char(255) NOT NULL,
  `clave` varchar(100) NOT NULL DEFAULT '',
  `tipo` varchar(255) NOT NULL DEFAULT '',
  `estilo` varchar(50) NOT NULL DEFAULT 'basico',
  `idioma` varchar(50) NOT NULL DEFAULT 'es_es',
  `estado` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_usuario`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
--
-- Estructura de tabla para la tabla `<nombre>registrado_subsistema`
--

CREATE TABLE IF NOT EXISTS `<nombre>usuario_subsistema` (
  `id_usuario` int(6) NOT NULL default '0',
  `id_subsistema` int(6) NOT NULL default '0',
  `estado` int(2) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Relacion de usuarios que tienen acceso a modulos especiales';

--
-- Volcar la base de datos para la tabla `<nombre>registrado_subsistema`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>subsistema`
--

CREATE TABLE IF NOT EXISTS `<nombre>subsistema` (
  `id_subsistema` int(7) NOT NULL auto_increment,
  `nombre` varchar(250) collate utf8_unicode_ci NOT NULL,
  `etiqueta` varchar(100) collate utf8_unicode_ci NOT NULL,
  `id_pagina` int(7) NOT NULL default '0',
  `observacion` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id_subsistema`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0 COMMENT='Subsistemas que componen el aplicativo';

--
-- Volcar la base de datos para la tabla `<nombre>subsistema`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>tempFormulario`
--

CREATE TABLE IF NOT EXISTS `<nombre>tempFormulario` (
  `id_sesion` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `formulario` char(100) COLLATE utf8_unicode_ci NOT NULL,
  `campo` char(100) COLLATE utf8_unicode_ci NOT NULL,
  `valor` text COLLATE utf8_unicode_ci NOT NULL,
  `fecha` char(50) COLLATE utf8_unicode_ci NOT NULL,
  KEY `id_sesion` (`id_sesion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>valor_sesion`
--

CREATE TABLE IF NOT EXISTS `<nombre>valor_sesion` (
  `sesionId` char(32) collate utf8_unicode_ci NOT NULL,
  `variable` char(20) collate utf8_unicode_ci NOT NULL,
  `valor` char(255) collate utf8_unicode_ci NOT NULL,
  `expiracion` bigint NOT NULL,
  PRIMARY KEY  (`sesionId`,`variable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Valores de sesion';

--
-- Volcar la base de datos para la tabla `<nombre>valor_sesion`
--

