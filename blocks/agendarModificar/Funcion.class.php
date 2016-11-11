<?php

namespace agendarModificar;

use agendarModificar\funcion\redireccion;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/builder/InspectorHTML.class.php");
include_once ("core/builder/Mensaje.class.php");
include_once ("core/crypto/Encriptador.class.php");

// Esta clase contiene la logica de negocio del bloque y extiende a la clase funcion general la cual encapsula los
// metodos mas utilizados en la aplicacion
// Para evitar redefiniciones de clases el nombre de la clase del archivo funcion debe corresponder al nombre del bloque
// en camel case precedido por la palabra Funcion
class Funcion {
	var $sql;
	var $funcion;
	var $lenguaje;
	var $ruta;
	var $miConfigurador;
	var $miInspectorHTML;
	var $error;
	var $miRecursoDB;
	var $crypto;
	// function verificarCampos() {
	// include_once ($this->ruta . "/funcion/verificarCampos.php");
	// if ($this->error == true) {
	// return false;
	// } else {
	// return true;
	// }
	// }
	
	function procesarAjax() {
		include_once ($this->ruta . "funcion/procesarAjax.php");
	}
	function cambiarAgenda() {
		include_once ($this->ruta . "/funcion/procesarParametros.php");
	}
	function action() {
		
		// Evitar que se ingrese codigo HTML y PHP en los campos de texto
		// Campos que se quieren excluir de la limpieza de código. Formato: nombreCampo1|nombreCampo2|nombreCampo3
		$excluir = "";
		$_REQUEST = $this->miInspectorHTML->limpiarPHPHTML ( $_REQUEST );
		
		// Aquí se coloca el código que procesará los diferentes formularios que pertenecen al bloque
		// aunque el código fuente puede ir directamente en este script, para facilitar el mantenimiento
		// se recomienda que aqui solo sea el punto de entrada para incluir otros scripts que estarán
		// en la carpeta funcion
		// Importante: Es adecuado que sea una variable llamada opcion o action la que guie el procesamiento:
		$_REQUEST = $this->miInspectorHTML->limpiarSQL ( $_REQUEST );
		// Realizar una validación específica para los campos de este formulario:
		// $validacion = $this->verificarCampos ();
		// if ($validacion == false) {
		// // Instanciar a la clase pagina con mensaje de correcion de datos
		// echo "Datos Incorrectos";
		// } else {
		// Validar las variables para evitar un tipo insercion de SQL
		// $this->Redireccionador( "exito" );
		// }
		/*
		 * Se realiza la decodificación de los campos "validador" de los 
		 * componentes del FormularioHtml. Se realiza la validación. En caso de que algún parámetro
		 * sea ingresado fuera de lo correspondiente en el campo "validador", este será ajustado
		 * (o convertido a) a un parámetro permisible o simplemente de no ser válido se devolverá 
		 * el valor false. Si lo que se quiere es saber si los parámetros son correctos o no, se
		 * puede introducir un tercer parámetro $arreglar, que es un parámetro booleano que indica,
		 * si es pertinente o no realizar un recorte de los datos "string" para que cumpla los requerimientos
		 * de longitud (tamaño) del campo.
		 */
		if(isset($_REQUEST['validadorCampos']) && !(isset($_REQUEST ["botonRegresar"]) && $_REQUEST ["botonRegresar"] == 'true')){
			$validadorCampos = $this->miInspectorHTML->decodificarCampos($_REQUEST['validadorCampos']);
			$respuesta = $this->miInspectorHTML->validacionCampos($_REQUEST,$validadorCampos,false,false);
			if ($respuesta != false){
				$_REQUEST = $respuesta;
			} else {
				//Lo que se desea hacer si los parámetros son inválidos
				$miPaginaActual = $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
				$variable = "pagina=accesoIncorrecto";
				$variable .= "&opcion=error";
				$variable .= "&paginaError=".$miPaginaActual;
				$variable .= "&parametros=".$this->miInspectorHTML->codificarCampos($_REQUEST);
				$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $variable );
				$url = $this->miConfigurador->configuracion ["host"] . $this->miConfigurador->configuracion ["site"] . "/index.php";
				$enlace = $this->miConfigurador->configuracion ['enlace'];				
				$redireccion = $url . $enlace . '=' . $variable;
				echo '<!DOCTYPE html><html><body></body><html><script>
				var form = document.createElement("form");
				form.setAttribute("method", "POST");
				form.setAttribute("action", "'.$url.'");
				var hiddenField = document.createElement("input");
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", "'.$enlace.'");
				hiddenField.setAttribute("value", "'.$variable.'");
				form.appendChild(hiddenField);
				document.body.appendChild(form);
				form.submit();
				</script>';
				return 0;
			}
		}
		
		if (isset ( $_REQUEST ['procesarAjax'] )) {
			$this->procesarAjax ();
		} else if (isset ( $_REQUEST ["opcion"] )) {
			
			switch ($_REQUEST ["opcion"]) {
				case 'cambiarAgenda' :
					$this->cambiarAgenda();
					break;
				
			}
		} else {
			echo "request opcion no existe";
		}
	}
	function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->miInspectorHTML = \InspectorHTML::singleton ();
		
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );
		
		$this->miMensaje = \Mensaje::singleton ();
		
		$conexion = "aplicativo";
		$this->miRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		if (! $this->miRecursoDB) {
			
			$this->miConfigurador->fabricaConexiones->setRecursoDB ( $conexion, "tabla" );
			$this->miRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		}
	}
	public function setRuta($unaRuta) {
		$this->ruta = $unaRuta;
	}
	function setSql($a) {
		$this->sql = $a;
	}
	function setFuncion($funcion) {
		$this->funcion = $funcion;
	}
	public function setLenguaje($lenguaje) {
		$this->lenguaje = $lenguaje;
	}
	public function setFormulario($formulario) {
		$this->formulario = $formulario;
	}
}

?>
