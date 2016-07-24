<?php
//Se establece el espacio de nombre
namespace development\saraFormCreator\formulario;
// Se verifica si el usuario está autorizado
if (!isset($GLOBALS['autorizado'])) {
	include ('../index.php');
	exit();
}

// Se llaman la clase del elemento form creator
include_once ($this -> ruta . "/builder/formCreator.class.php");
use development\saraFormCreator\builder\formCreator;

class saraFormCreator {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	function __construct($lenguaje, $formulario) {
		$this -> miConfigurador = \Configurador::singleton();

		$this -> miConfigurador -> fabricaConexiones -> setRecursoDB('principal');

		$this -> lenguaje = $lenguaje;

		$this -> miFormulario = $formulario;
	}

	function seleccionarForm() {
		// Rescatar los datos de este bloque
		$esteBloque = $this -> miConfigurador -> getVariableConfiguracion("esteBloque");

		// ---------------- SECCION: Parámetros Globales de SARA Form Creator ----------------------------------
		//Se establecen parámetros que se envian en la creación del elemento
		$atributos['contenedores'] = array('formulario', 'division');
		$atributos['componentesBasicos'] = array('campoBoton', 'campoCuadroTexto', 'campoMensaje');
		$atributos['otrosComponentes'] = array(
			'campoBotonRadial', 
			'campoCuadroLista', 
			'campoCuadroSeleccion',
			'campoFecha',
			'campoImagen',
			'campoTextArea',
			'cuadro_lista',
			'cuadro_texto',
			'enlace',
			'enlaceWiki',
			'listaNoOrdenada',
			'marcoAgrupacion',
			'recaptcha'
		);
		
		//Se crea un nuevo objeto a partir de la clase formCreator y se imprime el contenido
		//elemento a partir de los parámetros $atributos
		$formCreator = new formCreator;
		echo $formCreator -> formulario($atributos);
		//---------------- END: Parámetros Globales de SARA Form Creator ----------------------------------
	}

	function mensaje() {

		// Si existe algun tipo de error en el login aparece el siguiente mensaje
		$mensaje = $this -> miConfigurador -> getVariableConfiguracion('mostrarMensaje');
		$this -> miConfigurador -> setVariableConfiguracion('mostrarMensaje', null);

		if ($mensaje) {
			$tipoMensaje = $this -> miConfigurador -> getVariableConfiguracion('tipoMensaje');
			if ($tipoMensaje == 'json') {

				$atributos['mensaje'] = $mensaje;
				$atributos['json'] = true;
			} else {
				$atributos['mensaje'] = $this -> lenguaje -> getCadena($mensaje);
			}
			// ------------------Division para los botones-------------------------
			$atributos['id'] = 'divMensaje';
			$atributos['estilo'] = 'marcoBotones';
			echo $this -> miFormulario -> division("inicio", $atributos);

			// -------------Control texto-----------------------
			$esteCampo = 'mostrarMensaje';
			$atributos["tamanno"] = '';
			$atributos["estilo"] = 'information';
			$atributos["etiqueta"] = '';
			$atributos["columnas"] = '';
			// El control ocupa 47% del tamaño del formulario
			echo $this -> miFormulario -> campoMensaje($atributos);
			unset($atributos);

			// ------------------Fin Division para los botones-------------------------
			echo $this -> miFormulario -> division("fin");
		}
	}

}

$miSeleccionador = new saraFormCreator($this -> lenguaje, $this -> miFormulario);

$miSeleccionador -> mensaje();

$miSeleccionador -> seleccionarForm();
?>