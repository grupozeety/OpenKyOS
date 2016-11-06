<?php

namespace gestionBeneficiarios\generacionContrato\frontera;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

/**
 * IMPORTANTE: Este formulario está utilizando jquery.
 * Por tanto en el archivo ready.php se declaran algunas funciones js
 * que lo complementan.
 */
class Registrador {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public function __construct($lenguaje, $formulario, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		
		$this->lenguaje = $lenguaje;
		
		$this->miFormulario = $formulario;
		
		$this->miSql = $sql;
	}
	public function seleccionarForm() {
		$ruta = $_REQUEST ['ruta'];
		$idArchivo = $_REQUEST ['archivo'];
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		// ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
		
		$atributosGlobales ['campoSeguro'] = 'true';
		
		$_REQUEST ['tiempo'] = time ();
		
		$data = array (
				'tipo' => $_REQUEST ['tipo_beneficiario'],
				'codigo' => $_REQUEST ['tipologia'] 
		);
		
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultaRequisitosEspecificos', $data );
		$requisitos = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		if($_REQUEST['tipologia']='128'){
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultaRequisitosContrato');
			$requisitos = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		}
		
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
		
		// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
		$atributos ['tipoEtiqueta'] = 'inicio';
		echo $this->miFormulario->formulario ( $atributos );
		{
			
			$esteCampo = 'Agrupacion';
			$atributos ['id'] = $esteCampo;
			$atributos ['leyenda'] = "Detalles del Archivo";
			echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
			unset ( $atributos );
			
			{
				
				// ----------------INICIO CONTROL: Cambio imagen--------------------------------------------------------
				$esteCampo = $requisitos [0] ['codigo']; // Código documento
				$atributos ["id"] = $esteCampo; // No cambiar este nombre
				$atributos ["nombre"] = $esteCampo;
				$atributos ["tipo"] = "file";
				$atributos ["obligatorio"] = true;
				$atributos ["etiquetaObligatorio"] = false;
				$atributos ["tabIndex"] = $tab ++;
				$atributos ["columnas"] = 1;
				$atributos ["estilo"] = "textoIzquierda";
				$atributos ["anchoEtiqueta"] = 2;
				$atributos ["tamanno"] = 500000;
				$atributos ["etiqueta"] = "<b>" . $requisitos [0] ['codigo'] . "</b> " . $requisitos [0] ['descripcion'];
				if ($requisitos [0] ['obligatoriedad'] == 1) {
					$atributos ["etiqueta"] = "<b>" . $requisitos [0] ['codigo'] . "</b> " . $requisitos [0] ['descripcion'] . "<b> (*)</b>";
				}
				$atributos ["estilo"] = "file";
				$atributos ["anchoCaja"] = 3;
				
				$atributos ["bootstrap"] = true;
				// $atributos ["valor"] = $valorCodificado;
				$atributos = array_merge ( $atributos );
				echo $this->miFormulario->campoCuadroTexto ( $atributos );
				unset ( $atributos );
			}
			echo $this->miFormulario->agrupacion ( 'fin' );
			unset ( $atributos );
			
			// ----------------INICIO CONTROL: Archivo---------------------------
			// ------------------Division para los botones-------------------------
			$atributos ["id"] = "botones";
			$atributos ["estilo"] = "marcoBotones";
			$atributos ["estiloEnLinea"] = "display:block;";
			echo $this->miFormulario->division ( "inicio", $atributos );
			unset ( $atributos );
			{
				// -----------------CONTROL: Botón ----------------------------------------------------------------
				$esteCampo = 'verificar';
				$atributos ["id"] = $esteCampo;
				$atributos ["tabIndex"] = $tab;
				$atributos ["tipo"] = 'boton';
				// submit: no se coloca si se desea un tipo button genérico
				$atributos ['submit'] = true;
				$atributos ["simple"] = true;
				$atributos ["columnas"] = 1;
				$atributos ["estiloMarco"] = '';
				$atributos ["estiloBoton"] = 'jqueryui';
				$atributos ["block"] = false;
				// verificar: true para verificar el formulario antes de pasarlo al servidor.
				$atributos ["verificar"] = '';
				$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
				$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
				$tab ++;
				
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoBoton ( $atributos );
				unset ( $atributos );
				// -----------------FIN CONTROL: Botón -----------------------------------------------------------
			}
			// ------------------Fin Division para los botones-------------------------
			echo $this->miFormulario->division ( "fin" );
			unset ( $atributos );
			
			$atributos ["id"] = "botones";
			$atributos ["estilo"] = "marcoBotones";
			$atributos ["estiloEnLinea"] = "display:block;";
			echo $this->miFormulario->division ( "inicio", $atributos );
			unset ( $atributos );
			{
				// -----------------CONTROL: Botón ----------------------------------------------------------------
				$esteCampo = 'actualizar';
				$atributos ["id"] = $esteCampo;
				$atributos ["tabIndex"] = $tab;
				$atributos ["tipo"] = 'boton';
				// submit: no se coloca si se desea un tipo button genérico
				$atributos ['submit'] = true;
				$atributos ["simple"] = true;
				$atributos ["estiloMarco"] = '';
				$atributos ["columnas"] = 1;
				$atributos ["estiloBoton"] = 'jqueryui';
				$atributos ["block"] = false;
				// verificar: true para verificar el formulario antes de pasarlo al servidor.
				$atributos ["verificar"] = '';
				$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
				$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
				$tab ++;
				
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoBoton ( $atributos );
				unset ( $atributos );
				// -----------------FIN CONTROL: Botón -----------------------------------------------------------
			}
			// ------------------Fin Division para los botones-------------------------
			echo $this->miFormulario->division ( "fin" );
			unset ( $atributos );
			// ----------------INICIO CONTROL: Archivo---------------------------
			
			{
				
				$esteCampo = 'imagen';
				$atributos ['id'] = $esteCampo;
				$atributos ['leyenda'] = "Archivo Seleccionado";
				echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
				unset ( $atributos );
				
				$tipo = getimagesize ( str_replace ( "\\", "", $ruta ) );
				
				if ($tipo != false) {
					
					$atributos ['imagen'] = str_replace ( "\\", "", $_REQUEST ['ruta'] );
					$atributos ['estilo'] = '';
					$atributos ['etiqueta'] = '';
					$atributos ['borde'] = '';
					$atributos ['ancho'] = '100%';
					$atributos ['alto'] = '';
					$atributos ['enlace'] = str_replace ( "\\", "", $ruta );
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoImagen ( $atributos );
					unset ( $atributos );
				} else {
					$esteCampo = 'imagen';
					$atributos ['id'] = $esteCampo;
					$atributos ['leyenda'] = "Archivo Seleccionado";
					$atributos ['imagen'] = str_replace ( "\\", "", $_REQUEST ['ruta'] );
					$atributos ['estilo'] = '';
					$atributos ['etiqueta'] = '';
					$atributos ['borde'] = '';
					$atributos ['enlace'] = str_replace ( "\\", "", $ruta );
					$atributos ['enlaceTexto'] = "<b>" . $requisitos [0] ['codigo'] . "</b> " . $requisitos [0] ['descripcion'];
					
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->enlace ( $atributos );
					unset ( $atributos );
				}
				echo $this->miFormulario->agrupacion ( 'fin' );
				unset ( $atributos );
			}
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
			$valorCodificado .= "&opcion=modificarArchivo";
			$valorCodificado .= "&id_archivo=" . $idArchivo;
			$valorCodificado .= "&tipologia=" . $_REQUEST ['tipologia'];
			$valorCodificado .= "&id_beneficiario=" . $_REQUEST ['id_beneficiario'];
			$valorCodificado .= "&tipo_beneficiario=" . $_REQUEST ['tipo_beneficiario'];
			$valorCodificado .= "&proceso=verificarRequisitos";
			
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

$miSeleccionador = new Registrador ( $this->lenguaje, $this->miFormulario, $this->sql );

$miSeleccionador->mensaje ();

$miSeleccionador->seleccionarForm ();

?>
