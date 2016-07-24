<?php
/*
 * ############################################################################ # UNIVERSIDAD DISTRITAL Francisco Jose de Caldas # # Copyright: Vea el archivo LICENCIA.txt que viene con la distribucion # ############################################################################
 */
/*
 * *************************************************************************
 * @name oci8.class.php
 * @author Paulo Cesar Coronado
 * @Última revisión 12 de agosto 2015
 * ***************************************************************************
 * @subpackage
 * @package clase
 * @copyright
 * @version 0.2
 * @author Paulo Cesar Coronado
 * @link http://computo.udistrital.edu.co
 * @description Esta clase esta disennada para administrar todas las tareas
 * relacionadas con la base de datos ORACLE con OCI8.
 *
 * ****************************************************************************
 */
/*
 * ***************************************************************************
 * Atributos
 *
 * @access private
 * @param $servidor
 * URL del servidor de bases de datos.
 * @param $db
 * Nombre de la base de datos
 * @param $usuario
 * Usuario de la base de datos
 * @param $clave
 * Clave de acceso al servidor de bases de datos
 * @param $enlace
 * Identificador del enlace a la base de datos
 * @param $dbms
 * Nombre del DBMS oci8
 * @param $cadenaSql
 * Clausula SQL a ejecutar
 * @param $error
 * Mensaje de error devuelto por el DBMS
 * @param $numero
 * Número de registros a devolver en una consulta
 * @param $conteo
 * Número de registros que existen en una consulta
 * @param $registro
 * Matriz para almacenar los resultados de una búsqueda
 * @param $campo
 * Número de campos que devuelve una consulta
 * TO DO Implementar la funcionalidad en DBMS ORACLE con OCI8
 * *****************************************************************************
 */
/*
 * ***************************************************************************
 * Métodos
 *
 * @access public
 *
 * @name db_admin
 * Constructor. Define los valores por defecto
 * @name especificar_db
 * Especifica a través de código el nombre de la base de datos
 * @name especificar_usuario
 * Especifica a través de código el nombre del usuario de la DB
 * @name especificar_clave
 * Especifica a través de código la clave de acceso al servidor de DB
 * @name especificar_servidor
 * Especificar a través de código la URL del servidor de DB
 * @name especificar_dbms
 * Especificar a través de código el nombre del DBMS
 * @name especificar_enlace
 * Especificar el recurso de enlace a la DBMS
 * @name conectar_db
 * Conecta a un DBMS
 * @name probar_conexion
 * Con la cual se realizan acciones que prueban la validez de la conexión
 * @name desconectar_db
 * Libera la conexion al DBMS
 * @name ejecutar_acceso_db
 * Ejecuta clausulas SQL de tipo INSERT, UPDATE, DELETE
 * @name obtener_error
 * Devuelve el mensaje de error generado por el DBMS
 * @name obtener_conteo_dbregistro_db
 * Devuelve el número de registros que tiene una consulta
 * @name registro_db
 * Ejecuta clausulas SQL de tipo SELECT
 * @name getRegistroDb
 * Devuelve el resultado de una consulta como una matriz bidimensional
 * @name obtener_error
 * Realiza una consulta SQL y la guarda en una matriz bidimensional
 *
 * ****************************************************************************
 */
class Oci8 extends ConectorDb {
	/**
	 *
	 * @name conectar_db
	 * @return void
	 * @access public
	 *        
	 */
	function conectar_db() {
		$this->enlace = oci_connect ( $this->usuario, $this->clave, $this->servidor . ':' . $this->puerto . '/' . $this->db );
		
		if ($this->enlace) {
			return $this->enlace;
		} else {
			$this->error = oci_error ();
			return false;
		}
	}
	// Fin del método conectar_db
	/**
	 *
	 * @name probar_conexion
	 * @return void
	 * @access public
	 *        
	 */
	function probar_conexion() {
		return $this->enlace;
	}
	// Fin del método probar_conexion
	/**
	 *
	 * @name desconectar_db
	 * @param
	 *        	resource enlace
	 * @return void
	 * @access public
	 *        
	 */
	function desconectar_db() {
		oci_close ( $this->enlace );
	}
	// Fin del método desconectar_db
	// Funcion para el acceso a las bases de datos
	function ejecutarAcceso($cadena, $tipo = "", $numeroRegistros = 0) {
		if (! is_resource ( $this->enlace )) {
			return FALSE;
		}
		if ($tipo == "busqueda") {
			$this->ejecutar_busqueda ( $cadena, $numeroRegistros );
			return $this->getRegistroDb ();
		} else {
			return $this->ejecutar_acceso_db ( $cadena );
		}
	}
	/**
	 *
	 * @name obtener_error
	 * @param
	 *        	string cadena_sql
	 * @param
	 *        	string conexion_id
	 * @return boolean
	 * @access public
	 *        
	 */
	function obtener_error() {
		return $this->error;
	}
	// Fin del método obtener_error
	/**
	 *
	 * @name registro_db
	 * @param
	 *        	string cadena_sql
	 * @param
	 *        	int numero
	 * @return boolean
	 * @access public
	 *        
	 */
	function registro_db($cadena, $numeroRegistros = 0) {
		if (! is_resource ( $this->enlace )) {
			return FALSE;
		}
		$cadenaParser = oci_parse ( $this->enlace, $cadena );
		if (oci_execute ( $cadenaParser )) {
			return $this->procesarResultado ( $cadenaParser, $numeroRegistros );
		} else {
			unset ( $this->registro );
			$this->error = oci_error ( $this->enlace );
			return 0;
		}
	}
	// Fin del método registro_db
	private function procesarResultado($cadenaParser, $numeroRegistros) {
		unset ( $this->registro );
		
		$busqueda = OCIExecute ( $cadenaParser );
		
		if ($busqueda !== TRUE) {
			$mensaje = "ERROR EN LA CADENA " . $cadena_sql;
			error_log ( $mensaje );
		}
		if ($busqueda) {
			
			// carga una a una las filas en $this->registro
			while ( $row = oci_fetch_array ( $cadenaParser, OCI_BOTH ) ) {
				$this->registro [] = $row;
			}
			
			// si por lo menos una fila es cargada a $this->registro entonces cuenta
			if (isset ( $this->registro )) {
				
				$this->conteo = count ( $this->registro );
				
			}
			
			@OCIFreeCursor ( $cadenaParser );
			// en caso de que existan cero (0) registros retorna falso ($this->conteo=false)
			return $this->conteo;
		} else {
			$this->error = oci_error ();
			echo $this->error ();
			
			return 0;
		}
	}
	/**
	 *
	 * @name transaccion
	 * @return boolean resultado
	 * @access public
	 *        
	 */
	function transaccion($clausulas) {
		$acceso = true;
		$this->instrucciones = count ( $clausulas );
		for($contador = 0; $contador < $this->instrucciones; $contador ++) {
			$acceso .= $this->ejecutar_acceso_db ( $insert [$contador], true );
		}
		if (! $acceso) {
			oci_rollback ( $this->enlace );
			return FALSE;
		} else {
			oci_commit ( $this->enlace );
			return TRUE;
		}
	}
	// Fin del método transaccion
	function limpiarVariables($variables) {
		return $variables;
	}
	function tratarCadena($cadena) {
		return $cadena;
	}
	/**
	 *
	 * @name db_admin
	 *      
	 *      
	 */
	function __construct($registro) {
		$this->servidor = trim ( $registro ["dbdns"] );
		$this->db = trim ( $registro ["dbnombre"] );
		$this->puerto = isset ( $registro ['dbpuerto'] ) ? $registro ['dbpuerto'] : 1521;
		$this->usuario = trim ( $registro ["dbusuario"] );
		$this->clave = trim ( $registro ["dbclave"] );
		$this->dbsys = trim ( $registro ["dbsys"] );
		$this->dbesquema = trim ( $registro ['dbesquema'] );
		$this->enlace = $this->conectar_db ();
	}
	// Fin del método db_admin
	function ejecutar_busqueda($cadena, $numeroRegistros = 0) {
		$this->registro_db ( $cadena, $numeroRegistros );
		return $this->getRegistroDb ();
	}
	/**
	 *
	 * @name ejecutar_acceso_db
	 * @param
	 *        	string cadena_sql
	 * @param
	 *        	string conexion_id
	 * @return boolean
	 * @access public
	 *        
	 */
	private function ejecutar_acceso_db($cadena, $esTransaccion = false) {
		$cadenaParser = oci_parse ( $this->enlace, $cadena );
		if ($esTransaccion) {
			$busqueda = oci_execute ( $cadenaParser, OCI_NO_AUTO_COMMIT );
		} else {
			$busqueda = oci_execute ( $cadenaParser );
		}
		return $busqueda;
	}
}
// Fin de la clase db_admin
?>