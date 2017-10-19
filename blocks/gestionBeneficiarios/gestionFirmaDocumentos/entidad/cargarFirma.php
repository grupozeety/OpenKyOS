<?php

namespace gestionBeneficiarios\gestionFirmaDocumentos\entidad;

include_once 'Redireccionador.php';

class GenerarReporteInstalaciones
{
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $proyectos;
    public $proyectos_general;
    public $directorio_archivos;
    public $ruta_directorio = '';
    public $archivo;

    public function __construct($sql)
    {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $_REQUEST['tiempo'] = time();

        $conexion = "interoperacion";

        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        /** 1. Validar Firma **/

        $this->validarArchivoFirma();

        /** 2. Cargar Firma **/

        $this->cargarFirmaDirectorio();

        /** 2. Registro y/o Actualizacíón Firma **/

        $this->gestionFirma();

        if ($this->registro) {
            Redireccionador::redireccionar('exitoGestionFirma');
        }
    }

    public function gestionFirma()
    {

        $arreglo = array(
            'id_beneficiario' => $_REQUEST['id_beneficiario'],
            'nombre_archivo' => $this->archivo['nombre'],
            'ruta_archivo' => $this->archivo['ruta_archivo'],
        );

        $cadenaSql = $this->miSql->getCadenaSql('gestionarFirma', $arreglo);

        $this->registro = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

        if (!$this->registro) {

            $this->error('errorRegistroFirma');
        }

    }

    public function cargarFirmaDirectorio()
    {

        $informacion_archivo = pathinfo($this->archivo['name']);

        $prefijo = substr(md5(uniqid(time())), 0, 10) . '.' . $informacion_archivo['extension'];

        $this->archivo['nombre'] = str_replace(' ', '_', $prefijo);

        $this->archivo['ruta_archivo'] = "/archivos/firmasBeneficiarios/";

        $archivo_destino = $this->miConfigurador->configuracion['raizDocumento'] . $this->archivo['ruta_archivo'] . $this->archivo['nombre'];

        if (!copy($this->archivo['tmp_name'], $archivo_destino)) {

            $this->error('errorCargaArchivo');
        }

    }

    public function validarArchivoFirma()
    {

        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0 && $_FILES['archivo']['size'] > 0) {

            $this->archivo = $_FILES['archivo'];

            if ($this->archivo['type'] != 'image/png') {

                $this->error('errorFormatoArchivo');

            }

        } else {

            $this->error('errorArchivo');

        }

    }

    public function error($var = '')
    {
        Redireccionador::redireccionar($var);
    }

}

$miProcesador = new GenerarReporteInstalaciones($this->sql);
