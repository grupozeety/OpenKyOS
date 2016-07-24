<?php

namespace development\plugin\funcion;

class InstalarExtension {
    
    var $miConfigurador;
    var $lenguaje;
    private $directorio_sara;
    private $directorio_extensions;
    private $directorio_install_extension;
    
    function __construct($lenguaje) {
        
       	$this->miConfigurador = \Configurador::singleton ();
        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
        $this->lenguaje = $lenguaje;
        $this->directorio_sara = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
        $this->directorio_extensions = $this->directorio_sara . '/extensions';
    }
    
    public function instalarPorUrl($urlExtension) {
    	$nombreArchivo = basename($urlExtension);
    	echo 'Descargar paquete manualmente: <a href="'.$urlExtension.'">'.$nombreArchivo.'</a><br />';
    	if (is_dir($this->directorio_extensions)) {
    		echo '<p>(Warning) Directorio de Extensiones ya existe.</p>';
    	} else {
    		mkdir($this->directorio_extensions, 0755);
    	}
    	$direccionFichero = $this->directorio_extensions.'/'.$nombreArchivo;
    	$stream = $this->downloadFile($urlExtension);
    	$this->saveFile($stream,$direccionFichero);
    	$this->extractZip($direccionFichero,$this->directorio_extensions);
    	$this->directorio_install_extension = $this->directorio_extensions.'/'.pathinfo($urlExtension, PATHINFO_FILENAME);
    	$this->ejecutarInstruccionesExtension();
    }
    
    private function ejecutarInstruccionesExtension(){
    	$i = '-1';
    	$argv[$i++] = 'directorio_sara='.$this->directorio_sara;
    	$argv[$i++] = 'directorio_extensions='.$this->directorio_extensions;
    	$argv[$i++] = 'directorio_extension='.$this->directorio_install_extension;
    	require $this->directorio_install_extension.'/Extension.php';
    }
    
    private function downloadFile($url){
    	$conf['proxy_enable'] = true;
    	$conf['proxy_context_params'] = array(
    			'http' => array(
    					'proxy' => 'tcp://10.20.4.15:3128',
    					'request_fulluri' => true,
    			),
    	);
    	$conf['proxy_context'] = stream_context_create($conf['proxy_context_params']);
    	try{
    		if(isset($conf['proxy_enable'])&&$conf['proxy_enable']==true){
    			$content = file_get_contents($url, false, $conf['proxy_context']);
    		} else {
    			$content = file_get_contents($url, false);
    		}
    	} catch(Exception $e){
    		die(
    				'(Warning) No se detecta una conexión a internet desde el servidor.' .
    				EOL .
    				'Caught exception: '.  $e->getMessage() . EOL
    		);
    	}
    	return $content;
    }
    
    private function saveFile($strem,$dest){    	
    	if (file_exists($dest)){
    		unlink($dest);
    	}
    	file_put_contents($dest, $strem);
    }
    
    private function extractZip ($src, $dest){
    	$zip = new \ZipArchive;
    	if ($zip->open($src)===true)
    	{
    		$zip->extractTo($dest);
    		$zip->close();
    		return true;
    	}
    	return false;
    }
}

$miInstalador = new InstalarExtension ( $this->lenguaje );
