<?php

class SesionSql {
    
    private $prefijoTablas;
    
    var $cadenaSql;
    
    const SESIONID='sesionId';
    
    const EXPIRACION='expiracion';
    
    const VARIABLE='variable';    
    
    
    function __construct() {
    
    }
    
    function setPrefijoTablas($valor) {
        
        $this->prefijoTablas = $valor;        
        return true;
    
    }
    
    function getCadenaSql($indice, $parametro = "") {
        
        $this->clausula ( $indice, $parametro );
        if (isset ( $this->cadena_sql [$indice] )) {
            return $this->cadena_sql [$indice];
        }
        return false;
    
    }
    
    private function clausula($indice, $parametro) {
        $sqlDelete='DELETE FROM ';
        
        switch ($indice) {
            
            case "seleccionarPagina" :
                $this->cadena_sql [$indice] = "SELECT nivel  FROM " . $this->prefijoTablas . "pagina WHERE  nombre='" . $parametro . "' LIMIT 1";
                break;
            
            case "actualizarSesion" :
                
                $this->cadena_sql [$indice] = "UPDATE " . $this->prefijoTablas . "valor_sesion SET expiracion=" . $parametro [self::EXPIRACION] . " WHERE sesionid='" . $parametro [self::SESIONID] . "' ";
                break;
            
            case "borrarVariableSesion" :
                $this->cadena_sql [$indice] = $sqlDelete . $this->prefijoTablas . "valor_sesion  WHERE sesionid='" . $parametro [self::SESIONID] . " AND variable='" . $parametro ["dato"] . "'";
                break;
            
            case "borrarSesionesExpiradas" :
                $this->cadena_sql [$indice] = $sqlDelete . $this->prefijoTablas . "valor_sesion  WHERE  expiracion<" . time ();
                break;
            
            case "borrarSesion" :
                $this->cadena_sql [$indice] = $sqlDelete . $this->prefijoTablas . "valor_sesion WHERE sesionid='" . $parametro . "' ";
                break;
            
            case "buscarValorSesion" :
                $this->cadena_sql [$indice] = "SELECT valor, sesionid, variable, expiracion FROM " . $this->prefijoTablas . "valor_sesion WHERE sesionid ='" . $parametro [self::SESIONID] . "' AND variable='" . $parametro [self::VARIABLE] . "' ";
                break;
            
            case "actualizarValorSesion" :
                $this->cadena_sql [$indice] = "UPDATE " . $this->prefijoTablas . "valor_sesion SET valor='" . $parametro ["valor"] . "', expiracion='" . $parametro [self::EXPIRACION] . "' WHERE sesionid='" . $parametro [self::SESIONID] . "' AND variable='" . $parametro [self::VARIABLE] . "'";
                break;
            
            case "insertarValorSesion" :
                $this->cadena_sql [$indice] = "INSERT INTO " . $this->prefijoTablas . "valor_sesion ( sesionid, variable, valor, expiracion) VALUES ('" . $parametro [self::SESIONID] . "', '" . $parametro [self::VARIABLE] . "', '" . $parametro ["valor"] . "', '" . $parametro [self::EXPIRACION] . "' )";
                break;
            
            case "verificarNivelUsuario" :
                $this->cadena_sql [$indice] = "SELECT tipo FROM " . $this->prefijoTablas . "usuario WHERE id_usuario='" . $parametro . "' ";
                break;
            default :
        }
    
    }

}
?>