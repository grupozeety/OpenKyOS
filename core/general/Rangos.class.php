<?php
if (! isset ( $GLOBALS ['autorizado'] )) {
	include ('../index.php');
	exit ();
}
/**
 * Esta clase permite validar rangos de datos normales de un $_REQUEST
 */
class Rangos {
	/**
	 * El array de alias permite hacer alias de las funciones para mayor versatilidad.
	 * Para contectar con jquery.validationEngine se podría usar date como alias y el valor real
	 * para cargar la funcion es Fecha
	 */
	private static $arrayAlias;
	function __construct() {
		self::$arrayAlias = array (
				'minimo' => 'Minimo',
				'maximo' => 'Maximo',				
				'tamannominimo' => 'TamannoMinimo',
				'tamannomaximo' => 'TamannoMaximo',
				'min' => 'Minimo',
				'max' => 'Maximo',
				'minSize' => 'TamannoMinimo',
				'maxSize' => 'TamannoMaximo'
		);
	}
	private function validarMinimo($valor,$condicion){
		return !($valor<$condicion);
	}
	private function validarMaximo($valor,$condicion){
		return !($valor>$condicion);
	}
	private function validarTamannoMinimo($valor,$condicion){
		//Al no usar esta codificación utf8 la longitud da diferente en javascript que en PHP
		$tamanno = strlen(utf8_decode($valor));
		return !($tamanno<$condicion);
	}
	private function corregirTamannoMinimo($valor,$condicion){
		//Al no usar esta codificación utf8 la longitud da diferente en javascript que en PHP
		$tamanno = strlen(utf8_decode($valor));
		$faltante = $condicion - $tamanno;
		$faltante = str_repeat('',$faltante);
		$valor .= $faltante;
		return $valor;
	}
	private function validarTamannoMaximo($valor,$condicion){
		//Al no usar esta codificación utf8 la longitud da diferente en javascript que en PHP
		$tamanno = strlen(utf8_decode($valor));
		return !($tamanno>$condicion);
	}
	private function corregirTamannoMaximo($valor,$condicion){
		//Al no usar esta codificación utf8 la longitud da diferente en javascript que en PHP
		$tamanno = strlen(utf8_decode($valor));
		$sobrante = $condicion - $tamanno;
		$valor = substr($valor, 0, $sobrante);
		return $valor;
	}
	public static function getAlias($tipo = '') {
		$arrayAlias = self::$arrayAlias;
		if (isset ( $arrayAlias [$tipo] )) {
			return $arrayAlias [$tipo];
		}
		return $tipo;
	}
	public static function corregirRango($valor='',$condicion='',$tipo='') {
		$metodo = 'corregir' . strtoupper ( self::getAlias ( $tipo ) );
		if (method_exists ( get_class (), $metodo )) {
			return call_user_func_array ( array (
					get_class (),
					$metodo
			), array (
					$valor,
					$condicion
			) );
		}
		return false;
	}
	public static function validarRango($valor='',$condicion='',$tipo='') {
		$metodo = 'validar' . strtoupper ( self::getAlias ( $tipo ) );
		if (method_exists ( get_class (), $metodo )) {
			return call_user_func_array ( array (
					get_class (),
					$metodo 
			), array (
					$valor,
					$condicion
			) );
		}
		return false;
	}
}

?>
