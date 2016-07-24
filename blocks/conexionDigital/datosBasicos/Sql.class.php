<?php

namespace conexionDigital\datosBasicos;

if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/connection/Sql.class.php");

/**
 * IMPORTANTE: Se recomienda que no se borren registros. Utilizar mecanismos para - independiente del motor de bases de datos,
 * poder realizar rollbacks gestionados por el aplicativo.
 */
class Sql extends \Sql {

    var $miConfigurador;

    function getCadenaSql($tipo, $variable = '') {

        /**
         * 1.
         * Revisar las variables para evitar SQL Injection
         */
        $prefijo = $this->miConfigurador->getVariableConfiguracion("prefijo");
        $idSesion = $this->miConfigurador->getVariableConfiguracion("id_sesion");

        switch ($tipo) {

            /**
             * Clausulas específicas
             */
            case 'insertarRegistro' :
                $cadenaSql = 'INSERT INTO ';
                $cadenaSql .= $prefijo . 'pagina ';
                $cadenaSql .= '( ';
                $cadenaSql .= 'nombre,';
                $cadenaSql .= 'descripcion,';
                $cadenaSql .= 'modulo,';
                $cadenaSql .= 'nivel,';
                $cadenaSql .= 'parametro';
                $cadenaSql .= ') ';
                $cadenaSql .= 'VALUES ';
                $cadenaSql .= '( ';
                $cadenaSql .= '\'' . $_REQUEST ['nombrePagina'] . '\', ';
                $cadenaSql .= '\'' . $_REQUEST ['descripcionPagina'] . '\', ';
                $cadenaSql .= '\'' . $_REQUEST ['moduloPagina'] . '\', ';
                $cadenaSql .= $_REQUEST ['nivelPagina'] . ', ';
                $cadenaSql .= '\'' . $_REQUEST ['parametroPagina'] . '\'';
                $cadenaSql .= ') ';
                break;

            case 'actualizarRegistro' :
                $cadenaSql = 'INSERT INTO ';
                $cadenaSql .= $prefijo . 'pagina ';
                $cadenaSql .= '( ';
                $cadenaSql .= 'nombre,';
                $cadenaSql .= 'descripcion,';
                $cadenaSql .= 'modulo,';
                $cadenaSql .= 'nivel,';
                $cadenaSql .= 'parametro';
                $cadenaSql .= ') ';
                $cadenaSql .= 'VALUES ';
                $cadenaSql .= '( ';
                $cadenaSql .= '\'' . $_REQUEST ['nombrePagina'] . '\', ';
                $cadenaSql .= '\'' . $_REQUEST ['descripcionPagina'] . '\', ';
                $cadenaSql .= '\'' . $_REQUEST ['moduloPagina'] . '\', ';
                $cadenaSql .= $_REQUEST ['nivelPagina'] . ', ';
                $cadenaSql .= '\'' . $_REQUEST ['parametroPagina'] . '\'';
                $cadenaSql .= ') ';
                break;

            case 'buscarRegistro' :

                $cadenaSql = 'SELECT ';
                $cadenaSql .= 'id_pagina as PAGINA, ';
                $cadenaSql .= 'nombre as NOMBRE, ';
                $cadenaSql .= 'descripcion as DESCRIPCION,';
                $cadenaSql .= 'modulo as MODULO,';
                $cadenaSql .= 'nivel as NIVEL,';
                $cadenaSql .= 'parametro as PARAMETRO ';
                $cadenaSql .= 'FROM ';
                $cadenaSql .= $prefijo . 'pagina ';
                $cadenaSql .= 'WHERE ';
                $cadenaSql .= 'nombre=\'' . $_REQUEST ['nombrePagina'] . '\' ';
                break;

            case 'borrarRegistro' :
                $cadenaSql = 'INSERT INTO ';
                $cadenaSql .= $prefijo . 'pagina ';
                $cadenaSql .= '( ';
                $cadenaSql .= 'nombre,';
                $cadenaSql .= 'descripcion,';
                $cadenaSql .= 'modulo,';
                $cadenaSql .= 'nivel,';
                $cadenaSql .= 'parametro';
                $cadenaSql .= ') ';
                $cadenaSql .= 'VALUES ';
                $cadenaSql .= '( ';
                $cadenaSql .= '\'' . $_REQUEST ['nombrePagina'] . '\', ';
                $cadenaSql .= '\'' . $_REQUEST ['descripcionPagina'] . '\', ';
                $cadenaSql .= '\'' . $_REQUEST ['moduloPagina'] . '\', ';
                $cadenaSql .= $_REQUEST ['nivelPagina'] . ', ';
                $cadenaSql .= '\'' . $_REQUEST ['parametroPagina'] . '\'';
                $cadenaSql .= ') ';
                break;

            case 'consultausuario_basicos' :
                $cadenaSql = "SELECT (tipodocumento||''|| db.idusuario  ) documento, nombreusuario as Nombres, apellidousuario as Apellidos,estrato ";
                $cadenaSql .= " FROM data.datosbasicos db ";
                $cadenaSql .= " JOIN data.contratousuario cu ON cu.idusuario=db.idusuario ";
                $cadenaSql .= " JOIN data.tipocontrato tc on cu.tipocontrato=tc.tipocontrato ";
                $cadenaSql .= " WHERE db.estado_registro=TRUE";
                break;

            case 'consultausuario_contacto' :
                $cadenaSql = "SELECT direccion as ";
                $cadenaSql .= '  "Dirección" ,correo, telefono as "Teléfono" , celular';
                $cadenaSql .= " FROM data.datosbasicos db ";
                $cadenaSql .= " JOIN data.contratousuario cu ON cu.idusuario=db.idusuario ";
                $cadenaSql .= " JOIN data.tipocontrato tc on cu.tipocontrato=tc.tipocontrato ";
                $cadenaSql .= " WHERE db.estado_registro=TRUE";
                break;

            case 'consultausuario_contrato' :
                $cadenaSql = 'SELECT  idcontrato as Contrato, tc.descripcion as Tipo_Contrato';
                $cadenaSql .= " FROM data.datosbasicos db ";
                $cadenaSql .= " JOIN data.contratousuario cu ON cu.idusuario=db.idusuario ";
                $cadenaSql .= " JOIN data.tipocontrato tc on cu.tipocontrato=tc.tipocontrato ";
                $cadenaSql .= " WHERE db.estado_registro=TRUE";
                break;

            case 'consultausuario_terminos' :
                $cadenaSql = 'SELECT idcontrato , ts.descripcion as terminos ';
                $cadenaSql .= " FROM data.datosbasicos db ";
                $cadenaSql .= " JOIN data.contratousuario cu ON cu.idusuario=db.idusuario ";
                $cadenaSql .= " JOIN data.terminosservicio ts on cu.terminoscontrato=ts.idterminos ";
                $cadenaSql .= " WHERE db.estado_registro=TRUE";
                break;

            case 'enlacecomponente' :
                $cadenaSql = 'SELECT enlace ';
                $cadenaSql .= " FROM integracion.enlaces_componentes ec";
                $cadenaSql .= " WHERE ec.estado_registro=TRUE";
                $cadenaSql .= " AND ec.opcion='".$variable['opcion']."' ";
                $cadenaSql .= " AND ec.componente='".$variable['componente']."'";
                break;
        }

        return $cadenaSql;
    }

}

?>
