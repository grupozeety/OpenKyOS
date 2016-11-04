<?php
require_once ("core/log/loggerSql.class.php");
require_once ("core/log/loggerBase.class.php");

class logger extends loggerBase {
	private static $instancia;
	const ACCEDER = 'acceso';
	const BUSCAR = 'busqueda';
	var $sesionUsuario;
	
	/**
	 *
	 * @name sesiones
	 *       constructor
	 */
	public function __construct() {
		$this->miSql = new loggerSql ();
		$this->miConfigurador = \Configurador::singleton ();
		$this->sesionUsuario = \Sesion::singleton ();
		$this->setPrefijoTablas ( $this->miConfigurador->getVariableConfiguracion ( "prefijo" ) );
		$this->setConexion ( $this->miConfigurador->fabricaConexiones->getRecursoDB ( "estructura" ) );
	}
	public static function singleton() {
		if (! isset ( self::$instancia )) {
			$className = __CLASS__;
			self::$instancia = new $className ();
		}
		return self::$instancia;
	}
	
	/**
	 *
	 * @name sesiones registra en la tabla de log de usuarios
	 * @param
	 *        	string nombre_db
	 * @return void
	 * @access public
	 */
	function log_usuario($log) {
		date_default_timezone_set ( 'America/Bogota' );
// 		if(isset($log['opcion']) && (/*$log['opcion']=="buscar" ||*/ $log['opcion']=="insertar" || $log['opcion']=="actualizar")){
			if(strcmp($log['opcion'], "buscar")==0){
				$log['opcion']= "CONSULTAR";
			}
			if(strcmp($log['opcion'], "insertar")==0){
				$log['opcion']= "REGISTRAR";
			}
			if(strcmp($log['opcion'], "actualizar")==0){
				$log['opcion']= "MODIFICAR";
			}
			
			$log ['host'] = $this->obtenerIP ();
			$log ['machine'] = php_uname();
			$registroLog ['usuario'] = $this->sesionUsuario->getSesionUsuarioId();
			$registroLog ['accion'] = $log['opcion'];
			$registroLog ['fecha_log'] = date ( "F j, Y, g:i:s a" );
			$registroLog ['datos'] = array();
			foreach (array_keys($log) as $llaves){
				if($llaves != "tiempo" && $llaves != "campoSeguro" && $llaves != "validadorCampos" && $llaves != "action" && $llaves != "option" && $llaves != "opcion" && $llaves != "arreglo"){
					$registroLog ['datos'][$llaves]=$log[$llaves];
				}
			}
			$registroLog ['datos'] = json_encode($registroLog ['datos']);
			
			$cadenaSql = $this->miSql->getCadenaSql ( "registroLogUsuario", $registroLog );
			$resultado = $this->miConexion->ejecutarAcceso ( $cadenaSql, self::ACCEDER);
// 		}
	}
	function obtenerIP() {
		if (! empty ( $_SERVER ['HTTP_CLIENT_IP'] ))
			return $_SERVER ['HTTP_CLIENT_IP'];
		
		if (! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] ))
			return $_SERVER ['HTTP_X_FORWARDED_FOR'];
		
		return $_SERVER ['REMOTE_ADDR'];
	}
}

?>
