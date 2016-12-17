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
		
// 		$cadenaSql = $this->miSql->getCadenaSql ( 'consultaInformacionCertificado' );
		
// 		$infoCertificado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ) [0];
		
// 		$cadenaSql = $this->miSql->getCadenaSql ( 'consultaInformacionBeneficiario' );
// 		$infoBeneficiario = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
// 		$infoBeneficiario = $infoBeneficiario [0];
		
// 		{
// 			$arreglo = array (
// 					'nombres' => $infoBeneficiario ['nombre'],
// 					'primer_apellido' => $infoBeneficiario ['primer_apellido'],
// 					'segundo_apellido' => $infoBeneficiario ['segundo_apellido'],
// 					'tipo_documento' => $infoBeneficiario ['tipo_documento'],
// 					'numero_identificacion' => $infoBeneficiario ['identificacion'],
// 					'direccion' => $infoBeneficiario ['direccion'],
// 					'departamento' => $infoBeneficiario ['nombre_departamento'],
// 					'municipio' => $infoBeneficiario ['nombre_municipio'],
// 					'urbanizacion' => $infoBeneficiario ['nombre_urbanizacion'],
// 					'estrato' => $infoBeneficiario ['estrato'],
// 					'tipo_beneficiario' => $infoBeneficiario ['tipo_beneficiario'],
// 			);
			
// 			$_REQUEST = array_merge ( $_REQUEST, $arreglo );
			
// 		}
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
		$atributos ['estilo'] = 'main';
		$atributos ['marco'] = true;
		$tab = 1;
		// ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
		
		echo "<div class='modalLoad'></div>";
		
		// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
		$atributos ['tipoEtiqueta'] = 'inicio';
		echo $this->miFormulario->formulario ( $atributos );
		{
			
			{
				
				echo ' <div class="main">
      			<div class="row">
        			<div class="col-xs-14 col-lg-11">
          				<div class="panel panel-primary">
            				<div class="panel-body">
								<div class="row">';
				 
				$esteCampo = 'Agrupacion';
				$atributos['id'] = $esteCampo;
				$atributos['leyenda'] = "INFORMACIÓN DE SERVICIO DE BANADA ANCHA INSTALADO";
				
				echo $this->miFormulario->agrupacion('inicio', $atributos);
				unset($atributos);
				
				// ------------------Division para los botones-------------------------
				$atributos["id"] = "espacio_trabajo";
				$atributos["estilo"] = " ";
				$atributos["estiloEnLinea"] = "";
				echo $this->miFormulario->division("inicio", $atributos);
				unset($atributos);
				
				
				echo '<div class="panel-group" id="accordion">
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
						
						$esteCampo = 'mac_esc';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = "text";
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['tab'] = $tab ++;
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
// 						$atributos ['anchoEtiqueta'] =3;
// 						$atributos ['estilo'] = "bootstrap";
// 						$atributos ['evento'] = '';
// 						$atributos ['deshabilitado'] = true;
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
// 						$atributos ['anchoCaja'] =9;
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
// 						$atributos ['anchoEtiqueta'] =3;
// 						$atributos ['estilo'] = "bootstrap";
// 						$atributos ['evento'] = '';
// 						$atributos ['deshabilitado'] = true;
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
// 						$atributos ['anchoCaja'] =9;
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
// 						$atributos ['anchoEtiqueta'] =3;
// 						$atributos ['estilo'] = "bootstrap";
// 						$atributos ['evento'] = '';
// 						$atributos ['deshabilitado'] = true;
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
// 						$atributos ['anchoCaja'] =9;
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
// 						$atributos ['anchoEtiqueta'] =3;
// 						$atributos ['estilo'] = "bootstrap";
// 						$atributos ['evento'] = '';
// 						$atributos ['deshabilitado'] = true;
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
// 						$atributos ['anchoCaja'] =9;
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
// 						$atributos ['anchoEtiqueta'] =3;
// 						$atributos ['estilo'] = "bootstrap";
// 						$atributos ['evento'] = '';
// 						$atributos ['deshabilitado'] = true;
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
// 						$atributos ['anchoCaja'] =9;
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
// 						$atributos ['anchoEtiqueta'] =3;
// 						$atributos ['estilo'] = "bootstrap";
// 						$atributos ['evento'] = '';
// 						$atributos ['deshabilitado'] = true;
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
// 						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
						$atributos ['readonly'] = true;
						$atributos ['columnas'] = 1;
						$atributos ['tamanno'] = 1;
						$atributos ['placeholder'] = "";
						$atributos ['valor'] = 'Mbps';
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
						$atributos ['readonly'] = true;
						$atributos ['columnas'] = 1;
						$atributos ['tamanno'] = 1;
						$atributos ['placeholder'] = "";
						$atributos ['valor'] = 'Mbps';
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
						$atributos ['readonly'] = true;
						$atributos ['columnas'] = 1;
						$atributos ['tamanno'] = 1;
						$atributos ['placeholder'] = "";
						$atributos ['valor'] = 'ms';
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
				                            <a data-toggle="collapse" data-parent="#accordion4A" href="#collapse4D">Ping3</a>
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
						$atributos ['anchoEtiqueta'] =3;
						$atributos ['estilo'] = "bootstrap";
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = true;
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
						$atributos ['anchoCaja'] =9;
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
				echo '</div></div>
                    </div>
                </div>';
				
			}
			
			echo '
					</div>
					</div>
					</div>
		            </div>
        			</div>';
			
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
				
				/**
				 * SARA permite que los nombres de los campos sean dinámicos.
				 * Para ello utiliza la hora en que es creado el formulario para
				 * codificar el nombre de cada campo.
				 */
				$valorCodificado .= "&campoSeguro=" . $_REQUEST ['tiempo'];
				// Paso3: codificar la cadena resultante
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
				
			}
		}
		// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
		// Se debe declarar el mismo atributo de marco con que se inició el formulario.
		$atributos ['marco'] = true;
		$atributos ['tipoEtiqueta'] = 'fin';
		echo $this->miFormulario->formulario ( $atributos );
	}
}

$miSeleccionador = new Certificado ( $this->lenguaje, $this->miFormulario, $this->sql );

$miSeleccionador->edicionCertificado ();

?>
