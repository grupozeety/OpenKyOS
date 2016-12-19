<?php

namespace registroBeneficiario\formulario\datosBasicos;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}
use agendarComisionamiento\funcion\sincronizar;
use registroBeneficiario\funcion\redireccion;

require_once 'blocks/agendarComisionamiento/funcion/sincronizar.php';
// include "funcion/redireccionar.php";
class Formulario {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miSql;
	public function __construct($lenguaje, $formulario, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		
		$this->lenguaje = $lenguaje;
		
		$this->miFormulario = $formulario;
		
		$this->miSql = $sql;
		
		$this->sincronizacion = new sincronizar ( $lenguaje, $sql, $formulario );
	}
	public function formulario() {
		
		/**
		 * IMPORTANTE: Este formulario está utilizando jquery.
		 * Por tanto en el archivo script/ready.php y script/ready.js se declaran
		 * algunas funciones js que lo complementan.
		 */
		$conexion = "estructura";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		// Rescatar los datos de este bloque
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
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
		
		$deshabilitado = true;
		
		$cadena_sql = $this->miSql->getCadenaSql ( "cargarBeneficiarioPotencial" );
		$cargueDatos = $esteRecursoDB->ejecutarAcceso ( $cadena_sql, "busqueda" )[0];
		
		if($cargueDatos){
			$_REQUEST['id_beneficiario'] = $cargueDatos['id_beneficiario'];
		}
		
		$esteCampo = 'consecutivo';
		$atributos ["id"] = $esteCampo; // No cambiar este nombre
		$atributos ["tipo"] = "hidden";
		$atributos ['valor'] = '';
		$atributos ['estilo'] = '';
		$atributos ["obligatorio"] = false;
		$atributos ['marco'] = true;
		$atributos ["etiqueta"] = "";
		
		if (isset ( $cargueDatos [$esteCampo] )) {
			$atributos ['valor'] = $cargueDatos [$esteCampo];
		} else {
			$atributos ['valor'] = '';
		}
		
		$atributos = array_merge ( $atributos, $atributosGlobales );
		echo $this->miFormulario->campoCuadroTexto ( $atributos );
		unset ( $atributos );
		
		$esteCampo = 'ficheros';
		$atributos ['id'] = $esteCampo;
		$atributos ['leyenda'] = "Información Beneficiario Potencial (Titular)";
		echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
		unset ( $atributos );
		
		echo '<div class="panel-group" id="accordion">

               <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Datos Básicos</a>
                        </h3>
                    </div>
                    <div id="collapse1" class="panel-collapse collapse">
                        <div class="panel-body">';
		
		{
			
			// ----------------INICIO CONTROL: Campo Texto Id Beneficiario--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Identificación del Beneficiario--------------------------------------------------------
			
// 			$esteCampo = 'minvi';
// 			$atributos ['nombre'] = $esteCampo;
// 			$atributos ['tipo'] = "text";
// 			$atributos ['id'] = $esteCampo;
// 			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 			$atributos ["etiquetaObligatorio"] = true;
// 			$atributos ['tab'] = $tab ++;
// 			$atributos ['anchoEtiqueta'] = 3;
// 			$atributos ['estilo'] = "bootstrap";
// 			$atributos ['evento'] = '';
// 			$atributos ['deshabilitado'] = true;
// 			$atributos ['readonly'] = true;
// 			$atributos ['columnas'] = 1;
// 			$atributos ['tamanno'] = 1;
// 			$atributos ['placeholder'] = "";
// 			$atributos ['valor'] = "";
// 			$atributos ['ajax_function'] = "";
// 			$atributos ['ajax_control'] = $esteCampo;
// 			$atributos ['limitar'] = false;
// 			$atributos ['anchoCaja'] = 9;
// 			$atributos ['miEvento'] = '';
// 			// $atributos['validar'] = 'required';
// 			// Aplica atributos globales al control
			
// 			if (isset ( $cargueDatos [$esteCampo] )) {
// 				if ($cargueDatos [$esteCampo] == 't') {
// 					$atributos ['valor'] = 'SI';
// 				} else {
// 					$atributos ['valor'] = 'NO';
// 				}
// 			} else {
// 				$atributos ['valor'] = '';
// 			}
			
// 			$atributos = array_merge ( $atributos, $atributosGlobales );
// 			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 			unset ( $atributos );
			
// 			// ----------------FIN CONTROL: Campo Texto Nombre Completo Beneficiario--------------------------------------------------------
			
// 			$esteCampo = 'id_beneficiario';
// 			$atributos ['nombre'] = $esteCampo;
// 			$atributos ['tipo'] = "text";
// 			$atributos ['id'] = $esteCampo;
// 			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 			$atributos ["etiquetaObligatorio"] = true;
// 			$atributos ['tab'] = $tab ++;
// 			$atributos ['anchoEtiqueta'] = 3;
// 			$atributos ['estilo'] = "bootstrap";
// 			$atributos ['evento'] = '';
// 			$atributos ['deshabilitado'] = $deshabilitado;
// 			$atributos ['readonly'] = true;
// 			$atributos ['columnas'] = 1;
// 			$atributos ['tamanno'] = 1;
// 			$atributos ['placeholder'] = "";
// 			$atributos ['valor'] = "";
// 			$atributos ['ajax_function'] = "";
// 			$atributos ['ajax_control'] = $esteCampo;
// 			$atributos ['limitar'] = false;
// 			$atributos ['anchoCaja'] = 9;
// 			$atributos ['miEvento'] = '';
// 			// //$atributos['validar'] = 'required';
			
// 			if (isset ( $cargueDatos [$esteCampo] )) {
// 				$atributos ['valor'] = $cargueDatos [$esteCampo];
// 			} else {
// 				$atributos ['valor'] = '';
// 			}
			
// 			// Aplica atributos globales al control
// 			$atributos = array_merge ( $atributos, $atributosGlobales );
// 			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 			unset ( $atributos );
			
// 			// ----------------FIN CONTROL: Campo Texto Id Beneficiario--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Lista Tipo de Beneficiario--------------------------------------------------------
			
			$esteCampo = 'tipo_beneficiario';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['evento'] = '';
			$atributos ['seleccion'] = - 1;
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			$atributos ['validar'] = 'required';
			$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "parametroTipoBeneficiario" );
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
			
// 			$esteCampo = 'nomenclatura';
// 			$atributos ['nombre'] = $esteCampo;
// 			$atributos ['tipo'] = "text";
// 			$atributos ['id'] = $esteCampo;
// 			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 			$atributos ["etiquetaObligatorio"] = true;
// 			$atributos ['tab'] = $tab ++;
// 			$atributos ['anchoEtiqueta'] = 3;
// 			$atributos ['estilo'] = "bootstrap";
// 			$atributos ['evento'] = '';
// 			$atributos ['deshabilitado'] = $deshabilitado;
// 			$atributos ['readonly'] = true;
// 			$atributos ['columnas'] = 1;
// 			$atributos ['tamanno'] = 1;
// 			$atributos ['placeholder'] = "";
// 			$atributos ['valor'] = "";
// 			$atributos ['ajax_function'] = "";
// 			$atributos ['ajax_control'] = $esteCampo;
// 			$atributos ['limitar'] = false;
// 			$atributos ['anchoCaja'] = 9;
// 			$atributos ['miEvento'] = '';
// 			// $atributos['validar'] = 'required';
			
// 			if (isset ( $cargueDatos [$esteCampo] )) {
// 				$atributos ['valor'] = $cargueDatos [$esteCampo];
// 			} else {
// 				$atributos ['valor'] = '';
// 			}
			
// 			// Aplica atributos globales al control
// 			$atributos = array_merge ( $atributos, $atributosGlobales );
// 			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 			unset ( $atributos );
			
			// ----------------INICIO CONTROL: Lista Tipo de Documento de Identidad--------------------------------------------------------
			
			$esteCampo = 'tipo_documento';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['evento'] = '';
			$atributos ['seleccion'] = - 1;
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			// $atributos['validar'] = 'required';
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
			
			$esteCampo = 'identificacion_beneficiario';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			$atributos ['validar'] = 'required';
			
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
			
			$esteCampo = 'nombre_beneficiario';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
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
			
			$esteCampo = 'primer_apellido';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
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
			
			$esteCampo = 'segundo_apellido';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
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
			
			$esteCampo = 'genero_beneficiario';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['evento'] = '';
			$atributos ['seleccion'] = - 1;
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			// $atributos ['validar'] = '';
			$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "parametroGenero" );
			$matrizItems = array (
					array (
							0,
							' ' 
					) 
			);
			$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
			$atributos ['matrizItems'] = $matrizItems;
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['seleccion'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['seleccion'] = - 1;
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroListaBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Lista Tipo de Beneficiario--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Edad del Beneficiario--------------------------------------------------------
			
			$esteCampo = 'edad_beneficiario';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "number";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['minimo'] = 19;
			$atributos ['miEvento'] = '';
			$atributos ['validar'] = '';
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Texto Edad del Beneficiario--------------------------------------------------------
			
		}		
		echo '</div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">Información de Ubicación</a>
                        </h3>
                    </div>
                    <div id="collapse2" class="panel-collapse collapse">
                        <div class="panel-body">';
		
		{
			// ----------------INICIO CONTROL: Campo Texto Dirección--------------------------------------------------------
			
			$esteCampo = 'direccion';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
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
			
			// ----------------FIN CONTROL: Campo Texto Dirección-------------------------------------------------------
			
			// ----------------INICIO CONTROL: Lista Tipo de Vivienda--------------------------------------------------------
			
			$esteCampo = 'tipo_vivienda';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['evento'] = '';
			$atributos ['seleccion'] = - 1;
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			// $atributos ['validar'] = '';
			$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "parametroTipoVivienda" );
			$matrizItems = array (
					array (
							0,
							' ' 
					) 
			);
			$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
			$atributos ['matrizItems'] = $matrizItems;
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['seleccion'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['seleccion'] = - 1;
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroListaBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Lista Tipo de Vivienda--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Manzana--------------------------------------------------------
			
			$esteCampo = 'manzana';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			// $atributos['validar'] = '';
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Texto Manzana-------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Interior--------------------------------------------------------
			
			$esteCampo = 'interior';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			// $atributos['validar'] = '';
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Texto Torre-------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Torre--------------------------------------------------------
			
			$esteCampo = 'torre';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			// $atributos['validar'] = '';
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Texto Torre-------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Bloque--------------------------------------------------------
			
			$esteCampo = 'bloque';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			// $atributos['validar'] = '';
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Texto Bloque-------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Apartamento--------------------------------------------------------
			
			$esteCampo = 'apartamento';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			// $atributos['validar'] = '';
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Texto Apartamento-------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Apartamento--------------------------------------------------------
			
			$esteCampo = 'lote';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			// $atributos['validar'] = '';
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Texto Apartamento-------------------------------------------------------
			
			// ----------------INICIO CONTROL: Lista Proyecto--------------------------------------------------------
			
			$esteCampo = 'urbanizacion';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['evento'] = '';
			$atributos ['seleccion'] = - 1;
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			$atributos ['validar'] = 'required';
			
			$matrizItems = array (
					array (
							0,
							' ' 
					) 
			);
			
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
			
			// ----------------INICIO CONTROL: Campo Oculto ID de Urbanización-------------------------------------------------------
			
			$esteCampo = 'id_urbanizacion';
			$atributos ["id"] = $esteCampo; // No cambiar este nombre
			$atributos ["tipo"] = "hidden";
			$atributos ['valor'] = '';
			$atributos ['estilo'] = '';
			$atributos ["obligatorio"] = false;
			$atributos ['marco'] = true;
			$atributos ["etiqueta"] = "";
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTexto ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Oculto Cantidad ID de Urbanización--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Oculto ID de Urbanización-------------------------------------------------------
			
			$esteCampo = 'select_urbanizacion';
			$atributos ["id"] = $esteCampo; // No cambiar este nombre
			$atributos ["tipo"] = "hidden";
			$atributos ['valor'] = '';
			$atributos ['estilo'] = '';
			$atributos ["obligatorio"] = false;
			$atributos ['marco'] = true;
			$atributos ["etiqueta"] = "";
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTexto ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Oculto Cantidad ID de Urbanización--------------------------------------------------------
			
			// ----------------FIN CONTROL: Lista Proyecto--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Total Nodo Hasta Punto de Ingreso--------------------------------------------------------
			
			$esteCampo = 'departamento';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = true;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
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
			
			// ----------------FIN CONTROL: Campo Texto Total Nodo Hasta Punto de Ingreso--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Total Nodo Hasta Punto de Ingreso--------------------------------------------------------
			
			$esteCampo = 'municipio';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = true;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
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
			
			// ----------------FIN CONTROL: Campo Texto Total Nodo Hasta Punto de Ingreso--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Lista Territorio--------------------------------------------------------
			
			$esteCampo = 'territorio';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['evento'] = '';
			$atributos ['seleccion'] = - 1;
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			// $atributos ['validar'] = '';
			$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "parametroTerritorio" );
			$matrizItems = array (
					array (
							0,
							' ' 
					) 
			);
			$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
			$atributos ['matrizItems'] = $matrizItems;
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['seleccion'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['seleccion'] = - 1;
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroListaBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Lista Territorio--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Lista Estrato--------------------------------------------------------
			
			$esteCampo = 'estrato';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['evento'] = '';
			$atributos ['seleccion'] = - 1;
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			// $atributos['validar'] = 'required';
			$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "parametroEstrato" );
			$matrizItems = array (
					array (
							0,
							' ' 
					) 
			);
			$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
			$atributos ['matrizItems'] = $matrizItems;
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['seleccion'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['seleccion'] = - 1;
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroListaBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Lista Estrato--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Ubicación Geográfica: Geolocalización--------------------------------------------------------
			
			$esteCampo = 'geolocalizacion';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			$atributos ['validar'] = '';
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
		}
		// ----------------FIN CONTROL: Campo Texto Ubicación Geográfica: Geolocalización-------------------------------------------------------
		
		echo '</div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">Datos de Contacto</a>
                        </h3>
                    </div>
                    <div id="collapse3" class="panel-collapse collapse">
                        <div class="panel-body">';
		{
			// ----------------INICIO CONTROL: Campo Texto Correo Electrónico--------------------------------------------------------
			
			$esteCampo = 'correo';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "mail";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
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
			
			// ----------------FIN CONTROL: Campo Texto Correo Electrónico-------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Teléfono Fijo--------------------------------------------------------
			
			$esteCampo = 'telefono';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			$atributos ['validar'] = '';
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Texto Teléfono Fijo-------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Celular--------------------------------------------------------
			
			$esteCampo = 'celular';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
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
			
			// ----------------INICIO CONTROL: Campo Texto Whatsapp--------------------------------------------------------
			
			$esteCampo = 'whatsapp';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			$atributos ['validar'] = '';
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Texto Whatsapp-------------------------------------------------------
			
			// ----------------INICIO CONTROL: Campo Texto Whatsapp--------------------------------------------------------
			
			$esteCampo = 'facebook';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['tipo'] = "text";
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['evento'] = '';
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['readonly'] = false;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['placeholder'] = "";
			$atributos ['valor'] = "";
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			$atributos ['validar'] = '';
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['valor'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['valor'] = '';
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Campo Texto Whatsapp-------------------------------------------------------
		}
		echo '</div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse4">Otros Datos</a>
                        </h3>
                    </div>
                    <div id="collapse4" class="panel-collapse collapse">
                        <div class="panel-body">';
		{
			// ----------------INICIO CONTROL: Lista Nivel de Estudio--------------------------------------------------------
			
			$esteCampo = 'nivel_estudio';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['evento'] = '';
			$atributos ['seleccion'] = - 1;
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			// $atributos ['validar'] = '';
			$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "parametroNivelEstudio" );
			$matrizItems = array (
					array (
							0,
							' ' 
					) 
			);
			$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
			$atributos ['matrizItems'] = $matrizItems;
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['seleccion'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['seleccion'] = - 1;
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroListaBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Lista Nivel de Estudio--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Lista Jefe de Hogar--------------------------------------------------------
			
			$esteCampo = 'jefe_hogar';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['evento'] = '';
			$atributos ['seleccion'] = - 1;
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			// $atributos ['validar'] = '';
			$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "parametroJefeHogar" );
			$matrizItems = array (
					array (
							0,
							' ' 
					) 
			);
			$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
			$atributos ['matrizItems'] = $matrizItems;
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['seleccion'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['seleccion'] = - 1;
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroListaBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Lista Jefe de Hogar--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Lista Pertenencia Étnica--------------------------------------------------------
			
			$esteCampo = 'pertenencia_etnica';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['evento'] = '';
			$atributos ['seleccion'] = - 1;
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			// $atributos ['validar'] = '';
			$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "parametroPertenenciaEtnica" );
			$matrizItems = array (
					array (
							0,
							' ' 
					) 
			);
			$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
			$atributos ['matrizItems'] = $matrizItems;
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['seleccion'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['seleccion'] = - 1;
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroListaBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Lista Pertenencia Étnica--------------------------------------------------------
			
			// ----------------INICIO CONTROL: Lista Ocupación--------------------------------------------------------
			
			$esteCampo = 'ocupacion';
			$atributos ['nombre'] = $esteCampo;
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = true;
			$atributos ['tab'] = $tab ++;
			$atributos ['anchoEtiqueta'] = 3;
			$atributos ['evento'] = '';
			$atributos ['seleccion'] = - 1;
			$atributos ['deshabilitado'] = $deshabilitado;
			$atributos ['columnas'] = 1;
			$atributos ['tamanno'] = 1;
			$atributos ['ajax_function'] = "";
			$atributos ['ajax_control'] = $esteCampo;
			$atributos ['estilo'] = "bootstrap";
			$atributos ['limitar'] = false;
			$atributos ['anchoCaja'] = 9;
			$atributos ['miEvento'] = '';
			// $atributos ['validar'] = '';
			$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "parametroOcupacion" );
			$matrizItems = array (
					array (
							0,
							' ' 
					) 
			);
			$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
			$atributos ['matrizItems'] = $matrizItems;
			// Aplica atributos globales al control
			
			if (isset ( $cargueDatos [$esteCampo] )) {
				$atributos ['seleccion'] = $cargueDatos [$esteCampo];
			} else {
				$atributos ['seleccion'] = - 1;
			}
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroListaBootstrap ( $atributos );
			unset ( $atributos );
			
			// ----------------FIN CONTROL: Lista Ocupación--------------------------------------------------------
		}
		echo '</div>
                </div>
            </div>';
		
		// ------------------Division para los botones-------------------------
		$atributos ["id"] = "botones";
		$atributos ["estilo"] = "marcoBotones";
		echo $this->miFormulario->division ( "inicio", $atributos );
		unset ( $atributos );
		
		// // -----------------CONTROL: Botón ----------------------------------------------------------------
		// $esteCampo = 'botonContinuar';
		// $atributos["id"] = $esteCampo;
		// $atributos["tabIndex"] = $tab;
		// $atributos["tipo"] = 'boton';
		// // submit: no se coloca si se desea un tipo button genérico
		// $atributos['submit'] = false;
		// $atributos["basico"] = true;
		// $atributos["estiloMarco"] = '';
		// $atributos["estiloBoton"] = 'primary';
		// $atributos["block"] = false;
		// // verificar: true para verificar el formulario antes de pasarlo al servidor.
		// $atributos["verificar"] = '';
		// $atributos["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
		// $atributos["valor"] = $this->lenguaje->getCadena($esteCampo);
		// $atributos['nombreFormulario'] = $esteBloque['nombre'];
		// $tab++;
		
		// // Aplica atributos globales al control
		// $atributos = array_merge($atributos, $atributosGlobales);
		// echo $this->miFormulario->campoBotonBootstrapHtml($atributos);
		// unset($atributos);
		// // -----------------FIN CONTROL: Botón -----------------------------------------------------------
		
		// ------------------Fin Division para los botones-------------------------
		echo $this->miFormulario->division ( "fin" );
		unset ( $atributos );
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
		// Paso 3: codificar la cadena resultante
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
		
		if (isset ( $_REQUEST ['mensaje'] )) {
			$this->mensaje ( $tab, $esteBloque ['nombre'] );
		}
	}
	public function mensaje($tab = '', $nombreBloque = '') {
		switch ($_REQUEST ['mensaje']) {
			
			case 'confirmaAct' :
				$atributos ['estiloLinea'] = 'success'; // success,error,information,warning
				break;
			
			case 'errorAct' :
				$atributos ['estiloLinea'] = 'error'; // success,error,information,warning
				break;
			
			case 'confirma' :
				$atributos ['estiloLinea'] = 'success'; // success,error,information,warning
				break;
			
			case 'error' :
				$atributos ['estiloLinea'] = 'error'; // success,error,information,warning
				break;
		}
		
		$mensaje = $this->lenguaje->getCadena ( $_REQUEST ['mensaje'] );
		
		// ----------------INICIO CONTROL: Ventana Modal Beneficiario Eliminado---------------------------------
		
		$atributos ['tipoEtiqueta'] = 'inicio';
		$atributos ['titulo'] = 'Mensaje';
		$atributos ['id'] = 'mensaje';
		echo $this->miFormulario->modal ( $atributos );
		unset ( $atributos );
		
		// ----------------INICIO CONTROL: Mapa--------------------------------------------------------
		echo '<div style="text-align:center;">';
		
		echo '<p><h5>' . $mensaje . '</h5></p>';
		
		echo '</div>';
		
		// ----------------FIN CONTROL: Mapa--------------------------------------------------------
		
		echo '<div style="text-align:center;">';
		
		echo '</div>';
		
		$atributos ['tipoEtiqueta'] = 'fin';
		echo $this->miFormulario->modal ( $atributos );
		unset ( $atributos );
	}
}

$miFormulario = new Formulario ( $this->lenguaje, $this->miFormulario, $this->sql );

$miFormulario->formulario ();

?>
