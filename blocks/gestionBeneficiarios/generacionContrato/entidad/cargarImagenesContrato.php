<?php
namespace gestionBeneficiarios\generacionContrato\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

use gestionBeneficiarios\generacionContrato\entidad\Redireccionador;

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

include $ruta . "/plugin/html2pdf/html2pdf.class.php";
include_once 'Redireccionador.php';
require_once 'sincronizar.php';

class FormProcessor {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $archivos_datos;
    public $esteRecursoDB;
    public $datos_contrato;
    public $rutaURL;
    public $rutaAbsoluta;
    public $clausulas;
    public $registro_info_contrato;
    public function __construct($lenguaje, $sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");

        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");

        if (!isset($_REQUEST["bloqueGrupo"]) || $_REQUEST["bloqueGrupo"] == "") {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloque"] . "/";
        } else {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
        }

        $this->sincronizacion = new Sincronizar($lenguaje, $sql);
        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['tiempo'] = time();

        //$this->cargarClausula();

        /**
         *  1.
         **/

        $this->cargarArchivos();

        /**
         *  2.
         **/

        $this->estruturaDocumento();

        /**
         *  3.
         **/

        $this->asosicarCodigoDocumento();

        /**
         *  4.
         **/

        $this->crearPDF();

        /**
         *  5.
         **/

        $this->procesarInformacion();

        if ($this->registro_documento) {
            Redireccionador::redireccionar("InsertoInformacionDocumento");
        } else {
            Redireccionador::redireccionar("NoInsertoInformacionDocumento");
        }

    }

    public function procesarInformacion() {

        $arreglo = array(
            'ruta_contrato' => $this->ruta_documento_url,
            'nombre_contrato' => $this->nombreContrato,
        );

        $cadenaSql = $this->miSql->getCadenaSql('registrarDocumentoContrato', $arreglo);

        $this->registro_documento = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        unlink($this->ruta_absoluta_pagina_1);
        unlink($this->ruta_absoluta_pagina_2);

        $this->archivos_datos = array(
            'ruta_archivo' => $this->ruta_documento_url,
            'rutaabsoluta' => $this->ruta_documento_absoluta,
            'nombre_archivo' => $this->nombreContrato,
            'campo' => 901,
            'tipo_documento' => '128',
            'id_beneficiario' => $_REQUEST['id_beneficiario'],
        );

        $resultado = $this->sincronizacion->sincronizarAlfresco($_REQUEST['id_beneficiario'], $this->archivos_datos);

    }

    public function asosicarCodigoDocumento() {

        $this->prefijo = substr(md5(uniqid(time())), 0, 6);
        $cadenaSql = $this->miSql->getCadenaSql('consultarParametroContrato', '128');
        $id_parametro = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
        $tipo_documento = $id_parametro['id_parametro'];
        $descripcion_documento = $id_parametro['id_parametro'] . '_' . $id_parametro['descripcion'];
        $nombre_archivo = str_replace(" ", "_", $descripcion_documento);
        $this->nombreContrato = $_REQUEST['id_beneficiario'] . "_" . $nombre_archivo . "_" . $this->prefijo . '.pdf';

    }
    public function crearPDF() {
        //ini_set('xdebug.max_nesting_level', 400);
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
        $this->rutaURL .= '/archivos/contratos/';
        $this->rutaAbsoluta .= '/archivos/contratos/';

        $this->ruta_documento_url = $this->rutaURL . $this->nombreContrato;
        $this->ruta_documento_absoluta = $this->rutaAbsoluta . $this->nombreContrato;

        ob_start();
        $html2pdf = new \HTML2PDF('P', 'LEGAL', 'es', true, 'UTF-8', array(
            2,
            2,
            2,
            10,
        ));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($this->contenidoPagina);
        $html2pdf->Output($this->rutaAbsoluta . $this->nombreContrato, 'F');

    }
    public function estruturaDocumento() {

        $contenidoPagina = "
                            <style type=\"text/css\">
                                table {

                                    font-family:Helvetica, Arial, sans-serif; /* Nicer font */

                                    border-collapse:collapse; border-spacing: 3px;
                                }
                                td, th {
                                    border: 1px solid #CCC;
                                    height: 13px;
                                } /* Make cells a bit taller */

                                th {

                                    font-weight: bold; /* Make sure they're bold */
                                    text-align: center;
                                    font-size:10px;
                                }
                                td {

                                    text-align: left;

                                }

                                draw {

                                  transform:scale(2.0);

                                }
                            </style>



                        <page backtop='10mm' backbottom='10mm' backleft='10mm' backright='10mm' footer='page'>
                                    <table  style='width:100%;' >
                                          <tr>
                                            <td align='center' style='width:100%;border=none;' >
                                            <img src='" . $this->ruta_relativa_pagina_1 . "'  width='725' height='1210'>
                                            </td>
                                          </tr>
                                    </table>


                                    <table  style='width:100%;' >
                                          <tr>
                                            <td align='center' style='width:100%;border=none;' >
                                            <img src='" . $this->ruta_relativa_pagina_2 . "'  width='725' height='1210'>
                                            </td>
                                          </tr>
                                    </table>
                        </page>";

        $this->contenidoPagina = $contenidoPagina;

    }

    public function cargarArchivos() {

        $archivo_datos = '';
        foreach ($_FILES as $key => $archivo) {

            if ($archivo['error'] == 0) {

                $this->prefijo = substr(md5(uniqid(time())), 0, 6);
                /*
                 * obtenemos los datos del Fichero
                 */
                $tamano = $archivo['size'];

                $allowed = array(
                    'image/jpeg',
                    'image/png',
                );

                if (!in_array($_FILES[$key]['type'], $allowed)) {

                    Redireccionador::redireccionar("ErrorTipoArhivoCargar");
                }

                $nombre_archivo = str_replace(" ", "", $archivo['name']);
                /*
                 * guardamos el fichero en el Directorio
                 */
                $ruta_absoluta = $this->rutaAbsoluta . "/entidad/imagenes/" . $this->prefijo . "_" . $nombre_archivo;

                $ruta_relativa = $this->rutaURL . "/entidad/imagenes/" . $this->prefijo . "_" . $nombre_archivo;

                $archivo['rutaDirectorio'] = $ruta_absoluta;

                if (!copy($archivo['tmp_name'], $ruta_absoluta)) {
                    echo "Error Ruta No Encontrada o Permisos Directorio";exit;
                    Redireccionador::redireccionar("ErrorCargarFicheroDirectorio");
                }

                switch ($key) {
                    case 'foto_pag1':
                        $this->ruta_relativa_pagina_1 = $ruta_relativa;
                        $this->ruta_absoluta_pagina_1 = $ruta_absoluta;
                        break;

                    case 'foto_pag2':
                        $this->ruta_relativa_pagina_2 = $ruta_relativa;
                        $this->ruta_absoluta_pagina_2 = $ruta_absoluta;
                        break;

                }

            } else {
                //exit;
                Redireccionador::redireccionar("ErrorArchivo");
            }

        }

    }
}
$miProcesador = new FormProcessor($this->lenguaje, $this->sql);
?>

