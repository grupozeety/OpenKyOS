<?php

namespace development\plugin\funcion;

class CopiarArchivos {
    
    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;    
    
    function __construct($lenguaje) {
        
        $this->miConfigurador = \Configurador::singleton ();
        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
        $this->lenguaje = $lenguaje;    
    }
    
    function procesarFormulario() {    	
    	$_REQUEST['copiar'] = true;
    	if (! isset ( $_REQUEST['copiar']))
    	{
    		$resultado = false;
    	}else{
    		$this->buscarDirectorios($_REQUEST['dirOrigen'], $_REQUEST['dirDestino']);
    	}               
    }
    
    //Esta es una función es recursiva que permite generar un arreglo que contenga todos los directorios y subdirectorios de una ruta dada.
    
	function buscarDirectorios($dirOrigen, $dirDestino )
	{
		$directorio = array();
		$directorioDestino = array();
		$directorioFinal = array();
		$directorioOrigen = array();
	
		$directorio[] = $dirOrigen;
		$directorioDestino[] = $dirDestino;
		$contador = 0;
		do{	
			$contador2 = 0;
			for($i=$contador; $i<count($directorio); $i++){		
				if ($vcarga = opendir($directorio[$i]))
				{
					while($file = readdir($vcarga)) //lo recorro enterito
					{
						if ($file != "." && $file != "..") //quito el raiz y el padre
							{				
								if (is_dir($directorio[$i].$file)) //pregunto si no es directorio
								{				
									$directorio [] = $directorio[$i].$file.'/';
									$directorioDestino [] = $directorioDestino[$i].$file.'/';
									$contador++;
									$contador2++;		
										
								}									
							}
					}
					closedir($vcarga);
				}		
		
			}

			$directorioOrigen = array_merge($directorioOrigen, $directorio);
			$directorioFinal = array_merge($directorioFinal, $directorioDestino);	
		}while($contador2>0);

		$directorioOrigen =array_unique($directorioOrigen);
		$directorioFinal =array_unique($directorioFinal);
	
		for($i=0; $i<count($directorioOrigen); $i++){
			$this->copia($directorioOrigen[$i], $directorioFinal[$i]);			
		}		
	}


	function copia($dirOrigen, $dirDestino)
	{
		//Se crea el directorio destino

		mkdir($dirDestino, 0755, true);
		//Se Abre el directorio origen

		if ($vcarga = opendir($dirOrigen))
		{
			$contador = 0;
			while($file = readdir($vcarga)) //Se recorre el directorio por completo.
			{
				if ($file != "." && $file != "..") //Se quita la raíz y el padre
				{
					//echo "<b>$file</b>"; //muestro el nombre del archivo
					if (!is_dir($dirOrigen.$file)) //pregunto si no es directorio
					{
						if(copy($dirOrigen.$file, $dirDestino.$file)) //como no es directorio, copio de origen a destino
						{
							//echo "COPIADO!";
							//$_REQUEST['errorCopiar'] = true;
						}else{
							//echo "ERROR!";
							$this->miConfigurador->setVariableConfiguracion('mostrarMensaje','errorCopia');
							$_REQUEST['errorCopiar'] = true;
						}
					}
					//echo "<br />";				
				}
			}
			closedir($vcarga);
		}
	}
    
    function resetForm(){
        foreach($_REQUEST as $clave=>$valor){
             
            if($clave !='pagina' && $clave!='development' && $clave !='jquery' &&$clave !='tiempo'){
                unset($_REQUEST[$clave]);
            }
        }
    }    
}

$miRegistrador = new CopiarArchivos ( $this->lenguaje );

$resultado= $miRegistrador->procesarFormulario ();

