<?php

namespace registroBeneficiario\formulario\conformacionHogar;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class Formulario {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miSql;
	function __construct($lenguaje, $formulario, $sql) {
		$a = 0;
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		
		$this->lenguaje = $lenguaje;
		
		$this->miFormulario = $formulario;
		
		$this->miSql = $sql;
	}
	function formulario() {
		
		/**
		 * IMPORTANTE: Este formulario está utilizando jquery.
		 * Por tanto en el archivo script/ready.php y script/ready.js se declaran
		 * algunas funciones js que lo complementan.
		 */
		$conexion = "estructura";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		// Rescatar los datos de este bloque
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$rutaBloque .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/";
		$rutaBloque .= $esteBloque ['grupo'] . '/' . $esteBloque ['nombre'];
		
		// ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
		/**
		 * Atributos que deben ser aplicados a todos los controles de este formulario.
		 * Se utiliza un arreglo independiente debido a que los atributos individuales se reinician cada vez que se
		 * declara un campo.
		 *
		 * Si se utiliza esta técnica es necesario realizar un mezcla entre este arreglo y el específico en cada control:
		 * $atributos= array_merge($atributos,$atributosGlobales);
		 */
		
		$atributosGlobales ['campoSeguro'] = 'true';
		
		if (! isset ( $_REQUEST ['tiempo'] )) {
			$_REQUEST ['tiempo'] = time ();
		}
		
		// -------------------------------------------------------------------------------------------------
		
		// ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
		$esteCampo = $esteBloque ['nombre'];
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
		$tab = 1;
		// ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
		
		// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
		// $atributos ['tipoEtiqueta'] = 'inicio';
		// echo $this->miFormulario->formularioBootstrap ( $atributos );
		// unset($atributos);
		
		// ---------------- SECCION: Controles del Formulario -----------------------------------------------
		
		if (isset ( $_REQUEST ['mensaje'] )) {
			$esteCampo = 'mensajemodal';
			$atributos ["id"] = $esteCampo; // No cambiar este nombre
			$atributos ["tipo"] = "hidden";
			$atributos ['estilo'] = '';
			$atributos ["obligatorio"] = false;
			$atributos ['marco'] = true;
			$atributos ["etiqueta"] = "";
			$atributos ['valor'] = $_REQUEST ['mensaje'];
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTexto ( $atributos );
			unset ( $atributos );
		}
		
		$cargarFamiliar = array ();
		
		$esteCampo = 'hogar';
		$atributos ['id'] = $esteCampo;
		$atributos ['leyenda'] = "Conformación de Hogar";
		echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
		unset ( $atributos );
		
		if (isset ( $_REQUEST ['id'] )) {
			$cadena_sql = $this->miSql->getCadenaSql ( "cargarFamiliares", $_REQUEST ['id'] );
			$datos = $esteRecursoDB->ejecutarAcceso ( $cadena_sql, "busqueda" );
			if ($datos) {
				$fam = count ( $datos );
			} else {
				$fam = 0;
			}
		} else {
			$datos [0] ['tipo_documento_familiar'] = '-1';
			$datos [0] ['identificacion_familiar'] = '';
			$datos [0] ['nombre_familiar'] = '';
			$datos [0] ['primer_apellido_familiar'] = '';
			$datos [0] ['segundo_apellido_familiar'] = '';
			$datos [0] ['parentesco'] = '-1';
			$datos [0] ['genero_familiar'] = '-1';
			$datos [0] ['edad_familiar'] = '';
			$datos [0] ['celular_familiar'] = '';
			$datos [0] ['nivel_estudio_familiar'] = '-1';
			$datos [0] ['correo_familiar'] = '';
			$datos [0] ['grado_estudio_familiar'] = '';
			$datos [0] ['pertenencia_etnica_familiar'] = '-1';
			$datos [0] ['institucion_educativa_familiar'] = '';
			$datos [0] ['ocupacion_familiar'] = '-1';
			$fam = 1;
		}
		
		// ----------------INICIO CONTROL: Campo Oculto Cantidad d Familiares-------------------------------------------------------
		
		$esteCampo = 'familiares';
		$atributos ["id"] = $esteCampo; // No cambiar este nombre
		$atributos ["tipo"] = "hidden";
		$atributos ['valor'] = $fam;
		$atributos ['estilo'] = '';
		$atributos ["obligatorio"] = false;
		$atributos ['marco'] = true;
		$atributos ["etiqueta"] = "";
		
		$atributos = array_merge ( $atributos, $atributosGlobales );
		echo $this->miFormulario->campoCuadroTexto ( $atributos );
		unset ( $atributos );
		
		// ----------------FIN CONTROL: Campo Oculto Cantidad d Familiares--------------------------------------------------------
		
		for($i = 0; $i < count ( $datos ); $i++) {
			
			echo '<div class="panel-group" id="' . 'div_' . ($i + 1) . '">
    				<div class="panel panel-default">
      					<div class="panel-heading">
        					<h4 class="panel-title">
          						<a data-toggle="collapse" data-parent="#accordion" href="#familiar' . ($i + 1) . '">Familiar</a>
        					</h4>
      					</div>
      					<div id="familiar' . ($i + 1) . '" class="panel-collapse collapse">
       						<div class="panel-body">';
			
			$cargueDatos = array ();
			
			$cargueDatos ['tipo_documento_familiar' . '_' . $i] = $datos [$i] ['tipo_documento_familiar'];
			$cargueDatos ['identificacion_familiar' . '_' . $i] = $datos [$i] ['identificacion_familiar'];
			$cargueDatos ['nombre_familiar' . '_' . $i] = $datos [$i] ['nombre_familiar'];
			$cargueDatos ['primer_apellido_familiar' . '_' . $i] = $datos [$i] ['primer_apellido_familiar'];
			$cargueDatos ['segundo_apellido_familiar' . '_' . $i] = $datos [$i] ['segundo_apellido_familiar'];
			$cargueDatos ['parentesco' . '_' . $i] = $datos [$i] ['parentesco'];
			$cargueDatos ['genero_familiar' . '_' . $i] = $datos [$i] ['genero_familiar'];
			$cargueDatos ['edad_familiar' . '_' . $i] = $datos [$i] ['edad_familiar'];
			$cargueDatos ['celular_familiar' . '_' . $i] = $datos [$i] ['celular_familiar'];
			$cargueDatos ['nivel_estudio_familiar' . '_' . $i] = $datos [$i] ['nivel_estudio_familiar'];
			$cargueDatos ['correo_familiar' . '_' . $i] = $datos [$i] ['correo_familiar'];
			$cargueDatos ['grado_estudio_familiar' . '_' . $i] = $datos [$i] ['grado_estudio_familiar'];
			$cargueDatos ['pertenencia_etnica_familiar' . '_' . $i] = $datos [$i] ['pertenencia_etnica_familiar'];
			$cargueDatos ['institucion_educativa_familiar' . '_' . $i] = $datos [$i] ['institucion_educativa_familiar'];
			$cargueDatos ['ocupacion_familiar' . '_' . $i] = $datos [$i] ['ocupacion_familiar'];
			
			// ----------------INICIO CONTROL: Lista Tipo de Documento de Identidad--------------------------------------------------------
			
			$esteCampo = 'tipo_documento_familiar_' . $i;
			$esteCampoEtiqueta = 'tipo_documento_familiar';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampoEtiqueta );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 2;
			$atributos ['evento'] = '';
			$atributos ['seleccion'] = - 1;
			$atributos ['deshabilitado'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 10;
			$atributos ['miEvento'] = '';
			//$atributos['validar'] = 'required';
			$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "parametroTipoDocumento" );
			$matrizItems = array (
					array (
							0,
							' ' 
					) 
			);
			$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
			$atributos ['matrizItems'] = $matrizItems;
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['seleccion'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['seleccion'] = - 1;
			}
			
			// Aplica atributos globales al control
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroListaBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Lista Tipo de Documento de Identidad Beneficiario--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Identificación del Beneficiario--------------------------------------------------------
			
			$esteCampo = 'identificacion_familiar_' . $i;
			$esteCampoEtiqueta = 'identificacion_familiar';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampoEtiqueta );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 2;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = false;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 10;
			$atributos ['miEvento'] = '';
			// $atributos ['validar'] = 'required';
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			// Aplica atributos globales al control
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Texto Identificación del Beneficiario--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Identificación del Beneficiario--------------------------------------------------------
			
			$esteCampo = 'nombre_familiar_' . $i;
			$esteCampoEtiqueta = 'nombre_familiar';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampoEtiqueta );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 2;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = false;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 10;
			$atributos ['miEvento'] = '';
			// $atributos ['validar'] = 'required';
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			// Aplica atributos globales al control
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Texto Nombre Completo Beneficiario--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Identificación del Beneficiario--------------------------------------------------------
			
			$esteCampo = 'primer_apellido_familiar_' . $i;
			$esteCampoEtiqueta = 'primer_apellido_familiar';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampoEtiqueta );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 2;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = false;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 10;
			$atributos ['miEvento'] = '';
			// $atributos['validar'] = 'required';
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Texto Nombre Completo Beneficiario--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Identificación del Beneficiario--------------------------------------------------------
			
			$esteCampo = 'segundo_apellido_familiar_' . $i;
			$esteCampoEtiqueta = 'segundo_apellido_familiar';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampoEtiqueta );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 2;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = false;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 10;
			$atributos ['miEvento'] = '';
			// $atributos['validar'] = 'required';
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Texto Nombre Completo Beneficiario--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Lista Genero del Beneficiario--------------------------------------------------------
			
			$esteCampo = 'parentesco_' . $i;
			$esteCampoEtiqueta = 'parentesco';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampoEtiqueta );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 2;
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 10;
			$atributos ['miEvento'] = '';
			// $atributos ['validar'] = 'required';
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['seleccion'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['seleccion'] = - 1;
			}
			
			$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "parametroParentesco" );
			$matrizItems = array (
					array (
							0,
							' ' 
					) 
			);
			$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
			$atributos ['matrizItems'] = $matrizItems;
			
			// Aplica atributos globales al control
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroListaBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Lista Tipo de Beneficiario--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Lista Genero del Beneficiario--------------------------------------------------------
			
			$esteCampo = 'genero_familiar_' . $i;
			$esteCampoEtiqueta = 'genero_familiar';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampoEtiqueta );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 2;
			$atributos ['evento'] = '';
			$atributos ['seleccion'] = - 1;
			$atributos ['deshabilitado'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 10;
			$atributos ['miEvento'] = '';
			// //$atributos ['validar'] = 'required';
			$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "parametroGenero" );
			$matrizItems = array (
					array (
							0,
							' ' 
					) 
			);
			$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
			$atributos ['matrizItems'] = $matrizItems;
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['seleccion'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['seleccion'] = - 1;
			}
			
			// Aplica atributos globales al control
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroListaBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Lista Tipo de Beneficiario--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Edad del Beneficiario--------------------------------------------------------
			
			$esteCampo = 'edad_familiar_' . $i;
			$esteCampoEtiqueta = 'edad_familiar';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "number";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampoEtiqueta );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 2;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = false;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 10;
			$atributos ['minimo'] = 0;
			$atributos ['miEvento'] = '';
			// //$atributos ['validar'] = 'required';
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			// Aplica atributos globales al control
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Texto Edad del Beneficiario--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Celular--------------------------------------------------------
			
			$esteCampo = 'celular_familiar_' . $i;
			$esteCampoEtiqueta = 'celular_familiar';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampoEtiqueta );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 2;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = false;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 10;
			$atributos ['miEvento'] = '';
			// $atributos['validar'] = 'required';
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Texto Celular-------------------------------------------------------
			
			// ----------------INICIO CONTROL: Lista Nivel de Estudio--------------------------------------------------------
			
			$esteCampo = 'nivel_estudio_familiar_' . $i;
			$esteCampoEtiqueta = 'nivel_estudio_familiar';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampoEtiqueta );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 2;
			$atributos ['evento'] = '';
			$atributos ['seleccion'] = - 1;
			$atributos ['deshabilitado'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 10;
			$atributos ['miEvento'] = '';
			// //$atributos ['validar'] = 'required';
			$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "parametroNivelEstudio" );
			$matrizItems = array (
					array (
							0,
							' ' 
					) 
			);
			$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
			$atributos ['matrizItems'] = $matrizItems;
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['seleccion'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['seleccion'] = - 1;
			}
			
			// Aplica atributos globales al control
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroListaBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Lista Nivel de Estudio--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Correo Electrónico--------------------------------------------------------
			
			$esteCampo = 'correo_familiar_' . $i;
			$esteCampoEtiqueta = 'correo_familiar';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "mail";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampoEtiqueta );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 2;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = false;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 10;
			$atributos ['miEvento'] = '';
			// //$atributos ['validar'] = 'required';
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			// Aplica atributos globales al control
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Texto Correo Electrónico-------------------------------------------------------
			
			// ----------------INICIO CONTROL: Lista Grado--------------------------------------------------------
			
			$esteCampo = 'grado_familiar_' . $i;
			$esteCampoEtiqueta = 'grado_familiar';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampoEtiqueta );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 2;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = false;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 10;
			$atributos ['miEvento'] = '';
			// //$atributos ['validar'] = 'required';
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			// Aplica atributos globales al control
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Lista Grado--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Nombre de Institución Educativa--------------------------------------------------------
			
			$esteCampo = 'institucion_educativa_familiar_' . $i;
			$esteCampoEtiqueta = 'institucion_educativa_familiar';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampoEtiqueta );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 2;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = false;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 10;
			$atributos ['miEvento'] = '';
			// //$atributos ['validar'] = 'required';
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			// Aplica atributos globales al control
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Nombre de Institución Educativa-------------------------------------------------------
			
			// ----------------INICIO CONTROL: Lista Pertenencia Étnica--------------------------------------------------------
			
			$esteCampo = 'pertenencia_etnica_familiar_' . $i;
			$esteCampoEtiqueta = 'pertenencia_etnica_familiar';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampoEtiqueta );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 2;
			$atributos ['evento'] = '';
			$atributos ['seleccion'] = - 1;
			$atributos ['deshabilitado'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 10;
			$atributos ['miEvento'] = '';
			// //$atributos ['validar'] = 'required';
			$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "parametroPertenenciaEtnica" );
			$matrizItems = array (
					array (
							0,
							' ' 
					) 
			);
			$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
			$atributos ['matrizItems'] = $matrizItems;
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['seleccion'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['seleccion'] = - 1;
			}
			
			// Aplica atributos globales al control
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroListaBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Lista Pertenencia Étnica--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Lista Ocupación--------------------------------------------------------
			
			$esteCampo = 'ocupacion_familiar_' . $i;
			$esteCampoEtiqueta = 'ocupacion_familiar';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampoEtiqueta );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 2;
			$atributos ['evento'] = '';
			$atributos ['seleccion'] = - 1;
			$atributos ['deshabilitado'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 10;
			$atributos ['miEvento'] = '';
			// //$atributos ['validar'] = 'required';
			$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "parametroOcupacion" );
			$matrizItems = array (
					array (
							0,
							' ' 
					) 
			);
			$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
			$atributos ['matrizItems'] = $matrizItems;
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['seleccion'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['seleccion'] = - 1;
			}
			
			// Aplica atributos globales al control
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroListaBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Lista Ocupación--------------------------------------------------------
			
			echo '</div>
									</div>
									</div>
						    		</div>';
		}
		
		echo '<img src="' . $rutaBloque . "/imagenes/add_list_256_modificado.png" . '" id="botonEliminar" alt="Eliminar" style="width:30px;height:30px;">';
		echo '<img src="' . $rutaBloque . "/imagenes/add_list_256.png" . '" id="botonAgregar" alt="Agregar" style="width:30px;height:30px;">';
		echo '<p style="clear: right;"></p>';
		
		// ------------------Division para los botones-------------------------
		$atributos ["id"] = "botones";
		$atributos ["estilo"] = "marcoBotones";
		echo $this->miFormulario->division ( "inicio", $atributos );
		unset ( $atributos );
		
		// -----------------CONTROL: Botón ----------------------------------------------------------------
		$esteCampo = 'botonContinuar';
		$atributos ["id"] = $esteCampo;
		$atributos ["tabIndex"] = $tab;
		$atributos ["tipo"] = 'boton';
		// submit: no se coloca si se desea un tipo button genérico
		$atributos ['submit'] = true;
		$atributos ["basico"] = true;
		$atributos ["estiloMarco"] = '';
		$atributos ["estiloBoton"] = 'primary';
		$atributos ["block"] = false;
		// verificar: true para verificar el formulario antes de pasarlo al servidor.
		$atributos ["verificar"] = '';
		$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
		$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
		$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
		$tab ++;
		
		// Aplica atributos globales al control
		$atributos = array_merge ( $atributos, $atributosGlobales );
		echo $this->miFormulario->campoBotonBootstrapHtml ( $atributos );
		unset ( $atributos );
		// -----------------FIN CONTROL: Botón -----------------------------------------------------------
		
		// ------------------Fin Division para los botones-------------------------
		echo $this->miFormulario->division ( "fin" );
		
		// ------------------- SECCION: Paso de variables ------------------------------------------------
		
		echo $this->miFormulario->agrupacion ( 'fin' );
		unset ( $atributos );
		
		/**
		 * En algunas ocasiones es útil pasar variables entre las diferentes páginas.
		 * SARA permite realizar esto a través de tres
		 * mecanismos:
		 * (a). Registrando las variables como variables de sesión. Estarán disponibles durante toda la sesión de usuario. Requiere acceso a
		 * la base de datos.
		 * (b). Incluirlas de manera codificada como campos de los formularios. Para ello se utiliza un campo especial denominado
		 * formsara, cuyo valor será una cadena codificada que contiene las variables.
		 * (c) a través de campos ocultos en los formularios. (deprecated)
		 */
		
		// En este formulario se utiliza el mecanismo (b) para pasar las siguientes variables:
		
		// Paso 1: crear el listado de variables
		
		$valorCodificado = "action=" . $esteBloque ["nombre"];
		$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
		$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
		$valorCodificado .= "&opcion=registrarConsumo";
		/**
		 * SARA permite que los nombres de los campos sean dinámicos.
		 * Para ello utiliza la hora en que es creado el formulario para
		 * codificar el nombre de cada campo.
		 */
		$valorCodificado .= "&campoSeguro=" . $_REQUEST ['tiempo'];
		// Paso 2: codificar la cadena resultante
		$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
		
		$atributos ["id"] = "formSaraData"; // No cambiar este nombre
		$atributos ["tipo"] = "hidden";
		$atributos ['estilo'] = '';
		$atributos ["obligatorio"] = false;
		$atributos ['marco'] = true;
		$atributos ["etiqueta"] = "";
		$atributos ["valor"] = $valorCodificado;
		echo $this->miFormulario->campoCuadroTexto ( $atributos );
		unset ( $atributos );
		
		// ----------------FIN SECCION: Paso de variables -------------------------------------------------
		
		// ---------------- FIN SECCION: Controles del Formulario -------------------------------------------
		
		// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
		// Se debe declarar el mismo atributo de marco con que se inició el formulario.
		
		// $atributos ['marco'] = true;
		// $atributos ['tipoEtiqueta'] = 'fin';
		// echo $this->miFormulario->formulario ( $atributos );
		
		// -------------FIN CONTROL: Imagen Agregar Estudiante----------------------
	}
	function mensaje() {
		
		// Si existe algun tipo de error en el login aparece el siguiente mensaje
		$mensaje = $this->miConfigurador->getVariableConfiguracion ( 'mostrarMensaje' );
		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', null );
		
		if ($mensaje) {
			
			$tipoMensaje = $this->miConfigurador->getVariableConfiguracion ( 'tipoMensaje' );
			
			if ($tipoMensaje == 'json') {
				
				$atributos ['mensaje'] = $mensaje;
				$atributos ['json'] = true;
			} else {
				$atributos ['mensaje'] = $this->lenguaje->getCadena ( $mensaje );
			}
			// -------------Control texto-----------------------
			$esteCampo = 'divMensaje';
			$atributos ['id'] = $esteCampo;
			$atributos ["tamanno"] = '';
			$atributos ["estilo"] = 'information';
			$atributos ["etiqueta"] = '';
			$atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
			echo $this->miFormulario->campoMensaje ( $atributos );
			unset ( $atributos );
			
			return true;
		}
		function elementosAdicionales() {
		}
		
		return true;
	}
}

$miFormulario = new Formulario ( $this->lenguaje, $this->miFormulario, $this->sql );

$miFormulario->formulario ();
$miFormulario->mensaje ();

?>