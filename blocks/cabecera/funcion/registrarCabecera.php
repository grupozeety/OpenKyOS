<?php

namespace cabecera\funcion;

use cabecera\funcion\redireccionar;

include_once 'redireccionar.php';
if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}
class Registrar
{

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miFuncion;
    public $miSql;
    public $conexion;

    public function __construct($lenguaje, $sql, $funcion)
    {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;
        $this->miFuncion = $funcion;
    }
    public function procesarFormulario()
    {

        $cabecera = array();

        $cabecera['codigo_cabecera'] = $_REQUEST['codigo_cabecera'];
        $cabecera['descripcion'] = $_REQUEST['descripcion'];
        $departamento = explode(" ", $_REQUEST['departamento']);
        $cabecera['departamento'] = $departamento[0];
        $municipio = explode(" ", $_REQUEST['municipio']);
        $cabecera['municipio'] = $municipio[0];
        $cabecera['id_urbanizacion'] = $_REQUEST['urbanizacion'];
        $cabecera['ip_olt'] = $_REQUEST['ip_olt'];
        $cabecera['mac_olt'] = $_REQUEST['mac_olt'];
        $cabecera['port_olt'] = $_REQUEST['port_olt'];
        $cabecera['nombre_olt'] = $_REQUEST['nombre_olt'];
        $cabecera['puerto_olt'] = $_REQUEST['puerto_olt'];

        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

        $rutaBloque = $this->miConfigurador->getVariableConfiguracion("raizDocumento") . "/blocks/";
        $rutaBloque .= $esteBloque['nombre'];
        $host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/" . $esteBloque['nombre'];

        switch ($_REQUEST['opcion']) {
            case 'actualizarCabecera':
                $cadenaSql = $this->miSql->getCadenaSql('actualizarCabecera', $_REQUEST['id_cabecera']);

                $resultado_actualizacion = $esteRecursoDB->ejecutarAcceso($cadenaSql, "insertar");

                if ($resultado_actualizacion) {

                    $cadenaSql = $this->miSql->getCadenaSql('registrarCabecera', $cabecera);
                    $cadenaSql = str_replace("''", 'NULL', $cadenaSql);
                    $resultado_registro = $esteRecursoDB->ejecutarAcceso($cadenaSql, "insertar");
                }

                break;

            case 'registrarCabecera':

                $cadenaSql = $this->miSql->getCadenaSql('registrarCabecera', $cabecera);
                $cadenaSql = str_replace("''", 'NULL', $cadenaSql);
                $resultado_registro = $esteRecursoDB->ejecutarAcceso($cadenaSql, "insertar");
                break;
        }

        if (isset($resultado_registro) && $resultado_registro) {
            redireccion::redireccionar('inserto');
        } else {
            redireccion::redireccionar('noInserto');
        }
    }

    public function resetForm()
    {
        foreach ($_REQUEST as $clave => $valor) {

            if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
                unset($_REQUEST[$clave]);
            }
        }
    }
}

$miRegistrador = new Registrar($this->lenguaje, $this->sql, $this->funcion);

$resultado = $miRegistrador->procesarFormulario();
