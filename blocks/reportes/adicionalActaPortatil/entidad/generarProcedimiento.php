<?php

namespace reportes\adicionalActaPortatil\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}
include_once 'Redireccionador.php';
include_once 'generarDocumentoActa.php';
include_once 'generarDocumentoActaAdicional.php';

class GenerarDocumento
{
    public $miConfigurador;
    public $elementos;
    public $miSql;
    public $conexion;
    public $contenidoPagina;
    public $rutaURL;
    public $esteRecursoDB;
    public $clausulas;
    public $beneficiario;
    public $esteRecursoOP;
    public $rutaAbsoluta;
    public function __construct($lenguaje, $sql)
    {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('interoperacion');
        $this->miSql = $sql;
        $this->lenguaje = $lenguaje;
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");

        // Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/archivos/";
        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento") . "/archivos/";

        /**
         * x.Cambio de directorio de Trabajo
         **/

        $this->cambiarDirectorioTrabajo();

        /**
         * x.Consultar Documentos en el Sistema
         **/

        $this->consultarDocumentos();

        /**
         * x.Validar Formato Existencia y Formato
         **/

        $this->validarArchivos();

        /**
         * x.Generar Documento de acuerdo al tipo de archivo
         **/

        $this->crearDocumentoTipoArchivo();

        /**
         * x.Redireccionar
         **/

        Redireccionador::redireccionar("RegistrosProcesados", count($this->registro_actualizados));

    }
    public function crearDocumentoTipoArchivo()
    {

        $tipos_imagenes = array("png", "jpg", "jpeg");

        $tipos_documento = array("pdf");

        foreach ($this->documentos as $key => $value) {
            $arreglo = explode('.', $value['nombre_documento']);
            $tipo_archivo = strtolower(end($arreglo));
            $this->nombreArhivo = $arreglo[0];

            if (in_array($tipo_archivo, $tipos_imagenes)) {

                $objDocumento = new GenerarDocumentoActa($this->rutaAbsoluta . $value['nombre_documento'], $this->rutaAbsoluta);

                $this->documento_pagina_1 = $objDocumento->retornarNombreDocumento();

            } elseif (in_array($tipo_archivo, $tipos_documento)) {
                $this->documento_pagina_1 = $this->rutaAbsoluta . $value['nombre_documento'];
            }

            /**
             * x.Consultar Beneficiario
             **/

            $this->consultarBeneficiario($value['id_beneficiario']);

            /**
             * x.Crear Documento Adicional
             **/

            $objDocumentoAdicional = new GenerarDocumentoActaAdicional($this->beneficiario, $this->rutaAbsoluta);

            $this->documento_pagina_2 = $objDocumentoAdicional->retornarNombreDocumento();

            /**
             * x.Unir DocumentosPDF
             **/

            $this->nombre_nuevo_documento = $this->unirDocumentos();

            /**
             * x.Actualizar Registro de Documento
             **/

            $this->actualizarRegistroDocumento($value['id_beneficiario'], $value['id']);

        }

    }

    public function actualizarRegistroDocumento($id_beneficiario, $id_documento)
    {

        $arreglo = array(
            'nombre_documento' => $this->nombre_nuevo_documento,
            'ruta_relativa' => $this->rutaURL . $this->nombre_nuevo_documento,
            'id_beneficiario' => $id_beneficiario,
            'id_documento' => $id_documento,
        );

        $cadenaSql = $this->miSql->getCadenaSql('actualizarRegistroDocumento', $arreglo);

        $this->registro_actualizados[] = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

    }

    public function unirDocumentos()
    {

        $sentencia_linux = 'pdftk ' . $this->documento_pagina_1 . ' ' . $this->documento_pagina_2 . '  cat output ' . $this->nombreArhivo . 'RW01.pdf';

        shell_exec($sentencia_linux);

        return $this->nombreArhivo . 'RW01.pdf';

    }
    public function consultarBeneficiario($id_beneficiario = '')
    {

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionBeneficiario', $id_beneficiario);
        $this->beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

    }

    public function validarArchivos()
    {

        foreach ($this->documentos as $key => $value) {

            if (!file_exists($this->rutaAbsoluta . $value['nombre_documento'])) {
                unset($this->documentos[$key]);
            }

            $busqueda = strrpos($value['nombre_documento'], "RW01");

            if ($busqueda != false) {
                unset($this->documentos[$key]);
            }

        }

    }

    public function consultarDocumentos()
    {

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionDocumentos');
        $this->documentos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

    }

    public function cambiarDirectorioTrabajo()
    {

        $this->directorioInicial = exec('pwd');

        chdir('archivos');

    }

}
$miDocumento = new GenerarDocumento($this->lenguaje, $this->sql);
