-- Páginas
INSERT INTO <nombre>pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (-1,'development', 'Index módulo de desarrollo.', 'development', 0 ,'jquery=true&jquery-ui=true&jquery-validation=true' );
INSERT INTO <nombre>pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (-2,'cruder', 'Generador módulos CRUD.', 'development', 0 ,'jquery=true&jquery-ui=true&jquery-validation=true' );
INSERT INTO <nombre>pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (-3, 'desenlace', 'Analizar enlaces.', 'development', 0 ,'jquery=true&jquery-ui=true&jquery-validation=true' );
INSERT INTO <nombre>pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (-4, 'codificador', 'Codificar/decodificar cadenas.', 'development', 0 ,'jquery=true&jquery-ui=true&jquery-validation=true' );
INSERT INTO <nombre>pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (-5, 'registro', 'Registrar páginas o módulos.', 'development', 0 ,'jquery=true&jquery-ui=true&jquery-validation=true' );
INSERT INTO <nombre>pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (-6, 'constructor', 'Diseñar páginas.', 'development', 0 ,'jquery=true&jquery-ui=true&jquery-validation=true' );
INSERT INTO <nombre>pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (-7, 'plugin', 'Agregar plugin preconfigurados.', 'development', 0 ,'jquery=true&jquery-ui=true&jquery-validation=true' );
INSERT INTO <nombre>pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (-8, 'saraFormCreator', 'Módulo SARA form creator.', 'development', 0 ,'jquery=true&jquery-ui=true&jquery-validation=true' );
INSERT INTO <nombre>pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (-9, 'formatearSQL', 'Módulo Formatear/Desformatear SQL.', 'development', 0 ,'jquery=true&jquery-ui=true&jquery-validation=true' );
-- Bloques
INSERT INTO <nombre>bloque (id_bloque, nombre, descripcion, grupo) VALUES (-1,'menuLateral', 'Menú lateral módulo de desarrollo.', 'development');
INSERT INTO <nombre>bloque (id_bloque, nombre, descripcion, grupo) VALUES (-2,'pie', 'Pie de página módulo de desarrollo.', 'development');
INSERT INTO <nombre>bloque (id_bloque, nombre, descripcion, grupo) VALUES (-3,'banner', 'Banner módulo de desarrollo.', 'development');
INSERT INTO <nombre>bloque (id_bloque, nombre, descripcion, grupo) VALUES (-4,'cruder', 'Módulo para crear módulos CRUD.', 'development');
INSERT INTO <nombre>bloque (id_bloque, nombre, descripcion, grupo) VALUES (-5,'desenlace', 'Módulo de gestión de desenlace.', 'development');
INSERT INTO <nombre>bloque (id_bloque, nombre, descripcion, grupo) VALUES (-6,'registro', 'Módulo para registrar páginas o módulos.', 'development');
INSERT INTO <nombre>bloque (id_bloque, nombre, descripcion, grupo) VALUES (-7,'constructor', 'Módulo para diseñar páginas.', 'development');
INSERT INTO <nombre>bloque (id_bloque, nombre, descripcion, grupo) VALUES (-8,'contenidoCentral', 'Contenido página principal de desarrollo.', 'development');
INSERT INTO <nombre>bloque (id_bloque, nombre, descripcion, grupo) VALUES (-9,'codificador', 'Módulo para decodificar cadenas.', 'development');
INSERT INTO <nombre>bloque (id_bloque, nombre, descripcion, grupo) VALUES (-10,'plugin', 'Módulo para agregar plugin preconfigurados.', 'development');
INSERT INTO <nombre>bloque (id_bloque, nombre, descripcion, grupo) VALUES (-11,'saraFormCreator', 'Módulo para crear formulario con la recomendación de bloques de SARA.', 'development');
INSERT INTO <nombre>bloque (id_bloque, nombre, descripcion, grupo) VALUES (-12,'formatearSQL', 'Módulo para formatear cadenas SQL para el archivo SQL.class.php recomendado en SARA.', 'development');

-- Estructura

INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-1, -1, 'B', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-1, -2, 'E', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-1, -3, 'A', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-1, -8, 'C', 1 );

INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-2, -1, 'B', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-2, -2, 'E', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-2, -3, 'A', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-2, -4, 'C', 1 );

INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-3, -1, 'B', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-3, -2, 'E', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-3, -3, 'A', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-3, -5, 'C', 1 );

INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-4, -1, 'B', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-4, -2, 'E', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-4, -3, 'A', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-4, -9, 'C', 1 );

INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-5, -1, 'B', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-5, -2, 'E', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-5, -3, 'A', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-5, -6, 'C', 1 );

INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-6, -1, 'B', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-6, -2, 'E', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-6, -3, 'A', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-6, -7, 'C', 1 );

INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-7, -1, 'B', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-7, -2, 'E', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-7, -3, 'A', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-7, -10, 'C', 1 );
--Begin SARA form creator
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-8, -1, 'B', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-8, -2, 'E', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-8, -3, 'A', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-8, -11, 'C', 1 );
--End SARA form creator
--Begin formatearSQL
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-9, -1, 'B', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-9, -2, 'E', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-9, -3, 'A', 1 );
INSERT INTO <nombre>bloque_pagina (id_pagina, id_bloque, seccion, posicion) VALUES (-9, -12, 'C', 1 );
--End formatearSQL







