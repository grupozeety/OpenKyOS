<?php
namespace component\Notificador\interfaz;

interface ICreadorCampos{
    
	/**
	 * Obtener metainformación del campo
	 */
    function obtenerCampo($parametros);
    
    /**
     * Registrar un campo personalizado
     * @param Array $atributos
     */
    function crearCampo($parametros);
    
    /**
     * Eliminar un campo personalizado
     */
    
    function borrarCampo($parametros);
    
    /**
     * Actualizar la metainformación de un campo
     * @param unknown $parametros
     */
    function actualizarCampo($parametros);
    
}


?>