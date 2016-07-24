-- MySQL dump 10.13  Distrib 5.5.28, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: saraEstructura
-- ------------------------------------------------------
-- Server version	5.5.28-0ubuntu0.12.04.2
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,POSTGRESQL' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table "<nombre>bloque"
--

DROP TABLE IF EXISTS "<nombre>bloque";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "<nombre>bloque" (
  "id_bloque" int(5) NOT NULL,
  "nombre" char(50) COLLATE utf8_unicode_ci NOT NULL,
  "descripcion" char(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  "grupo" char(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY ("id_bloque"),
  KEY "id_bloque" ("id_bloque")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "<nombre>bloque"
--

LOCK TABLES "<nombre>bloque" WRITE;
/*!40000 ALTER TABLE "<nombre>bloque" DISABLE KEYS */;
/*!40000 ALTER TABLE "<nombre>bloque" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "<nombre>bloque_pagina"
--

DROP TABLE IF EXISTS "<nombre>bloque_pagina";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "<nombre>bloque_pagina" (
  "id_pagina" int(5) NOT NULL DEFAULT '0',
  "id_bloque" int(5) NOT NULL DEFAULT '0',
  "seccion" char(1) COLLATE utf8_unicode_ci NOT NULL,
  "posicion" int(2) NOT NULL DEFAULT '0'
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "<nombre>bloque_pagina"
--

LOCK TABLES "<nombre>bloque_pagina" WRITE;
/*!40000 ALTER TABLE "<nombre>bloque_pagina" DISABLE KEYS */;
/*!40000 ALTER TABLE "<nombre>bloque_pagina" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "<nombre>configuracion"
--

DROP TABLE IF EXISTS "<nombre>configuracion";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "<nombre>configuracion" (
  "id_parametro" int(3) NOT NULL,
  "parametro" char(255) COLLATE utf8_unicode_ci NOT NULL,
  "valor" char(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY ("id_parametro"),
  KEY "parametro" ("parametro")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "<nombre>configuracion"
--

LOCK TABLES "<nombre>configuracion" WRITE;
/*!40000 ALTER TABLE "<nombre>configuracion" DISABLE KEYS */;
/*!40000 ALTER TABLE "<nombre>configuracion" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "<nombre>dbms"
--

DROP TABLE IF EXISTS "<nombre>dbms";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "<nombre>dbms" (
  "nombre" char(50) COLLATE utf8_unicode_ci NOT NULL,
  "dbms" char(20) COLLATE utf8_unicode_ci NOT NULL,
  "servidor" char(50) COLLATE utf8_unicode_ci NOT NULL,
  "puerto" int(6) NOT NULL,
  "ssl" char(50) COLLATE utf8_unicode_ci NOT NULL,
  "db" char(100) COLLATE utf8_unicode_ci NOT NULL,
  "usuario" char(100) COLLATE utf8_unicode_ci NOT NULL,
  "password" char(200) COLLATE utf8_unicode_ci NOT NULL
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "<nombre>dbms"
--

LOCK TABLES "<nombre>dbms" WRITE;
/*!40000 ALTER TABLE "<nombre>dbms" DISABLE KEYS */;
/*!40000 ALTER TABLE "<nombre>dbms" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "<nombre>estilo"
--

DROP TABLE IF EXISTS "<nombre>estilo";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "<nombre>estilo" (
  "usuario" char(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  "estilo" char(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY ("usuario","estilo")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "<nombre>estilo"
--

LOCK TABLES "<nombre>estilo" WRITE;
/*!40000 ALTER TABLE "<nombre>estilo" DISABLE KEYS */;
/*!40000 ALTER TABLE "<nombre>estilo" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "<nombre>logger"
--

DROP TABLE IF EXISTS "<nombre>logger";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "<nombre>logger" (
  "id_usuario" char(5) COLLATE utf8_unicode_ci NOT NULL,
  "evento" char(255) COLLATE utf8_unicode_ci NOT NULL,
  "fecha" char(50) COLLATE utf8_unicode_ci NOT NULL
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "<nombre>logger"
--

LOCK TABLES "<nombre>logger" WRITE;
/*!40000 ALTER TABLE "<nombre>logger" DISABLE KEYS */;
/*!40000 ALTER TABLE "<nombre>logger" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "<nombre>pagina"
--

DROP TABLE IF EXISTS "<nombre>pagina";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "<nombre>pagina" (
  "id_pagina" int(5) NOT NULL,
  "nombre" char(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  "descripcion" char(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  "modulo" char(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  "nivel" int(2) NOT NULL DEFAULT '0',
  "parametro" char(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY ("id_pagina"),
  UNIQUE KEY "id_pagina" ("id_pagina")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "<nombre>pagina"
--

LOCK TABLES "<nombre>pagina" WRITE;
/*!40000 ALTER TABLE "<nombre>pagina" DISABLE KEYS */;
/*!40000 ALTER TABLE "<nombre>pagina" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "<nombre>registrado"
--

DROP TABLE IF EXISTS "<nombre>registrado";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "<nombre>registrado" (
  "id_usuario" int(4) NOT NULL,
  "nombre" varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  "apellido" varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  "correo" varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  "telefono" varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  "imagen" char(255) COLLATE utf8_unicode_ci NOT NULL,
  "clave" varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  "tipo" int(2) NOT NULL DEFAULT '0',
  "estilo" varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'basico',
  "idioma" varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'es_es',
  "estado" int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY ("id_usuario"),
  KEY "id_usuario" ("id_usuario")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "<nombre>registrado"
--

LOCK TABLES "<nombre>registrado" WRITE;
/*!40000 ALTER TABLE "<nombre>registrado" DISABLE KEYS */;
/*!40000 ALTER TABLE "<nombre>registrado" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "<nombre>registrado_subsistema"
--

DROP TABLE IF EXISTS "<nombre>registrado_subsistema";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "<nombre>registrado_subsistema" (
  "id_usuario" int(6) NOT NULL DEFAULT '0',
  "id_subsistema" int(6) NOT NULL DEFAULT '0',
  "estado" int(2) NOT NULL DEFAULT '0'
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "<nombre>registrado_subsistema"
--

LOCK TABLES "<nombre>registrado_subsistema" WRITE;
/*!40000 ALTER TABLE "<nombre>registrado_subsistema" DISABLE KEYS */;
/*!40000 ALTER TABLE "<nombre>registrado_subsistema" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "<nombre>subsistema"
--

DROP TABLE IF EXISTS "<nombre>subsistema";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "<nombre>subsistema" (
  "id_subsistema" int(7) NOT NULL,
  "nombre" varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  "etiqueta" varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  "id_pagina" int(7) NOT NULL DEFAULT '0',
  "observacion" text COLLATE utf8_unicode_ci,
  PRIMARY KEY ("id_subsistema")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "<nombre>subsistema"
--

LOCK TABLES "<nombre>subsistema" WRITE;
/*!40000 ALTER TABLE "<nombre>subsistema" DISABLE KEYS */;
/*!40000 ALTER TABLE "<nombre>subsistema" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "<nombre>tempFormulario"
--

DROP TABLE IF EXISTS "<nombre>tempFormulario";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "<nombre>tempFormulario" (
  "id_sesion" char(32) COLLATE utf8_unicode_ci NOT NULL,
  "formulario" char(100) COLLATE utf8_unicode_ci NOT NULL,
  "campo" char(100) COLLATE utf8_unicode_ci NOT NULL,
  "valor" text COLLATE utf8_unicode_ci NOT NULL,
  "fecha" char(50) COLLATE utf8_unicode_ci NOT NULL,
  KEY "id_sesion" ("id_sesion")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "<nombre>tempFormulario"
--

LOCK TABLES "<nombre>tempFormulario" WRITE;
/*!40000 ALTER TABLE "<nombre>tempFormulario" DISABLE KEYS */;
/*!40000 ALTER TABLE "<nombre>tempFormulario" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "<nombre>valor_sesion"
--

DROP TABLE IF EXISTS "<nombre>valor_sesion";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "<nombre>valor_sesion" (
  "id_sesion" char(32) COLLATE utf8_unicode_ci NOT NULL,
  "variable" char(20) COLLATE utf8_unicode_ci NOT NULL,
  "valor" char(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY ("id_sesion","variable")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "<nombre>valor_sesion"
--

LOCK TABLES "<nombre>valor_sesion" WRITE;
/*!40000 ALTER TABLE "<nombre>valor_sesion" DISABLE KEYS */;
/*!40000 ALTER TABLE "<nombre>valor_sesion" ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-11-27 23:34:33
