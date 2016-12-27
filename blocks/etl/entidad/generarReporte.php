<?php

namespace reportes\instalacionesGenerales\entidad;

class GenerarReporteInstalaciones {
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $proyectos;
	public $fecha;
	
    public function __construct($sql) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $_REQUEST['tiempo'] = time();

        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        //Formato fechas de inicio y fin Año-Mes-Día example: 2016-8-15
        //Cuando se quiere especificar un día puntual, la fecha de inicio y de fin deben ser iguales.
        
        //$_REQUEST['fechaInicio'] = "2016-8-20";
        //$_REQUEST['fechaFin']= "2016-8-25";
        
        if(isset($_REQUEST['fechaInicio']) && isset($_REQUEST['fechaFin'])){
        	
        	$valoresPrimera = explode ("-", $_REQUEST['fechaInicio']);   
		  	$valoresSegunda = explode ("-", $_REQUEST['fechaFin']); 
		  	$diaPrimera    = $valoresPrimera[2];  
		  	$mesPrimera  = $valoresPrimera[1];  
		  	$anyoPrimera   = $valoresPrimera[0]; 
		  	$diaSegunda   = $valoresSegunda[2];  
		  	$mesSegunda = $valoresSegunda[1];  
		  	$anyoSegunda  = $valoresSegunda[0];
		  	$diasPrimeraJuliano = gregoriantojd($mesPrimera, $diaPrimera, $anyoPrimera);  
		  	$diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anyoSegunda); 
		  	
		  	$diasA = array(0, 31,28,31,30,31,30,31,31,30,31,30,31);
		  	$diasB = array(0, 31,29,31,30,31,30,31,31,30,31,30,31);
		  	
        	if($diasPrimeraJuliano < $diasSegundaJuliano){
        		
        		/**
        		 * Obtener Actividades Paquetes de Trabajo
        		 */
        		$this->obtenerActividades();
        		
        		$copiaProyectos = $this->proyectos;
        		
        		for($i = $anyoPrimera; $i <= $anyoSegunda; $i++){
        			
        			if(($i % 4 == 0) && (($i % 100 != 0) || ($i % 400 == 0))){
        				$dias =  $diasB;
        			}else{
        				$dias =  $diasA;
        			}
        			
        			if($i == $anyoSegunda){
        				$mes = $mesSegunda;
        			}else if($anyoPrimera < $anyoSegunda){
        				$mes = 12;
        			}
        			for($j = $mesPrimera; $j <= $mes ; $j++){
        				
        				if($i == $anyoSegunda && $j == $mesSegunda){
        					$dia = $diaSegunda;
        				}else{
        					$dia = $dias[$j];
        				}
        				for($k = $diaPrimera; $k <= $dia; $k++){
        					
        					$fecha = $i . "-" . $j . "-" . $k;
        					
        					$this->fecha = $fecha;
        					
        					$this->proyectos = $copiaProyectos;
        					
        					/**
        					 * Filtrar Actividades Paquetes de Trabajo
        					 */
        					$this->filtrarActividades($fecha);
        					
        					/**
        					 * Crear Documento Hoja de Calculo(Reporte)
        					 */
        					
        					$this->crearHojaCalculo();
        				}
        				
        				$diaPrimera = 1;
        			}
        			
        			$mesPrimera = 1;
        		}
        		
        	}else if($diasPrimeraJuliano == $diasSegundaJuliano){
        		
        		$fecha = $_REQUEST['fechaInicio'];
	        	/**
	        	 * Obtener Actividades Paquetes de Trabajo
	        	 */
	        	$this->obtenerActividades();
	        	
	        	/**
	        	 * Filtrar Actividades Paquetes de Trabajo
	        	 */
	        	$this->filtrarActividades($fecha);
	        	
	        	/**
	        	 * Crear Documento Hoja de Calculo(Reporte)
	        	 */
	        	
	        	$this->crearHojaCalculo();
        	}else{
        		exit();
        	}
        }else{
        	$fecha = date("Y-m-d ");
        	/**
        	 * Obtener Actividades Paquetes de Trabajo
        	 */
        	$this->obtenerActividades();
        	
        	/**
        	 * Filtrar Actividades Paquetes de Trabajo
        	 */
        	$this->filtrarActividades($fecha);
        	
        	/**
        	 * Crear Documento Hoja de Calculo(Reporte)
        	 */
        	
        	$this->crearHojaCalculo();
        }
    }
    public function obtenerDetalleProyectos() {
        foreach ($this->proyectos as $key => $value) {

            $urlDetalle = $this->crearUrlDetalleProyectos($value['id_proyecto']);

            $detalle = file_get_contents($urlDetalle);

            $detalle = json_decode($detalle, true);

            $this->proyectos[$key]["info"] = $detalle;
        }
    }
    public function crearUrlDetalleProyectos($var = '') {

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=openKyosApi";
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . $_REQUEST['tiempo'];
        $variable .= "&metodo=proyectosDetalle";
        $variable .= "&id_proyecto=" . $var;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }
    public function crearHojaCalculo() {
    	
    	$conexion = "almacendatos";
    	$this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
    	 
        include_once "crearDocumentoHojaCalculo.php";
        $miProcesador = new GenerarReporteExcelInstalaciones();
        $miProcesador->iniciar($this->miSql, $this->proyectos, $this->fecha);
        
    }
    public function detallarCamposPersonalizadosProyecto() {
        foreach ($this->proyectos as $key => $value) {

            $urlPaquetes = $this->crearUrlDetalleProyecto($value['id_proyecto']);

            $detalleProyecto = file_get_contents($urlPaquetes);

            $detalleProyecto = json_decode($detalleProyecto, true);

            $this->proyectos[$key]['campos_personalizados'] = $detalleProyecto['custom_fields'];
        }
    }
    public function crearUrlDetalleProyecto($var = '') {

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=openKyosApi";
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . $_REQUEST['tiempo'];
        $variable .= "&metodo=proyectosDetalle";
        $variable .= "&id_proyecto=" . $var;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }
  
    public function filtrarActividades($fecha) {
    	
        foreach ($this->proyectos as $key => $value) {

            foreach ($value['campos_parametrizados'] as $llave => $valor) {

                if (isset($valor['paquetesTrabajo']['actividades'])) {

                    if ($valor['paquetesTrabajo']['type_id'] == 2) {

                        foreach ($valor['paquetesTrabajo']['actividades'] as $llave2 => $actividad) {

                            if ($actividad['_type'] != 'Activity::Comment') {
                                unset($this->proyectos[$key]['campos_parametrizados'][$llave]['paquetesTrabajo']['actividades'][$llave2]);
                            } else {
                                
								$val = (strpos($actividad['comment']['raw'], 'automáticamente cambiando'));
								
                                if (is_numeric($val)) {
                                	unset($this->proyectos[$key]['campos_parametrizados'][$llave]['paquetesTrabajo']['actividades'][$llave2]);
                                }
                            }

                            $fecha_actividad = substr($actividad['createdAt'], 0, 10);
                            $fecha_actividad = strtotime($fecha_actividad);
                            $fecha_inicio = strtotime($fecha);

                            if ($fecha_actividad != $fecha_inicio) {
                               unset($this->proyectos[$key]['campos_parametrizados'][$llave]['paquetesTrabajo']['actividades'][$llave2]);
                            }
                        }
                        
                    }
                }
            }
        }
        
    }

    public function obtenerProyectoCore() {

        $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionCore');
        $info_core = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $info_core = $info_core[0];

        $cadenaSql = $this->miSql->getCadenaSql('consultarCamposParametrizados', $info_core['id_proyecto']);
        $campos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $info_core['campos_parametrizados'] = $campos;

        $urlDetalle = $this->crearUrlDetalleProyectos($info_core['id_proyecto']);

        $detalle = file_get_contents($urlDetalle);

        $detalle = json_decode($detalle, true);

        $info_core['info'] = $detalle;

        $urlPaquetes = $this->crearUrlPaquetesTrabajo($info_core['id_proyecto']);

        $paquetesTrabajo = file_get_contents($urlPaquetes);

        $paquetesTrabajo = json_decode($paquetesTrabajo, true);

        $info_core['paquetesTrabajo'] = $paquetesTrabajo;

        $this->informacion_proyecto_core = $info_core;
        
    }
    public function obtenerProyectoCabecera() {
        $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionCabecera');
        $cabecera = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        foreach ($cabecera as $info_cabecera) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarCamposParametrizados', $info_cabecera['id_proyecto']);
            $campos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

            $info_cabecera['campos_parametrizados'] = $campos;

            $urlDetalle = $this->crearUrlDetalleProyectos($info_cabecera['id_proyecto']);

            $detalle = file_get_contents($urlDetalle);

            $detalle = json_decode($detalle, true);

            $info_cabecera['info'] = $detalle;

            $urlPaquetes = $this->crearUrlPaquetesTrabajo($info_cabecera['id_proyecto']);

            $paquetesTrabajo = file_get_contents($urlPaquetes);

            $paquetesTrabajo = json_decode($paquetesTrabajo, true);

            $info_cabecera['paquetesTrabajo'] = $paquetesTrabajo;

            $this->proyectos_cabecera[] = $info_cabecera;
        }
    }
    public function obtenerProyectoHFC() {
        $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionHFC');
        $hfc = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        foreach ($hfc as $info_hfc) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarCamposParametrizados', $info_hfc['id_proyecto']);
            $campos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

            $info_hfc['campos_parametrizados'] = $campos;

            $urlDetalle = $this->crearUrlDetalleProyectos($info_hfc['id_proyecto']);

            $detalle = file_get_contents($urlDetalle);

            $detalle = json_decode($detalle, true);

            $info_hfc['info'] = $detalle;

            $urlPaquetes = $this->crearUrlPaquetesTrabajo($info_hfc['id_proyecto']);

            $paquetesTrabajo = file_get_contents($urlPaquetes);

            $paquetesTrabajo = json_decode($paquetesTrabajo, true);

            $info_hfc['paquetesTrabajo'] = $paquetesTrabajo;

            $this->proyectos_hfc[] = $info_hfc;
        }
    }
    public function obtenerProyectoWman() {

        $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionWman');
        $wman = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        foreach ($wman as $info_wman) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarCamposParametrizados', $info_wman['id_proyecto']);
            $campos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

            if ($campos) {
                $info_wman['campos_parametrizados'] = $campos;
            } else {
                $info_wman['campos_parametrizados'] = array();
            }

            $urlDetalle = $this->crearUrlDetalleProyectos($info_wman['id_proyecto']);

            $detalle = file_get_contents($urlDetalle);

            $detalle = json_decode($detalle, true);

            $info_wman['info'] = $detalle;

            $urlPaquetes = $this->crearUrlPaquetesTrabajo($info_wman['id_proyecto']);

            $paquetesTrabajo = file_get_contents($urlPaquetes);

            $paquetesTrabajo = json_decode($paquetesTrabajo, true);

            $info_wman['paquetesTrabajo'] = $paquetesTrabajo;

            $this->proyectos_wman[] = $info_wman;

        }

    }
    public function obtenerActividades() {

        $this->obtenerProyectoCore();

        foreach ($this->informacion_proyecto_core['campos_parametrizados'] as $key => $valor) {

            if ($valor['sub_tipo'] == 'Centro de Gestión' || $valor['sub_tipo'] == 'Mesa  de Ayuda' || $valor['sub_tipo'] == 'Otros Equipos o Sistemas en el NOC') {

                foreach ($this->informacion_proyecto_core['paquetesTrabajo'] as $clave_paquete => $value_paquete) {

                    if ($value_paquete['id'] == $valor['valor_actividad']) {

                        $urlActividades = $this->crearUrlActividades($valor['valor_actividad']);
                        $actividades = json_decode(file_get_contents($urlActividades), true);

                        $value_paquete['actividades'] = $actividades;

                        $this->informacion_proyecto_core['campos_parametrizados'][$key]['paquetesTrabajo'] = $value_paquete;

                        if ($valor['info_hijos'] == 't') {

                            //$childs = $this->informacion_proyecto_core['campos_parametrizados'][$key]['paquetesTrabajo'];

                            foreach ($value_paquete['child_ids'] as $contenido) {

                                $urlActividades = $this->crearUrlActividades($contenido);
                                $actividades = json_decode(file_get_contents($urlActividades), true);
                                
                                if($actividades == null){
                                	$actividades = array();
                                }
                                
                                foreach ($actividades as $actividad) {
                                    $this->informacion_proyecto_core['campos_parametrizados'][$key]['paquetesTrabajo']['actividades'][] = $actividad;
                                }
                            }
                        }

                    }
                }
            } else {

                foreach ($this->informacion_proyecto_core['paquetesTrabajo'] as $clave_paquete => $value_paquete) {

                    if ($value_paquete['id'] == $valor['valor_actividad']) {
                        $this->informacion_proyecto_core['campos_parametrizados'][$key]['paquetesTrabajo'] = $value_paquete;
                    }
                }
            }
        }
        
        $this->proyectos[] = $this->informacion_proyecto_core;

        $this->obtenerProyectoCabecera();

        foreach ($this->proyectos_cabecera as $this->informacion_proyecto_cabecera) {

            foreach ($this->informacion_proyecto_cabecera['campos_parametrizados'] as $key => $valor) {

                if ($valor['sub_tipo'] == 'Infraestructura Nodos' || $valor['sub_tipo'] == 'Instalación Red troncal o interconexión ISP' || $valor['sub_tipo'] == 'Instalación y Puesta en Funcionamiento Equipos') {

                    foreach ($this->informacion_proyecto_cabecera['paquetesTrabajo'] as $clave_paquete => $value_paquete) {

                        if ($value_paquete['id'] == $valor['valor_actividad']) {

                            $urlActividades = $this->crearUrlActividades($valor['valor_actividad']);
                            $actividades = json_decode(file_get_contents($urlActividades), true);

                            $value_paquete['actividades'] = $actividades;

                            $this->informacion_proyecto_cabecera['campos_parametrizados'][$key]['paquetesTrabajo'] = $value_paquete;

                            if ($valor['info_hijos'] == 't') {

                                $childs = $this->informacion_proyecto_cabecera['campos_parametrizados'][$key]['paquetesTrabajo'];

                                foreach ($value_paquete['child_ids'] as $contenido) {

                                    $urlActividades = $this->crearUrlActividades($contenido);
                                    $actividades = json_decode(file_get_contents($urlActividades), true);

                                    if($actividades == null){
                                    	$actividades = array();
                                    }
                                    
                                    foreach ($actividades as $actividad) {
                                        $this->informacion_proyecto_cabecera['campos_parametrizados'][$key]['paquetesTrabajo']['actividades'][] = $actividad;
                                    }
                                }
                            }
                        }
                    }
                } else {

                    foreach ($this->informacion_proyecto_cabecera['paquetesTrabajo'] as $clave_paquete => $value_paquete) {

                        if ($value_paquete['id'] == $valor['valor_actividad']) {
                            $this->informacion_proyecto_cabecera['campos_parametrizados'][$key]['paquetesTrabajo'] = $value_paquete;
                        }
                    }
                }
            }

            $this->proyectos[] = $this->informacion_proyecto_cabecera;

        }

        $this->obtenerProyectoHFC();

        foreach ($this->proyectos_hfc as $this->informacion_proyecto_hfc) {

            foreach ($this->informacion_proyecto_hfc['campos_parametrizados'] as $key => $valor) {

                if ($valor['sub_tipo'] == 'Estado Construcción Red de Distribución' || $valor['sub_tipo'] == 'Tendido y Puesta en Funcionamiento Fibra Óptica' || $valor['sub_tipo'] == 'Infraestructura Nodo' || $valor['sub_tipo'] == 'Tendido y Puesta en Funcionameinto Red Coaxial') {

                    foreach ($this->informacion_proyecto_hfc['paquetesTrabajo'] as $clave_paquete => $value_paquete) {

                        if ($value_paquete['id'] == $valor['valor_actividad']) {

                            $urlActividades = $this->crearUrlActividades($valor['valor_actividad']);
                            $actividades = json_decode(file_get_contents($urlActividades), true);

                            $value_paquete['actividades'] = $actividades;

                            $this->informacion_proyecto_hfc['campos_parametrizados'][$key]['paquetesTrabajo'] = $value_paquete;

                            if ($valor['info_hijos'] == 't') {

                                $childs = $this->informacion_proyecto_hfc['campos_parametrizados'][$key]['paquetesTrabajo'];

                                foreach ($value_paquete['child_ids'] as $contenido) {

                                    $urlActividades = $this->crearUrlActividades($contenido);
                                    $actividades = json_decode(file_get_contents($urlActividades), true);

                                    if($actividades == null){
                                    	$actividades = array();
                                    }
                                    
                                    foreach ($actividades as $actividad) {
                                        $this->informacion_proyecto_hfc['campos_parametrizados'][$key]['paquetesTrabajo']['actividades'][] = $actividad;
                                    }
                                }
                            }
                        }
                    }
                } else {

                    foreach ($this->informacion_proyecto_hfc['paquetesTrabajo'] as $clave_paquete => $value_paquete) {

                        if ($value_paquete['id'] == $valor['valor_actividad']) {
                            $this->informacion_proyecto_hfc['campos_parametrizados'][$key]['paquetesTrabajo'] = $value_paquete;
                        }
                    }
                }
            }

            $this->proyectos[] = $this->informacion_proyecto_hfc;
        }

        $this->obtenerProyectoWman();

        foreach ($this->proyectos_wman as $this->informacion_proyecto_wman) {

            foreach ($this->informacion_proyecto_wman['campos_parametrizados'] as $key => $valor) {

                if ($valor['sub_tipo'] == 'Infraestructura Nodo' || $valor['sub_tipo'] == 'Instalación y Puesta en Funcionamiento Equipos') {

                    foreach ($this->informacion_proyecto_wman['paquetesTrabajo'] as $clave_paquete => $value_paquete) {

                        if ($value_paquete['id'] == $valor['valor_actividad']) {

                            $urlActividades = $this->crearUrlActividades($valor['valor_actividad']);
                            $actividades = json_decode(file_get_contents($urlActividades), true);

                            $value_paquete['actividades'] = $actividades;

                            $this->informacion_proyecto_wman['campos_parametrizados'][$key]['paquetesTrabajo'] = $value_paquete;

                            if ($valor['info_hijos'] == 't') {

                                $childs = $this->informacion_proyecto_wman['campos_parametrizados'][$key]['paquetesTrabajo'];

                                foreach ($value_paquete['child_ids'] as $contenido) {

                                    $urlActividades = $this->crearUrlActividades($contenido);
                                    $actividades = json_decode(file_get_contents($urlActividades), true);

                                    if($actividades == null){
                                    	$actividades = array();
                                    }
                                    
                                    foreach ($actividades as $actividad) {
                                        $this->informacion_proyecto_wman['campos_parametrizados'][$key]['paquetesTrabajo']['actividades'][] = $actividad;
                                    }
                                }
                            }
                        }
                    }
                } else {

                    foreach ($this->informacion_proyecto_wman['paquetesTrabajo'] as $clave_paquete => $value_paquete) {

                        if ($value_paquete['id'] == $valor['valor_actividad']) {
                            $this->informacion_proyecto_wman['campos_parametrizados'][$key]['paquetesTrabajo'] = $value_paquete;
                        }
                    }
                }
            }

            $this->proyectos[] = $this->informacion_proyecto_wman;
        }

    }
    public function obtenerHijosPaquetesTrabajo($contenido = '', $key = '', $llave = '', $variable = '') {

        foreach ($variable['child_ids'] as $llave_a => $contenido) {

            $urlActividades = $this->crearUrlActividades($contenido);

            $actividades = file_get_contents($urlActividades);

            $actividad = json_decode($actividades, true);

            foreach ($actividad as $avance) {

                $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
            }

            $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

            foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                $array_ordenado_paquete_trabajo[] = $val;
            }

            $variable = $array_ordenado_paquete_trabajo[$clave];

            if (!empty($variable['child_ids'])) {
                $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);
            }
        }
    }
    public function crearUrlActividades($var = '') {

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=openKyosApi";
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . $_REQUEST['tiempo'];
        $variable .= "&metodo=detalleActividadesPaquetesTrabajo";
        $variable .= "&id_paquete_trabajo=" . $var;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }

    public function obtenerPaquetesTrabajo() {

        foreach ($this->proyectos as $key => $value) {

            $urlPaquetes = $this->crearUrlPaquetesTrabajo($value['id_proyecto']);

            $paquetesTrabajo = file_get_contents($urlPaquetes);

            $paquetesTrabajo = json_decode($paquetesTrabajo, true);

            $this->proyectos[$key]['paquetesTrabajo'] = $paquetesTrabajo;
        }
    }

    public function crearUrlPaquetesTrabajo($var = '') {

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=openKyosApi";
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . $_REQUEST['tiempo'];
        $variable .= "&metodo=paquetesTrabajo";
        $variable .= "&id_proyecto=" . $var;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }

    public function filtrarProyectos() {
        foreach ($this->proyectos as $key => $value) {

            $this->proyectos[$key]['name'] = str_replace('?', ' ', utf8_decode($value['name']));
        }

        $cantidadProyectos = count($this->proyectos);

        for ($i = 1; $i < $cantidadProyectos; $i++) {

            if (isset($_REQUEST['item' . $i])) {

                $ident_proyectos[] = $_REQUEST['item' . $i];
            }
        }

        $this->obtenerDetalleProyectos();

        if (isset($ident_proyectos)) {

            foreach ($this->proyectos as $key => $value) {

                foreach ($ident_proyectos as $valor) {

                    if ($value['id'] == $valor) {

                        $proyectos[] = $value;

                        $llave = array_search($value['custom_fields'][3]['value'], array_column($this->proyectos, 'name'), true);

                        if (!is_bool($llave)) {
                            $proyectos[] = $this->proyectos[$llave];
                        }

                        $llave = array_search('ins', array_column($this->proyectos, 'identifier'), true);

                        if (!is_bool($llave)) {
                            $proyectos[] = $this->proyectos[$llave];
                        }
                    }
                }
            }

            $this->proyectos = $proyectos;
        }
    }

    public function consultarParametrizacion() {
        $cadenaSql = $this->miSql->getCadenaSql('consultarProyectosParametrizados');
        $proyectos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $this->proyectos = $proyectos;

        foreach ($this->proyectos as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarCamposParametrizados', $value['id_proyecto']);
            $campos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

            $this->proyectos[$key]['campos_parametrizados'] = $campos;
        }

        $this->obtenerDetalleProyectos();
    }
}

$miProcesador = new GenerarReporteInstalaciones($this->sql);

?>
