--
-- Base de datos: `saraEstructura`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>bloque`
--

CREATE TABLE IF NOT EXISTS "<nombre>bloque" (
  "id_bloque" int(5) NOT NULL,
  "nombre" char(50) COLLATE utf8_unicode_ci NOT NULL,
  "descripcion" char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  "grupo" char(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY ("id_bloque"),
  KEY "id_bloque" ("id_bloque")
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>bloque_pagina`
--

CREATE TABLE IF NOT EXISTS "<nombre>bloque_pagina" (
  "idrelacion" int(10) NOT NULL,
  "id_pagina" int(5) NOT NULL DEFAULT '0',
  "id_bloque" int(5) NOT NULL DEFAULT '0',
  "seccion" char(1) COLLATE utf8_unicode_ci NOT NULL,
  "posicion" int(2) NOT NULL DEFAULT '0'
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>configuracion`
--

CREATE TABLE IF NOT EXISTS "<nombre>configuracion" (
  "id_parametro" int(3) NOT NULL,
  "parametro" char(255) COLLATE utf8_unicode_ci NOT NULL,
  "valor" char(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY ("id_parametro"),
  KEY "parametro" ("parametro")
) AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>dbms`
--

CREATE TABLE IF NOT EXISTS "<nombre>dbms" (
  "idconexion" int(10) NOT NULL,
  "nombre" char(50) COLLATE utf8_unicode_ci NOT NULL,
  "dbms" char(20) COLLATE utf8_unicode_ci NOT NULL,
  "servidor" char(50) COLLATE utf8_unicode_ci NOT NULL,
  "puerto" int(6) NOT NULL,
  "conexionssh" char(50) COLLATE utf8_unicode_ci NOT NULL,
  "db" char(100) COLLATE utf8_unicode_ci NOT NULL,
  "esquema" char(100) COLLATE utf8_unicode_ci NOT NULL,
  "usuario" char(100) COLLATE utf8_unicode_ci NOT NULL,
  "password" char(200) COLLATE utf8_unicode_ci NOT NULL
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>estilo`
--

CREATE TABLE IF NOT EXISTS "<nombre>estilo" (
  "usuario" char(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  "estilo" char(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY ("usuario","estilo")
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>logger`
--

CREATE TABLE IF NOT EXISTS "<nombre>logger" (
  "id" int(10) COLLATE utf8_unicode_ci NOT NULL,
  "evento" char(255) COLLATE utf8_unicode_ci NOT NULL,
  "fecha" char(50) COLLATE utf8_unicode_ci NOT NULL
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>pagina`
--

CREATE TABLE IF NOT EXISTS "<nombre>pagina" (
  "id_pagina" int(5) NOT NULL,
  "nombre" char(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  "descripcion" char(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  "modulo" char(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  "nivel" int(2) NOT NULL DEFAULT '0',
  "parametro" char(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY ("id_pagina"),
  UNIQUE KEY "id_pagina" ("id_pagina")
) AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>registrado`
--

CREATE TABLE IF NOT EXISTS "<nombre>usuario" (
  "id_usuario" int(4) NOT NULL,
  "nombre" varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  "apellido" varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  "correo" varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  "telefono" varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  "imagen" char(255) COLLATE utf8_unicode_ci NOT NULL,
  "clave" varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  "tipo" varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  "estilo" varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'basico',
  "idioma" varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'es_es',
  "estado" int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY ("id_usuario"),
  KEY "id_usuario" ("id_usuario")
) AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>usuario_subsistema`
--

CREATE TABLE IF NOT EXISTS "<nombre>registrado_subsistema" (
  "id_usuario" int(6) NOT NULL DEFAULT '0',
  "id_subsistema" int(6) NOT NULL DEFAULT '0',
  "estado" int(2) NOT NULL DEFAULT '0'
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>subsistema`
--

CREATE TABLE IF NOT EXISTS "<nombre>subsistema" (
  "id_subsistema" int(7) NOT NULL,
  "nombre" varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  "etiqueta" varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  "id_pagina" int(7) NOT NULL DEFAULT '0',
  "observacion" text COLLATE utf8_unicode_ci,
  PRIMARY KEY ("id_subsistema")
) AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>tempFormulario`
--

CREATE TABLE IF NOT EXISTS "<nombre>tempFormulario" (
  "id_sesion" char(32) COLLATE utf8_unicode_ci NOT NULL,
  "formulario" char(100) COLLATE utf8_unicode_ci NOT NULL,
  "campo" char(100) COLLATE utf8_unicode_ci NOT NULL,
  "valor" text COLLATE utf8_unicode_ci NOT NULL,
  "fecha" char(50) COLLATE utf8_unicode_ci NOT NULL,
  KEY "id_sesion" ("id_sesion")
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `<nombre>valor_sesion`
--

CREATE TABLE IF NOT EXISTS "<nombre>valor_sesion" (
  "sesionId" char(32) COLLATE utf8_unicode_ci NOT NULL,
  "variable" char(20) COLLATE utf8_unicode_ci NOT NULL,
  "valor" char(255) COLLATE utf8_unicode_ci NOT NULL,
  expiracion bigInt DEFAULT 0 NOT NULL,
  PRIMARY KEY ("id_sesion","variable")
);
