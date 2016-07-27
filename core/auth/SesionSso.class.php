<?php

require_once ('core/log/logger.class.php');

class SesionSSO {
	
	private static $instancia;
	
	var $miSql;
	var $site;
	var $hostSSO;
	var $SPSSO;
	var $configurador;
	var $authnRequest;
	var $sesionUsuario;
	var $sesionUsuarioId;
	var $logger;
    
    /**
     *
     * @name sesiones
     *       constructor
     */
    //private 
    function __construct() {
    	$this->sesionUsuario = Sesion::singleton ();
    	$this->configurador = \Configurador::singleton ();
    	$this->site = $this->configurador->getVariableConfiguracion ( 'site' );
    	$this->hostSSO = $this->configurador->getVariableConfiguracion ( 'hostSSO' );
    	$this->SPSSO = $this->configurador->getVariableConfiguracion ( 'SPSSO' );// Fuente de autenticación definida en el authsources del SP
    	require_once ($this->configurador->getVariableConfiguracion ( 'direccionSSOAutoloader' ));
		$this->authnRequest = new SimpleSAML_Auth_Simple ( $this->SPSSO );// Se pasa como parametro la fuente de autenticación
    	$this->logger = new logger ();
    }
    
    public static function singleton() {
    
    	if (!isset(self::$instancia)) {
    		$className = __CLASS__;
    		self::$instancia = new $className ();
    	}
    	return self::$instancia;
    }

    /**
     *
     * @name sesiones Verifica la existencia de una sesion válida en la máquina del cliente
     * @param
     *            string nombre_db
     * @return void
     * @access public
     */
    function verificarSesion($pagina) {
        $resultado = true;
        // Se eliminan las sesiones expiradas
        //$this->borrarSesionExpirada();
        if($this->verificarSesionAbierta()){
        	$resultado = $this->getParametrosSesionAbierta();
        } else {
        	$resultado = $this->crearSesion();
        }
        $resultado = $this->verificarRolesPagina($resultado['perfil'],$pagina);//Se verifica que la página pertenezca al perfil
        // Si no tiene acceso a alguna página, se desloguea de SSO
        if($resultado==false){
        	$this->terminarSesion();
        }
        return $resultado;
    }

    /* Fin de la función numero_sesion */
    
    
    function verificarSesionAbierta() {
    	$respuesta = true;
    	//La sesión SP está abierta
    	if($this->authnRequest->isAuthenticated()){
    		//La sesión SP abierta pero usuario no ha iniciado sesión SP en SARA
    		if($this->sesionUsuario->numeroSesion()==''){    			
    			$this->crearSesion();
    		}
    	} else {
    		$respuesta = false;
    	}
    	return $respuesta;
    }
    
    function getParametrosSesionAbierta() {
    	return $this->authnRequest->getAttributes();
    }

    /**
     * @METHOD crear_sesion
     *
     * Crea una nueva sesión en la base de datos.
     * @PARAM usuario_aplicativo
     * @PARAM nivel_acceso
     * @PARAM expiracion
     * @PARAM conexion_id
     *
     * @return boolean
     * @access public
     */
    function crearSesion() {
    	
		$aplication_base_url = $this->hostSSO.$this->site.'/index.php';	
		//En este caso se va al index, podría irse a la página desde donde lo solicitaron.
		$login_params = array (
			'ReturnTo' => $aplication_base_url
		);
		
		$this->authnRequest->requireAuth ( $login_params );
		$atributos = $this->authnRequest->getAttributes();
		$registro = $_REQUEST;
		$registro['opcion'] = 'INGRESO';
		$this->logger->log_usuario($registro);
		
		$this->sesionUsuario->crearSesion($atributos['sn'][0]);
		return $atributos;
    }

    // Fin del método crear_sesion

    /**
     *
     * @name terminar_sesion_expirada
     * @return void
     * @access public
     */
    function terminarSesionExpirada() {
		/*
		 * No USADA
		 */
        $cadenaSql = $cadenaSql = $this->miSql->getCadenaSql('borrarSesionesExpiradas');

        return !$this->miConexion->ejecutarAcceso($cadenaSql);
    }

    // Fin del método terminar_sesion_expirada

    /**
     *
     * @name terminar_sesion
     * @return boolean
     * @access public
     */
    function terminarSesion() {
    	$sesionUsuarioId = $this->sesionUsuario->numeroSesion();
    	$this->sesionUsuario->terminarSesion($sesionUsuarioId);
    	$aplication_base_url = $this->hostSSO.$this->site.'/';
    	
    	$respuesta = $this->authnRequest->logout ( $aplication_base_url);
    	//Cerrar la sesión de SARA al salir.
    	return $respuesta;
    }
    
    // Fin del método terminar_sesion
    
    function verificarRolesPagina($perfiles,$pagina){
    	$cadenaSql = $this->sesionUsuario->miSql->getCadenaSql('verificarEnlaceUsuario', $pagina);
    	//Se busca en la tabla _menu_rol_enlace si la página pertenece al perfil.
    	$roles = $this->sesionUsuario->miConexion->ejecutarAcceso($cadenaSql,'busqueda');
    	if($roles){//Si la página tiene roles en el menú
	    	foreach ($perfiles as $perfil){
	    		foreach ($roles as $rol){
	    			if($rol[0]==$perfil){
	    				return true;
	    			}
	    		}
	    	}
    	}
    	return false;
    }
}

?>