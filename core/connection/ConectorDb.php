<?php

class ConectorDb implements Conector {
    
    /* Atributos: ** */
    
    /**
     *
     * @access privado
     */
    var $servidor;
    
    var $db;
    
    var $usuario;
    
    var $clave;
    
    var $enlace;
    
    var $dbsys;
    
    var $dbesquema;
    
    var $cadenaSql;
    
    var $error;
    
    var $numero;
    
    var $conteo;
    
    var $registro;
    
    var $campo;
    
    /* Fin de sección Atributos: ** */
    
    /**
     *
     * @name obtener_enlace
     * @return void
     * @access public
     */
    function getEnlace() {
        
        return $this->enlace;
    
    }
    
    /**
     *
     * @name getRegistroDb
     * @return registro []
     * @access public
     */
    function getRegistroDb() {
        
        if (isset ( $this->registro )) {
            return $this->registro;
        } else {
            
            return false;
        }
    
    }
    
    // Fin del método getRegistroDb
    
    /**
     *
     * @name obtener_conteo_db
     * @return int conteo
     * @access public
     */
    function getConteo() {
        
        return $this->conteo;
    
    }    
    /**
     *
     * @name especificar_db
     * @param
     *            string nombre_db
     * @return void
     * @access public
     */
    function especificar_db($nombreDb) {
        
        $this->db = $nombreDb;
    
    }
    
    // Fin del método especificar_db
    
    /**
     *
     * @name especificar_usuario
     * @param
     *            string usuario_db
     * @return void
     * @access public
     */
    function especificar_usuario($usuarioDb) {
        
        $this->usuario = $usuarioDb;
    
    }
    
    // Fin del método especificar_usuario
    
    /**
     *
     * @name especificar_clave
     * @param
     *            string nombre_db
     * @return voidreturn new $db($datosConfiguracion);
     * @access public
     */
    function especificar_clave($claveDb) {
        
        $this->clave = $claveDb;
    
    }
    
    // Fin del método especificar_clave
    
    /**
     *
     * @name especificar_servidor
     * @param
     *            string servidor_db
     * @return void
     * @access public
     */
    function especificar_servidor($servidorDb) {
        
        $this->servidor = $servidorDb;
    
    }
    
    // Fin del método especificar_servidor
    
    /**
     *
     * @name especificar_dbms
     * @param
     *            string dbms
     * @return void
     * @access public
     */
    function especificar_dbsys($sistema) {
        
        $this->dbsys = $sistema;
    
    }
    
    // Fin del método especificar_dbsys
    
    /**
     *
     * @name especificar_enlace
     * @param
     *            resource enlace
     * @return void
     * @access public
     */
    function especificar_enlace($unEnlace) {
        
        if (is_resource ( $unEnlace )) {
            $this->enlace = $unEnlace;
        }
    
    }
    
    // Fin del método especificar_enlace
    
    /**
     *
     * @name conectar_db
     * @return void
     * @access public
     */
    function conectar_db() {
    
    }
    
    /**
     *
     * @name probar_conexion
     * @return void
     * @access public
     */
    function probar_conexion() {
    
    }
    
    function logger($datosConfiguracion, $idUsuario, $evento) {
        
        $this->cadena_sql = "INSERT INTO " . $datosConfiguracion ["prefijo"] . "logger ";
        $this->cadena_sql .= "( `id_usuario` ,`evento` , `fecha` ) ";
        $this->cadena_sql .= "VALUES (";
        $this->cadena_sql .= $idUsuario . ",";
        $this->cadena_sql .= "'" . $evento . "',";
        $this->cadena_sql .= "'" . time () . "'";
        $this->cadena_sql .= ")";
        
        $this->ejecutar_acceso_db ( $this->cadena_sql );
        unset ( $this->db_sel );
        return TRUE;
    }
    
    /**
     *
     * @name desconectar_db
     * @param
     *            resource enlace
     * @return void
     * @access public
     */
    function desconectar_db() {
    
    }
    
    /**
     *
     * @name ejecutar_acceso_db
     * @param
     *            string cadena_sql
     * @param
     *            string tipo
     *            Tipo de acceso a realizar. Puede ser una consulta (búsqueda), una definición de datos (ddl)
     *            o insercion, borrado y atualización (accion)
     * @return Array boolean NULL en caso de éxito, false en caso de no tener resultados, NULL en caso de error de acceso.
     * @access public
     */
    function ejecutarAcceso($cadena, $tipo = "", $numeroRegistros = 0) {
        
        return $cadena . $tipo . $numeroRegistros;
    }
    
    /**
     *
     * @name obtener_error
     * @param
     *            string cadena_sql
     * @param
     *            string conexion_id
     * @return boolean
     * @access public
     */
    function obtener_error() {
    
    }
    
    /**
     *
     * @name registro_db
     * @param
     *            string cadena_sql
     * @param
     *            int numero
     * @return boolean
     * @access public
     */
    function registro_db($cadena, $numeroRegistros = 0) {
        return $cadena . $numeroRegistros;
    }
    
    function obtenerCadenaListadoTablas($variable) {
        return $variable;
    }
    
    function ultimo_insertado($unEnlace = "") {
        return $unEnlace;
    }
    
    /**
     *
     * @name transaccion
     * @return boolean resultado
     * @access public
     */
    function transaccion($clausulas) {
        return $clausulas;
    }
    
    function limpiarVariables($variables) {
        return $variables;
    }
    
    function tratarCadena($cadena){
        
    }

}

?>