<?php

namespace reportes\actaEntregaServicios\frontera;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}
class Certificado {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miSql;
	public $ruta;
	public $rutaURL;
	public function __construct($lenguaje, $formulario, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		
		$this->lenguaje = $lenguaje;
		
		$this->miFormulario = $formulario;
		
		$this->miSql = $sql;
		
		$esteBloque = $this->miConfigurador->configuracion ['esteBloque'];
		
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		
		if (! isset ( $esteBloque ["grupo"] ) || $esteBloque ["grupo"] == "") {
			$ruta .= "/blocks/" . $esteBloque ["nombre"] . "/";
			$this->rutaURL .= "/blocks/" . $esteBloque ["nombre"] . "/";
		} else {
			$this->ruta .= "/blocks/" . $esteBloque ["grupo"] . "/" . $esteBloque ["nombre"] . "/";
			$this->rutaURL .= "/blocks/" . $esteBloque ["grupo"] . "/" . $esteBloque ["nombre"] . "/";
		}
	}
	public function edicionCertificado() {
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$conexion = "openproject";
		$esteRecursoOP = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$_REQUEST ['id_beneficiario'] = $_REQUEST ['id'];
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultaInformacionCertificado' );
		
		$infoCertificado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
		
		if ($infoCertificado) {
			
			$variable = 'pagina=actaEntregaServicios';
			$variable .= '&opcion=resultadoActa';
			$variable .= '&mensaje=insertoInformacionCertificado';
			$variable .= '&id_beneficiario=' . $_REQUEST ['id_beneficiario'];
			$url = $this->miConfigurador->configuracion ["host"] . $this->miConfigurador->configuracion ["site"] . "/index.php?";
			$enlace = $this->miConfigurador->configuracion ['enlace'];
			$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $variable );
			$_REQUEST [$enlace] = $enlace . '=' . $variable;
			$redireccion = $url . $_REQUEST [$enlace];
			echo "<script>location.replace('" . $redireccion . "')</script>";
			
			exit ();
		}
		// Consulta información
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultaInformacionBeneficiario' );
		$infoBeneficiario = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$infoBeneficiario = $infoBeneficiario [0];
		
		{
			$arreglo = array (
					'nombres' => $infoBeneficiario ['nombre'],
					'primer_apellido' => $infoBeneficiario ['primer_apellido'],
					'segundo_apellido' => $infoBeneficiario ['segundo_apellido'],
					'tipo_documento' => $infoBeneficiario ['tipo_documento'],
					'numero_identificacion' => $infoBeneficiario ['identificacion'],
					'direccion' => $infoBeneficiario ['direccion'],
					'departamento' => $infoBeneficiario ['nombre_departamento'],
					'municipio' => $infoBeneficiario ['nombre_municipio'],
					'urbanizacion' => $infoBeneficiario ['nombre_urbanizacion'],
					'estrato' => $infoBeneficiario ['estrato'],
					'tipo_beneficiario' => $infoBeneficiario ['tipo_beneficiario'],
			);
			
			$_REQUEST = array_merge ( $_REQUEST, $arreglo );
			
			var_dump($_REQUEST);
		}
		
		{
		
			$anexo_dir = '';
		
			if ($infoBeneficiario['manzana_contrato'] != 0) {
				$anexo_dir .= " Manzana  #" . $infoBeneficiario['manzana_contrato'] . " - ";
			}
		
			if ($infoBeneficiario['bloque_contrato'] != 0) {
				$anexo_dir .= " Bloque #" . $infoBeneficiario['bloque_contrato'] . " - ";
			}
		
			if ($infoBeneficiario['torre_contrato'] != 0) {
				$anexo_dir .= " Torre #" . $infoBeneficiario['torre_contrato'] . " - ";
			}
		
			if ($infoBeneficiario['casa_apto_contrato'] != 0) {
				$anexo_dir .= " Casa/Apartamento #" . $infoBeneficiario['casa_apto_contrato'];
			}
		
			if ($infoBeneficiario['interior_contrato'] != 0) {
				$anexo_dir .= " Interior #" . $infoBeneficiario['interior_contrato'];
			}
		
			if ($infoBeneficiario['lote_contrato'] != 0) {
				$anexo_dir .= " Lote #" . $infoBeneficiario['lote_contrato'];
			}
		
		}
		// Rescatar los datos de este bloque
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		// ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
		
		$atributosGlobales ['campoSeguro'] = 'true';
		
		$_REQUEST ['tiempo'] = time ();
		// -------------------------------------------------------------------------------------------------
		
		// ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
		$esteCampo = $esteBloque ['nombre'];
		$atributos ['id'] = $esteCampo;
		$atributos ['nombre'] = $esteCampo;
		// Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
		$atributos ['tipoFormulario'] = 'multipart/form-data';
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
		
		echo "<div class='modalLoad'></div>";
		
		// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
		$atributos ['tipoEtiqueta'] = 'inicio';
		echo $this->miFormulario->formulario ( $atributos );
		{
			
			{
				
				echo '<div class="panel-group" id="accordion">
		
		               <div class="panel panel-default">
		                    <div class="panel-heading">
		                        <h4 class="panel-title">
		                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Datos Básicos</a>
		                        </h4>
		                    </div>
		                    <div id="collapse1" class="panel-collapse collapse">
		                        <div class="panel-body">';
				
				{
					
// 					// ----------------FIN CONTROL: Dirección-----------------------
// 					$esteCampo = 'producto';
// 					$atributos ['nombre'] = $esteCampo;
// 					$atributos ['tipo'] = "text";
// 					$atributos ['id'] = $esteCampo;
// 					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 					$atributos ["etiquetaObligatorio"] = true;
// 					$atributos ['tab'] = $tab ++;
// 					$atributos ['anchoEtiqueta'] = 2;
// 					$atributos ['estilo'] = "bootstrap";
// 					$atributos ['evento'] = '';
// 					$atributos ['deshabilitado'] = false;
// 					$atributos ['readonly'] = false;
// 					$atributos ['columnas'] = 1;
// 					$atributos ['tamanno'] = 1;
// 					$atributos ['placeholder'] = "Ingrese el Producto";
// 					if (isset ( $_REQUEST [$esteCampo] )) {
// 						$atributos ['valor'] = $_REQUEST [$esteCampo];
// 					} else {
// 						$atributos ['valor'] = '';
// 					}
// 					$atributos ['ajax_function'] = "";
// 					$atributos ['ajax_control'] = $esteCampo;
// 					$atributos ['limitar'] = false;
// 					$atributos ['anchoCaja'] = 10;
// 					$atributos ['miEvento'] = '';
// 					$atributos ['validar'] = ' ';
// 					// Aplica atributos globales al control
// 					$atributos = array_merge ( $atributos, $atributosGlobales );
// 					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 					unset ( $atributos );
// 					// ----------------FIN CONTROL: Dirección-----------------------
					
					$esteCampo = 'nombres';
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = "text";
					$atributos ['id'] = $esteCampo;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
					$atributos ["etiquetaObligatorio"] = true;
					$atributos ['tab'] = $tab ++;
					$atributos ['anchoEtiqueta'] = 2;
					$atributos ['estilo'] = "bootstrap";
					$atributos ['evento'] = '';
					$atributos ['deshabilitado'] = false;
					$atributos ['readonly'] = false;
					$atributos ['columnas'] = 1;
					$atributos ['tamanno'] = 1;
					$atributos ['placeholder'] = "Ingrese Nombres";
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['valor'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['valor'] = '';
					}
					$atributos ['ajax_function'] = "";
					$atributos ['ajax_control'] = $esteCampo;
					$atributos ['limitar'] = false;
					$atributos ['anchoCaja'] = 10;
					$atributos ['miEvento'] = '';
					$atributos ['validar'] = 'required';
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
					unset ( $atributos );
					
					$esteCampo = 'primer_apellido';
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = "text";
					$atributos ['id'] = $esteCampo;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
					$atributos ["etiquetaObligatorio"] = true;
					$atributos ['tab'] = $tab ++;
					$atributos ['anchoEtiqueta'] = 2;
					$atributos ['estilo'] = "bootstrap";
					$atributos ['evento'] = '';
					$atributos ['deshabilitado'] = false;
					$atributos ['readonly'] = false;
					$atributos ['columnas'] = 1;
					$atributos ['tamanno'] = 1;
					$atributos ['placeholder'] = "Ingrese Primer Apellido";
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['valor'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['valor'] = '';
					}
					$atributos ['ajax_function'] = "";
					$atributos ['ajax_control'] = $esteCampo;
					$atributos ['limitar'] = false;
					$atributos ['anchoCaja'] = 10;
					$atributos ['miEvento'] = '';
					$atributos ['validar'] = 'required';
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
					unset ( $atributos );
					
					$esteCampo = 'segundo_apellido';
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = "text";
					$atributos ['id'] = $esteCampo;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
					$atributos ["etiquetaObligatorio"] = true;
					$atributos ['tab'] = $tab ++;
					$atributos ['anchoEtiqueta'] = 2;
					$atributos ['estilo'] = "bootstrap";
					$atributos ['evento'] = '';
					$atributos ['deshabilitado'] = false;
					$atributos ['readonly'] = false;
					$atributos ['columnas'] = 1;
					$atributos ['tamanno'] = 1;
					$atributos ['placeholder'] = "Ingrese Segundo Apellido";
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['valor'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['valor'] = '';
					}
					$atributos ['ajax_function'] = "";
					$atributos ['ajax_control'] = $esteCampo;
					$atributos ['limitar'] = false;
					$atributos ['anchoCaja'] = 10;
					$atributos ['miEvento'] = '';
					//$atributos ['validar'] = 'required';
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
					unset ( $atributos );
					
					$esteCampo = 'tipo_documento';
					$atributos ['nombre'] = $esteCampo;
					$atributos ['id'] = $esteCampo;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
					$atributos ["etiquetaObligatorio"] = true;
					$atributos ['tab'] = $tab ++;
					$atributos ['anchoEtiqueta'] = 2;
					$atributos ['evento'] = '';
					
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['seleccion'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['seleccion'] = '-1';
					}
					$atributos ['deshabilitado'] = false;
					$atributos ['columnas'] = 1;
					$atributos ['tamanno'] = 1;
					$atributos ['ajax_function'] = "";
					$atributos ['ajax_control'] = $esteCampo;
					$atributos ['estilo'] = "bootstrap";
					$atributos ['limitar'] = false;
					$atributos ['anchoCaja'] = 10;
					$atributos ['miEvento'] = '';
					$atributos ['validar'] = 'required';
					$atributos ['cadena_sql'] = 'required';
					// $cadenaSql = $this->miSql->getCadenaSql('consultarMedioPago');
					// $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
					$matrizItems = array (
							array (
									'1',
									'Cédula de Ciudadanía' 
							),
							array (
									'2',
									'Tarjeta de Identidad' 
							) 
					);
					$atributos ['matrizItems'] = $matrizItems;
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroListaBootstrap ( $atributos );
					unset ( $atributos );
					
					$esteCampo = 'numero_identificacion';
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = "text";
					$atributos ['id'] = $esteCampo;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
					$atributos ["etiquetaObligatorio"] = true;
					$atributos ['tab'] = $tab ++;
					$atributos ['anchoEtiqueta'] = 2;
					$atributos ['estilo'] = "bootstrap";
					$atributos ['evento'] = '';
					$atributos ['deshabilitado'] = false;
					$atributos ['readonly'] = false;
					$atributos ['columnas'] = 1;
					$atributos ['tamanno'] = 1;
					$atributos ['placeholder'] = "Ingrese Número Identificacion";
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['valor'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['valor'] = '';
					}
					$atributos ['ajax_function'] = "";
					$atributos ['ajax_control'] = $esteCampo;
					$atributos ['limitar'] = false;
					$atributos ['anchoCaja'] = 10;
					$atributos ['miEvento'] = '';
					$atributos ['validar'] = 'required';
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
					unset ( $atributos );
					
// 					// ----------------INICIO CONTROL: Campo Texto Correo Electrónico--------------------------------------------------------
// 					$esteCampo = 'correo';
// 					$atributos ['nombre'] = $esteCampo;
// 					$atributos ['tipo'] = "mail";
// 					$atributos ['id'] = $esteCampo;
// 					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 					$atributos ["etiquetaObligatorio"] = true;
// 					$atributos ['tab'] = $tab ++;
// 					$atributos ['anchoEtiqueta'] = 2;
// 					$atributos ['estilo'] = "bootstrap";
// 					$atributos ['evento'] = '';
// 					$atributos ['deshabilitado'] = false;
// 					$atributos ['readonly'] = false;
// 					$atributos ['columnas'] = 1;
// 					$atributos ['tamanno'] = 1;
// 					$atributos ['placeholder'] = "";
// 					$atributos ['valor'] = "";
// 					$atributos ['ajax_function'] = "";
// 					$atributos ['ajax_control'] = $esteCampo;
// 					$atributos ['limitar'] = false;
// 					$atributos ['anchoCaja'] = 10;
// 					$atributos ['miEvento'] = '';
// 					// $atributos['validar'] = 'required';
// 					// Aplica atributos globales al control
					
// 					if (isset ( $cargueDatos [$esteCampo] )) {
// 						$atributos ['valor'] = $cargueDatos [$esteCampo];
// 					} else {
// 						$atributos ['valor'] = '';
// 					}
					
// 					$atributos = array_merge ( $atributos, $atributosGlobales );
// 					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 					unset ( $atributos );
					
// 					// ----------------FIN CONTROL: Campo Texto Correo Electrónico-------------------------------------------------------
// 					// ----------------INICIO CONTROL: Fecha de Agendamiento--------------------------------------------------------
					
					$esteCampo = 'fecha_instalacion';
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = "text";
					$atributos ['id'] = $esteCampo;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
					$atributos ["etiquetaObligatorio"] = true;
					$atributos ['tab'] = $tab ++;
					$atributos ['anchoEtiqueta'] = 2;
					$atributos ['estilo'] = "bootstrap";
					$atributos ['evento'] = '';
					$atributos ['deshabilitado'] = false;
					$atributos ['readonly'] = false;
					$atributos ['columnas'] = 1;
					$atributos ['tamanno'] = 1;
					$atributos ['placeholder'] = "Seleccione la Fecha de Instalación";
					$atributos ['valor'] = "";
					$atributos ['ajax_function'] = "";
					$atributos ['ajax_control'] = $esteCampo;
					$atributos ['limitar'] = false;
					$atributos ['anchoCaja'] = 10;
					$atributos ['miEvento'] = '';
					$atributos ['validar'] = 'required';
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
					unset ( $atributos );
					// ----------------FIN CONTROL: Fecha de Agendamiento-----------------------
					
					// ----------------INICIO CONTROL: Lista Tipo de Beneficiario--------------------------------------------------------
					$esteCampo = 'tipo_beneficiario';
					$atributos ['nombre'] = $esteCampo;
					$atributos ['id'] = $esteCampo;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
					
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['seleccion'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['seleccion'] = - 1;
					}
					
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroListaBootstrap ( $atributos );
					unset ( $atributos );
					// ----------------FIN CONTROL: Lista Tipo de Beneficiario--------------------------------------------------------
					
					// ----------------INICIO CONTROL: Lista Estrato--------------------------------------------------------
					$esteCampo = 'estrato';
					$atributos ['nombre'] = $esteCampo;
					$atributos ['id'] = $esteCampo;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
					$atributos['validar'] = 'required';
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
					
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['seleccion'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['seleccion'] = - 1;
					}
					
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroListaBootstrap ( $atributos );
					unset ( $atributos );
					// ----------------FIN CONTROL: Lista Estrato--------------------------------------------------------
					
// 					echo '<div id="div1">';
// 					$esteCampo = 'valor_tarificacion';
// 					$atributos['nombre'] = $esteCampo;
// 					$atributos['id'] = $esteCampo;
// 					$atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
// 					$atributos['tipo'] = "number";
// 					{
// 						$atributos['decimal'] = true;
// 					}
// 					$atributos["etiquetaObligatorio"] = true;
// 					$atributos['tab'] = $tab++;
// 					$atributos['anchoEtiqueta'] = 2;
// 					$atributos['evento'] = '';
// 					$atributos['deshabilitado'] = false;
// 					$atributos['columnas'] = 1;
// 					$atributos['readonly'] = false;
// 					$atributos['tamanno'] = 1;
// 					$atributos['ajax_function'] = "";
// 					$atributos['ajax_control'] = $esteCampo;
// 					$atributos['estilo'] = "bootstrap";
// 					$atributos['limitar'] = false;
// 					$atributos['anchoCaja'] = 10;
// 					$atributos['minimo'] = 0;
// 					$atributos['miEvento'] = '';
// 					//$atributos['validar'] = 'required';
// 					// Aplica atributos globales al control
// 					$atributos = array_merge($atributos, $atributosGlobales);

// 					echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
// 					unset($atributos);
					
// 					echo '</div>';
					
					// ----------------FIN CONTROL: Dirección-----------------------
					$esteCampo = 'direccion';
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = "text";
					$atributos ['id'] = $esteCampo;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
					$atributos ["etiquetaObligatorio"] = true;
					$atributos ['tab'] = $tab ++;
					$atributos ['anchoEtiqueta'] = 2;
					$atributos ['estilo'] = "bootstrap";
					$atributos ['evento'] = '';
					$atributos ['deshabilitado'] = false;
					$atributos ['readonly'] = false;
					$atributos ['columnas'] = 1;
					$atributos ['tamanno'] = 1;
					$atributos ['placeholder'] = "Ingrese la Dirección";
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['valor'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['valor'] = '';
					}
					$atributos ['ajax_function'] = "";
					$atributos ['ajax_control'] = $esteCampo;
					$atributos ['limitar'] = false;
					$atributos ['anchoCaja'] = 10;
					$atributos ['miEvento'] = '';
					$atributos ['validar'] = ' ';
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
					unset ( $atributos );
					// ----------------FIN CONTROL: Dirección-----------------------
					
					// ----------------INICIO CONTROL: Lista Urbanización--------------------------------------------------------
					
					// ----------------INICIO CONTROL: Campo Texto Departamento--------------------------------------------------------
					$esteCampo = 'urbanizacion';
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = "text";
					$atributos ['id'] = $esteCampo;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
					$atributos ["etiquetaObligatorio"] = true;
					$atributos ['tab'] = $tab ++;
					$atributos ['anchoEtiqueta'] = 2;
					$atributos ['estilo'] = "bootstrap";
					$atributos ['evento'] = '';
					$atributos ['deshabilitado'] = false;
					$atributos ['readonly'] = true;
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
					
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['valor'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['valor'] = '';
					}
					
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
					unset ( $atributos );
					
// 					// ----------------INICIO CONTROL: Campo Oculto ID de Urbanización-------------------------------------------------------
					
// 					$esteCampo = 'id_urbanizacion';
// 					$atributos ["id"] = $esteCampo; // No cambiar este nombre
// 					$atributos ["tipo"] = "hidden";
// 					$atributos ['valor'] = '';
// 					$atributos ['estilo'] = '';
// 					$atributos ["obligatorio"] = false;
// 					$atributos ['marco'] = true;
// 					$atributos ["etiqueta"] = "";
					
// 					if (isset ( $_REQUEST [$esteCampo] )) {
// 						$atributos ['valor'] = $_REQUEST [$esteCampo];
// 					} else {
// 						$atributos ['valor'] = '';
// 					}
					
// 					$atributos = array_merge ( $atributos, $atributosGlobales );
// 					echo $this->miFormulario->campoCuadroTexto ( $atributos );
// 					unset ( $atributos );
					
// 					// ----------------FIN CONTROL: Campo Oculto Cantidad ID de Urbanización--------------------------------------------------------
					
					// ----------------FIN CONTROL: Lista Proyecto--------------------------------------------------------
					
					// ----------------INICIO CONTROL: Campo Texto Departamento--------------------------------------------------------
					$esteCampo = 'departamento';
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = "text";
					$atributos ['id'] = $esteCampo;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
					$atributos ["etiquetaObligatorio"] = true;
					$atributos ['tab'] = $tab ++;
					$atributos ['anchoEtiqueta'] = 2;
					$atributos ['estilo'] = "bootstrap";
					$atributos ['evento'] = '';
					$atributos ['deshabilitado'] = false;
					$atributos ['readonly'] = true;
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
					
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['valor'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['valor'] = '';
					}
					
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
					unset ( $atributos );
					// ----------------FIN CONTROL: Campo Texto Departamento--------------------------------------------------------
					
					// ----------------INICIO CONTROL: Campo Texto Municipio--------------------------------------------------------
					$esteCampo = 'municipio';
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = "text";
					$atributos ['id'] = $esteCampo;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
					$atributos ["etiquetaObligatorio"] = true;
					$atributos ['tab'] = $tab ++;
					$atributos ['anchoEtiqueta'] = 2;
					$atributos ['estilo'] = "bootstrap";
					$atributos ['evento'] = '';
					$atributos ['deshabilitado'] = false;
					$atributos ['readonly'] = true;
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
					
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['valor'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['valor'] = '';
					}
					
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
					unset ( $atributos );
					// ----------------FIN CONTROL: Campo Texto Municipio--------------------------------------------------------
					
// 					// ----------------INICIO CONTROL: Campo Texto Código DANE--------------------------------------------------------
// 					$esteCampo = 'codigo_dane';
// 					$atributos ['nombre'] = $esteCampo;
// 					$atributos ['tipo'] = "text";
// 					$atributos ['id'] = $esteCampo;
// 					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 					$atributos ["etiquetaObligatorio"] = true;
// 					$atributos ['tab'] = $tab ++;
// 					$atributos ['anchoEtiqueta'] = 2;
// 					$atributos ['estilo'] = "bootstrap";
// 					$atributos ['evento'] = '';
// 					$atributos ['deshabilitado'] = false;
// 					$atributos ['readonly'] = true;
// 					$atributos ['columnas'] = 1;
// 					$atributos ['tamanno'] = 1;
// 					$atributos ['placeholder'] = "";
// 					$atributos ['valor'] = "";
// 					$atributos ['ajax_function'] = "";
// 					$atributos ['ajax_control'] = $esteCampo;
// 					$atributos ['limitar'] = false;
// 					$atributos ['anchoCaja'] = 10;
// 					$atributos ['miEvento'] = '';
// 					// $atributos['validar'] = 'required';
// 					// Aplica atributos globales al control
					
// 					if (isset ( $cargueDatos [$esteCampo] )) {
// 						$atributos ['valor'] = $_REQUEST [$esteCampo];
// 					} else {
// 						$atributos ['valor'] = '';
// 					}
					
// 					$atributos = array_merge ( $atributos, $atributosGlobales );
// 					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 					unset ( $atributos );
// 					// ----------------FIN CONTROL: Campo Texto Código DANE--------------------------------------------------------
					
					// ----------------INICIO CONTROL: Lista Tipo Tecnología--------------------------------------------------------
					$esteCampo = 'tipo_tecnologia';
					$atributos ['nombre'] = $esteCampo;
					$atributos ['id'] = $esteCampo;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
					// $atributos['validar'] = 'required';
					$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "parametroTipoTecnologia" );
					$matrizItems = array (
							array (
									0,
									' ' 
							) 
					);
					$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
					$atributos ['matrizItems'] = $matrizItems;
					// Aplica atributos globales al control
					
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['seleccion'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['seleccion'] = - 1;
					}
					
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroListaBootstrap ( $atributos );
					unset ( $atributos );
					// ----------------FIN CONTROL: Lista Tipo Tecnología--------------------------------------------------------
					
					// ----------------INICIO CONTROL: Campo Texto Ubicación Geográfica: Geolocalización--------------------------------------------------------
					
					$esteCampo = 'geolocalizacion';
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = "text";
					$atributos ['id'] = $esteCampo;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
					$atributos ['validar'] = '';
					// Aplica atributos globales al control
					
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['valor'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['valor'] = '';
					}
					
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
					unset ( $atributos );
					// ----------------FIN CONTROL: Campo Texto Ubicación Geográfica: Geolocalización-------------------------------------------------------
				}
// 				echo '</div>
//                     </div>
//                 </div>
//                 <div class="panel panel-default">
//                     <div class="panel-heading">
//                         <h4 class="panel-title">
//                             <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">Información de Contacto</a>
//                         </h4>
//                     </div>
//                     <div id="collapse2" class="panel-collapse collapse">
//                         <div class="panel-body">';
// 				{
// 					// ----------------FIN CONTROL: Contacto-----------------------
// 					$esteCampo = 'contacto';
// 					$atributos ['nombre'] = $esteCampo;
// 					$atributos ['tipo'] = "text";
// 					$atributos ['id'] = $esteCampo;
// 					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 					$atributos ["etiquetaObligatorio"] = true;
// 					$atributos ['tab'] = $tab ++;
// 					$atributos ['anchoEtiqueta'] = 2;
// 					$atributos ['estilo'] = "bootstrap";
// 					$atributos ['evento'] = '';
// 					$atributos ['deshabilitado'] = false;
// 					$atributos ['readonly'] = false;
// 					$atributos ['columnas'] = 1;
// 					$atributos ['tamanno'] = 1;
// 					$atributos ['placeholder'] = "Ingrese persona a contactar";
// 					if (isset ( $_REQUEST ['nombres'] )) {
// 						$atributos ['valor'] = $_REQUEST ['nombres'] . " " . $_REQUEST ['primer_apellido'] . " " . $_REQUEST ['segundo_apellido'];
// 					} else {
// 						$atributos ['valor'] = '';
// 					}
// 					$atributos ['ajax_function'] = "";
// 					$atributos ['ajax_control'] = $esteCampo;
// 					$atributos ['limitar'] = false;
// 					$atributos ['anchoCaja'] = 10;
// 					$atributos ['miEvento'] = '';
// 					$atributos ['validar'] = ' ';
// 					// Aplica atributos globales al control
// 					$atributos = array_merge ( $atributos, $atributosGlobales );
// 					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 					unset ( $atributos );
// 					// ----------------FIN CONTROL: Contacto-----------------------
					
// 					$esteCampo = 'numero_identificacion_cont';
// 					$atributos ['nombre'] = $esteCampo;
// 					$atributos ['tipo'] = "text";
// 					$atributos ['id'] = $esteCampo;
// 					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 					$atributos ["etiquetaObligatorio"] = true;
// 					$atributos ['tab'] = $tab ++;
// 					$atributos ['anchoEtiqueta'] = 2;
// 					$atributos ['estilo'] = "bootstrap";
// 					$atributos ['evento'] = '';
// 					$atributos ['deshabilitado'] = false;
// 					$atributos ['readonly'] = false;
// 					$atributos ['columnas'] = 1;
// 					$atributos ['tamanno'] = 1;
// 					$atributos ['placeholder'] = "Ingrese Número Identificacion";
// 					if (isset ( $_REQUEST ['numero_identificacion'] )) {
// 						$atributos ['valor'] = $_REQUEST ['numero_identificacion'];
// 					} else {
// 						$atributos ['valor'] = '';
// 					}
// 					$atributos ['ajax_function'] = "";
// 					$atributos ['ajax_control'] = $esteCampo;
// 					$atributos ['limitar'] = false;
// 					$atributos ['anchoCaja'] = 10;
// 					$atributos ['miEvento'] = '';
// 					$atributos ['validar'] = 'required';
// 					// Aplica atributos globales al control
// 					$atributos = array_merge ( $atributos, $atributosGlobales );
// 					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 					unset ( $atributos );
						
// 					// ----------------FIN CONTROL: Celular-----------------------
// 					$esteCampo = 'telefono';
// 					$atributos ['nombre'] = $esteCampo;
// 					$atributos ['tipo'] = "text";
// 					$atributos ['id'] = $esteCampo;
// 					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 					$atributos ["etiquetaObligatorio"] = true;
// 					$atributos ['tab'] = $tab ++;
// 					$atributos ['anchoEtiqueta'] = 2;
// 					$atributos ['estilo'] = "bootstrap";
// 					$atributos ['evento'] = '';
// 					$atributos ['deshabilitado'] = false;
// 					$atributos ['readonly'] = false;
// 					$atributos ['columnas'] = 1;
// 					$atributos ['tamanno'] = 1;
// 					$atributos ['placeholder'] = "Ingrese Número Telefónico";
// 					if (isset ( $_REQUEST [$esteCampo] )) {
// 						$atributos ['valor'] = $_REQUEST [$esteCampo];
// 					} else {
// 						$atributos ['valor'] = '';
// 					}
// 					$atributos ['ajax_function'] = "";
// 					$atributos ['ajax_control'] = $esteCampo;
// 					$atributos ['limitar'] = false;
// 					$atributos ['anchoCaja'] = 10;
// 					$atributos ['miEvento'] = '';
// 					$atributos ['validar'] = ' ';
// 					// Aplica atributos globales al control
// 					$atributos = array_merge ( $atributos, $atributosGlobales );
// 					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 					unset ( $atributos );
// 					// ----------------FIN CONTROL: Celular-----------------------
					
// 					// ----------------FIN CONTROL: Celular-----------------------
// 					$esteCampo = 'celular';
// 					$atributos ['nombre'] = $esteCampo;
// 					$atributos ['tipo'] = "text";
// 					$atributos ['id'] = $esteCampo;
// 					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 					$atributos ["etiquetaObligatorio"] = true;
// 					$atributos ['tab'] = $tab ++;
// 					$atributos ['anchoEtiqueta'] = 2;
// 					$atributos ['estilo'] = "bootstrap";
// 					$atributos ['evento'] = '';
// 					$atributos ['deshabilitado'] = false;
// 					$atributos ['readonly'] = false;
// 					$atributos ['columnas'] = 1;
// 					$atributos ['tamanno'] = 1;
// 					$atributos ['placeholder'] = "Ingrese Número Celular";
// 					if (isset ( $_REQUEST [$esteCampo] )) {
// 						$atributos ['valor'] = $_REQUEST [$esteCampo];
// 					} else {
// 						$atributos ['valor'] = '';
// 					}
// 					$atributos ['ajax_function'] = "";
// 					$atributos ['ajax_control'] = $esteCampo;
// 					$atributos ['limitar'] = false;
// 					$atributos ['anchoCaja'] = 10;
// 					$atributos ['miEvento'] = '';
// 					$atributos ['validar'] = ' ';
// 					// Aplica atributos globales al control
// 					$atributos = array_merge ( $atributos, $atributosGlobales );
// 					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 					unset ( $atributos );
// 					// ----------------FIN CONTROL: Celular-----------------------
// 				}
				
				echo '</div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">Detalle de Equipos Instalados</a>
                        </h4>
                    </div>
                    <div id="collapse3" class="panel-collapse collapse">
                        <div class="panel-body">';
				{
					
					echo '<div class="panel-group" id="accordion2">
							
						               <div class="panel panel-default">
						                    <div class="panel-heading">
						                        <h4 class="panel-title">
						                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse3A">Esclavo</a>
						                        </h4>
						                    </div>
						                    <div id="collapse3A" class="panel-collapse collapse">
						                        <div class="panel-body">';
					
					{
						
// 						$esteCampo = 'numero_act_esc';
// 						$atributos ['nombre'] = $esteCampo;
// 						$atributos ['tipo'] = "text";
// 						$atributos ['id'] = $esteCampo;
// 						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 						$atributos ["etiquetaObligatorio"] = true;
// 						$atributos ['tab'] = $tab ++;
// 						$atributos ['anchoEtiqueta'] = 2;
// 						$atributos ['estilo'] = "bootstrap";
// 						$atributos ['evento'] = '';
// 						$atributos ['deshabilitado'] = false;
// 						$atributos ['readonly'] = false;
// 						$atributos ['columnas'] = 1;
// 						$atributos ['tamanno'] = 1;
// 						$atributos ['placeholder'] = "";
// 						if (isset ( $_REQUEST [$esteCampo] )) {
// 							$atributos ['valor'] = $_REQUEST [$esteCampo];
// 						} else {
// 							$atributos ['valor'] = '';
// 						}
// 						$atributos ['ajax_function'] = "";
// 						$atributos ['ajax_control'] = $esteCampo;
// 						$atributos ['limitar'] = false;
// 						$atributos ['anchoCaja'] = 10;
// 						$atributos ['miEvento'] = '';
// 						//$atributos ['validar'] = 'required';
// 						// Aplica atributos globales al control
// 						$atributos = array_merge ( $atributos, $atributosGlobales );
// 						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 						unset ( $atributos );
						
						$esteCampo = 'mac_esc';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'mac2_esc';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'serial_esc';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'marca_esc';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'cant_esc';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'ip_esc';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
					}
// 					echo '</div>
// 				                    </div>
// 				                </div>
// 				                <div class="panel panel-default">
// 				                    <div class="panel-heading">
// 				                        <h4 class="panel-title">
// 				                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse3B">Computador</a>
// 				                        </h4>
// 				                    </div>
// 				                    <div id="collapse3B" class="panel-collapse collapse">
// 				                        <div class="panel-body">';
// 					{
						
// 						$esteCampo = 'numero_act_comp';
// 						$atributos ['nombre'] = $esteCampo;
// 						$atributos ['tipo'] = "text";
// 						$atributos ['id'] = $esteCampo;
// 						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 						$atributos ["etiquetaObligatorio"] = true;
// 						$atributos ['tab'] = $tab ++;
// 						$atributos ['anchoEtiqueta'] = 2;
// 						$atributos ['estilo'] = "bootstrap";
// 						$atributos ['evento'] = '';
// 						$atributos ['deshabilitado'] = false;
// 						$atributos ['readonly'] = false;
// 						$atributos ['columnas'] = 1;
// 						$atributos ['tamanno'] = 1;
// 						$atributos ['placeholder'] = "";
// 						if (isset ( $_REQUEST [$esteCampo] )) {
// 							$atributos ['valor'] = $_REQUEST [$esteCampo];
// 						} else {
// 							$atributos ['valor'] = '';
// 						}
// 						$atributos ['ajax_function'] = "";
// 						$atributos ['ajax_control'] = $esteCampo;
// 						$atributos ['limitar'] = false;
// 						$atributos ['anchoCaja'] = 10;
// 						$atributos ['miEvento'] = '';
// 						//$atributos ['validar'] = 'required';
// 						// Aplica atributos globales al control
// 						$atributos = array_merge ( $atributos, $atributosGlobales );
// 						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 						unset ( $atributos );
						
// 						$esteCampo = 'mac_comp';
// 						$atributos ['nombre'] = $esteCampo;
// 						$atributos ['tipo'] = "text";
// 						$atributos ['id'] = $esteCampo;
// 						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 						$atributos ["etiquetaObligatorio"] = true;
// 						$atributos ['tab'] = $tab ++;
// 						$atributos ['anchoEtiqueta'] = 2;
// 						$atributos ['estilo'] = "bootstrap";
// 						$atributos ['evento'] = '';
// 						$atributos ['deshabilitado'] = false;
// 						$atributos ['readonly'] = false;
// 						$atributos ['columnas'] = 1;
// 						$atributos ['tamanno'] = 1;
// 						$atributos ['placeholder'] = "";
// 						if (isset ( $_REQUEST [$esteCampo] )) {
// 							$atributos ['valor'] = $_REQUEST [$esteCampo];
// 						} else {
// 							$atributos ['valor'] = '';
// 						}
// 						$atributos ['ajax_function'] = "";
// 						$atributos ['ajax_control'] = $esteCampo;
// 						$atributos ['limitar'] = false;
// 						$atributos ['anchoCaja'] = 10;
// 						$atributos ['miEvento'] = '';
// 						//$atributos ['validar'] = 'required';
// 						// Aplica atributos globales al control
// 						$atributos = array_merge ( $atributos, $atributosGlobales );
// 						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 						unset ( $atributos );
						
// 						$esteCampo = 'serial_comp';
// 						$atributos ['nombre'] = $esteCampo;
// 						$atributos ['tipo'] = "text";
// 						$atributos ['id'] = $esteCampo;
// 						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 						$atributos ["etiquetaObligatorio"] = true;
// 						$atributos ['tab'] = $tab ++;
// 						$atributos ['anchoEtiqueta'] = 2;
// 						$atributos ['estilo'] = "bootstrap";
// 						$atributos ['evento'] = '';
// 						$atributos ['deshabilitado'] = false;
// 						$atributos ['readonly'] = false;
// 						$atributos ['columnas'] = 1;
// 						$atributos ['tamanno'] = 1;
// 						$atributos ['placeholder'] = "";
// 						if (isset ( $_REQUEST [$esteCampo] )) {
// 							$atributos ['valor'] = $_REQUEST [$esteCampo];
// 						} else {
// 							$atributos ['valor'] = '';
// 						}
// 						$atributos ['ajax_function'] = "";
// 						$atributos ['ajax_control'] = $esteCampo;
// 						$atributos ['limitar'] = false;
// 						$atributos ['anchoCaja'] = 10;
// 						$atributos ['miEvento'] = '';
// 						//$atributos ['validar'] = 'required';
// 						// Aplica atributos globales al control
// 						$atributos = array_merge ( $atributos, $atributosGlobales );
// 						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 						unset ( $atributos );
						
// 						$esteCampo = 'marca_comp';
// 						$atributos ['nombre'] = $esteCampo;
// 						$atributos ['tipo'] = "text";
// 						$atributos ['id'] = $esteCampo;
// 						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 						$atributos ["etiquetaObligatorio"] = true;
// 						$atributos ['tab'] = $tab ++;
// 						$atributos ['anchoEtiqueta'] = 2;
// 						$atributos ['estilo'] = "bootstrap";
// 						$atributos ['evento'] = '';
// 						$atributos ['deshabilitado'] = false;
// 						$atributos ['readonly'] = false;
// 						$atributos ['columnas'] = 1;
// 						$atributos ['tamanno'] = 1;
// 						$atributos ['placeholder'] = "";
// 						if (isset ( $_REQUEST [$esteCampo] )) {
// 							$atributos ['valor'] = $_REQUEST [$esteCampo];
// 						} else {
// 							$atributos ['valor'] = '';
// 						}
// 						$atributos ['ajax_function'] = "";
// 						$atributos ['ajax_control'] = $esteCampo;
// 						$atributos ['limitar'] = false;
// 						$atributos ['anchoCaja'] = 10;
// 						$atributos ['miEvento'] = '';
// 						//$atributos ['validar'] = 'required';
// 						// Aplica atributos globales al control
// 						$atributos = array_merge ( $atributos, $atributosGlobales );
// 						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 						unset ( $atributos );
						
// 						$esteCampo = 'cant_comp';
// 						$atributos ['nombre'] = $esteCampo;
// 						$atributos ['tipo'] = "text";
// 						$atributos ['id'] = $esteCampo;
// 						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 						$atributos ["etiquetaObligatorio"] = true;
// 						$atributos ['tab'] = $tab ++;
// 						$atributos ['anchoEtiqueta'] = 2;
// 						$atributos ['estilo'] = "bootstrap";
// 						$atributos ['evento'] = '';
// 						$atributos ['deshabilitado'] = false;
// 						$atributos ['readonly'] = false;
// 						$atributos ['columnas'] = 1;
// 						$atributos ['tamanno'] = 1;
// 						$atributos ['placeholder'] = "";
// 						if (isset ( $_REQUEST [$esteCampo] )) {
// 							$atributos ['valor'] = $_REQUEST [$esteCampo];
// 						} else {
// 							$atributos ['valor'] = '';
// 						}
// 						$atributos ['ajax_function'] = "";
// 						$atributos ['ajax_control'] = $esteCampo;
// 						$atributos ['limitar'] = false;
// 						$atributos ['anchoCaja'] = 10;
// 						$atributos ['miEvento'] = '';
// 						//$atributos ['validar'] = 'required';
// 						// Aplica atributos globales al control
// 						$atributos = array_merge ( $atributos, $atributosGlobales );
// 						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 						unset ( $atributos );
						
// 						$esteCampo = 'ip_comp';
// 						$atributos ['nombre'] = $esteCampo;
// 						$atributos ['tipo'] = "text";
// 						$atributos ['id'] = $esteCampo;
// 						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 						$atributos ["etiquetaObligatorio"] = true;
// 						$atributos ['tab'] = $tab ++;
// 						$atributos ['anchoEtiqueta'] = 2;
// 						$atributos ['estilo'] = "bootstrap";
// 						$atributos ['evento'] = '';
// 						$atributos ['deshabilitado'] = false;
// 						$atributos ['readonly'] = false;
// 						$atributos ['columnas'] = 1;
// 						$atributos ['tamanno'] = 1;
// 						$atributos ['placeholder'] = "";
// 						if (isset ( $_REQUEST [$esteCampo] )) {
// 							$atributos ['valor'] = $_REQUEST [$esteCampo];
// 						} else {
// 							$atributos ['valor'] = '';
// 						}
// 						$atributos ['ajax_function'] = "";
// 						$atributos ['ajax_control'] = $esteCampo;
// 						$atributos ['limitar'] = false;
// 						$atributos ['anchoCaja'] = 10;
// 						$atributos ['miEvento'] = '';
// 						//$atributos ['validar'] = 'required';
// 						// Aplica atributos globales al control
// 						$atributos = array_merge ( $atributos, $atributosGlobales );
// 						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 						unset ( $atributos );
// 					}
					
					echo '</div></div>
				                    </div>
				                </div>';
				}
				echo '</div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse4">Pruebas</a>
                        </h4>
                    </div>
                    <div id="collapse4" class="panel-collapse collapse">
                        <div class="panel-body">';
				{
					echo '<div class="panel-group" id="accordion4A">
				
						               <div class="panel panel-default">
						                    <div class="panel-heading">
						                        <h4 class="panel-title">
						                            <a data-toggle="collapse" data-parent="#accordion4A" href="#collapse4A">Velocidad de Subida</a>
						                        </h4>
						                    </div>
						                    <div id="collapse4A" class="panel-collapse collapse">
						                        <div class="panel-body">';
					
					{
						
						$esteCampo = 'hora_prueba_vs';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'resultado_vs';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'unidad_vs';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['tab'] = $tab ++;
						$atributos ['anchoEtiqueta'] = 2;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = false;
						$atributos ['readonly'] = true;
						$atributos ['columnas'] = 1;
						$atributos ['tamanno'] = 1;
						$atributos ['placeholder'] = "";
						$atributos ['valor'] = 'Mbps';
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'observaciones_vs';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
					}
					echo '</div>
				                    </div>
				                </div>
				                <div class="panel panel-default">
				                    <div class="panel-heading">
				                        <h4 class="panel-title">
				                            <a data-toggle="collapse" data-parent="#accordion4A" href="#collapse4B">Velocidad de Bajada</a>
				                        </h4>
				                    </div>
				                    <div id="collapse4B" class="panel-collapse collapse">
				                        <div class="panel-body">';
					{
						$esteCampo = 'hora_prueba_vb';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'resultado_vb';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'unidad_vb';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['tab'] = $tab ++;
						$atributos ['anchoEtiqueta'] = 2;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = false;
						$atributos ['readonly'] = true;
						$atributos ['columnas'] = 1;
						$atributos ['tamanno'] = 1;
						$atributos ['placeholder'] = "";
						$atributos ['valor'] = 'Mbps';
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'observaciones_vb';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
					}
					echo '</div>
				                    </div>
				                </div>
				                <div class="panel panel-default">
				                    <div class="panel-heading">
				                        <h4 class="panel-title">
				                            <a data-toggle="collapse" data-parent="#accordion4A" href="#collapse4C">Ping 1</a>
				                        </h4>
				                    </div>
				                    <div id="collapse4C" class="panel-collapse collapse">
				                        <div class="panel-body">';
					{
						
						$esteCampo = 'hora_prueba_p1';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'resultado_p1';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'unidad_p1';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['tab'] = $tab ++;
						$atributos ['anchoEtiqueta'] = 2;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = false;
						$atributos ['readonly'] = true;
						$atributos ['columnas'] = 1;
						$atributos ['tamanno'] = 1;
						$atributos ['placeholder'] = "";
						$atributos ['valor'] = 'ms';
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'observaciones_p1';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = 'www.mintic.gov.co';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
					}
					echo '</div>
				                    </div>
				                </div>
				                <div class="panel panel-default">
				                    <div class="panel-heading">
				                        <h4 class="panel-title">
				                            <a data-toggle="collapse" data-parent="#accordion4A" href="#collapse4D">Ping 2</a>
				                        </h4>
				                    </div>
				                    <div id="collapse4D" class="panel-collapse collapse">
				                        <div class="panel-body">';
					{
						
						$esteCampo = 'hora_prueba_p2';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'resultado_p2';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'unidad_p2';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['tab'] = $tab ++;
						$atributos ['anchoEtiqueta'] = 2;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = false;
						$atributos ['readonly'] = true;
						$atributos ['columnas'] = 1;
						$atributos ['tamanno'] = 1;
						$atributos ['placeholder'] = "";
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = 'ms';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'observaciones_p2';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = 'http://www.louvre.fr/en';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
					}
					echo '</div>
				                    </div>
				                </div>
				                <div class="panel panel-default">
				                    <div class="panel-heading">
				                        <h4 class="panel-title">
				                            <a data-toggle="collapse" data-parent="#accordion4A" href="#collapse4E">Ping 3</a>
				                        </h4>
				                    </div>
				                    <div id="collapse4E" class="panel-collapse collapse">
				                        <div class="panel-body">';
					{
						
						$esteCampo = 'hora_prueba_p3';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'resultado_p3';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'unidad_p3';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['tab'] = $tab ++;
						$atributos ['anchoEtiqueta'] = 2;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = false;
						$atributos ['readonly'] = true;
						$atributos ['columnas'] = 1;
						$atributos ['tamanno'] = 1;
						$atributos ['placeholder'] = "";
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = 'ms';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'observaciones_p3';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = 'https://www.wikipedia.org';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
					}
					echo '</div>
				                    </div>
				                </div>
				                <div class="panel panel-default">
				                    <div class="panel-heading">
				                        <h4 class="panel-title">
				                            <a data-toggle="collapse" data-parent="#accordion4A" href="#collapse4F">Traceroute</a>
				                        </h4>
				                    </div>
				                    <div id="collapse4F" class="panel-collapse collapse">
				                        <div class="panel-body">';
					{
						
						$esteCampo = 'hora_prueba_tr1';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'resultado_tr1';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'unidad_tr1';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['tab'] = $tab ++;
						$atributos ['anchoEtiqueta'] = 2;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = false;
						$atributos ['readonly'] = true;
						$atributos ['columnas'] = 1;
						$atributos ['tamanno'] = 1;
						$atributos ['placeholder'] = "";
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = 'estado conexión';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'observaciones_tr1';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = 'https://www.sivirtual.gov.co';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
					}
					echo '</div>
				                    </div>
				                </div>
				                <div class="panel panel-default">
				                    <div class="panel-heading">
				                        <h4 class="panel-title">
				                            <a data-toggle="collapse" data-parent="#accordion4A" href="#collapse4G">Traceroute</a>
				                        </h4>
				                    </div>
				                    <div id="collapse4G" class="panel-collapse collapse">
				                        <div class="panel-body">';
					{
						
						$esteCampo = 'hora_prueba_tr2';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'resultado_tr2';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'unidad_tr2';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['tab'] = $tab ++;
						$atributos ['anchoEtiqueta'] = 2;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = false;
						$atributos ['readonly'] = true;
						$atributos ['columnas'] = 1;
						$atributos ['tamanno'] = 1;
						$atributos ['placeholder'] = "";
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = 'Paso NAP';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'observaciones_tr2';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
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
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = 'https://www.sivirtual.gov.co';
						}
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 10;
						$atributos ['miEvento'] = '';
						//$atributos ['validar'] = 'required';
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
						unset ( $atributos );
					}
					
					echo '</div></div>
				                    </div>
				                </div>';
				}
// 				echo '</div>
//                     </div>
//                 </div>
//                 <div class="panel panel-default">
//                     <div class="panel-heading">
//                         <h4 class="panel-title">
//                             <a data-toggle="collapse" data-parent="#accordion" href="#collapse5">Información del Instalador</a>
//                         </h4>
//                     </div>
//                     <div id="collapse5" class="panel-collapse collapse">
//                         <div class="panel-body">';
// 				{
// 					$esteCampo = 'nombre_ins';
// 					$atributos ['nombre'] = $esteCampo;
// 					$atributos ['tipo'] = "text";
// 					$atributos ['id'] = $esteCampo;
// 					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 					$atributos ["etiquetaObligatorio"] = true;
// 					$atributos ['tab'] = $tab ++;
// 					$atributos ['anchoEtiqueta'] = 2;
// 					$atributos ['estilo'] = "bootstrap";
// 					$atributos ['evento'] = '';
// 					$atributos ['deshabilitado'] = false;
// 					$atributos ['readonly'] = false;
// 					$atributos ['columnas'] = 1;
// 					$atributos ['tamanno'] = 1;
// 					$atributos ['placeholder'] = "";
// 					if (isset ( $_REQUEST [$esteCampo] )) {
// 						$atributos ['valor'] = $_REQUEST [$esteCampo];
// 					} else {
// 						$atributos ['valor'] = '';
// 					}
// 					$atributos ['ajax_function'] = "";
// 					$atributos ['ajax_control'] = $esteCampo;
// 					$atributos ['limitar'] = false;
// 					$atributos ['anchoCaja'] = 10;
// 					$atributos ['miEvento'] = '';
// 					// $atributos ['validar'] = 'required';
// 					// Aplica atributos globales al control
// 					$atributos = array_merge ( $atributos, $atributosGlobales );
// 					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 					unset ( $atributos );
					
// 					$esteCampo = 'identificacion_ins';
// 					$atributos ['nombre'] = $esteCampo;
// 					$atributos ['tipo'] = "text";
// 					$atributos ['id'] = $esteCampo;
// 					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 					$atributos ["etiquetaObligatorio"] = true;
// 					$atributos ['tab'] = $tab ++;
// 					$atributos ['anchoEtiqueta'] = 2;
// 					$atributos ['estilo'] = "bootstrap";
// 					$atributos ['evento'] = '';
// 					$atributos ['deshabilitado'] = false;
// 					$atributos ['readonly'] = false;
// 					$atributos ['columnas'] = 1;
// 					$atributos ['tamanno'] = 1;
// 					$atributos ['placeholder'] = "";
// 					if (isset ( $_REQUEST [$esteCampo] )) {
// 						$atributos ['valor'] = $_REQUEST [$esteCampo];
// 					} else {
// 						$atributos ['valor'] = '';
// 					}
// 					$atributos ['ajax_function'] = "";
// 					$atributos ['ajax_control'] = $esteCampo;
// 					$atributos ['limitar'] = false;
// 					$atributos ['anchoCaja'] = 10;
// 					$atributos ['miEvento'] = '';
// 					// $atributos ['validar'] = 'required';
// 					// Aplica atributos globales al control
// 					$atributos = array_merge ( $atributos, $atributosGlobales );
// 					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 					unset ( $atributos );
					
// 					$esteCampo = 'celular_ins';
// 					$atributos ['nombre'] = $esteCampo;
// 					$atributos ['tipo'] = "text";
// 					$atributos ['id'] = $esteCampo;
// 					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 					$atributos ["etiquetaObligatorio"] = true;
// 					$atributos ['tab'] = $tab ++;
// 					$atributos ['anchoEtiqueta'] = 2;
// 					$atributos ['estilo'] = "bootstrap";
// 					$atributos ['evento'] = '';
// 					$atributos ['deshabilitado'] = false;
// 					$atributos ['readonly'] = false;
// 					$atributos ['columnas'] = 1;
// 					$atributos ['tamanno'] = 1;
// 					$atributos ['placeholder'] = "";
// 					if (isset ( $_REQUEST [$esteCampo] )) {
// 						$atributos ['valor'] = $_REQUEST [$esteCampo];
// 					} else {
// 						$atributos ['valor'] = '';
// 					}
// 					$atributos ['ajax_function'] = "";
// 					$atributos ['ajax_control'] = $esteCampo;
// 					$atributos ['limitar'] = false;
// 					$atributos ['anchoCaja'] = 10;
// 					$atributos ['miEvento'] = '';
// 					// $atributos ['validar'] = 'required';
// 					// Aplica atributos globales al control
// 					$atributos = array_merge ( $atributos, $atributosGlobales );
// 					echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 					unset ( $atributos );
// 				}
				echo '</div></div>
                    </div>
                </div>';
				
// 				$esteCampo = 'Agrupacion';
// 				$atributos ['id'] = $esteCampo;
// 				$atributos ['leyenda'] = "Firmas Interesados";
// 				// echo $this->miFormulario->agrupacion('inicio', $atributos);
// 				unset ( $atributos );
// 				{
					
// 					$esteCampo = 'Agrupacion';
// 					$atributos ['id'] = $esteCampo;
// 					$atributos ['leyenda'] = "Firmas Instalador";
// 					echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
// 					unset ( $atributos );
// 					{
// 						echo "<div id='mensaje_firma_ins' style='display:none;'><center><b>Firma Guardada<b></center></div>";
// 						echo "<div id='firma_digital_instalador' style='border-style:double;'></div>";
// 						echo "<br>";
// 						echo "<input type='button'  style='float:left' class='btn btn-default' id='guardarIns' value='Guardar'><input type='button' id='limpiarIns'  style='float:right' class='btn btn-default' value='Limpiar'>";
						
// 						$esteCampo = 'firmaInstalador';
// 						$atributos ["id"] = $esteCampo; // No cambiar este nombre
// 						$atributos ["tipo"] = "hidden";
// 						$atributos ['estilo'] = '';
// 						$atributos ["obligatorio"] = false;
// 						$atributos ['marco'] = true;
// 						$atributos ["etiqueta"] = "";
// 						if (isset ( $_REQUEST [$esteCampo] )) {
// 							$atributos ['valor'] = $_REQUEST [$esteCampo];
// 						} else {
// 							$atributos ['valor'] = '';
// 						}
// 						$atributos = array_merge ( $atributos, $atributosGlobales );
// 						echo $this->miFormulario->campoCuadroTexto ( $atributos );
// 						unset ( $atributos );
// 					}
					
// 					echo $this->miFormulario->agrupacion ( 'fin' );
// 					unset ( $atributos );
					
					$esteCampo = 'Agrupacion';
					$atributos ['id'] = $esteCampo;
					$atributos ['leyenda'] = "Firmas Beneficiario ";
					echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
					unset ( $atributos );
					{
						echo "<div id='mensaje_firma_bn' style='display:none;'><center><b>Firma Guardada<b></center></div>";
						echo "<div id='firma_digital_beneficiario'  style='border-style:double;'></div>";
						echo "<br>";
						echo "<input type='button' style='float:left' class='btn btn-default' id='guardarBn' value='Guardar'> <input type='button' id='limpiarBn' style='float:right' class='btn btn-default' value='Limpiar'>";
						
						$esteCampo = 'firmaBeneficiario';
						$atributos ["id"] = $esteCampo; // No cambiar este nombre
						$atributos ["tipo"] = "hidden";
						$atributos ['estilo'] = '';
						$atributos ["obligatorio"] = false;
						$atributos ['marco'] = true;
						$atributos ["etiqueta"] = "";
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
					}
					
					echo $this->miFormulario->agrupacion ( 'fin' );
					unset ( $atributos );
					
// 					$esteCampo = "foto_soporte";
// 					$atributos ["id"] = $esteCampo;
// 					$atributos ["nombre"] = $esteCampo;
// 					$atributos ["tipo"] = "file";
// 					$atributos ["obligatorio"] = true;
// 					$atributos ["etiquetaObligatorio"] = false;
// 					$atributos ["tabIndex"] = $tab ++;
// 					$atributos ["columnas"] = 1;
// 					$atributos ["anchoCaja"] = "12";
// 					$atributos ["estilo"] = "textoIzquierda";
// 					$atributos ["anchoEtiqueta"] = 0;
// 					$atributos ["tamanno"] = 500000;
// 					$atributos ["validar"] = " ";
// 					$atributos ["estilo"] = "file";
// 					$atributos ["etiqueta"] = $this->lenguaje->getCadena ( $esteCampo );
// 					$atributos ["bootstrap"] = true;
// 					$tab ++;
// 					// $atributos ["valor"] = $valorCodificado;
// 					$atributos = array_merge ( $atributos );
// 					echo $this->miFormulario->campoCuadroTexto ( $atributos );
// 					unset ( $atributos );
// 				}
				
// 				$esteCampo = 'ciudad';
// 				$atributos ['nombre'] = $esteCampo;
// 				$atributos ['tipo'] = "text";
// 				$atributos ['id'] = $esteCampo;
// 				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 				$atributos ["etiquetaObligatorio"] = true;
// 				$atributos ['tab'] = $tab ++;
// 				$atributos ['anchoEtiqueta'] = 2;
// 				$atributos ['estilo'] = "bootstrap";
// 				$atributos ['evento'] = '';
// 				$atributos ['deshabilitado'] = false;
// 				$atributos ['readonly'] = false;
// 				$atributos ['columnas'] = 1;
// 				$atributos ['tamanno'] = 1;
// 				$atributos ['placeholder'] = "Ingrese Ciudad de Expedición de la Identificación";
// 				if (isset ( $_REQUEST [$esteCampo] )) {
// 					$atributos ['valor'] = $_REQUEST [$esteCampo];
// 				} else {
// 					$atributos ['valor'] = '';
// 				}
// 				$atributos ['ajax_function'] = "";
// 				$atributos ['ajax_control'] = $esteCampo;
// 				$atributos ['limitar'] = false;
// 				$atributos ['anchoCaja'] = 10;
// 				$atributos ['miEvento'] = '';
// 				$atributos ['validar'] = 'required';
// 				// Aplica atributos globales al control
// 				$atributos = array_merge ( $atributos, $atributosGlobales );
// 				echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 				unset ( $atributos );
				
// 				$esteCampo = 'ciudad_firma';
// 				$atributos ['nombre'] = $esteCampo;
// 				$atributos ['tipo'] = "text";
// 				$atributos ['id'] = $esteCampo;
// 				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
// 				$atributos ["etiquetaObligatorio"] = true;
// 				$atributos ['tab'] = $tab ++;
// 				$atributos ['anchoEtiqueta'] = 2;
// 				$atributos ['estilo'] = "bootstrap";
// 				$atributos ['evento'] = '';
// 				$atributos ['deshabilitado'] = false;
// 				$atributos ['readonly'] = false;
// 				$atributos ['columnas'] = 1;
// 				$atributos ['tamanno'] = 1;
// 				$atributos ['placeholder'] = "Ingrese Ciudad de firma acta de entrega";
// 				if (isset ( $_REQUEST [$esteCampo] )) {
// 					$atributos ['valor'] = $_REQUEST [$esteCampo];
// 				} else {
// 					$atributos ['valor'] = '';
// 				}
// 				$atributos ['ajax_function'] = "";
// 				$atributos ['ajax_control'] = $esteCampo;
// 				$atributos ['limitar'] = false;
// 				$atributos ['anchoCaja'] = 10;
// 				$atributos ['miEvento'] = '';
// 				$atributos ['validar'] = 'required';
// 				// Aplica atributos globales al control
// 				$atributos = array_merge ( $atributos, $atributosGlobales );
// 				echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
// 				unset ( $atributos );
				
				{
					
					$esteCampo = 'Agrupacion';
					$atributos ['id'] = $esteCampo;
					$atributos ['leyenda'] = "Firmas Interesados";
					// echo $this->miFormulario->agrupacion('inicio', $atributos);
					unset ( $atributos );
					{
					}
					
					// echo $this->miFormulario->agrupacion('fin');
					unset ( $atributos );
					// ------------------Division para los botones-------------------------
					$atributos ["id"] = "botones";
					$atributos ["estilo"] = "marcoBotones";
					$atributos ["estiloEnLinea"] = "display:block;";
					echo $this->miFormulario->division ( "inicio", $atributos );
					unset ( $atributos );
					{
						
						// -----------------CONTROL: Botón ----------------------------------------------------------------
						$esteCampo = 'botonGuardar';
						$atributos ["id"] = $esteCampo;
						$atributos ["tabIndex"] = $tab;
						$atributos ["tipo"] = 'boton';
						// submit: no se coloca si se desea un tipo button genérico
						$atributos ['submit'] = true;
						$atributos ["simple"] = true;
						$atributos ["estiloMarco"] = '';
						$atributos ["estiloBoton"] = 'default';
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
					}
					// ------------------Fin Division para los botones-------------------------
					echo $this->miFormulario->division ( "fin" );
					unset ( $atributos );
				}
				echo $this->miFormulario->agrupacion ( 'fin' );
				unset ( $atributos );
			}
			
			{
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
				// $valorCodificado .= "&opcion=generarCertificacion";
				$valorCodificado .= "&opcion=guardarInformacion";
				$valorCodificado .= "&id_beneficiario=" . $_REQUEST ['id'];
				$valorCodificado .= "&tipo_beneficiario=" . $infoBeneficiario['tipo_beneficiario'];
				$valorCodificado .= "&numero_contrato=" . $infoBeneficiario['numero_contrato'];
				$valorCodificado .= "&direccion=" . $infoBeneficiario['direccion'] . " " . $anexo_dir;
				$valorCodificado .= "&estrato_socioeconomico=" . $infoBeneficiario['estrato'];
				
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
				
				// ----------------INICIO CONTROL: Ventana Modal Mapa Geolocalización---------------------------------
				
				$atributos ['tipoEtiqueta'] = 'inicio';
				$atributos ['titulo'] = 'Geolocalización';
				$atributos ['id'] = 'myModal';
				echo $this->miFormulario->modal ( $atributos );
				unset ( $atributos );
				
				// ----------------INICIO CONTROL: Mapa--------------------------------------------------------
				
				echo '<div id="map-canvas" class="text-center"></div>';
				
				// ----------------FIN CONTROL: Mapa--------------------------------------------------------
				
				// ----------------INICIO CONTROL: Campo Texto Geolocalización------------------------------
				
				$esteCampo = 'geomodal';
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = "text";
				$atributos ['id'] = $esteCampo;
				$atributos ['etiqueta'] = ' ';
				$atributos ["etiquetaObligatorio"] = true;
				$atributos ['tab'] = $tab ++;
				$atributos ['anchoEtiqueta'] = 0;
				$atributos ['evento'] = '';
				$atributos ['estilo'] = "bootstrap";
				$atributos ['deshabilitado'] = false;
				$atributos ['columnas'] = 1;
				$atributos ['tamanno'] = 1;
				$atributos ['placeholder'] = "";
				$atributos ['valor'] = "";
				$atributos ['minimo'] = "1";
				$atributos ['ajax_function'] = "";
				$atributos ['ajax_control'] = $esteCampo;
				$atributos ['limitar'] = false;
				$atributos ['anchoCaja'] = 12;
				$atributos ['miEvento'] = '';
				// $atributos ['validar'] = 'required';
				// Aplica atributos globales al control
				echo $this->miFormulario->campoCuadroTextoBootstrap ( $atributos );
				unset ( $atributos );
				
				// ----------------FIN CONTROL: Campo Texto Geolocalización------------------------------
				
				// -----------------CONTROL: Botón ----------------------------------------------------------------
				$esteCampo = 'botonAgregarLocalizacion';
				$atributos ["id"] = $esteCampo;
				$atributos ["tabIndex"] = $tab;
				$atributos ["tipo"] = 'boton';
				$atributos ["basico"] = false;
				// submit: no se coloca si se desea un tipo button genérico
				$atributos ['submit'] = true;
				$atributos ["estiloMarco"] = 'text-center';
				$atributos ["estiloBoton"] = 'default';
				$atributos ["block"] = false;
				$atributos ['deshabilitado'] = true;
				
				// verificar: true para verificar el formulario antes de pasarlo al servidor.
				$atributos ["verificar"] = '';
				$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
				$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
				$tab ++;
				
				// Aplica atributos globales al control
				// $atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoBotonBootstrapHtml ( $atributos );
				unset ( $atributos );
				// -----------------FIN CONTROL: Botón -----------------------------------------------------------
				
				$atributos ['tipoEtiqueta'] = 'fin';
				echo $this->miFormulario->modal ( $atributos );
				unset ( $atributos );
				
				// -----------------FIN CONTROL: Ventana Modal Geolocalización -----------------------------------------------------------
				
				echo '  <script>
                    var markers = [];
					function initMap() {
					    var map = new google.maps.Map(document.getElementById("map-canvas"), {
					        center: {lat: 4.6482837, lng: -74.2478939},
					        zoom: 6
					    });
					    var infoWindow = new google.maps.InfoWindow({map: map});
		
					     if (navigator.geolocation) {
					         navigator.geolocation.getCurrentPosition(function(position) {
					         var pos = {
					                 lat: position.coords.latitude,
					                 lng: position.coords.longitude
					         };
		
					         infoWindow.setPosition(pos);
					         infoWindow.setContent("Localización Encontrada.");
					         map.setCenter(pos);
					         }, function() {
					         handleLocationError(true, infoWindow, map.getCenter());
					         });
						 } else {
						         // Browser doesnt support Geolocation
						         handleLocationError(false, infoWindow, map.getCenter());
						 }
		
                    }
		
					function handleLocationError(browserHasGeolocation, infoWindow, pos) {
					    infoWindow.setPosition(pos);
					    infoWindow.setContent(browserHasGeolocation ?
					          "Error: The Geolocation service failed." :
					          "Error: Your browser doesn\'t support geolocation.");
					}
                </script>
                <script async defer
                    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDgAHnG5AICmnNuBCpu75evMTBr4ZU3i60&callback=initMap">
                </script>';
				// -----------------INICIO CONTROL: Ventana Modal Mensaje -----------------------------------------------------------
			}
		}
		
		// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
		// Se debe declarar el mismo atributo de marco con que se inició el formulario.
		$atributos ['marco'] = true;
		$atributos ['tipoEtiqueta'] = 'fin';
		echo $this->miFormulario->formulario ( $atributos );
	}
	public function mensaje() {
		switch ($_REQUEST ['mensaje']) {
			case 'inserto' :
				$estilo_mensaje = 'success'; // information,warning,error,validation
				$atributos ["mensaje"] = 'Requisitos Correctamente Validados<br>Se ha Habilitado la Opcion de Descargar Borrador del Contrato';
				break;
			
			case 'noinserto' :
				$estilo_mensaje = 'error'; // information,warning,error,validation
				$atributos ["mensaje"] = 'Error al validar los Requisitos.<br>Verifique los Documentos de Requisitos';
				break;
			
			default :
				// code...
				break;
		}
		// ------------------Division para los botones-------------------------
		$atributos ['id'] = 'divMensaje';
		$atributos ['estilo'] = 'marcoBotones';
		echo $this->miFormulario->division ( "inicio", $atributos );
		
		// -------------Control texto-----------------------
		$esteCampo = 'mostrarMensaje';
		$atributos ["tamanno"] = '';
		$atributos ["etiqueta"] = '';
		$atributos ["estilo"] = $estilo_mensaje;
		$atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
		echo $this->miFormulario->campoMensaje ( $atributos );
		unset ( $atributos );
		
		// ------------------Fin Division para los botones-------------------------
		echo $this->miFormulario->division ( "fin" );
		unset ( $atributos );
	}
}

$miSeleccionador = new Certificado ( $this->lenguaje, $this->miFormulario, $this->sql );

$miSeleccionador->edicionCertificado ();

?>
