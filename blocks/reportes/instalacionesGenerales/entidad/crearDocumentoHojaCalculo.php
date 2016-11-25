<?php
namespace reportes\instalacionesGenerales\entidad;

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

require_once $ruta . "/plugin/PHPExcel/Classes/PHPExcel.php";

class GenerarReporteExcelInstalaciones {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $proyectos;
    public $objCal;

    public function __construct($sql, $proyectos) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->proyectos = $proyectos;

        /**
         * 1. Configuración Propiedades Documento
         **/
        $this->configurarDocumento();

        /**
         * 2. Estruturamiento Esquema Reporte
         **/
        $this->generarEsquemaDocumento();

        /**
         * 3. Estruturamiento Información OpenProject
         **/
        $this->estruturarInformacion();

        /**
         * XX. Retornar Documento Reporte
         **/
        $this->retornarDocumento();

    }
    
    public function asignarValoresCampos($informacion){
    	
    	$this->A = $informacion['a_'];
    	$this->B = $informacion['b_'];
    	$this->C = $informacion['c_'];
    	$this->D = $informacion['d_'];
    	$this->E = $informacion['e_'];
    	$this->F = $informacion['f_'];
    	$this->G = $informacion['g_'];
    	$this->H = $informacion['h_'];
    	$this->I = $informacion['i_'];
    	$this->J = $informacion['j_'];
    	$this->K = $informacion['k_'];
    	$this->L = $informacion['l_'];
    	$this->M = $informacion['m_'];
    	$this->N = $informacion['n_'];
    	$this->O = $informacion['o_'];
    	$this->P = $informacion['p_'];
    	$this->Q = $informacion['q_'];
    	$this->R = $informacion['r_'];
    	$this->S = $informacion['s_'];
    	$this->T = $informacion['t_'];
    	$this->U = $informacion['u_'];
    	$this->V = $informacion['v_'];
    	$this->W = $informacion['w_'];
    	$this->X = $informacion['x_'];
    	
    	$this->Y = $informacion['c_a']; //Nuevos Campos
    	
    	$this->Z = $informacion['y_'];
    	
    	$this->AA = $informacion['c_b']; //Nuevos Campos
    	
    	$this->AB = $informacion['z_'];
    	$this->AC = $informacion['a_a'];
    	
    	$this->AD = $informacion['c_c']; //Nuevos Campos
    	
    	$this->AE = $informacion['a_b'];
    	$this->AF = $informacion['a_c'];
    	
    	$this->AG = $informacion['a_d'];
    	$this->AH = $informacion['a_e'];
    	$this->AI = $informacion['a_f'];
    	
    	$this->AJ = $informacion['a_j'];
    	
    	$this->AK = $informacion['a_g'];
    	$this->AL = $informacion['a_h'];
    	
    	$this->AM = $informacion['a_i'];
    	
    	$this->AN = $informacion['a_k'];
    	$this->AO = $informacion['a_l'];
    	$this->AP = $informacion['a_m'];
    	
    	$this->AQ = $informacion['a_n'];
    	
    	$this->AR = $informacion['a_o'];
    	
    	$this->AS = $informacion['a_p'];
    	
    	$this->AT = $informacion['c_d']; //Nuevos Campos
    	
    	$this->AU = $informacion['a_q'];
    	
    	$this->AV = $informacion['a_r'];
    	$this->AW = $informacion['a_s'];
    	$this->AX = $informacion['a_t'];
    	
    	$this->AY = $informacion['a_u'];
    	$this->AZ = $informacion['a_v'];
    	$this->BA = $informacion['a_w'];
    	$this->BB = $informacion['a_x'];
    	$this->BC = $informacion['a_y'];
    	$this->BD = $informacion['a_z'];
    	
    	$this->BE = $informacion['b_a'];
    	$this->BF = $informacion['b_b'];
    	$this->BG = $informacion['b_c'];
    	
    	$this->BH = $informacion['b_d'];
    	$this->BI = $informacion['b_e'];
    	$this->BJ = $informacion['b_f'];
    	$this->BK = $informacion['b_g'];
    	$this->BL = $informacion['b_h'];
    	
    	$this->BM = $informacion['b_i'];
    	
    	$this->BN = $informacion['b_m'];
    	$this->BO = $informacion['b_n'];
    	
    	$this->BP = $informacion['c_e']; //Nuevos Campos
    	
    	$this->BQ = $informacion['b_o'];
    	
    	$this->BR = $informacion['c_f']; //Nuevos Campos
    	
    	$this->BS = $informacion['b_j'];
    	$this->BT = $informacion['b_k'];
    	$this->BU = $informacion['b_l'];
    	
    	$this->BV = $informacion['b_p'];
    	$this->BW = $informacion['b_q'];
    	
    	$this->BX = $informacion['b_r'];
    	
    	$this->BY = $informacion['c_g']; //Nuevos Campos
    	$this->BZ = $informacion['c_h']; //Nuevos Campos
    	$this->CA = $informacion['c_i']; //Nuevos Campos
    	
    	$this->CB = $informacion['b_s'];
    	$this->CC = $informacion['b_t'];
    	$this->CD = $informacion['b_u'];
    	
    	$this->CE = $informacion['b_v'];
    	$this->CF = $informacion['b_w'];
    	
    	$this->CG = $informacion['b_x'];
    	$this->CH = $informacion['b_y'];
    	$this->CI = $informacion['b_z'];
    	
    }

    public function estruturarInformacion() {

        // Estilos Celdas
        {
            $styleCentrado = array(
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
            );
            $styleCentradoVertical = array(
                'alignment' => array(
                    'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
            );
        }
        //echo "Creando Reporte";

        //$llave_Ins = array_search('ins', array_column($this->proyectos, 'identifier'), true);

        $i = 4;

        foreach ($this->proyectos as $key => $value) {

        	$this->asignarValoresCampos($value);
        	
            $informacion_general = json_decode(base64_decode($value["a_2"]), true);

            //$var = strpos($value['identifier'], 'becera');

            $this->objCal->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('A' . $i, $this->A)
                 ->getStyle("A" . $i)->applyFromArray($styleCentrado);

            {
                //Avance y  Estado Instalación NOC

                {
                    //var_dump($this->proyectos);exit;
                    // Centro de Gestión
                    // $contenido_CentroGestion = $this->compactarAvances($this->proyectos[$llave_Ins], "Centro de gestión");
                    //$paquete_CentroGestion = $this->consultarPaqueteTrabajo($this->proyectos[$llave_Ins], "Centro de gestión");

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('B' . $i, $this->B)
                         ->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('C' . $i, $this->C)
                         ->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('D' . $i, $this->D)
                         ->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('E' . $i, $this->E)
                         ->getStyle('E' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('F' . $i, $this->F)
                         ->getStyle('F' . $i)->applyFromArray($styleCentradoVertical);

                }

                {
                    // Mesa Ayuda
                    //$contenido_MesaAyuda = $this->compactarAvances($this->proyectos[$llave_Ins], "Mesa de ayuda");
                    //$paquete_MesaAyuda = $this->consultarPaqueteTrabajo($this->proyectos[$llave_Ins], "Mesa de ayuda");

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('G' . $i, $this->G)
                         ->getStyle('G' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('H' . $i, $this->H)
                         ->getStyle('H' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('I' . $i, $this->I)
                         ->getStyle('I' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('J' . $i, $this->J)
                         ->getStyle('J' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('K' . $i, $this->K)
                         ->getStyle('K' . $i)->applyFromArray($styleCentradoVertical);

                }

                {

                    // Otros Sistemas
                    //$contenido_OtrosSistemas = $this->compactarAvances($this->proyectos[$llave_Ins], "Otros equipos o sistemas en el NOC");

                    //$paquete_OtrosSistemas = $this->consultarPaqueteTrabajo($this->proyectos[$llave_Ins], "Otros equipos o sistemas en el NOC");

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('L' . $i, $this->L)
                         ->getStyle('L' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('M' . $i, $this->M)
                         ->getStyle('M' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('N' . $i, $this->N)
                         ->getStyle('N' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('O' . $i, $this->O)
                         ->getStyle('O' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('P' . $i, $this->P)
                         ->getStyle('P' . $i)->applyFromArray($styleCentradoVertical);

                }

                //$paquete_avance_instalacion_noc = $this->consultarPaqueteTrabajo($this->proyectos[$llave_Ins], "Avance y  estado instalación NOC");

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('Q' . $i, $this->Q .  "% ")
                     ->getStyle('Q' . $i)->applyFromArray($styleCentrado);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('R' . $i, $this->R)
                     ->getStyle('R' . $i)->applyFromArray($styleCentradoVertical);

            }

            //$clave_departamento = array_search(1, array_column($value['campos_personalizados'], 'id'), true);
            //$longitud = strlen($value['campos_personalizados'][$clave_departamento]['value']);
            //$departamento = substr($value['campos_personalizados'][$clave_departamento]['value'], 5, $longitud);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('S' . $i, $this->S)
                 ->getStyle("S" . $i)->applyFromArray($styleCentradoVertical);

            //$clave_municipio = array_search(2, array_column($value['campos_personalizados'], 'id'), true);
            //$longitud = strlen($value['campos_personalizados'][$clave_municipio]['value']);
            //$municipio = substr($value['campos_personalizados'][$clave_municipio]['value'], 8, $longitud);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('T' . $i, $this->T)
                 ->getStyle("T" . $i)->applyFromArray($styleCentradoVertical);

            //$codigo_dane = substr($value['campos_personalizados'][$clave_municipio]['value'], 0, 4);
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('U' . $i, $this->U)
                 ->getStyle("U" . $i)->applyFromArray($styleCentradoVertical);

            //$clave_urbanizacion = array_search(33, array_column($value['campos_personalizados'], 'id'), true);
            //$urbanizacion = $value['campos_personalizados'][$clave_urbanizacion]['value'];

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('V' . $i, $this->V)
                 ->getStyle("V" . $i)->applyFromArray($styleCentradoVertical);

            {
                //Avance y Estado Instalación Nodo Cabecera

                //$clave_cabecera_campo = array_search(43, array_column($value['campos_personalizados'], 'id'), true);
                //$cabecera_campo = $value['campos_personalizados'][$clave_cabecera_campo]['value'];
                //$clave_cabecera_proyecto = array_search($cabecera_campo, array_column($this->proyectos, 'name'), true);x
                //$cabecera = $this->proyectos[$clave_cabecera_proyecto];

//                    var_dump($cabecera);exit;

                {
                    //Infraestructura Nodos

                    //  $contenido_InfraestructuraNodos = $this->compactarAvances($cabecera, "Infraestructura nodos");
                    //$paquete_InfraestructuraNodos = $this->consultarPaqueteTrabajo($cabecera, "Infraestructura nodos");

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('W' . $i, $this->W)
                         ->getStyle("W" . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('X' . $i, $this->X)
                         ->getStyle('X' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('Y' . $i, $this->Y)
                         ->getStyle('Y' . $i)->applyFromArray($styleCentradoVertical);

                }
                {
                    //Instalación Red troncal o interconexión ISP

                    //$contenido_RedTroncalISP = $this->compactarAvances($cabecera, "Instalación red troncal o interconexión ISP");
                    //$paquete_RedTroncalISP = $this->consultarPaqueteTrabajo($cabecera, "Instalación red troncal o interconexión ISP");

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('Z' . $i, $this->Z)
                         ->getStyle("Z" . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AA' . $i, $this->AA)
                         ->getStyle('AA' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AB' . $i, $this->AB)
                         ->getStyle('AB' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AC' . $i, $this->AC)
                         ->getStyle('AC' . $i)->applyFromArray($styleCentradoVertical);
                }

                {
                    //Instalación Red troncal o interconexión ISP

                    //  $paquete_InstFuncEquiNodoCab = $this->consultarPaqueteTrabajo($cabecera, "Instalación y puesta en funcionamiento equipos");

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AD' . $i, $this->AD)
                         ->getStyle('AD' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AE' . $i, $this->AE)
                         ->getStyle('AE' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AF' . $i, $this->AF)
                         ->getStyle('AF' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AG' . $i, $this->AG)
                         ->getStyle('AG' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AH' . $i, $this->AH)
                         ->getStyle('AH' . $i)->applyFromArray($styleCentradoVertical);
                }

                {

                    //$cabecera_key_fecha_funcionamiento = array_search(48, array_column($cabecera['campos_personalizados'], 'id'), true);
                    //$fecha_funcionamiento_cabecera = $cabecera['campos_personalizados'][$cabecera_key_fecha_funcionamiento]['value'];

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AI' . $i, $this->AI)
                         ->getStyle('AI' . $i)->applyFromArray($styleCentradoVertical);

                    //$paquete_AvancInstNodoCab = $this->consultarPaqueteTrabajo($cabecera, "Avance y estado instalación nodo cabecera");

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AJ' . $i, $this->AJ . "%")
                         ->getStyle('AJ' . $i)->applyFromArray($styleCentrado);

                }

            }

            {
                //Avance y Estado Instalación Red de Distribución

                {
                    //Estado Construcción Red de Distribución

                    //$contenido_ConsRedDistrb = $this->compactarAvances($value, "Estado construcción red de distribución");

                    //$paquete_ConsRedDistrb = $this->consultarPaqueteTrabajo($value, "Estado construcción red de distribución");

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AK' . $i, $this->AK)
                         ->getStyle("AK" . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AL' . $i, $this->AL)
                         ->getStyle('AL' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AM' . $i, $this->AM)
                         ->getStyle('AM' . $i)->applyFromArray($styleCentradoVertical);

                }

                {
                    //Tendido y puesta en funcionamiento Fibra óptica

                    //$contenido_FunFibrOp = $this->compactarAvances($value, "Tendido y puesta en funcionamiento fibra óptica");

                    //$paquete_FunFibrOp = $this->consultarPaqueteTrabajo($value, "Tendido y puesta en funcionamiento fibra óptica");

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AN' . $i, $this->AN)
                         ->getStyle("AN" . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AO' . $i, $this->AO)
                         ->getStyle('AO' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AP' . $i, $this->AP)
                         ->getStyle('AP' . $i)->applyFromArray($styleCentradoVertical);

                }

                {

                    //$paquete_AvanRedDist = $this->consultarPaqueteTrabajo($value, "Avance y estado instalación red de distribución");

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AQ' . $i, $this->AQ )
                         ->getStyle('AQ' . $i)->applyFromArray($styleCentrado);

                }

            }

            {

                //Avance y Estado Instalación Nodo EOC

                {
                    //Estado Construcción Red de Distribución

                    //$contenido_ConsRedDistrb = $this->compactarAvances($value, "Infraestructura nodo (Avance y estado instalación nodo EOC)", "description");
                    //var_dump($contenido_ConsRedDistrb);
                    //$paquete_ConsRedDistrb = $this->consultarPaqueteTrabajo($value, "Infraestructura nodo (Avance y estado instalación nodo EOC)", "description");
                    //var_dump($paquete_ConsRedDistrb);exit;
                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AR' . $i, $this->AR)
                         ->getStyle("AR" . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AS' . $i, $this->AS)
                         ->getStyle('AS' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AT' . $i, $this->AT)
                         ->getStyle('AT' . $i)->applyFromArray($styleCentradoVertical);

                }

                {
                    //Instalación y Puesta en Funcionamiento Equipos

                    //$contenido_PFuncEqEOC = $this->compactarAvances($value, "Instalación y puesta en funcionamiento equipos (Avance y estado instalación nodo EOC)", "description");

                    //$paquete_PFuncEqEOC = $this->consultarPaqueteTrabajo($value, "Instalación y puesta en funcionamiento equipos (Avance y estado instalación nodo EOC)", "description");

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AW' . $i, $this->AW)
                         ->getStyle('AW' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AX' . $i, $this->AX)
                         ->getStyle('AX' . $i)->applyFromArray($styleCentradoVertical);

                }

                {
                    //unset($llaveFechaFuncionamiento);

                    //$paquete_AvancInstNodoEoc = $this->consultarPaqueteTrabajo($value, "Avance y estado instalación nodo EOC");

                    //$llaveEocInstalar = array_search(29, array_column($value['campos_personalizados'], 'id'), true);
                    //$llaveEocInstaladas = array_search(35, array_column($value['campos_personalizados'], 'id'), true);
                    //$llaveFechaFuncionamiento = array_search(48, array_column($value['campos_personalizados'], 'id'), true);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AU' . $i, $this->AU . "%")
                         ->getStyle('AU' . $i)->applyFromArray($styleCentrado);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AV' . $i, $this->AV)
                         ->getStyle('AV' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AY' . $i, $this->AY)
                         ->getStyle('AY' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AZ' . $i, $this->AZ)
                         ->getStyle('AZ' . $i)->applyFromArray($styleCentrado);
                }

            }

            {
                //Avance y Estado Instalación Nodo Inalámbrico

                {
                    //Infraestructura Nodo

                    //$contenido_InsNoInala = $this->compactarAvances($value, "Infraestructura nodo (Avance y estado instalación nodo inalámbrico)", "description");

                    // $paquete_InsNoInala = $this->consultarPaqueteTrabajo($value, "Infraestructura nodo (Avance y estado instalación nodo inalámbrico)", "description");

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('BA' . $i, $this->BA)
                         ->getStyle("BA" . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('BB' . $i, $this->BB)
                         ->getStyle('BB' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('BC' . $i, $this->BC)
                         ->getStyle('BC' . $i)->applyFromArray($styleCentradoVertical);

                }

                {
                    //Instalación y Puesta en Funcionamiento Equipos

                    //$contenido_InsPusFunEquInala = $this->compactarAvances($value, "Instalación y puesta en funcionamiento equipos (Avance y estado instalación nodo inalámbrico)", "description");
                    //var_dump($contenido_InsNoInala);
                    //$paquete_InsPusFunEquInala = $this->consultarPaqueteTrabajo($value, "Instalación y puesta en funcionamiento equipos (Avance y estado instalación nodo inalámbrico)", "description");

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('BF' . $i, $this->BF)
                         ->getStyle('BF' . $i)->applyFromArray($styleCentradoVertical);

                }

                {

                    //unset($llaveFechaFuncionamiento);
                    //$paquete_AvancInstNodoInal = $this->consultarPaqueteTrabajo($value, "Avance y estado instalación nodo inalámbrico");
                    //var_dump($paquete_AvancInstNodoInal);exit;
                    //$llaveCeldasInstalar = array_search(30, array_column($value['campos_personalizados'], 'id'), true);
                    //$llaveCeldasInstaladas = array_search(34, array_column($value['campos_personalizados'], 'id'), true);
                    //$llaveFechaFuncionamiento = array_search(48, array_column($value['campos_personalizados'], 'id'), true);
                    /*var_dump($llaveCeldasInstalar);
                    var_dump($llaveCeldasInstaladas);
                    var_dump($llaveFechaFuncionamiento);
                    var_dump($value);exit;*/
                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('BD' . $i, $this->BD  . "%")
                         ->getStyle('BD' . $i)->applyFromArray($styleCentrado);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('BE' . $i, $this->BE)
                         ->getStyle('BE' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('BG' . $i, $this->BG)
                         ->getStyle('BG' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('BH' . $i, $this->BH)
                         ->getStyle('BH' . $i)->applyFromArray($styleCentrado);
                }

            }

            {

                //$llaveFechaPrevistaInterventoria = array_search(49, array_column($value['campos_personalizados'], 'id'), true);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BI' . $i, $this->BI)
                     ->getStyle('BI' . $i)->applyFromArray($styleCentradoVertical);

                //$llaveHFCInstalar = array_search(31, array_column($value['campos_personalizados'], 'id'), true);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BJ' . $i, $this->BJ)
                     ->getStyle('BJ' . $i)->applyFromArray($styleCentradoVertical);
            }

            {

                //Avance y Estado Instalación Accesos HFC

                {
                    //$paquete_EstaInsHFC = $this->consultarPaqueteTrabajo($value, "Avance y estado instalación accesos HFC");

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('BK' . $i, $this->BK)
                         ->getStyle('BK' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('BL' . $i, $this->BL  . "%")
                         ->getStyle('BL' . $i)->applyFromArray($styleCentrado);

                    //$llaveHFCInstalados = array_search(36, array_column($value['campos_personalizados'], 'id'), true);
                    //$llaveAccVIP = array_search(37, array_column($value['campos_personalizados'], 'id'), true);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('BP' . $i, $this->BP)
                         ->getStyle('BP' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('BQ' . $i, $this->BQ)
                         ->getStyle('BQ' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('BR' . $i, $this->BR)
                         ->getStyle('BR' . $i)->applyFromArray($styleCentradoVertical);

                }
                {
                    //Tendido y Puesta en Funcionameinto Red Coaxial

                    // $contenido_TenPusRedCox = $this->compactarAvances($value, "Tendido y puesta en funcionamiento red coaxial");

                    //$paquete_TenPusRedCox = $this->consultarPaqueteTrabajo($value, "Tendido y puesta en funcionamiento red coaxial");

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('BM' . $i, $this->BM)
                         ->getStyle("BM" . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('BN' . $i, $this->BN)
                         ->getStyle('BN' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('BO' . $i, $this->BO)
                         ->getStyle('BO' . $i)->applyFromArray($styleCentradoVertical);

                }

            }

            {

                {

                    //$paquete_EstaAvanAccInhabala = $this->consultarPaqueteTrabajo($value, "Avance y estado instalación accesos inalámbricos");

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('BT' . $i, $this->BT)
                         ->getStyle('BT' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('BU' . $i, $this->BU)
                         ->getStyle('BU' . $i)->applyFromArray($styleCentradoVertical);

                }

                // $llaveAccInalam = array_search(32, array_column($value['campos_personalizados'], 'id'), true);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BS' . $i, $this->BS)
                     ->getStyle('BS' . $i)->applyFromArray($styleCentradoVertical);

                //$llaveSMCPE = array_search(40, array_column($value['campos_personalizados'], 'id'), true);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BV' . $i, $this->BV)
                     ->getStyle('BV' . $i)->applyFromArray($styleCentradoVertical);

                //$llaveE1E2 = array_search(41, array_column($value['campos_personalizados'], 'id'), true);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BW' . $i, $this->BW)
                     ->getStyle('BW' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BX' . $i, $this->BX)
                     ->getStyle('BX' . $i)->applyFromArray($styleCentradoVertical);

                //$llaveRInternve = array_search(42, array_column($value['campos_personalizados'], 'id'), true);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BY' . $i, $this->BY)
                     ->getStyle('BY' . $i)->applyFromArray($styleCentradoVertical);

            }

            $i++;

        }

    }

    public function consultarPaqueteTrabajo($proyecto = '', $nombre_paquete = '', $tipo = '') {
        $contenido = '';
        foreach ($proyecto['paquetesTrabajo'] as $key => $value) {

            if ($tipo != '' && $value[$tipo] == $nombre_paquete) {
                $contenido = $value;

            } else if ($value['subject'] == $nombre_paquete) {

                $contenido = $value;
            }

        }
        if ($contenido == '') {

            $contenido = false;

        }

        return $contenido;
    }

    public function compactarAvances($proyecto = '', $tema = '', $tipo = '') {

        $contenido = '';
        foreach ($proyecto['paquetesTrabajo'] as $key => $value) {

            if ($tipo != '' && $value[$tipo] == $tema) {

                foreach ($value['actividades'] as $llave => $valor) {

                    $fecha_actividad = substr($valor['createdAt'], 0, 10);

                    $contenido .= "(" . $fecha_actividad . ") " . $valor['comment']['raw'] . "\n";
                }

            } elseif ($value['subject'] == $tema) {

                foreach ($value['actividades'] as $llave => $valor) {

                    $fecha_actividad = substr($valor['createdAt'], 0, 10);

                    $contenido .= "(" . $fecha_actividad . ") " . $valor['comment']['raw'] . "\n";
                }

            }

        }

        if ($contenido == '') {

            $contenido = false;

        } else {
            $piezas = explode("\n", $contenido);

            $piezas = array_unique($piezas);

            $contenido = implode("\n", $piezas);
        }

        return $contenido;
    }

    public function generarEsquemaDocumento() {

        // Estilos Celdas
        {
            $styleCentrado = array(
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
            );
        }
        // Add some data

        $this->objCal->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->objCal->getActiveSheet()->getStyle('A')->getAlignment()->setWrapText(true);

        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('A3', 'Operador')
             ->getStyle("A3")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->mergeCells('B1:R1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('B1', 'Avance y  Estado Instalación NOC')
             ->getStyle("B1")->applyFromArray($styleCentrado);
        {
            $this->objCal->setActiveSheetIndex(0)->mergeCells('B2:F2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('B2', 'Centro de Gestión')
                 ->getStyle("B2")->applyFromArray($styleCentrado);

            {

                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('B')->setWidth(50);
                    $this->objCal->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('F')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('B')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('C')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('D')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('E')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('F')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getRowDimension('3')->setRowHeight(100);
                    $this->objCal->getActiveSheet()->getRowDimension('2')->setRowHeight(75);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('B3', 'Descripción actividades de instalación, parametrización, integración con la red, pruebas, recibo')
                     ->getStyle("B3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('C3', 'Feha Inicio instalación y adecuaciones')
                     ->getStyle("C3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('D3', 'Fecha terminación instalación, integracion con red y pruebas de recibo')
                     ->getStyle("D3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('E3', 'Feha prevista en PI&PS Inicio instalación y adecuaciones')
                     ->getStyle("E3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('F3', 'Fecha prevista PI&PS terminación instalación y puesta en servicio')
                     ->getStyle("F3")->applyFromArray($styleCentrado);

            }

            $this->objCal->setActiveSheetIndex(0)->mergeCells('G2:K2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('G2', 'Mesa de Ayuda')
                 ->getStyle("G2")->applyFromArray($styleCentrado);

            {

                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('G')->setWidth(50);
                    $this->objCal->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('K')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('G')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('H')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('I')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('J')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('K')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getRowDimension('3')->setRowHeight(100);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('G3', 'Descripción actividades de instalación, parametrización, pruebas, recibo')
                     ->getStyle("G3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('H3', 'Feha Inicio instalación y adecuaciones')
                     ->getStyle("H3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('I3', 'Fecha terminación instalación y pruebas de recibo')
                     ->getStyle("I3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('J3', 'Feha prevista en PI&PS Inicio instalación y adecuaciones')
                     ->getStyle("J3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('K3', 'Fecha prevista PI&PS terminación instalación y puesta en servicio')
                     ->getStyle("K3")->applyFromArray($styleCentrado);

            }

            $this->objCal->setActiveSheetIndex(0)->mergeCells('L2:P2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('L2', 'Otros Equipos o Sistemas en el NOC')
                 ->getStyle("L2")->applyFromArray($styleCentrado);

            {

                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('L')->setWidth(50);
                    $this->objCal->getActiveSheet()->getColumnDimension('M')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('N')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('O')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('P')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('L')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('M')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('N')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('O')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('P')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getRowDimension('3')->setRowHeight(100);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('L3', 'Descripción actividades de instalación, pruebas, recibo')
                     ->getStyle('L3')->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('M3', 'Feha Inicio instalación y adecuación')
                     ->getStyle('M3')->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('N3', 'Fecha terminación instalación y pruebas de recibo')
                     ->getStyle('N3')->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('O3', 'Feha prevista en PI&PS Inicio instalación y adecuaciones')
                     ->getStyle('O3')->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('P3', 'Fecha prevista PI&PS terminación instalación y puesta en servicio')
                     ->getStyle('P3')->applyFromArray($styleCentrado);

            }

            {
                {
                    // Estilos

                    $this->objCal->getActiveSheet()->getStyle('Q')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getColumnDimension('Q')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('R')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getColumnDimension('R')->setWidth(15);

                }

                $this->objCal->setActiveSheetIndex(0)->mergeCells('Q2:Q3');
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('Q2', '% Avance Instalación NOC')
                     ->getStyle("Q2")->applyFromArray($styleCentrado);

                $this->objCal->setActiveSheetIndex(0)->mergeCells('R2:R3');
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('R2', 'Fecha prevista para verificación Interventoría')
                     ->getStyle("R2")->applyFromArray($styleCentrado);

            }
        }
        $this->objCal->setActiveSheetIndex(0)->mergeCells('S1:V1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('S1', 'Proyectos /Municipios')
             ->getStyle("S1")->applyFromArray($styleCentrado);

        {
            {
                // Estilos

                $this->objCal->getActiveSheet()->getStyle('S')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getColumnDimension('S')->setWidth(15);

                $this->objCal->getActiveSheet()->getStyle('T')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getColumnDimension('T')->setWidth(15);

                $this->objCal->getActiveSheet()->getStyle('U')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getColumnDimension('U')->setWidth(15);

                $this->objCal->getActiveSheet()->getStyle('V')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getColumnDimension('V')->setWidth(15);

            }
            $this->objCal->setActiveSheetIndex(0)->mergeCells('S2:S3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('S2', 'DEPARTAMENTO')
                 ->getStyle('S2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)->mergeCells('T2:T3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('T2', 'MUNICIPIO')
                 ->getStyle('T2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)->mergeCells('U2:U3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('U2', 'Código DANE')
                 ->getStyle('U2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)->mergeCells('V2:V3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('V2', 'Proyecto / Urbanización')
                 ->getStyle('V2')->applyFromArray($styleCentrado);

        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('W1:AM1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('W1', 'Avance y Estado Instalación Nodo Cabecera')
             ->getStyle("W1")->applyFromArray($styleCentrado);
        {
            $this->objCal->setActiveSheetIndex(0)->mergeCells('W2:AA2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('W2', 'Infraestructura Nodos')
                 ->getStyle("W2")->applyFromArray($styleCentrado);

            {
                {
                	
                	{
                		// Estilos Columnas
                		$this->objCal->getActiveSheet()->getColumnDimension('W')->setWidth(50);
                		$this->objCal->getActiveSheet()->getColumnDimension('X')->setWidth(15);
                		$this->objCal->getActiveSheet()->getColumnDimension('Y')->setWidth(15);
                		$this->objCal->getActiveSheet()->getColumnDimension('Z')->setWidth(15);
                		$this->objCal->getActiveSheet()->getColumnDimension('AA')->setWidth(15);
                	
                		$this->objCal->getActiveSheet()->getStyle('W')->getAlignment()->setWrapText(true);
                		$this->objCal->getActiveSheet()->getStyle('X')->getAlignment()->setWrapText(true);
                		$this->objCal->getActiveSheet()->getStyle('Y')->getAlignment()->setWrapText(true);
                		$this->objCal->getActiveSheet()->getStyle('Z')->getAlignment()->setWrapText(true);
                		$this->objCal->getActiveSheet()->getStyle('AA')->getAlignment()->setWrapText(true);
                	
                	}
                	{
	                    // Estilos Columnas
	                    $this->objCal->getActiveSheet()->getColumnDimension('W')->setWidth(50);
	                    $this->objCal->getActiveSheet()->getColumnDimension('X')->setWidth(15);
	                    $this->objCal->getActiveSheet()->getColumnDimension('Y')->setWidth(15);
	
	                    $this->objCal->getActiveSheet()->getStyle('W')->getAlignment()->setWrapText(true);
	                    $this->objCal->getActiveSheet()->getStyle('X')->getAlignment()->setWrapText(true);
	                    $this->objCal->getActiveSheet()->getStyle('Y')->getAlignment()->setWrapText(true);
                	}
                }
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('W3', 'Descripción obra o actividad')
                     ->getStyle("W3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('X3', 'Estado Avance (en construcción, terminado)')
                     ->getStyle("X3")->applyFromArray($styleCentrado);
               
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('Y3', '% Avance Infraestructura')
                     ->getStyle("Y3")->applyFromArray($styleCentrado);
                 
               	$this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('Z3', 'Fecha Prevista Terminación')
                     ->getStyle("Z3")->applyFromArray($styleCentrado);
               	
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AA3', 'Fecha prevista en el PI&PS para la terminación')
                     ->getStyle("AA3")->applyFromArray($styleCentrado);
            }


            $this->objCal->setActiveSheetIndex(0)->mergeCells('AB2:AF2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AB2', 'Instalación Red troncal o interconexión ISP')
                 ->getStyle("AB2")->applyFromArray($styleCentrado);

            {
                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('AB')->setWidth(50);
                    $this->objCal->getActiveSheet()->getColumnDimension('AC')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AD')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AE')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AF')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('AB')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AC')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AD')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AE')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AF')->getAlignment()->setWrapText(true);

                }

                $this->objCal->setActiveSheetIndex(0)
                ->setCellValue('AB3', 'Descripción Actividad')
                ->getStyle("AB3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                ->setCellValue('AC3', 'Estado avance Instalación o entrega (Adquirido, instalado, probado, en funcionamiento)')
                ->getStyle("AC3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                ->setCellValue('AD3', '% avance inteconexión ISP')
                ->getStyle("AD3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AE3', 'Fecha Prevista Funcionamiento')
                     ->getStyle("AE3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AF3', 'Fecha Prevista en el PI&PS para la Instalación o Entrega Interconexión  ISP ')
                     ->getStyle("AF3")->applyFromArray($styleCentrado);

            }

            $this->objCal->setActiveSheetIndex(0)->mergeCells('AG2:AM2');
            
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AG2', 'Instalación y Puesta en Funcionamiento Equipos')
                 ->getStyle("AG2")->applyFromArray($styleCentrado);

            {
                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('AG')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AH')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AI')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AJ')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AK')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AL')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AM')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('AG')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AH')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AI')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AJ')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AK')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AL')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AM')->getAlignment()->setWrapText(true);
                    

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AG3', 'OLTs(Instalado, Probado, En Funcionamiento)')
                     ->getStyle("AG3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AH3', 'Equipos Networking (Instalados, Probados, En Funcionamiento)')
                     ->getStyle("AH3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AI3', 'Equipos de Energía y Complementarios (Instalados, Probados, En Funcionamiento)')
                     ->getStyle("AI3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AJ3', '% avance instalacion equipos nodo cabecera')
                     ->getStyle("AJ3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AK3', 'Fecha Prevista Funcionamiento Nodo Cabecera')
                     ->getStyle("AK3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AL3', 'Fecha prevista en el PI&PS para el inicio instalación nodo Cabecera')
                     ->getStyle("AL3")->applyFromArray($styleCentrado);
                     
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AM3', 'Fecha prevista en el PI&PS para la puesta en funcionamiento nodo cabecera')
                     ->getStyle("AM3")->applyFromArray($styleCentrado);

            }

            {
//                 {
//                     // Estilos

//                     $this->objCal->getActiveSheet()->getStyle('AJ')->getAlignment()->setWrapText(true);
//                     $this->objCal->getActiveSheet()->getColumnDimension('AJ')->setWidth(15);

//                     $this->objCal->getActiveSheet()->getColumnDimension('AI')->setWidth(15);
//                     $this->objCal->getActiveSheet()->getStyle('AI')->getAlignment()->setWrapText(true);

//                 }

//                 $this->objCal->setActiveSheetIndex(0)->mergeCells('AL2:AL3');
//                 $this->objCal->setActiveSheetIndex(0)
//                      ->setCellValue('AL2', 'Fecha prevista en el PI&PS para la Puesta en Funcionamiento Nodo Cabecera')
//                      ->getStyle("AL2")->applyFromArray($styleCentrado);

//                 $this->objCal->setActiveSheetIndex(0)->mergeCells('AJ2:AJ3');
//                 $this->objCal->setActiveSheetIndex(0)
//                      ->setCellValue('AL2', '% Avance Instalación Nodo Cabecera')
//                      ->getStyle("AL2")->applyFromArray($styleCentrado);

            }
        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('AN1:AU1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('AN1', 'Avance y Estado Instalación Red de Distribución')
             ->getStyle("AN1")->applyFromArray($styleCentrado);

        {

            $this->objCal->setActiveSheetIndex(0)->mergeCells('AN2:AP2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AN2', 'Estado Construcción Red de Distribución')
                 ->getStyle("AN2")->applyFromArray($styleCentrado);

            {
                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('AN')->setWidth(50);
                    $this->objCal->getActiveSheet()->getColumnDimension('AO')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AP')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('AN')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AO')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AP')->getAlignment()->setWrapText(true);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AN3', 'Descripción Construcción (Postería, Canalizaciones, Cámaras, Acometidas, Etc,  Cuando Aplique)')
                     ->getStyle("AN3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AO3', 'Estado Avance (En Construcción, Terminado)')
                     ->getStyle("AO3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AP3', 'Fecha Prevista Terminación')
                     ->getStyle("AP3")->applyFromArray($styleCentrado);

            }

            $this->objCal->setActiveSheetIndex(0)->mergeCells('AQ2:AT2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AQ2', 'Tendido y Puesta en Funcionamiento Fibra Óptica')
                 ->getStyle("AQ2")->applyFromArray($styleCentrado);

            {

                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('AQ')->setWidth(50);
                    $this->objCal->getActiveSheet()->getColumnDimension('AR')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AS')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AT')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('AQ')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AR')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AS')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AT')->getAlignment()->setWrapText(true);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AQ3', 'Descripción Actividades')
                     ->getStyle("AQ3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AR3', 'Estado Avance (En Construcción, Terminado, Probado, En Funcionamiento)')
                     ->getStyle("AR3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AS3', 'Fecha Prevista Puesta en Funcionamiento')
                     ->getStyle("AS3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AT3', 'Fecha prevista PI&PS red de distribución terminada')
                     ->getStyle("AT3")->applyFromArray($styleCentrado);

            }

            {
                {
                    // Estilos

                    $this->objCal->getActiveSheet()->getStyle('AU')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getColumnDimension('AU')->setWidth(15);

                }

                $this->objCal->setActiveSheetIndex(0)->mergeCells('AU2:AU3');
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AU2', '% Avance Instalación Red Distribución')
                     ->getStyle("AU2")->applyFromArray($styleCentrado);

            }
        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('AV1:BD1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('AV1', 'Avance y Estado Instalación Nodo EOC')
             ->getStyle("AV1")->applyFromArray($styleCentrado);

        {

            $this->objCal->setActiveSheetIndex(0)->mergeCells('AV2:AX2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AV2', 'Infraestructura Nodo')
                 ->getStyle("AV2")->applyFromArray($styleCentrado);

            {

                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('AV')->setWidth(50);
                    $this->objCal->getActiveSheet()->getColumnDimension('AW')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AX')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('AV')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AW')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AX')->getAlignment()->setWrapText(true);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AV3', 'Descripción Obra o Actividad')
                     ->getStyle("AV3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AW3', 'Estado Avance (En Construcción, Terminado)')
                     ->getStyle("AW3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AX3', 'Fecha Prevista Terminación')
                     ->getStyle("AX3")->applyFromArray($styleCentrado);

            }

            $this->objCal->setActiveSheetIndex(0)->mergeCells('AY2:BC2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AY2', 'Instalación y Puesta en Funcionamiento Equipos ')
                 ->getStyle("AY2")->applyFromArray($styleCentrado);

            {

                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('AY')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AZ')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AA')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('BA')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('BB')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('BC')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('AY')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AZ')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('BA')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('BB')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('BC')->getAlignment()->setWrapText(true);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AY3', 'Cantidad de EOCs a Instalar Requeridos')
                     ->getStyle("AY3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AZ3', 'Cantidad de EOCs Instalados, Probados y En Funcionamiento')
                     ->getStyle("AZ3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BA3', 'Equipos Networking (Instalados, Probados,En Funcionamiento)')
                     ->getStyle("BA3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BB3', 'Fecha Prevista Nodo EOC en Funcionameinto')
                     ->getStyle("BB3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BC3', 'Fecha Prevista en el PI&PS Nodo EOC en Funcionamiento')
                     ->getStyle("BC3")->applyFromArray($styleCentrado);

            }

            {
                {
                    // Estilos

                    $this->objCal->getActiveSheet()->getStyle('BD')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getColumnDimension('BD')->setWidth(15);

                }

                $this->objCal->setActiveSheetIndex(0)->mergeCells('BD2:BD3');
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BD2', '% Avance instalación Nodo EOC')
                     ->getStyle("BD2")->applyFromArray($styleCentrado);

            }
        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('BE1:BL1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('BE1', 'Avance y Estado Instalación Nodo Inalámbrico')
             ->getStyle("BE1")->applyFromArray($styleCentrado);

        {

            $this->objCal->setActiveSheetIndex(0)->mergeCells('BE2:BG2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BE2', 'Infraestructura Nodo')
                 ->getStyle("BE2")->applyFromArray($styleCentrado);

            {

                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('BE')->setWidth(50);
                    $this->objCal->getActiveSheet()->getColumnDimension('BF')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('BG')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('BE')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('BF')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('BG')->getAlignment()->setWrapText(true);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BE3', 'Descripción Obra o Actividad')
                     ->getStyle("BE3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BF3', 'Estado Avance (En Construcción, Terminado)')
                     ->getStyle("BF3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BG3', 'Fecha Prevista Terminación')
                     ->getStyle("BG3")->applyFromArray($styleCentrado);
            }

            $this->objCal->setActiveSheetIndex(0)->mergeCells('BH2:BK2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BH2', 'Instalación y Puesta en Funcionamiento Equipos')
                 ->getStyle("BH2")->applyFromArray($styleCentrado);

            {

                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('BH')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('BI')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('BJ')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('BK')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('BH')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('BI')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('BJ')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('BK')->getAlignment()->setWrapText(true);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BH3', 'Cantidad de Celdas a Instalar Requeridos')
                     ->getStyle("BH3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BI3', 'Cantidad de Celdas Instaladas, Probadas y En Funcionamiento')
                     ->getStyle("BI3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BJ3', 'Fecha Prevista Nodo Inalámbrico en Funcionameinto')
                     ->getStyle("BJ3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BK3', 'Fecha Prevista en el PI&PS Nodo Inalámbrico en Funcionamiento')
                     ->getStyle("BK3")->applyFromArray($styleCentrado);

            }

            {
                {
                    // Estilos

                    $this->objCal->getActiveSheet()->getStyle('BL')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getColumnDimension('BL')->setWidth(15);

                }

                $this->objCal->setActiveSheetIndex(0)->mergeCells('BL2:BL3');
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BL2', '% Avance Instalación Nodo Inalámbrico')
                     ->getStyle("BL2")->applyFromArray($styleCentrado);

            }

        }

        $this->objCal->getActiveSheet()->getStyle('BM')->getAlignment()->setWrapText(true);
        $this->objCal->getActiveSheet()->getColumnDimension('BM')->setWidth(30);
        $this->objCal->setActiveSheetIndex(0)->mergeCells('BM1:BM3');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('BM1', 'Fecha Prevista para Verificación por la Interventoría del Nodo de Cabecera, Nodo EOC, Nodo Inalámbrico y Red de Distribución')
             ->getStyle("BM1")->applyFromArray($styleCentrado);

             
       $this->objCal->setActiveSheetIndex(0)->mergeCells('BN2:BR2');
            $this->objCal->setActiveSheetIndex(0)
            ->setCellValue('BN2', 'Tendido y puesta en funcionameinto Red Coaxial')
            ->getStyle("BN2")->applyFromArray($styleCentrado);
             
            {
             
             {
             	// Estilos Columnas
             	$this->objCal->getActiveSheet()->getColumnDimension('BN')->setWidth(15);
             	$this->objCal->getActiveSheet()->getColumnDimension('BO')->setWidth(15);
             	$this->objCal->getActiveSheet()->getColumnDimension('BP')->setWidth(15);
             	$this->objCal->getActiveSheet()->getColumnDimension('BQ')->setWidth(15);
             	$this->objCal->getActiveSheet()->getColumnDimension('BR')->setWidth(15);
             
             	$this->objCal->getActiveSheet()->getStyle('BN')->getAlignment()->setWrapText(true);
             	$this->objCal->getActiveSheet()->getStyle('BO')->getAlignment()->setWrapText(true);
             	$this->objCal->getActiveSheet()->getStyle('BP')->getAlignment()->setWrapText(true);
             	$this->objCal->getActiveSheet()->getStyle('BQ')->getAlignment()->setWrapText(true);
             	$this->objCal->getActiveSheet()->getStyle('BR')->getAlignment()->setWrapText(true);
             		 
             		
             }
             
             $this->objCal->setActiveSheetIndex(0)
             	->setCellValue('BN3', 'Descripción actividades')
             	->getStyle("BN3")->applyFromArray($styleCentrado);
             
             $this->objCal->setActiveSheetIndex(0)
             	->setCellValue('BO3', 'Estado avance (en construcción, terminado, probado, en funcionamiento)')
             	->getStyle("BO3")->applyFromArray($styleCentrado);
             
             $this->objCal->setActiveSheetIndex(0)
             	->setCellValue('BP3', '% avance')
             	->getStyle("BP3")->applyFromArray($styleCentrado);
             
             $this->objCal->setActiveSheetIndex(0)
             	->setCellValue('BQ3', 'Fecha prevista puesta en funcionamiento')
             	->getStyle("BQ3")->applyFromArray($styleCentrado);
             
             $this->objCal->setActiveSheetIndex(0)
             	->setCellValue('BR3', 'Fecha en el PI&PS para la terminación')
             	->getStyle("BR3")->applyFromArray($styleCentrado);
             
        	}
             
        $this->objCal->setActiveSheetIndex(0)->mergeCells('BN1:BX1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('BN1', 'Avance y Estado Instalación Accesos HFC')
             ->getStyle("BN1")->applyFromArray($styleCentrado);

        {

        	// Estilos Columnas
        	$this->objCal->getActiveSheet()->getColumnDimension('BS')->setWidth(15);
        	$this->objCal->getActiveSheet()->getColumnDimension('BT')->setWidth(15);
        	$this->objCal->getActiveSheet()->getColumnDimension('BU')->setWidth(15);
        	 
        	$this->objCal->getActiveSheet()->getStyle('BS')->getAlignment()->setWrapText(true);
        	$this->objCal->getActiveSheet()->getStyle('BT')->getAlignment()->setWrapText(true);
        	$this->objCal->getActiveSheet()->getStyle('BU')->getAlignment()->setWrapText(true);
        	
            $this->objCal->getActiveSheet()->getStyle('BS')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getColumnDimension('BS')->setWidth(15);
            $this->objCal->setActiveSheetIndex(0)->mergeCells('BS2:BS3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BS2', 'Cantidad de Accesos HFC a Instalar Requeridos HFC')
                 ->getStyle("BS2")->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getStyle('BT')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getColumnDimension('BT')->setWidth(15);
            $this->objCal->setActiveSheetIndex(0)->mergeCells('BT2:BT3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BT2', 'Fecha Prevista en el PI&PS para el Inicio Instalación Accesos HFC')
                 ->getStyle("BT2")->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getStyle('BU')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getColumnDimension('BU')->setWidth(15);
            $this->objCal->setActiveSheetIndex(0)->mergeCells('BU2:BU3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BU2', 'Fecha Prevista en el PI&PS para la Terminación Instalación Accesos HFC')
                 ->getStyle("BU2")->applyFromArray($styleCentrado);


            {
                // Estilos Columnas

                $this->objCal->getActiveSheet()->getColumnDimension('BV')->setWidth(15);
                $this->objCal->getActiveSheet()->getColumnDimension('BW')->setWidth(15);

                $this->objCal->getActiveSheet()->getStyle('BV')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getStyle('BW')->getAlignment()->setWrapText(true);

            }
            $this->objCal->setActiveSheetIndex(0)->mergeCells('BV2:BW2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BV2', 'Cantidad de Accesos HFC Instalados en la Semana Reportada')
                 ->getStyle("BV2")->applyFromArray($styleCentrado);

            {
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BV3', 'EOC Cliente (Cantidad Instalados)')
                     ->getStyle("BV3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BW3', 'Accesos VIP')
                     ->getStyle("BW3")->applyFromArray($styleCentrado);

            }

            {
                // Estilos Columnas

                $this->objCal->getActiveSheet()->getColumnDimension('BX')->setWidth(15);

                $this->objCal->getActiveSheet()->getStyle('BX')->getAlignment()->setWrapText(true);

            }
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BX2', 'Cantidad de Accesos HFC Instalados Acumulados')
                 ->getStyle("BX2")->applyFromArray($styleCentrado);

            {

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BX3', 'Accesos VIP')
                     ->getStyle("BX3")->applyFromArray($styleCentrado);

            }

        }

        
        $this->objCal->setActiveSheetIndex(0)->mergeCells('BY2:CA2');
        $this->objCal->setActiveSheetIndex(0)
        ->setCellValue('BY2', 'Infraestructura Red de acceso')
        ->getStyle("BY2")->applyFromArray($styleCentrado);
         
        {
        	 
        	{
        		// Estilos Columnas
        		$this->objCal->getActiveSheet()->getColumnDimension('BY')->setWidth(50);
        		$this->objCal->getActiveSheet()->getColumnDimension('BZ')->setWidth(15);
        		$this->objCal->getActiveSheet()->getColumnDimension('CA')->setWidth(15);

        		$this->objCal->getActiveSheet()->getStyle('BY')->getAlignment()->setWrapText(true);
        		$this->objCal->getActiveSheet()->getStyle('BZ')->getAlignment()->setWrapText(true);
        		$this->objCal->getActiveSheet()->getStyle('CA')->getAlignment()->setWrapText(true);
        		 
        	}
        	 
        	$this->objCal->setActiveSheetIndex(0)
        	->setCellValue('BY3', 'Descripción actividades y estado de avance')
        	->getStyle("BY3")->applyFromArray($styleCentrado);
        	 
        	$this->objCal->setActiveSheetIndex(0)
        	->setCellValue('BZ3', '% avance')
        	->getStyle("BZ3")->applyFromArray($styleCentrado);
        	 
        	$this->objCal->setActiveSheetIndex(0)
        	->setCellValue('CA3', 'Fecha prevista en el PI&PS para la terminación')
        	->getStyle("CA3")->applyFromArray($styleCentrado);
        	 
        }
        
        
        $this->objCal->setActiveSheetIndex(0)->mergeCells('BY1:CI1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('BY1', 'Avance y Estado Instalación Accesos Inalámbricos')
             ->getStyle("BY1")->applyFromArray($styleCentrado);

        {

            $this->objCal->getActiveSheet()->getStyle('CB')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getColumnDimension('CB')->setWidth(15);
            $this->objCal->setActiveSheetIndex(0)->mergeCells('CB2:CB3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('CB2', 'Cantidad de Accesos Inalámbricos a Instalar Requeridos')
                 ->getStyle("CB2")->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getStyle('CC')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getColumnDimension('CC')->setWidth(15);
            $this->objCal->setActiveSheetIndex(0)->mergeCells('CC2:CC3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('CC2', 'Fecha Prevista en el PI&PS para el Inicio Instalación Accesos Inalámbricos')
                 ->getStyle("CC2")->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getStyle('CD')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getColumnDimension('CD')->setWidth(15);
            $this->objCal->setActiveSheetIndex(0)->mergeCells('CD2:CD3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('CD2', 'Fecha Prevista en el PI&PS para la Terminación Instalación Accesos Inalámbricos')
                 ->getStyle("CD2")->applyFromArray($styleCentrado);

            {
                // Estilos Columnas

                $this->objCal->getActiveSheet()->getColumnDimension('CE')->setWidth(15);
                $this->objCal->getActiveSheet()->getColumnDimension('CF')->setWidth(15);

                $this->objCal->getActiveSheet()->getStyle('CE')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getStyle('CF')->getAlignment()->setWrapText(true);

            }
            $this->objCal->setActiveSheetIndex(0)->mergeCells('CE2:CF2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('CE2', 'Cantidad de Accesos Inalámbricos Instalados en la Semana Reportada')
                 ->getStyle("CE2")->applyFromArray($styleCentrado);

            {

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('CE3', 'SM /CPE (Cantidad Instalados)')
                     ->getStyle("CE3")->applyFromArray($styleCentrado);
                
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('CF3', 'Accesos E1 y E2')
                     ->getStyle("CF3")->applyFromArray($styleCentrado);

            }

            {
                // Estilos Columnas

                $this->objCal->getActiveSheet()->getColumnDimension('CG')->setWidth(15);

                $this->objCal->getActiveSheet()->getStyle('CG')->getAlignment()->setWrapText(true);

            }
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('CG2', 'Cantidad de Accesos Inalámbricos Instalados Acumulados')
                 ->getStyle("CG2")->applyFromArray($styleCentrado);

            {

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('CG3', 'Accesos E1 y E2')
                     ->getStyle("CG3")->applyFromArray($styleCentrado);

            }
        }

        $this->objCal->getActiveSheet()->getColumnDimension('CH')->setWidth(15);
        $this->objCal->getActiveSheet()->getColumnDimension('CI')->setWidth(15);
        
        $this->objCal->getActiveSheet()->getStyle('CH')->getAlignment()->setWrapText(true);
        $this->objCal->getActiveSheet()->getStyle('CI')->getAlignment()->setWrapText(true);
        
        
        $this->objCal->setActiveSheetIndex(0)->mergeCells('CH2:CI2');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('CH2', 'Reporte Accesos')
             ->getStyle("CH2")->applyFromArray($styleCentrado);

        {
        	
        		$this->objCal->setActiveSheetIndex(0)
        		->setCellValue('CH3', 'Cantidad de Accesos a Reportar a la Interventoría')
        		->getStyle("CH3")->applyFromArray($styleCentrado);
        	
        		$this->objCal->setActiveSheetIndex(0)
        		->setCellValue('CI3', 'Fecha Prevista Reporte Accesos Instalados a Interventoría')
        		->getStyle("CI3")->applyFromArray($styleCentrado);
        	
        }

    }
    public function configurarDocumento() {

        $this->objCal = new \PHPExcel();

        // Set document properties
        $this->objCal->getProperties()->setCreator("OpenKyOS")
             ->setLastModifiedBy("OpenKyOS")
             ->setTitle("Reporte de Instalaciones (" . $_REQUEST['fecha_inicio'] . ")-(" . $_REQUEST['fecha_final'] . ")")
             ->setSubject("Reporte Instalaciones")
             ->setDescription("Reporte de Instalaciones en un determinado periodo de tiempo")
             ->setCategory("Reporte");

    }

    public function retornarDocumento() {

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ReporteInstalaciones' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        ob_clean();
        $objWriter = \PHPExcel_IOFactory::createWriter($this->objCal, 'Excel2007');
        $objWriter->save('php://output');

        exit();

    }

}

$miProcesador = new GenerarReporteExcelInstalaciones($this->miSql, $this->info_proyectos);

?>

