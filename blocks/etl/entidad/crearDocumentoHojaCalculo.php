<?php

namespace reportes\instalacionesGenerales\entidad;

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

class GenerarReporteExcelInstalaciones {
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $proyectos;
    public $objCal;
    public $informacion;
    public $fecha;
    
    public function iniciar($sql, $proyectos, $fecha='') {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->proyectos = $proyectos;
		$this->fecha = $fecha;

		/**
         * 1.
         * Estruturamiento Información OpenProject
         */
        $this->estruturarInformacion();

        /**
         * 2.
         * Registrar Información Almacén de datos
         */
        $this->registrarAlmacenDatos();

    }

    public function ajustarComentarios($actividades) {

    	
        $contenido = '';

        foreach ($actividades as $key => $value) {
        	
        	$val = (strpos($value['comment']['raw'], 'automáticamente cambiando'));
        	
        	if (!is_numeric($val) && $value['comment']['raw'] != "") {
        		$fecha_actividad = substr($value['createdAt'], 0, 10);
        		$contenido .= "(" . $fecha_actividad . ") " . $value['comment']['raw'] . "\n";
        	}
        }

        if ($contenido == '') {

            $contenido = "";
        } else {
            $piezas = explode("\n", $contenido);

            $piezas = array_unique($piezas);

            $contenido = implode("\n", $piezas);
        }

        return $contenido;

    }

    public function estruturarInformacion() {

        $i = 4;

        foreach ($this->proyectos as $key => $value) {

            if ($value['tipo_proyecto'] === "core") {
                $llave_Ins = $key;
            }
            
            {
                // Core y Cabeceras 
                foreach ($this->proyectos[$llave_Ins]['campos_parametrizados'] as $key_c => $value_c) {

                    switch ($value_c['sub_tipo'] . "-" . $value_c['nombre_formulario']) {

                        case 'Centro de Gestión-Descripcion Actividades':

                        	$this->informacionCorCab['b_'] = (isset($value_c['paquetesTrabajo']['actividades'])) ? $this->ajustarComentarios($value_c['paquetesTrabajo']['actividades']) : " ";
                            break;
                        case 'Centro de Gestión-Fecha Inicio instalación Adecuaciones':

                        	$this->informacionCorCab['c_'] = (isset($value_c['paquetesTrabajo']['cf_12']) && $value_c['paquetesTrabajo']['cf_12'] != '') ? $value_c['paquetesTrabajo']['cf_12'] : "";
                            break;
                        case 'Centro de Gestión-Fecha Terminación instalación Adecuaciones':

                        	$this->informacionCorCab['d_'] = (isset($value_c['paquetesTrabajo']['cf_13']) && $value_c['paquetesTrabajo']['cf_13'] != '') ? $value_c['paquetesTrabajo']['cf_13'] : "";
                            break;
                        case 'Centro de Gestión-Fecha Prevista PI&PS Inicio':

                            $this->informacionCorCab['e_'] = (isset($value_c['paquetesTrabajo']['start_date']) && $value_c['paquetesTrabajo']['start_date'] != '') ? $value_c['paquetesTrabajo']['start_date'] : "";

                            break;
                        case 'Centro de Gestión-Fecha Prevista PI&PS Terminación':

                            $this->informacionCorCab['f_'] = (isset($value_c['paquetesTrabajo']['due_date']) && $value_c['paquetesTrabajo']['due_date'] != '') ? $value_c['paquetesTrabajo']['due_date'] : "";

                            break;
                        case 'Mesa  de Ayuda-Descripcion Actividades':

                            $this->informacionCorCab['g_'] = (isset($value_c['paquetesTrabajo']['actividades'])) ? $this->ajustarComentarios($value_c['paquetesTrabajo']['actividades']) : "";

                            break;
                        case 'Mesa  de Ayuda-Feha Inicio instalación Adecuaciones':

                        	$this->informacionCorCab['h_'] = (isset($value_c['paquetesTrabajo']['cf_12']) && $value_c['paquetesTrabajo']['cf_12'] != '') ? $value_c['paquetesTrabajo']['cf_12'] : "";
                            break;
                        case 'Mesa  de Ayuda-Fecha Terminación instalación Adecuaciones':

                        	$this->informacionCorCab['i_'] = (isset($value_c['paquetesTrabajo']['cf_13']) && $value_c['paquetesTrabajo']['cf_13'] != '') ? $value_c['paquetesTrabajo']['cf_13'] : "";
                            break;
                        case 'Mesa  de Ayuda-Fecha Prevista PI&PS Inicio':

                            $this->informacionCorCab['j_'] = (isset($value_c['paquetesTrabajo']['start_date']) && $value_c['paquetesTrabajo']['start_date'] != '') ? $value_c['paquetesTrabajo']['start_date'] : "";
                            break;
                        case 'Mesa  de Ayuda-Fecha Prevista PI&PS Terminación':

                            $this->informacionCorCab['k_'] = (isset($value_c['paquetesTrabajo']['due_date']) && $value_c['paquetesTrabajo']['due_date'] != '') ? $value_c['paquetesTrabajo']['due_date'] : "";

                            break;
                        case 'Otros Equipos o Sistemas en el NOC-Descripcion Actividades':

                            $this->informacionCorCab['l_'] = (isset($value_c['paquetesTrabajo']['actividades'])) ? $this->ajustarComentarios($value_c['paquetesTrabajo']['actividades']) : " ";
                            break;
                            
                        case 'Otros Equipos o Sistemas en el NOC-Feha Inicio instalación Adecuaciones':

                        	$this->informacionCorCab['m_'] = (isset($value_c['paquetesTrabajo']['cf_12']) && $value_c['paquetesTrabajo']['cf_12'] != '') ? $value_c['paquetesTrabajo']['cf_12'] : "";
                            break;
                            
                        case 'Otros Equipos o Sistemas en el NOC-Fecha Terminación instalación Adecuaciones':

                        	$this->informacionCorCab['n_'] = (isset($value_c['paquetesTrabajo']['cf_13']) && $value_c['paquetesTrabajo']['cf_13'] != '') ? $value_c['paquetesTrabajo']['cf_13'] : "";
                            break;
                            
                        case 'Otros Equipos o Sistemas en el NOC-Fecha Prevista PI&PS Inicio':

                            $this->informacionCorCab['o_'] = (isset($value_c['paquetesTrabajo']['start_date']) && $value_c['paquetesTrabajo']['start_date'] != '') ? $value_c['paquetesTrabajo']['start_date'] : "";

                            break;
                        case 'Otros Equipos o Sistemas en el NOC-Fecha Prevista PI&PS Terminación':

                            $this->informacionCorCab['p_'] = (isset($value_c['paquetesTrabajo']['due_date']) && $value_c['paquetesTrabajo']['due_date'] != '') ? $value_c['paquetesTrabajo']['due_date'] : "";

                            break;
                        case 'general-Porcentaje Avance':

                            $this->informacionCorCab['q_'] = $value_c['paquetesTrabajo']['done_ratio'];
                            break;
                        case 'general-Fecha Prevista Verificación Interventoría':

                        	$this->informacionCorCab['r_'] = (isset($value_c['paquetesTrabajo']['cf_15']) && $value_c['paquetesTrabajo']['cf_15'] != '') ? $value_c['paquetesTrabajo']['cf_15'] : "";
                            break;

                    }

                }

            }


            if ($value['tipo_proyecto'] != "core" && $value['tipo_proyecto'] != "cabecera") {

            	$this->camposBlancos($key);
            	
            	foreach ( $this->informacionCorCab as $key_corecab => $corecab){
            		$this->informacion[$key][$key_corecab] = $corecab;
            	}
            	
            	$this->informacion[$key]['a_'] = 'Politécnica';
            	
            	$this->informacion[$key]['a_0'] = $value['info']['id'];
            	$this->informacion[$key]['a_1'] = '';
            	$this->informacion[$key]['a_2'] = json_encode($value['info']);
            	
                $value['campos_personalizados'] = $value['info']['custom_fields'];

                $clave_departamento = array_search(1, array_column($value['campos_personalizados'], 'id'), true);
                $longitud = strlen($value['campos_personalizados'][$clave_departamento]['value']);
                $departamento = substr($value['campos_personalizados'][$clave_departamento]['value'], 5, $longitud);

                $this->informacion[$key]['s_'] = $departamento;

                $clave_municipio = array_search(2, array_column($value['campos_personalizados'], 'id'), true);
                $longitud = strlen($value['campos_personalizados'][$clave_municipio]['value']);
                $municipio = substr($value['campos_personalizados'][$clave_municipio]['value'], 8, $longitud);
                $codigo_dane = substr($value['campos_personalizados'][$clave_municipio]['value'], 0, 4);

                $this->informacion[$key]['t_'] = $municipio;
                $this->informacion[$key]['u_'] = $codigo_dane;

                $clave_urbanizacion = array_search(33, array_column($value['campos_personalizados'], 'id'), true);
                $urbanizacion = $value['campos_personalizados'][$clave_urbanizacion]['value'];

                $this->informacion[$key]['v_'] = $urbanizacion;

                {

                    $clave_cabecera_campo = array_search(43, array_column($value['info']['custom_fields'], 'id'), true); // Burcar Id_ doende esta el nombre de la Cabecera

                    $cabecera_campo = $value['info']['custom_fields'][$clave_cabecera_campo]['value'];

                    foreach ($this->proyectos as $key_proyecto => $valor_proyecto) {

                        if ($valor_proyecto['info']['name'] === $cabecera_campo) {
                            $cabecera = $valor_proyecto;
                        }

                    }
                    
                    if(isset($cabecera)){
                    	
	                    foreach ($cabecera['campos_parametrizados'] as $key_cb => $value_cb) {
	
	                        switch ($value_cb['sub_tipo'] . "-" . $value_cb['nombre_formulario']) {
	                            // Cabecera
	                            case "Infraestructura Nodos-Descripcion Actividades":
	
	                                $this->informacion[$key]['w_'] = (isset($value_cb['paquetesTrabajo']['actividades'])) ? $this->ajustarComentarios($value_cb['paquetesTrabajo']['actividades']) : "";
	                                break;
	                            case "Infraestructura Nodos-Estado Avance":
	                                $this->informacion[$key]['x_'] = (isset($value_cb['paquetesTrabajo']['cf_14']) && !is_null($value_cb['paquetesTrabajo']['cf_14'])) ? $value_cb['paquetesTrabajo']['cf_14'] : "";
	
	                                break;
	                            case "Infraestructura Nodos-Fecha Prevista Terminación":
	
	                                $this->informacion[$key]['y_'] = (isset($value_cb['paquetesTrabajo']['due_date']) && $value_cb['paquetesTrabajo']['due_date'] != '') ? $value_cb['paquetesTrabajo']['due_date'] : "";
	
	                                break;
	                            case "Instalación Red troncal o interconexión ISP-Descripcion Actividades":
	
	                                $this->informacion[$key]['z_'] = (isset($value_cb['paquetesTrabajo']['actividades'])) ? $this->ajustarComentarios($value_cb['paquetesTrabajo']['actividades']) : "";
	
	                                break;
	                            case "Instalación Red troncal o interconexión ISP-Estado Avance":
	
	                                $this->informacion[$key]['a_a'] = (isset($value_cb['paquetesTrabajo']['cf_14']) && !is_null($value_cb['paquetesTrabajo']['cf_14'])) ? $value_cb['paquetesTrabajo']['cf_14'] : "";
	
	                                break;
	                            case "Instalación Red troncal o interconexión ISP-Fecha Funcionamiento":
	
	                                $this->informacion[$key]['a_b'] = (isset($value_cb['paquetesTrabajo']['cf_16']) && !is_null($value_cb['paquetesTrabajo']['cf_16'])) ? $value_cb['paquetesTrabajo']['cf_16'] : "";
	
	                                break;
	                            case "Instalación Red troncal o interconexión ISP-Fecha Prevista PI&PS Instalación":
	
	                                $this->informacion[$key]['a_c'] = (isset($value_cb['paquetesTrabajo']['cf_17']) && !is_null($value_cb['paquetesTrabajo']['cf_17'])) ? $value_cb['paquetesTrabajo']['cf_17'] : "";
	
	                                break;
	                            case "Instalación y Puesta en Funcionamiento Equipos-Estado OTLs":
	
	                                $this->informacion[$key]['a_d'] = (isset($value_cb['paquetesTrabajo']['cf_45']) && !is_null($value_cb['paquetesTrabajo']['cf_45'])) ? $value_cb['paquetesTrabajo']['cf_45'] : "";
	
	                                break;
	                            case "Instalación y Puesta en Funcionamiento Equipos-Estado Equipos Networking":
	
	                                $this->informacion[$key]['a_e'] = (isset($value_cb['paquetesTrabajo']['cf_46']) && !is_null($value_cb['paquetesTrabajo']['cf_46'])) ? $value_cb['paquetesTrabajo']['cf_46'] : "";
	
	                                break;
	                            case "Instalación y Puesta en Funcionamiento Equipos-Estado Equipos Energia":
	
	                                $this->informacion[$key]['a_f'] = (isset($value_cb['paquetesTrabajo']['cf_47']) && !is_null($value_cb['paquetesTrabajo']['cf_47'])) ? $value_cb['paquetesTrabajo']['cf_47'] : "";
	
	                                break;
	                            case "Instalación y Puesta en Funcionamiento Equipos-Fecha Funcionamiento":
	
	                                $this->informacion[$key]['a_g'] = (isset($value_cb['paquetesTrabajo']['cf_16']) && !is_null($value_cb['paquetesTrabajo']['cf_16'])) ? $value_cb['paquetesTrabajo']['cf_16'] : "";
	
	                                break;
	                            case "Instalación y Puesta en Funcionamiento Equipos-Fecha Prevista PI&PS Instalación":
	
	                                $this->informacion[$key]['a_h'] = (isset($value_cb['paquetesTrabajo']['cf_17']) && !is_null($value_cb['paquetesTrabajo']['cf_17'])) ? $value_cb['paquetesTrabajo']['cf_17'] : "";
	                                break;
	
	                            case 'general-Fecha prevista en el PI&PS Funcionamiento':
	                            	$this->informacion[$key]['c_b'] = (isset($value_cb['paquetesTrabajo']['start_date']) && !is_null($value_cb['paquetesTrabajo']['start_date'])) ? $value_cb['paquetesTrabajo']['start_date'] : "";
	                                break;
	
	                            case "general-Porcentaje Avance":
	                                $this->informacion[$key]['a_j'] = (isset($value_cb['paquetesTrabajo']['done_ratio']) && !is_null($value_cb['paquetesTrabajo']['done_ratio'])) ? $value_cb['paquetesTrabajo']['done_ratio'] : "";
	                                break;
	                                
	                           	case "Insfraestructura Nodos-Porcentaje Avance":
	                           		$this->informacion[$key]['c_a'] = (isset($value_cb['paquetesTrabajo']['done_ratio']) && !is_null($value_cb['paquetesTrabajo']['done_ratio'])) ? $value_cb['paquetesTrabajo']['done_ratio'] : "";
	                               	break;
	                               	
	                             case "Insfraestructura Nodos-Fecha Prevista PI&PS Terminación":
	                             	$this->informacion[$key]['c_b'] = (isset($value_cb['paquetesTrabajo']['due_date']) && !is_null($value_cb['paquetesTrabajo']['due_date'])) ? $value_cb['paquetesTrabajo']['due_date'] : "";
	                             	break;
	                             	
	                            case "Instalación Red troncal o interconexión ISP-Porcentaje Avance":
	                            	$this->informacion[$key]['c_c'] = (isset($value_cb['paquetesTrabajo']['done_ratio']) && !is_null($value_cb['paquetesTrabajo']['done_ratio'])) ? $value_cb['paquetesTrabajo']['done_ratio'] : "";
	                            	break;
	                            	
	                        }
	
	                    }
	                    //var_dump($cabecera['info']['custom_fields']);
	                    $cabecera_key_fecha_funcionamiento = array_search(48, array_column($cabecera['info']['custom_fields'], 'id'), true);
	                    $fecha_funcionamiento_cabecera = $cabecera['info']['custom_fields'][$cabecera_key_fecha_funcionamiento]['value'];
	                    $this->informacion[$key]['a_i'] = (!is_null($fecha_funcionamiento_cabecera) && $fecha_funcionamiento_cabecera != '' && $cabecera_key_fecha_funcionamiento != false) ? $fecha_funcionamiento_cabecera : "";
	
	                }
	                
            	}

                
                if ($value['tipo_proyecto'] == 'hfc') {

                    // HFC
                    foreach ($value['campos_parametrizados'] as $key_hfc => $value_hfc) {

                        switch ($value_hfc['sub_tipo'] . "-" . $value_hfc['nombre_formulario']) {

                            case "Estado Construcción Red de Distribución-Descripción Construcción":
                                if (isset($value_hfc['paquetesTrabajo']['actividades']) && $value_hfc['paquetesTrabajo']['actividades'] != '') {
                                    $this->informacion[$key]['a_k'] = $this->ajustarComentarios($value_hfc['paquetesTrabajo']['actividades']);
                                } else {
                                    $this->informacion[$key]['a_k'] = '';
                                }
                                break;
                            case "Estado Construcción Red de Distribución-Estado Avance":
                                $this->informacion[$key]['a_l'] = (isset($value_hfc['paquetesTrabajo']['cf_14']) && $value_hfc['paquetesTrabajo']['cf_14'] != '') ? $value_hfc['paquetesTrabajo']['cf_14'] : "";
                                break;
                            case "Estado Construcción Red de Distribución-Fecha Prevista Terminación":
                                $this->informacion[$key]['a_m'] = (isset($value_hfc['paquetesTrabajo']['due_date']) && $value_hfc['paquetesTrabajo']['due_date'] != '') ? $value_hfc['paquetesTrabajo']['due_date'] : "";
                                break;
                            case "Tendido y Puesta en Funcionamiento Fibra Óptica-Descripcion Actividades":
                                if (isset($value_hfc['paquetesTrabajo']['actividades']) && $value_hfc['paquetesTrabajo']['actividades'] != '') {
                                    $this->informacion[$key]['a_n'] = $this->ajustarComentarios($value_hfc['paquetesTrabajo']['actividades']);
                                } else {
                                    $this->informacion[$key]['a_n'] = '';
                                }
                                break;
                            case "Tendido y Puesta en Funcionamiento Fibra Óptica-Estado Avance":
                                $this->informacion[$key]['a_o'] = (isset($value_hfc['paquetesTrabajo']['cf_14']) && $value_hfc['paquetesTrabajo']['cf_14'] != '') ? $value_hfc['paquetesTrabajo']['cf_14'] : "";
                                break;
                            case "Tendido y Puesta en Funcionamiento Fibra Óptica-Fecha Funcionamiento":
                                $this->informacion[$key]['a_p'] = (isset($value_hfc['paquetesTrabajo']['cf_16']) && $value_hfc['paquetesTrabajo']['cf_16'] != '') ? $value_hfc['paquetesTrabajo']['cf_16'] : "";
                                break;
                            case "general-Porcentaje Avance":
                                if ($value_hfc['tipo'] == "Avance y Estado Instalación Red de Distribución") {
                                    $this->informacion[$key]['a_q'] = (isset($value_hfc['paquetesTrabajo']['done_ratio']) && $value_hfc['paquetesTrabajo']['done_ratio'] != '') ? $value_hfc['paquetesTrabajo']['done_ratio'] : "";
                                } else if ($value_hfc['tipo'] == "Avance y Estado Instalación Nodo EOC") {
                                    $this->informacion[$key]['a_z'] = (isset($value_hfc['paquetesTrabajo']['done_ratio']) && $value_hfc['paquetesTrabajo']['done_ratio'] != '') ? $value_hfc['paquetesTrabajo']['done_ratio'] : "";
                                }
                                break;
                            case "Infraestructura Nodo-Descripcion Obra o Actividad":
                                if (isset($value_hfc['paquetesTrabajo']['actividades']) && $value_hfc['paquetesTrabajo']['actividades'] != '') {
                                    $this->informacion[$key]['a_r'] = $this->ajustarComentarios($value_hfc['paquetesTrabajo']['actividades']);
                                } else {
                                    $this->informacion[$key]['a_r'] = '';
                                }
                                break;
                            case "Infraestructura Nodo-Estado Avance":
                                $this->informacion[$key]['a_s'] = (isset($value_hfc['paquetesTrabajo']['cf_14']) && $value_hfc['paquetesTrabajo']['cf_14'] != '') ? $value_hfc['paquetesTrabajo']['cf_14'] : "";
                                break;
                            case "Infraestructura Nodo-Fecha Prevista Terminación":
                                $this->informacion[$key]['a_t'] = (isset($value_hfc['paquetesTrabajo']['due_date']) && $value_hfc['paquetesTrabajo']['due_date'] != '') ? $value_hfc['paquetesTrabajo']['due_date'] : "";
                                break;
                            case "Instalación y Puesta en Funcionamiento Equipos-Estado Equipos Networking":
                                $this->informacion[$key]['a_w'] = (isset($value_hfc['paquetesTrabajo']['cf_46']) && $value_hfc['paquetesTrabajo']['cf_46'] != '') ? $value_hfc['paquetesTrabajo']['cf_46'] : "";
                                break;
                            case "Instalación y Puesta en Funcionamiento Equipos-Fecha Funcionamiento":
                                $this->informacion[$key]['a_x'] = (isset($value_hfc['paquetesTrabajo']['cf_16']) && $value_hfc['paquetesTrabajo']['cf_16'] != '') ? $value_hfc['paquetesTrabajo']['cf_16'] : "";
                                break;
                            case "general-Fecha Inicio instalación Acc HFC":
                                $this->informacion[$key]['b_k'] = (isset($value_hfc['paquetesTrabajo']['start_date']) && $value_hfc['paquetesTrabajo']['start_date'] != '') ? $value_hfc['paquetesTrabajo']['start_date'] : "";
                                break;
                            case "general-Fecha Terminación instalación Acc HFC":
                                $this->informacion[$key]['b_l'] = (isset($value_hfc['paquetesTrabajo']['due_date']) && $value_hfc['paquetesTrabajo']['due_date'] != '') ? $value_hfc['paquetesTrabajo']['due_date'] : "";
                                break;
                            case "Tendido y Puesta en Funcionameinto Red Coaxial-Descripcion Actividades":
                                if (isset($value_hfc['paquetesTrabajo']['actividades']) && $value_hfc['paquetesTrabajo']['actividades'] != '') {
                                    $this->informacion[$key]['b_m'] = $this->ajustarComentarios($value_hfc['paquetesTrabajo']['actividades']);
                                } else {
                                    $this->informacion[$key]['b_m'] = '';
                                }
                                break;
                            case "Tendido y Puesta en Funcionameinto Red Coaxial-Estado Avance":
                                $this->informacion[$key]['b_n'] = (isset($value_hfc['paquetesTrabajo']['cf_14']) && $value_hfc['paquetesTrabajo']['cf_14'] != '') ? $value_hfc['paquetesTrabajo']['cf_14'] : "";
                                break;
                            case "Tendido y Puesta en Funcionameinto Red Coaxial-Fecha Funcionamiento":
                                $this->informacion[$key]['b_o'] = (isset($value_hfc['paquetesTrabajo']['start_date']) && $value_hfc['paquetesTrabajo']['start_date'] != '') ? $value_hfc['paquetesTrabajo']['start_date'] : "";
                                break;
                               
                            case "Tendido y Puesta en Funcionamiento Fibra Óptica-Fecha Prevista PI&PS Terminación Red Distribución":
                                $this->informacion[$key]['c_d'] = (isset($value_cb['paquetesTrabajo']['due_date']) && !is_null($value_cb['paquetesTrabajo']['due_date'])) ? $value_cb['paquetesTrabajo']['due_date'] : "";
                                break;
                             
                            case "Tendido y Puesta en Funcionameinto Red Coaxial-Porcentaje Avance":
                           		$this->informacion[$key]['c_e'] = (isset($value_hfc['paquetesTrabajo']['done_ratio']) && $value_hfc['paquetesTrabajo']['done_ratio'] != '') ? $value_hfc['paquetesTrabajo']['done_ratio'] : "";
                               	break;
                               	
                            case "Tendido y Puesta en Funcionameinto Red Coaxial-Fecha Prevista PI&PS Terminación":
                            	$this->informacion[$key]['c_f'] = (isset($value_cb['paquetesTrabajo']['due_date']) && !is_null($value_cb['paquetesTrabajo']['due_date'])) ? $value_cb['paquetesTrabajo']['due_date'] : "";
                            	break;
                            
                        }
                    }

                    $llaveFechaPrevistaInterventoria = array_search(49, array_column($value['info']['custom_fields'], 'id'), true);
                    $llaveHFCInstalar = array_search(31, array_column($value['info']['custom_fields'], 'id'), true);

                    $this->informacion[$key]['b_i'] = (($llaveFechaPrevistaInterventoria != false && $value['info']['custom_fields'][$llaveFechaPrevistaInterventoria]['value'] != '')) ? $value['info']['custom_fields'][$llaveFechaPrevistaInterventoria]['value'] : "";
                    $this->informacion[$key]['b_j'] = (($llaveHFCInstalar != false && $value['info']['custom_fields'][$llaveHFCInstalar]['value'] != '')) ? $value['info']['custom_fields'][$llaveHFCInstalar]['value'] : "";

                    $llaveHFCInstalados = array_search(36, array_column($value['info']['custom_fields'], 'id'), true);
                    $llaveAccVIP = array_search(37, array_column($value['info']['custom_fields'], 'id'), true);

                    $this->informacion[$key]['b_p'] = (($llaveHFCInstalados != false && $value['info']['custom_fields'][$llaveHFCInstalados]['value'] != '')) ? $value['info']['custom_fields'][$llaveHFCInstalados]['value'] : "";
                    $this->informacion[$key]['b_q'] = (($llaveAccVIP != false && $value['info']['custom_fields'][$llaveAccVIP]['value'] != '')) ? $value['info']['custom_fields'][$llaveAccVIP]['value'] : "";
                    $this->informacion[$key]['b_r'] = (($llaveAccVIP != false && $value['info']['custom_fields'][$llaveAccVIP]['value'] != '')) ? $value['info']['custom_fields'][$llaveAccVIP]['value'] : "";

                }
                if ($value['tipo_proyecto'] == 'wman') {

                    //Wman
                    foreach ($value['campos_parametrizados'] as $key_wman => $value_wman) {

                        switch ($value_wman['sub_tipo'] . "-" . $value_wman['nombre_formulario']) {

                            case "Infraestructura Nodo-Descripcion Obra o Actividad":
                                if (isset($value_hfc['paquetesTrabajo']['actividades']) && $value_hfc['paquetesTrabajo']['actividades'] != '') {
                                    $this->informacion[$key]['b_a'] = $this->ajustarComentarios($value_hfc['paquetesTrabajo']['actividades']);
                                } else {
                                    $this->informacion[$key]['b_a'] = '';
                                }
                                break;
                            case "Infraestructura Nodo-Estado Avance":
                                $this->informacion[$key]['b_b'] = (isset($value_hfc['paquetesTrabajo']['cf_14']) && $value_hfc['paquetesTrabajo']['cf_14'] != '') ? $value_hfc['paquetesTrabajo']['cf_14'] : "";
                                break;
                            case "Infraestructura Nodo-Fecha Prevista Terminación":
                                $this->informacion[$key]['b_c'] = (isset($value_hfc['paquetesTrabajo']['due_date']) && $value_hfc['paquetesTrabajo']['due_date'] != '') ? $value_hfc['paquetesTrabajo']['due_date'] : "";
                                break;
                            case "Instalación y Puesta en Funcionamiento Equipos-Fecha Funcionamiento":
                                $this->informacion[$key]['b_f'] = (isset($value_hfc['paquetesTrabajo']['cf_16']) && $value_hfc['paquetesTrabajo']['cf_16'] != '') ? $value_hfc['paquetesTrabajo']['cf_16'] : "";
                                break;
                            case "general-Porcentaje Avance":
                                $this->informacion[$key]['b_h'] = (isset($value_hfc['paquetesTrabajo']['done_ratio']) && $value_hfc['paquetesTrabajo']['done_ratio'] != '') ? $value_hfc['paquetesTrabajo']['done_ratio'] : "";
                                break;
                            case "general-Fecha Inicio instalación Acc Inalámbricos":
                                $this->informacion[$key]['b_t'] = (isset($value_hfc['paquetesTrabajo']['start_date']) && $value_hfc['paquetesTrabajo']['start_date'] != '') ? $value_hfc['paquetesTrabajo']['start_date'] : "";
                                break;
                            case "general-Fecha Terminación instalación Acc Inalámbricos":
                                $this->informacion[$key]['b_u'] = (isset($value_hfc['paquetesTrabajo']['due_date']) && $value_hfc['paquetesTrabajo']['due_date'] != '') ? $value_hfc['paquetesTrabajo']['due_date'] : "";
                                break;
                                
                            case "Infraestructura Red Acceso-Descripción Actividades":
                               	if (isset($value_hfc['paquetesTrabajo']['actividades']) && $value_hfc['paquetesTrabajo']['actividades'] != '') {
                               		$this->informacion[$key]['c_g'] = $this->ajustarComentarios($value_hfc['paquetesTrabajo']['actividades']);
                               	} else {
                               		$this->informacion[$key]['c_g'] = '';
                               	}
                               	break;
                               	
                            case "Infraestructura Red Acceso-Porcentaje Avance":
                            	$this->informacion[$key]['c_h'] = (isset($value_hfc['paquetesTrabajo']['done_ratio']) && $value_hfc['paquetesTrabajo']['done_ratio'] != '') ? $value_hfc['paquetesTrabajo']['done_ratio'] : "";
                               	break;
                               	
                            case "Infraestructura Red Acceso-Fecha Prevista PI&PS Terminación":
                               	$this->informacion[$key]['c_i'] = (isset($value_cb['paquetesTrabajo']['due_date']) && !is_null($value_cb['paquetesTrabajo']['due_date'])) ? $value_cb['paquetesTrabajo']['due_date'] : "";
                               	break;
                        }
                    }

                    $llaveCeldasInstalar = array_search(30, array_column($value['info']['custom_fields'], 'id'), true);
                    $llaveCeldasInstaladas = array_search(34, array_column($value['info']['custom_fields'], 'id'), true);
                    $llaveFechaFuncionamiento = array_search(48, array_column($value['info']['custom_fields'], 'id'), true);

                    $this->informacion[$key]['b_d'] = (($llaveCeldasInstalar != false && $value['info']['custom_fields'][$llaveCeldasInstalar]['value'] != '')) ? $value['info']['custom_fields'][$llaveCeldasInstalar]['value'] : "";
                    $this->informacion[$key]['b_e'] = (($llaveCeldasInstaladas != false && $value['info']['custom_fields'][$llaveCeldasInstaladas]['value'] != '')) ? $value['info']['custom_fields'][$llaveCeldasInstaladas]['value'] : "";
                    $this->informacion[$key]['b_g'] = (($value['info']['custom_fields'][$llaveCeldasInstaladas]['value'] != '' && $llaveFechaFuncionamiento != false && $value['info']['custom_fields'][$llaveFechaFuncionamiento]['value'] != '')) ? $value['info']['custom_fields'][$llaveFechaFuncionamiento]['value'] : "";

                    $llaveAccInalam = array_search(32, array_column($value['info']['custom_fields'], 'id'), true);
                    $llaveSMCPE = array_search(40, array_column($value['info']['custom_fields'], 'id'), true);

                    $this->informacion[$key]['b_s'] = (($llaveAccInalam != false && $value['info']['custom_fields'][$llaveAccInalam]['value'] != '')) ? $value['info']['custom_fields'][$llaveAccInalam]['value'] : "";
                    $this->informacion[$key]['b_v'] = (($llaveSMCPE != false && $value['info']['custom_fields'][$llaveSMCPE]['value'] != '')) ? $value['info']['custom_fields'][$llaveSMCPE]['value'] : "";

                    $llaveE1E2 = array_search(41, array_column($value['info']['custom_fields'], 'id'), true);

                    $this->informacion[$key]['b_w'] = (($llaveE1E2 != false && $value['info']['custom_fields'][$llaveE1E2]['value'] != '')) ? $value['info']['custom_fields'][$llaveE1E2]['value'] : "";
                    $this->informacion[$key]['b_x'] = (($llaveE1E2 != false && $value['info']['custom_fields'][$llaveE1E2]['value'] != '')) ? $value['info']['custom_fields'][$llaveE1E2]['value'] : "";

                    $llaveRInternve = array_search(42, array_column($value['info']['custom_fields'], 'id'), true);

                    $this->informacion[$key]['b_y'] = (($llaveRInternve != false && $value['info']['custom_fields'][$llaveRInternve]['value'] != '')) ? $value['info']['custom_fields'][$llaveRInternve]['value'] : "";

                }

                $i++;
            }
        }
    }

    public function registrarAlmacenDatos() {
        $conexion = "almacendatos";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        ksort($this->informacion);

        if($this->fecha == ""){
        	$cadenaSql = $this->miSql->getCadenaSql('actualizarProyectosAlmacen', $this->informacion);
        	$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "actualizar");
        	
        	$cadenaSql = $this->miSql->getCadenaSql('registrarProyectosAlmacen', $this->informacion);
        	$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "insertar");
        }else{
        	$cadenaSql = $this->miSql->getCadenaSql('registrarProyectosAlmacenMasivo', $this->informacion, $this->fecha);
        	$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "insertar");
        }
    }

    public function consultarPaqueteTrabajo($proyecto = '', $nombre_paquete = '', $tipo = '') {

        $contenido = '';

        foreach ($proyecto['campos_parametrizados'] as $key => $value) {

            if ($tipo != "") {
                if ($value['nombre_formulario'] == $nombre_paquete && $value['tipo'] == $tipo) {

                    if (isset($value['paquetesTrabajo'])) {
                        $contenido = $value['paquetesTrabajo'];
                    }

                }
            } else {

                if ($value['sub_tipo'] == $nombre_paquete) {

                    if (isset($value['paquetesTrabajo'])) {
                        $contenido = $value['paquetesTrabajo'];
                    }
                }
            }
        }

        if ($contenido == '') {

            $contenido = false;
        }

        return $contenido;
    }

    public function compactarAvances($proyecto = '', $tema = '', $tipo = '') {

        $contenido = '';
        foreach ($proyecto['campos_parametrizados'] as $key => $value) {

            if ($tipo != '' && $value[$tipo] == $tema) {

                foreach ($value['actividades'] as $llave => $valor) {

                    $fecha_actividad = substr($valor['createdAt'], 0, 10);

                    $contenido .= "(" . $fecha_actividad . ") " . $valor['comment']['raw'] . "\n";
                }
            } elseif ($value['sub_tipo'] == $tema) {

                if (isset($value['paquetesTrabajo'])) {

                    foreach ($value['paquetesTrabajo']['actividades'] as $llave => $valor) {

                        $fecha_actividad = substr($valor['createdAt'], 0, 10);

                        $contenido .= "(" . $fecha_actividad . ") " . $valor['comment']['raw'] . "\n";
                    }

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

    public function camposBlancos($key) {
        $this->informacion[$key]['a_0'] = '';
        $this->informacion[$key]['a_1'] = '';
        $this->informacion[$key]['a_2'] = '';
        $this->informacion[$key]['a_'] = '';
        $this->informacion[$key]['b_'] = '';
        $this->informacion[$key]['c_'] = '';
        $this->informacion[$key]['d_'] = '';
        $this->informacion[$key]['e_'] = '';
        $this->informacion[$key]['f_'] = '';
        $this->informacion[$key]['g_'] = '';
        $this->informacion[$key]['h_'] = '';
        $this->informacion[$key]['i_'] = '';
        $this->informacion[$key]['j_'] = '';
        $this->informacion[$key]['k_'] = '';
        $this->informacion[$key]['l_'] = '';
        $this->informacion[$key]['m_'] = '';
        $this->informacion[$key]['n_'] = '';
        $this->informacion[$key]['o_'] = '';
        $this->informacion[$key]['p_'] = '';
        $this->informacion[$key]['q_'] = '';
        $this->informacion[$key]['r_'] = '';
        $this->informacion[$key]['s_'] = '';
        $this->informacion[$key]['t_'] = '';
        $this->informacion[$key]['u_'] = '';
        $this->informacion[$key]['v_'] = '';
        $this->informacion[$key]['w_'] = '';
        $this->informacion[$key]['x_'] = '';
        $this->informacion[$key]['y_'] = '';
        $this->informacion[$key]['z_'] = '';
        $this->informacion[$key]['a_a'] = '';
        $this->informacion[$key]['a_b'] = '';
        $this->informacion[$key]['a_c'] = '';
        $this->informacion[$key]['a_d'] = '';
        $this->informacion[$key]['a_e'] = '';
        $this->informacion[$key]['a_f'] = '';
        $this->informacion[$key]['a_g'] = '';
        $this->informacion[$key]['a_h'] = '';
        $this->informacion[$key]['a_i'] = '';
        $this->informacion[$key]['a_j'] = '';
        $this->informacion[$key]['a_k'] = '';
        $this->informacion[$key]['a_l'] = '';
        $this->informacion[$key]['a_m'] = '';
        $this->informacion[$key]['a_n'] = '';
        $this->informacion[$key]['a_o'] = '';
        $this->informacion[$key]['a_p'] = '';
        $this->informacion[$key]['a_q'] = '';
        $this->informacion[$key]['a_r'] = '';
        $this->informacion[$key]['a_s'] = '';
        $this->informacion[$key]['a_t'] = '';
        $this->informacion[$key]['a_u'] = '';
        $this->informacion[$key]['a_v'] = '';
        $this->informacion[$key]['a_w'] = '';
        $this->informacion[$key]['a_x'] = '';
        $this->informacion[$key]['a_y'] = '';
        $this->informacion[$key]['a_z'] = '';
        $this->informacion[$key]['b_a'] = '';
        $this->informacion[$key]['b_b'] = '';
        $this->informacion[$key]['b_c'] = '';
        $this->informacion[$key]['b_d'] = '';
        $this->informacion[$key]['b_e'] = '';
        $this->informacion[$key]['b_f'] = '';
        $this->informacion[$key]['b_g'] = '';
        $this->informacion[$key]['b_h'] = '';
        $this->informacion[$key]['b_i'] = '';
        $this->informacion[$key]['b_j'] = '';
        $this->informacion[$key]['b_k'] = '';
        $this->informacion[$key]['b_l'] = '';
        $this->informacion[$key]['b_m'] = '';
        $this->informacion[$key]['b_n'] = '';
        $this->informacion[$key]['b_o'] = '';
        $this->informacion[$key]['b_p'] = '';
        $this->informacion[$key]['b_q'] = '';
        $this->informacion[$key]['b_r'] = '';
        $this->informacion[$key]['b_s'] = '';
        $this->informacion[$key]['b_t'] = '';
        $this->informacion[$key]['b_u'] = '';
        $this->informacion[$key]['b_v'] = '';
        $this->informacion[$key]['b_w'] = '';
        $this->informacion[$key]['b_x'] = '';
        $this->informacion[$key]['b_y'] = '';
        $this->informacion[$key]['b_z'] = '';
        $this->informacion[$key]['c_a'] = '';
        $this->informacion[$key]['c_b'] = '';
        $this->informacion[$key]['c_b'] = '';
        $this->informacion[$key]['c_d'] = '';
        $this->informacion[$key]['c_e'] = '';
        $this->informacion[$key]['c_e'] = '';
        $this->informacion[$key]['c_f'] = '';
        $this->informacion[$key]['c_g'] = '';
        $this->informacion[$key]['c_h'] = '';
        $this->informacion[$key]['c_i'] = '';
        
        
    }

}

?>

