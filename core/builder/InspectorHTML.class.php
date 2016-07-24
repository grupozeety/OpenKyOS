<?php
use core\general\ValidadorCampos;
class InspectorHTML {

	private static $instance;

	// Constructor
	function __construct() {

	}

	public static function singleton() {

		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className();
		}
		return self::$instance;

	}

	function limpiarPHPHTML($arreglo, $excluir = "") {

		if ($excluir != "") {
			$variables = explode("|", $excluir);
		} else {
			$variables[0] = "";
		}

		foreach ($arreglo as $clave => $valor) {
			if (!in_array($clave, $variables)) {

				$arreglo[$clave] = strip_tags($valor);
			}
		}

		return $arreglo;

	}

	function limpiarSQL($arreglo, $excluir = "") {

		if ($excluir != "") {
			$variables = explode("|", $excluir);
		} else {
			$variables[0] = "";
		}

		foreach ($arreglo as $clave => $valor) {
			if (!in_array($clave, $variables)) {

				$arreglo[$clave] = addcslashes($valor, '%_');
			}
		}

		return $arreglo;

	}

	/*
	 * Permite validar un campo con un arreglo de parámetros al estilo jquery-validation-engine
	 */
	function validarCampo($valorCampo, $parametros, $corregir=false, $showError=false) {
		
		if (isset($parametros['required'])) {
			$campoVacio = ($valorCampo == '') ? false : true;
			if (!$campoVacio) {
				if($showError){
					return array("errorType"=>"required","errorMessage"=>"Campo Vacío") ;
				}
				return false;
			}
		}
		//Si el campo es diferente de vacío y no es required, se entra a verificar su valor.
		if($valorCampo!=''){
			
			include_once ('core/general/ValidadorCampos.class.php');
			$miValidador = new ValidadorCampos();
			
			if (isset($parametros['minSize'])) {
				$valido = $miValidador->validarRango($valorCampo,$parametros['minSize'],'minSize');
				if (!$valido) {
					if(!$corregir){
						if($showError){
							return array("errorType"=>"minSize","errorMessage"=>"La longitud es menor a ".$parametros['minSize']);
						}
						return false;
					}
					$valorCampo = $miValidador->corregirRango($valorCampo,$parametros['minSize'],'minSize');
				}
			}
			
			if (isset($parametros['min'])) {
				$valido = $miValidador->validarRango($valorCampo,$parametros['min'],'min');
				if (!$valido) {
					if($showError){
						return array("errorType"=>"min","errorMessage"=>"El número es menor a ".$parametros['min']);
					}
					return false;
				}
			}
			
			if (isset($parametros['maxSize'])) {
				$valido = $miValidador->validarRango($valorCampo,$parametros['maxSize'],'maxSize');
				if (!$valido) {
					if(!$corregir){
						if($showError){
							return array("errorType"=>"maxSize","errorMessage"=>"La longitud es mayor a ".$parametros['maxSize']);
						}
						return false;
					}
					$valorCampo = $miValidador->corregirRango($valorCampo,$parametros['maxSize'],'maxSize');
				}
			}
			
			if (isset($parametros['max'])) {
				$valido = $miValidador->validarRango($valorCampo,$parametros['max'],'max');
				if (!$valido) {
					if($showError){
						return array("errorType"=>"max","errorMessage"=>"El número es mayor a ".$parametros['max']);
					}
					return false;
				}
			}
			
			if (isset($parametros['custom'])) {			
				$valido = $miValidador->validarTipo($valorCampo,$parametros['custom']);
				if (!$valido) {
					if($showError){
						return array("errorType"=>"custom","errorMessage"=>"El campo no es del tipo ".$parametros['custom']);
					}
					return false;
				}
			}
		}
		
		/*
		 * Como se supone que ya superó la barrera de inyeccion SQl en la funcion limpiarSQL.
		 * Se hace la corrección al insertar campos de texto con ' con el comodín ''. 
		 */
		/*
		 * "'" - simple 
		 * "\0" - NULL
		 * "\t" - tab
		 * "\n" - new line
		 * "\x0B" - vertical tab
		 * "\r" - carriage return
		 * " " - ordinary white space
		 * "\x00" - NULL
		 * "\x1a" - EOF
		 */
		$valorCampo = trim($valorCampo);
		$valorCampo = str_replace('\'', '\'\'', $valorCampo);
		//Se propone guardar el string de los campos como carácteres html y luego si se necesita
		//Decodificarlos con htmlspecialchars_decode
		//$valorCampo = htmlspecialchars(nl2br($valorCampo),ENT_QUOTES);
		//http://php.net/manual/en/pdo.quote.php
		//http://php.net/manual/en/pdo.prepare.php
		
		return $valorCampo;
	}

	/*
	 * Permite que los valores de $_REQUEST se validen del lado del servidor con el módulo
	 * ValidadorCampos de los componentes generales del CORE de SARA
	 */
	function validacionCampos($variables, $validadorCampos, $corregir=false, $showError=false) {

		function get_string_between($string, $start, $end) {
			$string = " " . $string;
			$ini = strpos($string, $start);
			if ($ini == 0)
				return "";
			$ini += strlen($start);
			$len = strpos($string, $end, $ini) - $ini;
			return substr($string, $ini, $len);
		}

		function get_string_before_char($string, $char) {
			$str = strstr($string, $char, true);
			return ($str == '') ? $string : $str;
		}

		function erase_string_spaces($string) {
			return str_replace(' ', '', $string);
		}

		function separarParametros($texto = '') {
			$valores = explode(",", $texto);
			$parametros = array();
			foreach ($valores as $valor) {
				$clave = erase_string_spaces(get_string_before_char($valor, "["));
				$valor = erase_string_spaces(get_string_between($valor, "[", "]"));
				$parametros[$clave] = $valor;
			}
			return $parametros;
		}
		
		foreach ($validadorCampos as $nombreCampo => $validador) {
			if (isset($variables[$nombreCampo])) {
				$parametros = separarParametros($validador);
				$validez = $this -> validarCampo($variables[$nombreCampo], $parametros, $corregir, $showError);
				if ($validez===false) {
					return false;
				}
				if (isset($validez['errorType'])) {
					return 'El campo "'.$nombreCampo.'" con valor "'.$variables[$nombreCampo].'" arroja el error: "'.$validez['errorMessage'].'"';
				}
				$variables[$nombreCampo] = $validez;
			}
		}
		
		return $variables;
	}
	
	/*
	 * Permite codificar los campos de $_REQUEST para enviar como parámetros a FormularioHtml.class.php.
	 * Permite saltar la restricción de validación SQL, PHP y HTML en los campos para enviar datos sin alteración.
	 */
	function codificarCampos($valor){
    	return base64_encode(serialize($valor));
    }
	
	/*
	 * Permite decodificar los campos de $_REQUEST que hayan sido enviados codificados
	 * con la funcion "codificarCampos" del las instancias FormularioHtml.class.php.
	 */
	function decodificarCampos($valor){
		return unserialize(base64_decode($valor));
	}

}
