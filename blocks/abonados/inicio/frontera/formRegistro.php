<?php

namespace cambioClave\formRegistro;

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
		
		// Rescatar los datos de este bloque
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		$miPaginaActual = $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		
		$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
		$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$rutaBloque .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/";
		$rutaBloque .= $esteBloque ['grupo'] . '/' . $esteBloque ['nombre'];
		
		$this->_rutaBloque = $rutaBloque;
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
		$atributos ['estilo'] = 'main';
		$atributos ['marco'] = true;
		$tab = 1;
		// ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
		
		// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
		$atributos ['tipoEtiqueta'] = 'inicio';
		echo $this->miFormulario->formularioBootstrap ( $atributos );
		unset ( $atributos );
		
		$cadena_sql = $this->miSql->getCadenaSql ( "consultarColor" );
		$colores = $esteRecursoDB->ejecutarAcceso ( $cadena_sql, "busqueda" ) [0];
		
		if ($colores) {
			$esteCampo = 'color1';
			$atributos ["id"] = $esteCampo; // No cambiar este nombre
			$atributos ["tipo"] = "hidden";
			$atributos ['valor'] = $colores ['color1'];
			$atributos ['estilo'] = '';
			$atributos ["obligatorio"] = false;
			$atributos ['marco'] = true;
			$atributos ["etiqueta"] = "";
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTexto ( $atributos );
			unset ( $atributos );
			
			$esteCampo = 'color2';
			$atributos ["id"] = $esteCampo; // No cambiar este nombre
			$atributos ["tipo"] = "hidden";
			$atributos ['valor'] = $colores ['color2'];
			$atributos ['estilo'] = '';
			$atributos ["obligatorio"] = false;
			$atributos ['marco'] = true;
			$atributos ["etiqueta"] = "";
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTexto ( $atributos );
			unset ( $atributos );
			
			$esteCampo = 'color3';
			$atributos ["id"] = $esteCampo; // No cambiar este nombre
			$atributos ["tipo"] = "hidden";
			$atributos ['valor'] = $colores ['color3'];
			$atributos ['estilo'] = '';
			$atributos ["obligatorio"] = false;
			$atributos ['marco'] = true;
			$atributos ["etiqueta"] = "";
			
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroTexto ( $atributos );
			unset ( $atributos );
		}
		
		echo ' <div class="main">
      			<div class="row">
        			<div class="col-xs-14 col-lg-11">
          				<div class="panel panel-primary">
            				<div class="panel-body">
								<div class="row">';
		
		echo '<div class="well well-lg superior">
				<div class="row">
  					<div class="col-md-4">Hola, Pepito</div>
  					<div align="center" id="bannerReloj" class="col-md-4"></div>
  					<div align="right" class="col-md-4"><span id="bgcolor" class="glyphicon glyphicon-cog" aria-hidden="true"></span></div>
			  	</div>
		     </div>
				';
		
		echo ' <div class="col-lg-4 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-blue">
            <div class="inner">
              <h4>44</h4><br>

              <p>Número de Contrato</p>
            </div>
            <div class="icon">
              <i class="ion ion-person"></i>
            </div>
          </div>
        </div>';
		
		echo ' <div class="col-lg-4 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h4>23/12/1 2016</h4><br>
		
              <p>Fecha Limite de Pago</p>
            </div>
            <div class="icon">
              <i class="ion ion-calendar"></i>
            </div>
          </div>
        </div>';
		
		echo ' <div class="col-lg-4 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h4>4 MB</h4><br>
		
              <p>Velocidad del Servicio</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
          </div>
        </div>';
		
		echo '	<div class="home-doctors  clearfix">

					        
					        <div class="row">



<!-- entry1 -->
						            <div class="col-lg-12 col-md-12 col-sm-12  text-center doc-item">
				                        <div class="common-doctor animated fadeInUp clearfix ae-animation-fadeInUp">

					                        <ul class="list-inline social-lists animate">
												<li><a href="#"><i class="fa fa-skype"></i></a></li>
												<li><a href="#"><i class="fa fa-skype"></i></a></li>
												<li><a href="#"><i class="fa fa-twitter"></i></a></li>
												<li><a href="#"><i class="fa fa-facebook"></i></a></li>
											</ul>

	                                        <figure>
								                    <img width="100%" height="200" src="' . $rutaBloque . '/frontera/css/imagen/finger-769300_1920.jpg" class="doc-img animate attachment-gallery-post-single wp-post-image" alt="doctor-2"> 
								            </figure>

						                    <div class="text-content">
						                        <h5>Redes Sociales</h5>
						                        <!-- <div class="for-border"></div> -->
						                    </div>
						                </div>
						            </div>









					                                            <div class="visible-sm clearfix margin-gap"></div>

					    </div>

					</div>';
		echo '
					</div>
					</div>
					</div>
		            </div>
        			</div>';
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
		$valorCodificado .= "&opcion=generarCertificacion";
		/**
		 * SARA permite que los nombres de los campos sean dinámicos.
		 * Para ello utiliza la hora en que es creado el formulario para
		 * codificar el nombre de cada campo.
		 */
		$valorCodificado .= "&campoSeguro=" . $_REQUEST ['tiempo'];
		// Paso 4: codificar la cadena resultante
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
		
		$atributos ['marco'] = true;
		$atributos ['tipoEtiqueta'] = 'fin';
		echo $this->miFormulario->formulario ( $atributos );
	}
	public function mensaje() {
		switch ($_REQUEST ['mensaje']) {
			
			case 'sucess' :
				$estilo_mensaje = 'success'; // information,warning,error,validation
				$mensa = explode ( "\n", $_REQUEST ['valor'] );
				$atributos ["mensaje"] = "";
				foreach ( $mensa as $m ) {
					$atributos ["mensaje"] .= $m . "<br>";
				}
				break;
			
			case 'error' :
				$estilo_mensaje = 'error'; // information,warning,error,validation
				$mensa = explode ( "\n", $_REQUEST ['valor'] );
				$atributos ["mensaje"] = "";
				foreach ( $mensa as $m ) {
					$atributos ["mensaje"] .= $m . "<br>";
				}
				
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

$miFormulario = new Formulario ( $this->lenguaje, $this->miFormulario, $this->sql );

$miFormulario->formulario ();

?>