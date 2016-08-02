<?php

namespace complementos\registrarPortatil\funcion;


class ElementoXml {
	var $name;
	var $attributes;
	var $content;
	var $children;
};


class ParserXML {
    
    var $miConfigurador;
    var $parser;
    var $xml;
    var $elementos;
    var $valores;
    
    
    function __construct($lenguaje, $sql) {
        
        $this->miConfigurador = \Configurador::singleton ();
        
    }
    
    function procesarArchivo() {
    	$this->leerArchivo();
		$this->parser = xml_parser_create();
    	xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
    	xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
    	xml_parse_into_struct($this->parser, $this->xml, $etiquetas);
    	xml_parser_free($this->parser);
    	
    	var_dump($etiquetas);exit;
    	
    }
    
    function leerArchivo(){
    	$myfile = fopen('a.xml', 'r') or die('Unable to open file!');
    	$this->xml= fread($myfile,filesize('a.xml'));
    	
    	var_dump($this->xml);
    	fclose($myfile);
    	
    }
    
    
}

$myParser = new ParserXML( $this->lenguaje, $this->sql );

$resultado= $myParser->procesarArchivo ();

