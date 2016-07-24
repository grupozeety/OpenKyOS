<?php
require_once ("core/log/loggerSql.class.php");

class loggerBase {
    
    /**
     * Atributos de la sesión
     */
    var $sesionUsuarioId;

    var $miSql;
    
    var $prefijoTablas;
    
    function setConexion($conexion) {
        
        $this->miConexion = $conexion;
    
    }
    
    /**
     * @METHOD setIdusuario
     *
     * @return valor
     * @access public
     */
            
    function setPrefijoTablas($valor) {
        
        $this->prefijoTablas = $valor;
        $this->miSql->setPrefijoTablas ( $this->prefijoTablas );
    
    }
    
    function getSesionUsuarioId() {
        
        return $this->sesionUsuarioId;
    
    }

}

?>
