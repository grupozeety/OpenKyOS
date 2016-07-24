<?php
/**
 * IMPORTANTE: Este formulario está utilizando jquery.
 * Por tanto en el archivo ready.php se delaran algunas funciones js
 * que lo complementan.
 */

// Rescatar los datos de este bloque
$this->esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );

// ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
/**
 * Atributos que deben ser aplicados a todos los controles de este formulario.
 * Se utiliza un arreglo
 * independiente debido a que los atributos individuales se reinician cada vez que se declara un campo.
 *
 * Si se utiliza esta técnica es necesario realizar un mezcla entre este arreglo y el específico en cada control:
 * $atributos= array_merge($atributos,$atributosGlobales);
 */
$this->atributosGlobales ['tiempo'] = time ();
$this->atributosGlobales ['campoSeguro'] = 'true';

// -------------------------------------------------------------------------------------------------

// ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
$esteCampo = $this->esteBloque ['nombre'];
$atributos ['id'] = $esteCampo;
$atributos ['nombre'] = $esteCampo;

// Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
$atributos ['tipoFormulario'] = '';

// Si no se coloca, entonces toma el valor predeterminado 'POST'
$atributos ['metodo'] = 'POST';

// Si no se coloca, entonces toma el valor predeterminado 'index.php' (Recomendado)
$atributos ['action'] = 'index.php';
$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo );

// Si no se coloca, entonces toma el valor predeterminado.
$atributos ['estilo'] = '';
$atributos ['marco'] = true;
$this->tab = 1;
// ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------

// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
$atributos ['tipoEtiqueta'] = 'inicio';
echo $this->miFormulario->formulario ( $atributos );
?>