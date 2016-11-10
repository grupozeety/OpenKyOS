<?php
namespace gestionBeneficiarios\generacionContrato\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

include $ruta . "/plugin/html2pdf/html2pdf.class.php";

include_once "core/auth/SesionSso.class.php";

class GenerarDocumento {
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
    public function __construct($sql) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miSesionSso = \SesionSso::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->info_usuario = $this->miSesionSso->getParametrosSesionAbierta();

        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $conexion = "openproject";
        $this->esteRecursoOP = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        foreach ($this->info_usuario['description'] as $key => $rol) {

            $this->info_usuario['rol'][] = $rol;
        }

        if (!isset($_REQUEST["bloqueGrupo"]) || $_REQUEST["bloqueGrupo"] == "") {

            $this->rutaURL .= "/blocks/" . $_REQUEST["bloque"] . "/";
        } else {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
        }

        /**
         *  2. Información de Beneficiario
         **/

        $this->obtenerInformacionBeneficiario();

        /**
         *  3. Estruturar Documento
         **/

        $this->estruturaDocumento();

        /**
         *  4. Crear PDF
         **/

        $this->crearPDF();

    }
    public function obtenerInformacionBeneficiario() {

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionContrato');

        $beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $this->beneficiario = $beneficiario[0];

        //var_dump($this->beneficiario);exit;

    }

    public function crearPDF() {

        ob_start();
        $html2pdf = new \HTML2PDF('P', 'LEGAL', 'es', true, 'UTF-8', array(
            2,
            2,
            2,
            10,
        ));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($this->contenidoPagina);
        $html2pdf->Output('BorradorContrato_N_' . $this->beneficiario['numero_contrato'] . '_' . date('Y-m-d') . '.pdf', 'D');

    }
    public function estruturaDocumento() {
        unset($requisitos);
        $arreglo = array(
            'perfil_beneficiario' => $_REQUEST['tipo'],
            'id_beneficiario' => $this->beneficiario['id_beneficiario'],

        );

        $cadenaSql = $this->miSql->getCadenaSql('consultarValidacionRequisitos', $arreglo);
        $requisitos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $anexo_dir = '';

        if ($this->beneficiario['manzana'] != 0) {
            $anexo_dir .= " Manzana  #" . $this->beneficiario['manzana'] . " - ";
        }

        if ($this->beneficiario['bloque'] != 0) {
            $anexo_dir .= " Bloque #" . $this->beneficiario['bloque'] . " - ";
        }

        if ($this->beneficiario['torre'] != 0) {
            $anexo_dir .= " Torre #" . $this->beneficiario['torre'] . " - ";
        }

        if ($this->beneficiario['casa_apartamento'] != 0) {
            $anexo_dir .= " Casa/Apartamento #" . $this->beneficiario['casa_apartamento'];
        }

        $cadenaSql = $this->miSql->getCadenaSql('consultaNombreProyecto', $this->beneficiario['urbanizacion']);
        $urbanizacion = $this->esteRecursoOP->ejecutarAcceso($cadenaSql, "busqueda");
        $urbanizacion = $urbanizacion[0];

        $cadenaSql = $this->miSql->getCadenaSql('consultarTipoDocumento', "Cédula de Ciudadanía");
        $CodigoCedula = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $CodigoCedula = $CodigoCedula[0];

        $cadenaSql = $this->miSql->getCadenaSql('consultarTipoDocumento', "Tarjeta de Identidad");
        $CodigoTargeta = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $CodigoTargeta = $CodigoTargeta[0];

        $cedula = ($this->beneficiario['tipo_documento'] == $CodigoCedula['codigo']) ? '<b>(X)</b>' : '';
        $targeta = ($this->beneficiario['tipo_documento'] == $CodigoTargeta['codigo']) ? '<b>(X)</b>' : '';
        {
            $firma_contratista = "__________________________";

            $firma_beneficiario = "__________________________";
        }

        {

            $cadenaSql = $this->miSql->getCadenaSql('consultarParametroParticular', $this->beneficiario['medio_pago']);
            $medioPago = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            //var_dump($medioPago);
            $cadenaSql = $this->miSql->getCadenaSql('consultarParametroParticular', $this->beneficiario['tipo_pago']);
            $tipoPago = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
            //var_dump($tipoPago);exit;

            $medio_virtual = ($medioPago['descripcion'] == 'Virtual') ? "X" : " ";

            $medio_efectivo = ($medioPago['descripcion'] == 'Efectivo') ? "X" : " ";

            $tipo_prepago = ($tipoPago['descripcion'] == 'Prepago') ? "X" : " ";

            $tipo_pospago = ($tipoPago['descripcion'] == 'Pospago') ? "X" : " ";

        }
        {
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
                            </style>



                        <page backtop='35mm' backbottom='10mm' backleft='10mm' backright='10mm' footer='page'>
                            <page_header>
                            <br>
                            <br>
                                    <table  style='width:100%;' >
                                        <tr>
                                               <td align='center' style='width:30%;border=none;' >
                                                <img src='" . $this->rutaURL . "frontera/css/imagen/vivedigital.png'  width='125' height='40'>
                                                </td>
                                               <td align='center' style='width:70%;border=none;' >
                                                <font size='40px'><b>CONTRATO DE PRESTACIÓN DE SERVICIOS DE COMUNICACIONES</b></font>

                                            </td>
                                        </tr>
                                    </table>

                        </page_header>";

            $contenidoPagina .= "

    <table style='width:100%;'>
                        <tr>
                            <td style='width:35%;text-align=center;border:none'> </td>
                            <td style='width:30%;text-align=center;border:none'>
                                    <table style='width:100%;'>
                                        <tr>
                                            <td style='width:100%;border:none;text-align:center'><b>COMPRAVENTA DE SERVICIOS</b></td>
                                        </tr>
                                    </table>
                            </td>
                            <td style='width:5%;text-align=center;border:none'> </td>
                            <td style='width:30%;text-align=center;border:none'></td>
                        </tr>
                    </table>
                    <br>
                    <table style='width:100%;'>
                        <tr>
                            <td style='width:35%;text-align=center;border:none'> </td>
                            <td style='width:30%;text-align=center;border:none'>
                                    <table style='width:100%;'>
                                        <tr>
                                            <td style='width:25%;text-align=center;'>Fecha</td>
                                            <td style='width:25%;text-align=center;color:#c5c5c5;'>DD</td>
                                            <td style='width:25%;text-align=center;color:#c5c5c5;'>MM</td>
                                            <td style='width:25%;text-align=center;color:#c5c5c5;'>AAAA</td>
                                        </tr>
                                    </table>
                            </td>
                            <td style='width:5%;text-align=center;border:none'> </td>
                            <td style='width:30%;text-align=center;border:none'>
                                <table style='width:100%;'>
                                        <tr>
                                            <td style='width:50%;text-align=center;'>N° Contrato</td>
                                            <td style='width:50%;text-align=center;'>" . $this->beneficiario['numero_contrato'] . "</td>
                                        </tr>
                                    </table>
                             </td>
                        </tr>
                    </table>
                    <br>
                    <P style='text-align:justify;font-size:10px'>
    Entre LA CORPORACIÓN POLITÉCNICA NACIONAL DE COLOMBIA, en adelante POLITÉCNICA, entidad sin ánimo de lucro, domiciliada en la ciudad de Bogotá D.C, por una parte, y por la otra, la persona identificada como USUARIO, cuyos datos son los registrados a continuación, quien ha leído y aceptado en todos sus términos el presente documento y sus respectivos anexos, hemos convenido celebrar el presente CONTRATO DE SERVICIOS DE COMUNICACIONES, con el objeto de establecer las condiciones técnicas, jurídicas y económicas que regirán la prestación al USUARIO de servicios de comunicaciones, específicamente Internet fijo, en condiciones de calidad y eficiencia, a cambio de un precio en dinero, el cual se regirá por lo dispuesto en el Anexo Técnico del Contrato de Aporte N° 681 de 2015 y al documento de recomendaciones de la CRC “CONEXIONES DIGITALES Esquema para la implementación de subsidios e Incentivos para el acceso a Internet de Última milla”, la ley 1341/09, Resolución 3066/11 de la CRC, la regulación y reglamentación que expidan la CRC, el Min TIC y la SuperIndustria y Comercio, según su competencia, condiciones y anexos del presente contrato, en el marco del Contrato de Aporte N° 681 de 2015. y en las normas que la modifiquen o deroguen. El USUARIO, al iniciar el (los) procedimiento (s) de suscripción para el (los) plan (es) del Servicio de Comunicaciones en la modalidad postpago y prepago, según  aplique, (en lo sucesivo el Servicio) a través del medio que POLITÉCNICA ponga a disposición del USUARIO; y al suministrar sus datos personales, se entiende que acuerda suscribirse a uno de los planes ofrecidos por POLITÉCNICA del Servicio y expresa su entera e incondicional aceptación, a los términos y condiciones contenidos en el presente contrato y en los anexos que lo integran (en lo sucesivo denominado, el Contrato) para disponer del Servicio.
                    </P>
                    <table style='width:100%;'>
                        <tr>
                            <td rowspan='6' style='width:15%;text-align=center;'><b>DATOS ABONADO SUSCRIPTOR</b></td>
                            <td style='width:15%;text-align=center;'><b>Nombres</b></td>
                            <td style='width:10%;text-align=center;'>" . $this->beneficiario['nombres'] . "</td>
                            <td style='width:10%;text-align=center;'><b>Primer Apellido</b></td>
                            <td style='width:10%;text-align=center;'>" . $this->beneficiario['primer_apellido'] . "</td>
                            <td style='width:5%;text-align=center;'><b>Segundo Apellido</b></td>
                            <td colspan='2' style='width:10%;text-align=center;'>" . $this->beneficiario['segundo_apellido'] . "</td>
                        </tr>
                        <tr>
                            <td style='width:15%;text-align=center;'><b>Tipo Documento</b></td>
                            <td style='width:5%;text-align=center;'>CC " . $cedula . "</td>
                            <td style='width:5%;text-align=center;'>TI " . $targeta . "</td>
                            <td style='width:10%;text-align=center;'><b>Número</b></td>
                            <td style='width:15%;text-align=center;'>" . $this->beneficiario['numero_identificacion'] . "</td>
                            <td style='width:15%;text-align=center;'><b>Lugar/Fecha Expedición</b></td>
                            <td style='width:10%;text-align=center;'>" . $this->beneficiario['fecha_expedicion'] . "</td>
                        </tr>
                         <tr>
                            <td style='width:15%;text-align=center;'><b>Dirección Domicilio</b></td>
                            <td colspan='6' style='width:70%;text-align=center;'>" . $this->beneficiario['direccion_domicilio'] . " " . $anexo_dir . "</td>
                        </tr>
                       <tr>
                            <td style='width:15%;text-align=center;'><b>Departamento</b></td>
                            <td colspan='1'style='width:10%;text-align=center;'>" . $this->beneficiario['nombre_departamento'] . "</td>
                            <td style='width:10%;text-align=center;'><b>Municipio</b></td>
                            <td colspan='1' style='width:10%;text-align=center;'>" . $this->beneficiario['nombre_municipio'] . "</td>
                           <td colspan='1'style='width:5%;text-align=center;'><b>Urbanización</b></td>
                            <td colspan='2'style='width:20%;text-align=center;'>" . $urbanizacion['nombre'] . "</td>
                        </tr>
                        <tr>
                            <td style='width:15%;text-align=center;'><b>Estrato</b></td>
                            <td style='width:5%;text-align=center;'>VIP</td>
                            <td style='width:5%;text-align=center;'>1 Residencial</td>
                            <td style='width:5%;text-align=center;'>2 Residencial</td>
                           <td colspan='1'style='width:5%;text-align=center;'>Barrio</td>
                            <td colspan='2'style='width:10%;text-align=center;'>" . $this->beneficiario['barrio'] . " </td>
                        </tr>
                         <tr>
                            <td style='width:15%;text-align=center;'><b>Telefono</b></td>
                             <td colspan='1' style='width:10%;text-align=center;'>" . $this->beneficiario['telefono'] . "</td>
                            <td style='width:10%;text-align=center;'><b>Celular</b></td>
                            <td style='width:10%;text-align=center;'>" . $this->beneficiario['celular'] . "</td>
                             <td colspan='1' style='width:5%;text-align=center;'><b>Correo Electrónico</b></td>
                            <td colspan='2'style='width:10%;text-align=center;'>" . $this->beneficiario['correo'] . "</td>
                        </tr>
                    </table>
                    <br>
                    <table style='width:100%;'>
                        <tr>
                            <td rowspan='3' style='width:15%;text-align=center;'><b>DATOS SERVICIO</b></td>
                            <td style='width:15%;text-align=center;'><b>Velocidad Internet</b></td>
                            <td style='width:30%;text-align=right;'>" . $this->beneficiario['velocidad_internet'] . " MB</td>
                            <td style='width:20%;text-align=center;'><b>Vigencia Servicio</b></td>
                            <td style='width:20%;text-align=center;'>  </td>
                        </tr>
                        <tr>
                            <td style='width:15%;text-align=center;'><b>Fecha de inicio del Servicio</b></td>
                            <td colspan='3' style='width:70%;text-align=center;'><b>" . $this->beneficiario['fecha_inicio_vigencia_servicio'] . "</b></td>
                        </tr>
                        <tr>
                            <td style='width:15%;text-align=center;'><b>Valor Mensual Servicio Básico </b></td>
                            <td colspan='3' style='width:70%;text-align=left;'><b>$ " . $this->beneficiario['valor_tarificacion'] . "</b></td>
                        </tr>
                     </table>
                     <br>
                      <table style='width:100%;'>
                         <tr>
                            <td rowspan='3' style='width:15%;text-align=center;'><b>DATOS FACTURACIÓN</b></td>
                            <td style='width:15%;text-align=center;'><b>Forma de Pago</b></td>
                            <td style='width:15%;text-align=center;'>Prepago (<b>" . $tipo_prepago . "</b>)</td>
                            <td style='width:15%;text-align=center;'>Postpago (<b>" . $tipo_pospago . "</b>)</td>
                        </tr>
                        <tr>
                            <td style='width:15%;text-align=center;'><b>Mecanismos de Pago</b></td>
                            <td style='width:15%;text-align=center;'>Virtual (<b>" . $medio_virtual . "</b>)</td>
                            <td style='width:15%;text-align=center;'>Efectivo (<b>" . $medio_efectivo . "</b>)</td>
                        </tr>
                        <tr>
                            <td style='width:15%;text-align=center;'><b>TOTAL A PAGAR FACTURA MENSUAL</b></td>
                            <td  colspan='2' style='width:70%;text-align=left;'>$ " . $this->beneficiario['valor_tarificacion'] . "</td>
                        </tr>
                        </table>
                     <br>
                     <br>
                     <br>
                     <br>
                    <table style='width:100%;'>
                        <tr>
                            <td text-align=center;'><b>DECLARACIONES DEL SUSCRIPTOR</b></td>
                        </tr>
                        <tr>
                            <td text-align=justify;font-size:9.5px'>
                            1. Que comprendo que el equipo de cómputo y el módem se entregan en comodato.<br>
                            2. Que me responsabilizo a hacer buen uso y manipular correctamente el equipo entregado y me hago responsable por el deterioro por mal uso o pérdida del mismo.<br>
                            3. Que conozco que el mal uso de los equipos entregados puede acarrear la suspensión temporal o total del servicio.<br>
                            4. Que me comprometo a no transferir el equipo a ubicación o persona diferente a la consignada en el presente documento.<br>
                            5. Que al finalizar el contrato de servicio devolveré a la Corporación Politécnica el equipo entregado y los accesorios que permitían el acceso a internet.<br>
                            6. Que se ha informado de los mecanismos que tengo a mi disposición para solicitar o reportar fallos en los servicios de acceso, así como de los tiempos máximos en los cuales se tramitarán las peticiones, quejas o reclamos.<br>
                            7. Que conozco y acepto los términos de Protección de la Información y Tratamiento de Uso de Datos que se encuentran descritos en el documento de Términos y Condiciones de Protección de Datos, disponible en el portal Web del Proyecto.<br>
                            8. Que conozco, comprendo y acepto el Régimen de Derechos y Obligaciones plasmado en los anexos del presente contrato.
                            </td>
                        </tr>
                        <tr>
                            <td text-align=center;'><b>RÉGIMEN DE DERECHOS DEL USUARIO EN RELACIÓN CON LOS SERVICIOS PRESTADOS</b></td>
                        </tr>
                        <tr>
                            <td text-align=justify;font-size:9.5px'>
                            Son derechos de los usuarios (Art 10 Resolución CRC 3066/11): 1. Contar con la medición apropiada de sus consumos reales, mediante los instrumentos tecnológicos apropiados para efectuar dicha medición, dentro de los plazos fijados por la CRC. 2. Ejercer los derechos contenidos en el Régimen de Protección al USUARIO de los Servicios de Comunicaciones, establecido en la Resolución CRC 3066 de 2011 y demás normas que lo modifiquen o deroguen; así como todos aquellos derechos que se deriven de las disposiciones contenidas en la regulación expedida por el MinTIC, la CRC y la Superintendencia de Industria y Comercio. 3. Recibir los servicios contratados de manera continua e ininterrumpida en los términos del Anexo Técnico del Contrato de Aporte N° 681 de 2015 salvo por circunstancias de fuerza mayor, caso fortuito o hecho de un tercero que impidan la prestación del servicio en condiciones normales; en caso de que el (los) servicios prestados por parte de POLITÉCNICA sean suspendidos por motivos de algún daño que impida la prestación del servicio por causas diferentes a fuerza mayor, caso fortuito o hecho de un tercero, POLITÉCNICA reparará el daño en la mayor brevedad posible, de tal forma que la prestación del servicio no se vea gravemente afectada.
                            </td>
                        </tr>
                    </table>
                    <nobreak>
                    <table>
                        <tr>
                            <td text-align=center;'><b>RÉGIMEN DE OBLIGACIONES DEL USUARIO EN RELACIÓN CON LOS SERVICIOS PRESTADOS</b></td>
                        </tr>
                        <tr>
                            <td text-align=justify;font-size:9.5px'>
                            1. Las contenidas en la Resolución CRC 3066/11 y demás normas que lo modifiquen o deroguen; así como todas aquellas derivadas de las disposiciones contenidas en la regulación expedida por el MinTIC, la CRC y la Superintendencia de Industria y Comercio.<br>
2. La utilización de la clave de acceso al servicio cuando ello resulte aplicable, es personal e intransferible y está a cargo y bajo la plena responsabilidad del USUARIO.
3. Facilitar el acceso al inmueble a personas debidamente autorizadas e identificadas por POLITÉCNICA para efectuar revisiones a las instalaciones internas.<br>
4. Responder por cualquier anomalía, fraude o adulteración que se encuentre en las instalaciones internas, así como por las variaciones o modiﬁcaciones que sin autorización de POLITÉCNICA se hagan en relación con las condiciones del servicio contratado. Una vez suscrita el Acta de entrega de equipos y servicio de banda ancha, el USUARIO será responsable de la instalación entregada.<br>
5. El USUARIO entiende y acepta que le está prohibida la comercialización a terceras personas del servicio y que en consecuencia los beneficios que obtenga en virtud del mismo no son objeto de venta o comercialización, y que de hacerlo, su conducta constituye causal de cancelación del servicio y terminación del contrato.<br>
6. Proporcionar a las instalaciones internas, y equipos en general, el mantenimiento y uso adecuado con el ﬁn de prevenir daños que puedan ocasionar deﬁciencias o interrupciones en el suministro del servicio contratado.<br>
7. El USUARIO entiende y acepta que es responsable absoluto de la utilización del servicio de comunicaciones, por lo tanto, es el único responsable de las transacciones que realice por intermedio de éste, así como de la seguridad de su identificación de ingreso, su clave y cualquier código de seguridad de bloqueo que utilice para proteger el acceso a sus datos, su(s) nombre(s) de archivo y sus archivos, el acceso a la red, o cualquier otra información que el USUARIO difunda a través del uso del servicio de POLITÉCNICA.<br>
8. Informar de inmediato a POLITÉCNICA sobre cualquier irregularidad, anomalía o cambio que se presente en las instalaciones internas, o la variación del propietario, dirección u otra novedad que implique modiﬁcación a las condiciones y datos registrados en el contrato de servicios y/o en el sistema de información comercial.<br>
9.  El USUARIO no transferirá su cuenta o le permitirá a otra persona usar su cuenta, de ser así, este será el único responsable del uso que se le dé a la misma.<br>
10. Informar sobre cualquier irregularidad, omisión, inconsistencia o variación que se detecte en la factura de cobro.<br>
11. Al ﬁnalizar este contrato o en cualquier tiempo, permitir el retiro de los equipos y elementos que le hayan sido entregados a título de comodato, y devolver cualquier información técnica de soporte que tenga en su poder.<br>
12. Abstenerse de trasladar los equipos, realizar modiﬁcación a la red instalada, establecer derivaciones o utilizar cualquier otro mecanismo que permita extender el servicio de comunicaciones a otros computadores, puntos, lugares o establecimientos comerciales diferentes a los cobijados y autorizados por este contrato, sin previa autorización escrita de POLITÉCNICA.<br>
13. En caso de pérdida, hurto o deterioro imputable al USUARIO, responder y pagar en forma inmediata su valor correspondiente, conforme a los precios vigentes a la fecha en que se haga efectivo el pago.<br>
14. Cumplir con los requisitos mínimos de prestación del servicio, exigidos por POLITÉCNICA, de conformidad con la modalidad del servicio contratado.<br>
15. De conformidad con lo establecido en la Ley 679 /01 y el Decreto 1524/02, abstenerse de alojar en su propio sitio de Internet, imágenes, textos, documentos o archivos audiovisuales que impliquen directa o indirectamente actividades sexuales con menores de edad; material pornográﬁco, en especial en modo de imágenes o videos, cuando existan indicios de que las personas fotograﬁadas o ﬁlmadas son menores de edad; vínculos o “links” sobre sitios telemáticos que contengan o distribuyan material pornográﬁco relativo a menores de edad.<br>
16. De conformidad con la Ley 679/01, denunciar ante las autoridades competentes cualquier acto criminal contra menores de edad de que tengan conocimiento, incluso de la difusión de material pornográﬁco asociado a menores; combatir con todos los medios técnicos a su alcance la difusión de material pornográﬁco con menores de edad; abstenerse de usar las redes globales de información para divulgación de material ilegal con menores de edad; establecer mecanismos técnicos de bloqueo por medio de los cuales los usuarios se puedan proteger a sí mismos o a sus hijos de material ilegal, ofensivo o indeseable en relación con menores de edad.<br>
17. Cerciorarse que el uso que está dando a la red no viola ninguna norma municipal, departamental o nacional, además de no violar las leyes en materia de derechos de autor, difamación, invasión de privacidad, distribución de información conﬁdencial, información protegida y propiedad intelectual.<br>
18. El USUARIO suministrará a POLITÉCNICA información precisa, veraz, completa y actualizada para mantener actualizado el servicio; así mismo, deberá notificar a POLITÉCNICA dentro de los diez (10) días hábiles, de cualquier cambio en sus datos o información. Si el USUARIO incumple esta obligación responderá por los daños y perjuicios que tal omisión le cause a POLITÉCNICA, y en todo caso, el USUARIO queda en la obligación de pagar oportunamente el servicio.<br>
19. Abstenerse de utilizar el servicio con ﬁnes ilegales o contra la moral pública, ni comercializarlo a terceros, ni para perturbar a terceros, ni de tal manera que llegare a interferir injustiﬁcadamente con el uso del servicio por parte de otros clientes o terceros.<br>
20. Contratar exclusivamente con ﬁrmas instaladoras caliﬁcadas, la ejecución de instalaciones internas, o la realización de labores relacionadas con modiﬁcaciones, ampliaciones y trabajos similares. Quedan bajo su exclusiva responsabilidad los riesgos que puedan presentarse por el incumplimiento de esta disposición.<br>
21. Adoptar las políticas y las recomendaciones de seguridad que garanticen el uso adecuado de las claves de acceso a internet, correo electrónico, y demás servicios conexos o derivados para el intercambio de información por internet.<br>
22. El USUARIO, en caso de ser padre de familia o tener a su cargo personas menores de edad, conoce y acepta que la información obtenible a través de cualquier servicio de acceso a internet puede incluir materiales no aptos para menores, como sexuales explícitos o de contenido adulto, por lo cual el USUARIO será el único responsable del acceso que menores de edad puedan tener a ese material a través de la utilización de su cuenta.<br>
 23. Las demás obligaciones previstas en el presente contrato y en la normatividad vigente.
                            </td>
                        </tr>
                    </table>
                    <table style='width:100%;'>
                        <tr>
                            <td text-align=center;'><b>PETICIONES, QUEJAS Y RECURSOS</b></td>
                        </tr>
                        <tr>
                            <td text-align=justify;font-size:9.5px'>
                            El USUARIO tiene derecho a presentar peticiones, quejas y recursos –PQR- ante POLITÉCNICA, en forma verbal o escrita, mediante los medios tecnológicos o electrónicos asociados a los mecanismos obligatorios de atención al USUARIO dispuestos en el presente contrato. La presentación de peticiones, quejas y recursos –PQR- y el trámite de las mismas no requieren presentación personal ni de intervención de abogado, aunque el USUARIO autorice a otra persona para que presente la PQR. Las peticiones, quejas y recursos serán tramitadas por POLITÉCNICA de conformidad con las normas vigentes sobre el derecho de petición y recursos previstos en el Código de Procedimiento Administrativo y de lo Contencioso Administrativo y en la regulación vigente. Cuando se presenten las PQR en forma verbal, bastará con informarle a POLITÉCNICA, el nombre completo del peticionario, el número de identificación y el motivo por el cual presenta la PQR. En dicho caso, POLITÉCNICA podrá responderle de la misma forma, dejando constancia de la presentación de la PQR. Las PQR presentadas de forma escrita, deberán contener lo siguiente: el nombre del proveedor al que se dirige (POLITÉCNICA), el nombre, identificación y dirección de notificación del USUARIO, y los hechos en que se fundamenta la solicitud. POLITÉCNICA le informará por cualquier medio físico o electrónico la constancia de presentación de la PQR y un código único numérico –CUN-, el cual deberá mantenerse durante todo el trámite.(Art 39 y ss. Res 3066/2011 CRC).
                            </td>
                        </tr>
                        <tr>
                            <td text-align=center;'><b>MECANISMOS DE ATENCIÓN</b></td>
                        </tr>
                        <tr>
                            <td text-align=justify;font-size:9.5px'>
                            De acuerdo con lo establecido en la Resolución 3066 de 2011, POLITÉCNICA tiene a disposición del USUARIO los siguientes mecanismos de atención: Oficina Principal: Carrera 20 # 27-87 Oficina 302, Edificio Cámara de Comercio Sincelejo - Sucre. Línea Gratuita Nacional: 018000961016. Correo Electrónico:  soportecd2@soygenial.co.
                            </td>
                        </tr>
                    </table>
                    ";

            $contenidoPagina .= "
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <table style='width:100%;border:none'>
            <tr>
                <td style='width:50%;border:none'>
                    <table style='width:100%;border:none'>
                    <tr>
                    <td style='width:25%;text-align:left;border:none'>Nombre Instalador:</td>
                    <td style='width:25%;text-align:left;border:none'>" . $this->info_usuario['uid'][0] . "</td>
                    <td style='width:50%;text-align:center;border:none'> </td>
                    </tr>
                    <tr>
                    <td style='width:25%;text-align:left;border:none'>FIRMA :</td>
                    <td style='width:25%;text-align:left;border:none'>" . $firma_contratista . "</td>
                    <td style='width:50%;text-align:center;border:none'> </td>
                    </tr>
                    </table>
                </td>
                <td style='width:50%;border:none'>
                    <table style='width:100%;border:none'>
                    <tr>
                    <td style='width:25%;text-align:left;border:none'>Nombre Suscriptor:</td>
                    <td style='width:25%;text-align:left;border:none'>" . $this->beneficiario['nombres'] . " " . $this->beneficiario['primer_apellido'] . " " . $this->beneficiario['segundo_apellido'] . "</td>
                    <td style='width:50%;text-align:center;border:none'> </td>
                    </tr>
                    <tr>
                    <td style='width:25%;text-align:left;border:none'>C.C :</td>
                    <td style='width:25%;text-align:left;border:none'>" . $this->beneficiario['numero_identificacion'] . "</td>
                    <td style='width:50%;text-align:center;border:none'> </td>
                    </tr>
                    <tr>
                    <td style='width:25%;text-align:left;border:none'>FIRMA :</td>
                    <td style='width:25%;text-align:left;border:none'>" . $firma_beneficiario . "</td>
                    <td style='width:50%;text-align:center;border:none'> </td>
                    </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>
        <br>
        <br>
        <br>
        <br>
        <table style='width:100%;border:none'>
                        <tr>
                            <td text-align=center;' style='width:100%;'><b>OBSERVACIONES DEL OPERADOR</b></td>
                        </tr>
                        <tr>
                            <td style='width:100%;'><br><br><br></td>
                        </tr>
        </table>
        </nobreak>";

            /* if ($requisitos) {

            $contenidoDocumentos = "<br> <div style='page-break-after:always; clear:both'></div>
            <P style='text-align:center'><b>Documentos Faltantes para el Contrato</b></P><br><br>";
            foreach ($requisitos as $key => $value) {
            if ($value['obligatoriedad'] = '1' && is_null($value['nombre_documento'])) {
            $requisitosFaltantesObligatorios = true;

            $contenidoPagina .= $contenidoDocumentos . "<P style='text-align:left'>" . $value['nombre_requisitos'] . "</P><br>";
            $contenidoDocumentos = '';

            }

            }
            }*/

            $contenidoPagina .= "</page>";
        }

        $this->contenidoPagina = $contenidoPagina;
    }
}
$miDocumento = new GenerarDocumento($this->sql);

?>
