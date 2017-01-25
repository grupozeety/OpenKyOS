<?php

namespace facturacion\calculoFactura\frontera;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

/**
 * IMPORTANTE: Este formulario está utilizando jquery.
 * Por tanto en el archivo ready.php se declaran algunas funciones js
 * que lo complementan.
 */
class Consultar {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public function __construct($lenguaje, $formulario, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		
		$this->lenguaje = $lenguaje;
		
		$this->miFormulario = $formulario;
		
		$this->sql = $sql;
	}
	public function seleccionarForm() {
		// Rescatar los datos de este bloque
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		// ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
		
		$atributosGlobales ['campoSeguro'] = 'true';
		
		$_REQUEST ['tiempo'] = time ();
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->sql->getCadenaSql ( 'consultarRolUsuario', $_REQUEST ['id_beneficiario'] );
		$rolusuario = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
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
		$atributos ['tipoEtiqueta'] = 'inicio';
		echo $this->miFormulario->formulario ( $atributos );
		{
			
			{
				$esteCampo = 'Agrupacion';
				$atributos ['id'] = $esteCampo;
				$atributos ['leyenda'] = "Asociar Periodo de Facturación a Rol";
				echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
				unset ( $atributos );
				
				{
					/**
					 * echo '<table id="example" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
					 * <thead>
					 * <tr>
					 * <th><center>Beneficiario<center></th>
					 * <th><center>Rol<center></th>
					 * <th><center>Periodo<center></th>
					 * <th><center>Inicio Periodo<center></th>
					 * </tr>
					 * </thead>
					 * <tfoot>
					 * <tr>
					 * <th><center>Beneficiario<center></th>
					 * <th><center>Rol<center></th>
					 * <th><center>Periodo<center></th>
					 * <th><center>Inicio Periodo<center></th>
					 * </tr>
					 * </tfoot>
					 * </table>';
					 */
					if ($rolusuario != FALSE) {
						foreach ( $rolusuario as $key => $values ) {
							
							$esteCampo = 'Agrupacion';
							$atributos ['id'] = $esteCampo;
							$atributos ['leyenda'] = "Rol " . $values ['descripcion'];
							echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
							unset ( $atributos );
							
							{
								
								$esteCampo = $values['id_rol'].'_periodo';
								$atributos ['nombre'] = $esteCampo;
								$atributos ['id'] = $esteCampo;
								$atributos ['etiqueta'] = 'Periodo de Facturación';
								$atributos ["etiquetaObligatorio"] = true;
								$atributos ['tab'] = $tab ++;
								$atributos ['anchoEtiqueta'] = 2;
								$atributos ['evento'] = '';
								$atributos ['seleccion'] = 1;
								$atributos ['deshabilitado'] = false;
								$atributos ['columnas'] = 1;
								$atributos ['tamanno'] = 1;
								$atributos ['ajax_function'] = "";
								$atributos ['ajax_control'] = $esteCampo;
								$atributos ['estilo'] = "bootstrap";
								$atributos ['limitar'] = false;
								$atributos ['anchoCaja'] = 3;
								$atributos ['miEvento'] = '';
								// $atributos ['validar'] = '';
								$atributos ['cadena_sql'] = $this->sql->getCadenaSql ( "parametroPeriodos" );
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
								
								$esteCampo = $values['id_rol'].'_cantidad';
								$atributos['nombre'] = $esteCampo;
								$atributos['tipo'] = "text";
								$atributos['id'] = $esteCampo;
								$atributos['etiqueta'] = 'Cantidad';
								$atributos["etiquetaObligatorio"] = true;
								$atributos['tab'] = $tab++;
								$atributos['anchoEtiqueta'] = 2;
								$atributos['estilo'] = "bootstrap";
								$atributos['evento'] = '';
								$atributos['deshabilitado'] = false;
								$atributos['readonly'] = false;
								$atributos['columnas'] = 1;
								$atributos['tamanno'] = 1;
								$atributos['placeholder'] = "Ingrese cantidad de la unidad";
								$atributos['valor'] = "";
								$atributos['ajax_function'] = "";
								$atributos['ajax_control'] = $esteCampo;
								$atributos['limitar'] = false;
								$atributos['anchoCaja'] = 3;
								$atributos['miEvento'] = '';
								$atributos['validar'] = 'required';
								// Aplica atributos globales al control
								$atributos = array_merge($atributos, $atributosGlobales);
								echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
								unset($atributos);
								
								$esteCampo = $values['id_rol'].'_fecha';
								$atributos['nombre'] = $esteCampo;
								$atributos['tipo'] = "text";
								$atributos['id'] = $esteCampo;
								$atributos['etiqueta'] = 'Fecha Inicio Facturación';
								$atributos["etiquetaObligatorio"] = true;
								$atributos['tab'] = $tab++;
								$atributos['anchoEtiqueta'] = 2;
								$atributos['estilo'] = "bootstrap";
								$atributos['evento'] = '';
								$atributos['deshabilitado'] = false;
								$atributos['readonly'] = false;
								$atributos['columnas'] = 1;
								$atributos['tamanno'] = 1;
								$atributos['placeholder'] = "Seleccione Fecha Inicio";
								$atributos['valor'] = "";
								$atributos['ajax_function'] = "";
								$atributos['ajax_control'] = $esteCampo;
								$atributos['limitar'] = false;
								$atributos['anchoCaja'] = 5;
								$atributos['miEvento'] = '';
								$atributos['validar'] = 'required';
								// Aplica atributos globales al control
								$atributos = array_merge($atributos, $atributosGlobales);
								echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
								unset($atributos);
							}
							echo $this->miFormulario->agrupacion ( 'fin' );
							unset ( $atributos );
						}
					}
					// ------------------Division para los botones-------------------------
					$atributos ["id"] = "botones";
					$atributos ["estilo"] = "marcoBotones";
					$atributos ["estiloEnLinea"] = "display:block;";
					echo $this->miFormulario->division ( "inicio", $atributos );
					unset ( $atributos );
					{
						// -----------------CONTROL: Botón ----------------------------------------------------------------
						$esteCampo = 'botonCalcular';
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
				$valorCodificado .= "&opcion=calcularFactura";
				
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
			}
		}
		
		// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
		// Se debe declarar el mismo atributo de marco con que se inició el formulario.
		$atributos ['marco'] = true;
		$atributos ['tipoEtiqueta'] = 'fin';
		echo $this->miFormulario->formulario ( $atributos );
	}
	public function mensaje() {
		
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
			// ------------------Division para los botones-------------------------
			$atributos ['id'] = 'divMensaje';
			$atributos ['estilo'] = 'marcoBotones';
			echo $this->miFormulario->division ( "inicio", $atributos );
			
			// -------------Control texto-----------------------
			$esteCampo = 'mostrarMensaje';
			$atributos ["tamanno"] = '';
			$atributos ["estilo"] = 'information';
			$atributos ["etiqueta"] = '';
			$atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
			echo $this->miFormulario->campoMensaje ( $atributos );
			unset ( $atributos );
			
			// ------------------Fin Division para los botones-------------------------
			echo $this->miFormulario->division ( "fin" );
		}
	}
}

$miSeleccionador = new Consultar ( $this->lenguaje, $this->miFormulario, $this->sql );

$miSeleccionador->mensaje ();

$miSeleccionador->seleccionarForm ();

?>
