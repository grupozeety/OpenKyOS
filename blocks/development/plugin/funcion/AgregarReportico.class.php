<?php

namespace development\plugin\funcion;

class AgregarReportico {
    
    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
        
    function __construct($lenguaje) {
        
       $this->miConfigurador = \Configurador::singleton ();
        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
        $this->lenguaje = $lenguaje;            
    }
    
    function procesarFormulario() {
        
        $resultado=true;

        $_REQUEST ['grupoBloque'] = $_REQUEST ['moduloPlugin'];
        
        $_REQUEST ['dirOrigen'] = getcwd ().'/plugin/reportico/';
        $_REQUEST ['dirDestino'] = getcwd ().'/blocks/'.$_REQUEST ['moduloPlugin'].'/reportico/';
        
        
        // 1. Verificar la integridad de las variables
        if (!file_exists($_REQUEST['dirOrigen'])){        	
        	$this->miConfigurador->setVariableConfiguracion('mostrarMensaje','errorDirectorio');
        	$resultado = false;
        }else{
        	
        	$_REQUEST['copiar'] = true;
        	
        	$_REQUEST ['nombreBloque']='reportico';
        	$_REQUEST ['descripcionBloque']= 'Bloque generado por plugin, para crear reportes mediante reportico. ';
        	$_REQUEST ['grupoBloque'] = $_REQUEST ['moduloPlugin'];
        	 
        	if($_REQUEST ['moduloPlugin']!=""){
        		$modulo=$_REQUEST ['moduloPlugin'].'\\';
        	}else{
        		$modulo='';
        	}        	
        	 
        	$archivo = getcwd ().'/blocks/'.$_REQUEST ['moduloPlugin'].'/reportico/bloque.php';
        	$cadena = "namespace ".$modulo."reportico;\n"."use ".$modulo."reportico\\funcion\\redireccion;\n";
        	$linea = 1; 
        	$vars_conf[] = array("archivo"=>$archivo, "cadena"=>$cadena, "linea"=>$linea);
        	 
        	$archivo = getcwd ().'/blocks/'.$_REQUEST ['moduloPlugin'].'/reportico/Frontera.class.php';
        	$cadena = "namespace ".$modulo."reportico;\n";
        	$linea = 1; 
        	$vars_conf[] = array("archivo"=>$archivo, "cadena"=>$cadena, "linea"=>$linea);
        	         	
        	$archivo = getcwd ().'/blocks/'.$_REQUEST ['moduloPlugin'].'/reportico/Funcion.class.php';
        	$cadena = "namespace ".$modulo."reportico;\n"."use ".$modulo."reportico\\funcion\\redireccion;\n";
        	$linea = 1; 
        	$vars_conf[] = array("archivo"=>$archivo, "cadena"=>$cadena, "linea"=>$linea);
        	 
        	$archivo = getcwd ().'/blocks/'.$_REQUEST ['moduloPlugin'].'/reportico/Lenguaje.class.php';
        	$cadena = "namespace ".$modulo."reportico;\n";
        	$linea = 1; 
        	$vars_conf[] = array("archivo"=>$archivo, "cadena"=>$cadena, "linea"=>$linea);
        	 
        	$archivo = getcwd ().'/blocks/'.$_REQUEST ['moduloPlugin'].'/reportico/Sql.class.php';
        	$cadena = "namespace ".$modulo."reportico;\n";
        	$linea = 1; 
        	$vars_conf[] = array("archivo"=>$archivo, "cadena"=>$cadena, "linea"=>$linea);
        	 
        	$archivo = getcwd ().'/blocks/'.$_REQUEST ['moduloPlugin'].'/reportico/funcion/redireccionar.php';
        	$cadena = "namespace ".$modulo."reportico\\funcion;\n";
        	$linea = 1; 
        	$vars_conf[] = array("archivo"=>$archivo, "cadena"=>$cadena, "linea"=>$linea);
        	
        	$archivo = getcwd ().'/blocks/'.$_REQUEST ['moduloPlugin'].'/reportico/formulario/consulta.php';
        	$cadena = "require_once('blocks/".$_REQUEST ['moduloPlugin']."/reportico/script/reportico/run.php');\n";
        	$linea = 1; 
        	$vars_conf[] = array("archivo"=>$archivo, "cadena"=>$cadena, "linea"=>$linea);       	
        	        	
        	$archivo = getcwd ().'/blocks/'.$_REQUEST ['moduloPlugin'].'/reportico/script/reportico/reportico.php';
        	$cadena = "set_include_path('blocks/".$_REQUEST ['moduloPlugin']."/reportico/script/reportico');\n";
        	$linea = 38;
        	$vars_conf[] = array("archivo"=>$archivo, "cadena"=>$cadena, "linea"=>$linea);
        	
        	$archivo = getcwd ().'/blocks/'.$_REQUEST ['moduloPlugin'].'/reportico/script/reportico/reportico.php';
			$cadena = "    var \$reportico_url_path = 'blocks/".$_REQUEST ['moduloPlugin']."/reportico/script/reportico';\n";        	
			$linea = 476;
        	$vars_conf[] = array("archivo"=>$archivo, "cadena"=>$cadena, "linea"=>$linea);
        	
        	$archivo = getcwd ().'/blocks/'.$_REQUEST ['moduloPlugin'].'/reportico/script/reportico/reportico.php';
			$cadena = "    var \$url_path_to_reportico_runner = 'blocks/".$_REQUEST ['moduloPlugin']."/reportico/script/reportico/run.php';\n";						
        	$linea = 478;
        	$vars_conf[] = array("archivo"=>$archivo, "cadena"=>$cadena, "linea"=>$linea);
        	
        	$archivo = getcwd ().'/blocks/'.$_REQUEST ['moduloPlugin'].'/reportico/script/reportico/reportico.php';
			$cadena = "    var \$url_path_to_assets = 'blocks/".$_REQUEST ['moduloPlugin']."/reportico/script/reportico';\n";        	
        	$linea = 480;
        	$vars_conf[] = array("archivo"=>$archivo, "cadena"=>$cadena, "linea"=>$linea);
        	
        	$archivo = getcwd ().'/blocks/'.$_REQUEST ['moduloPlugin'].'/reportico/script/reportico/reportico.php';
			$cadena = "    var \$url_path_to_calling_script = 'blocks/".$_REQUEST ['moduloPlugin']."/reportico/script/reportico';\n";        	
        	$linea = 490;        	
        	$vars_conf[] = array("archivo"=>$archivo, "cadena"=>$cadena, "linea"=>$linea);
        	
        	$_REQUEST ['variablesConf'] = $vars_conf;        	
        }   
    
    }
}

$miRegistrador = new AgregarReportico ( $this->lenguaje );

$resultado= $miRegistrador->procesarFormulario ();

