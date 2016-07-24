<?php
if (! isset ( $GLOBALS ['autorizado'] )) {
	include ('../index.php');
	exit ();
}
/**
 * Esta clase permite validar y evaluar los tipos de datos que vengan en un $_REQUEST
 * La validación consiste en un proceso de decir si o no es el datos que yo indico.
 * La evaluación permite volver el dato que le entre como String del $_REQUEST en
 * el tipo de dato que se le especifique. Por ejemplo un string de fecha sería convertido
 * en un tipo de dato Date.
 */
class Tipos {
	/**
	 * El array de alias permite hacer alias de las funciones para mayor versatilidad.
	 * Para contectar con jquery.validationEngine se podría usar date como alias y el valor real
	 * para cargar la funcion es Fecha.
	 */
	private static $arrayAlias;
	function __construct() {
		self::$arrayAlias = array (
				'boleano' => 'Boleano',
				'entero' => 'Entero',
				'doble' => 'Doble',
				'porcentaje' => 'Porcentaje',
				'fecha' => 'Fecha',
				'stringFecha' => 'StringFecha',
				'texto' => 'Texto',
				'lista' => 'Lista',
				'nulo' => 'Nulo',
				'email' => 'Correo',
				'phone' => 'Telefono',
				'url' => 'Url',
				'date' => 'StringFecha',
				'number' => 'Doble',
				'integer' => 'Entero',
				'onlyNumberSp' => 'NumerosYEspacios',
				'onlyLetterSp' => 'LetrasYEspacios',
				'onlyLetterNumber' => 'LetrasYNumeros',
				'onlyLetterNumberSp' => 'LetrasNumerosYEspacios',
				'onlyLetterNumberSpPunt' => 'LetrasNumerosEspacioYPuntuacion'
		);
	}
	/**
	 * Las funciones privadas son parte de las funciones validarTipo y evaluarTipo.
	 * Estas últimas son las que se deben llamar para evaluar datos indicando en su
	 * ejecución alguno de los alias del tipo de dato.
	 */
	private function validarBoleano($valor) {
		$valoresPosibles = array(TRUE,'t','true','y','yes','1',FALSE,'f','false','n','no','0');
		return in_array($valor, $valoresPosibles);
	}
	private function evaluarBoleano($valor) {
		$valoresPosibles = array(TRUE,'t','true','y','yes','1',FALSE,'f','false','n','no','0');
		return in_array($valor, $valoresPosibles) ? $valor : false;
	}
	private function validarEntero($valor) {
		$entero = ( int ) $valor;
		return (string)$entero === $valor;
	}
	private function evaluarEntero($valor) {
		$valor = ( int ) $valor;
		return is_int ( $valor ) ? $valor : false;
	}
	private function validarDoble($valor) {
		$flotante = ( float ) $valor;
		return (string)$flotante === $valor;
	}
	private function evaluarDoble($valor) {
		$valor = ( float ) $valor;
		return is_float ( $valor ) ? $valor : false;
	}
	private function validarPorcentaje($valor) {
		$valor = ( float ) $valor;
		return is_float ( $valor );
	}
	private function evaluarPorcentaje($valor) {
		$valor = ( float ) $valor;
		return is_float ( $valor ) ? $valor / 100 : false;
	}
	private function validarFecha($valor) {
		// Formato
		// 'd/m/Y'
		// 30/01/2014
		//
		$d = \DateTime::createFromFormat ( 'd/m/Y', $valor );
		return $d && $d->format ( 'd/m/Y' ) == $valor;
	}
	private function evaluarFecha($valor) {
		// Formato
		// 'd/m/Y'
		// 30/01/2014
		//
		$d = \DateTime::createFromFormat ( 'd/m/Y', $valor );
		return $d && $d->format ( 'd/m/Y' ) == $valor ? $d : false;
	}
	private function validarTexto($valor) {
		return is_string ( $valor );
	}
	private function evaluarTexto($valor) {
		return is_string ( $valor ) ? ( string ) $valor : false;
	}
	private function validarLista($valor) {
		return is_array ( explode ( ',', $valor ) );
	}
	private function evaluarLista($valor) {
		return is_array ( explode ( ',', $valor ) ) ? $valor : false;
	}
	private function validarNulo($valor) {
		$valor = null;
		return is_null ( $valor );
	}
	private function evaluarNulo($valor) {
		return null;
	}
	//http://www.phpliveregex.com/
	private function validarNumerosYEspacios($valor){
		if (preg_match('/^([[:digit:]]|[[:space:]])*$/',$valor)) {
			return true;
		} else {
			return false;
		}
	}
	
	private function evaluarNumerosYEspacios($valor){
		if (preg_match('/^([[:digit:]]|[[:space:]])*$/',$valor)) {
			return $valor;
		} else {
			return false;
		}
	}
	
	private function validarLetrasYEspacios($valor){
		if (preg_match('/^([[:alpha:]]|[[:space:]]|[áéíóúÁÉÍÓÚ])*$/',$valor)) {
			return true;
		} else {
			return false;
		}
	}
	
	private function evaluarLetrasYEspacios($valor){
		if (preg_match('/^([[:alpha:]]|[[:space:]]|[áéíóúÁÉÍÓÚ])*$/',$valor)) {
			return $valor;
		} else {
			return false;
		}
	}
	
	private function validarLetrasYNumeros($valor){
		if (preg_match('/^([[:alnum:]]|[áéíóúÁÉÍÓÚ])*$/',$valor)) {
			return true;
		} else {
			return false;
		}
	}
	
	private function evaluarLetrasNumeros($valor){
		if (preg_match('/^([[:alnum:]]|[áéíóúÁÉÍÓÚ])*$/',$valor)) {
			return $valor;
		} else {
			return false;
		}
	}
	
	private function validarLetrasNumerosYEspacios($valor){
		if (preg_match('/^([[:alnum:]]|[[:space:]]|[áéíóúÁÉÍÓÚ])*$/',$valor)) {
			return true;
		} else {
			return false;
		}
	}
	
	private function evaluarLetrasNumerosYEspacios($valor){
		if (preg_match('/^([[:alnum:]]|[[:space:]]|[áéíóúÁÉÍÓÚ])*$/',$valor)) {
			return $valor;
		} else {
			return false;
		}
	}
	
	private function validarLetrasNumerosEspacioYPuntuacion($valor){
		if (preg_match('/^([[:alnum:]]|[[:space:]]|[áéíóúÁÉÍÓÚ]|[.,])*$/',$valor)) {
			return true;
		} else {
			return false;
		}
	}
	
	private function evaluarLetrasNumerosEspacioYPuntuacion($valor){
		if (preg_match('/^([[:alnum:]]|[[:space:]]|[áéíóúÁÉÍÓÚ]|[.,])*$/',$valor)) {
			return $valor;
		} else {
			return false;
		}
	}
	
	
	// http://www.sergiomejias.com/2007/09/validar-una-fecha-con-expresiones-regulares-en-php/
	public function validarStringFecha($fecha) {
		$fecha = explode('-', $fecha);
		$anno = $fecha[0];
		$mes = $fecha[1];
		$dia = $fecha[2];
		if (checkdate($mes,$dia,$anno)) {
			return true;
		} else {
			return false;
		}
	}
	public function evaluarStringFecha($fecha) {
		$separado = explode('-', $fecha);
		$anno = $separado[0];
		$mes = $separado[1];
		$dia = $separado[2];
		if (checkdate($mes,$dia,$anno)) {
			return $fecha;
		} else {
			return false;
		}
	}
	/*
	 * Permite asignar alias a las funciones. Por ejemplo permite que a la función evaluarFecha
	 * se pueda llamar al decir que el tipo de dato es "fecha", "date", "Date" o cualquier otra asignación
	 * esto permite una mejor integración con otras convenciones de frameworks PHP o Javascript. 
	 */
	public static function getAlias($tipo = '') {
		$arrayAlias = self::$arrayAlias;
		if (isset ( $arrayAlias [$tipo] )) {
			return $arrayAlias [$tipo];
		}
		return $tipo;
	}
	/*
	 * Evalua el tipo de dato especificando el valor del dato y el alias del tipo de dato de PHP
	 * por ejemplo:
	 * objetoInstanciado->evaluarTipo('09/09/2015','fecha');
	 * Debería retornar el valor de la fecha como variable del tipo time de PHP.
	 */
	public static function evaluarTipo($valor = '', $tipo = '') {
		$metodo = 'evaluar' . self::getAlias ( $tipo );
		if (method_exists ( get_class (), $metodo )) {
			return call_user_func_array ( array (
					get_class (),
					$metodo 
			), array (
					$valor 
			) );
		}
		/**
		 * Si el tipo de dato que se desea evaluar no existe, se retorna verdadero en vez de falso
		 * para mejorar compatibilidad con campos custom
		 */
		return true;
	}
	/*
	 * Evalua el tipo de dato especificando el valor del dato y el alias del tipo de dato de PHP
	 * por ejemplo:
	 * objetoInstanciado->validarTipo('09/09/2015','fecha');
	 * Debería retornar el valor true ya que se reconoce como un string de fecha.
	 */
	public static function validarTipo($valor = '', $tipo = '') {
		$metodo = 'validar' . self::getAlias ( $tipo );
		if (method_exists ( get_class (), $metodo )) {
			return call_user_func_array ( array (
					get_class (),
					$metodo 
			), array (
					$valor 
			) );
		}
		/**
		 * Si el tipo de dato que se desea evaluar no existe, se retorna verdadero en vez de falso
		 * para mejorar compatibilidad con campos custom
		 */
		return true;
	}
}

?>
