--
-- Host: localhost    Database: saraEstructura
-- ------------------------------------------------------

--
-- Table structure for table <nombre>bloque
--

DROP TABLE IF EXISTS <nombre>bloque;


CREATE TABLE <nombre>bloque (
  id_bloque SERIAL,
  nombre char(50)  NOT NULL,
  descripcion char(255)  DEFAULT NULL,
  grupo char(200)  NOT NULL,
  PRIMARY KEY (id_bloque)
);


--
-- Table structure for table <nombre>bloque_pagina
--

DROP TABLE IF EXISTS <nombre>bloque_pagina;


CREATE TABLE <nombre>bloque_pagina (
  idrelacion SERIAL,	
  id_pagina integer NOT NULL DEFAULT '0',
  id_bloque integer NOT NULL DEFAULT '0',
  seccion char(1)  NOT NULL,
  posicion integer NOT NULL DEFAULT '0', 
  PRIMARY KEY (idrelacion)
);


--
-- Table structure for table <nombre>configuracion
--

DROP TABLE IF EXISTS <nombre>configuracion;


CREATE TABLE <nombre>configuracion (
  id_parametro  SERIAL,
  parametro char(255)  NOT NULL,
  valor char(255)  NOT NULL,
  PRIMARY KEY (id_parametro)
);


--

--


--
-- Table structure for table <nombre>dbms
--

DROP TABLE IF EXISTS <nombre>dbms;


CREATE TABLE <nombre>dbms (

  idconexion SERIAL,
  nombre varchar(50)  NOT NULL,
  dbms varchar(20)  NOT NULL,
  servidor varchar(50)  NOT NULL,
  puerto integer NOT NULL,
  conexionssh varchar(50)  NOT NULL,
  db varchar(100)  NOT NULL,
  esquema varchar(100)  NOT NULL,
  usuario varchar(100)  NOT NULL,
  password varchar(200)  NOT NULL,
  PRIMARY KEY (idconexion)
);


--
-- Dumping data for table <nombre>dbms
--

--
-- Table structure for table <nombre>estilo
--

DROP TABLE IF EXISTS <nombre>estilo;


CREATE TABLE <nombre>estilo (
  usuario char(50)  NOT NULL DEFAULT '0',
  estilo char(50)  NOT NULL,
  PRIMARY KEY (usuario,estilo)
);




--
-- Table structure for table <nombre>logger
--

DROP TABLE IF EXISTS <nombre>logger;


CREATE TABLE <nombre>logger (
  id SERIAL,
  evento char(255)  NOT NULL,
  fecha char(50)  NOT NULL
);




--
-- Table structure for table <nombre>pagina
--

DROP TABLE IF EXISTS <nombre>pagina;


CREATE TABLE <nombre>pagina (
  id_pagina  SERIAL,
  nombre char(50)  NOT NULL DEFAULT '',
  descripcion char(250)  NOT NULL DEFAULT '',
  modulo char(50)  NOT NULL DEFAULT '',
  nivel integer NOT NULL DEFAULT '0',
  parametro char(255)  NOT NULL,
  PRIMARY KEY (id_pagina)
);




--
-- Table structure for table <nombre>registrado
--

DROP TABLE IF EXISTS <nombre>usuario;


CREATE TABLE <nombre>usuario (
  id_usuario  SERIAL,
  nombre varchar(50)  NOT NULL DEFAULT '',
  apellido varchar(50)  NOT NULL DEFAULT '',
  correo varchar(100)  NOT NULL DEFAULT '',
  telefono varchar(50)  NOT NULL DEFAULT '',
  imagen char(255)  NOT NULL,
  clave varchar(100)  NOT NULL DEFAULT '',
  tipo varchar(255)  NOT NULL DEFAULT '',
  estilo varchar(50)  NOT NULL DEFAULT 'basico',
  idioma varchar(50)  NOT NULL DEFAULT 'es_es',
  estado integer NOT NULL DEFAULT '0',
  PRIMARY KEY (id_usuario)
);

--
-- Table structure for table <nombre>registrado_subsistema
--

DROP TABLE IF EXISTS <nombre>usuario_subsistema;


CREATE TABLE <nombre>usuario_subsistema (
  id_usuario integer NOT NULL DEFAULT '0',
  id_subsistema integer NOT NULL DEFAULT '0',
  estado integer NOT NULL DEFAULT '0'
);


--
-- Table structure for table <nombre>subsistema
--

DROP TABLE IF EXISTS <nombre>subsistema;


CREATE TABLE <nombre>subsistema (
  id_subsistema  SERIAL,
  nombre varchar(250)  NOT NULL,
  etiqueta varchar(100)  NOT NULL,
  id_pagina integer NOT NULL DEFAULT '0',
  observacion text ,
  PRIMARY KEY (id_subsistema)
);


--
-- Dumping data for table <nombre>subsistema
--


--
-- Table structure for table <nombre>tempFormulario
--

DROP TABLE IF EXISTS <nombre>tempFormulario;


CREATE TABLE <nombre>tempFormulario (
  id_sesion char(32)  NOT NULL,
  formulario char(100)  NOT NULL,
  campo char(100)  NOT NULL,
  valor text  NOT NULL,
  fecha char(50)  NOT NULL
);



--
-- Table structure for table <nombre>valor_sesion
--

DROP TABLE IF EXISTS <nombre>valor_sesion;


CREATE TABLE <nombre>valor_sesion (
    sesionId character(32) NOT NULL,
    variable character(20) NOT NULL,
    valor character(255) NOT NULL,
    expiracion bigInt DEFAULT 0 NOT NULL
);

ALTER TABLE ONLY <nombre>valor_sesion ADD CONSTRAINT <nombre>valor_sesion_pkey PRIMARY KEY (sesionId, variable);


