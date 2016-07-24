<?php
require_once ('Cruder.class.php');

class Disparador {
    
    var $miCruder;
    
    
function iniciarRegistro() {
        
        $pagina = "<html>";
        $pagina .= "<head>";
        $pagina .= "<meta charset='utf-8'>";
        $pagina .= "<script type='text/javascript' src='jquery.js'></script>";
        $pagina .= "<script type='text/javascript' src='jquery-ui.js'></script>";
        $pagina .= "<script type='text/javascript' src='ready.js'></script>";
        $pagina .= "<script type='text/javascript' src='select2.js'></script>";
        $pagina .= "<script type='text/javascript' src='select2_locale_es.js'></script>";
        $pagina .= "<link rel='stylesheet' href='jquery-ui-themes/themes/smoothness/jquery-ui.css' />";
        $pagina .= "<link rel='stylesheet' href='estilo.css' />";
        $pagina .= "<link rel='stylesheet' href='select2.css' />";
        $pagina .= "<title>";
        $pagina .= "Registro de elementos";
        $pagina .= "</title>";
        $pagina .= "</head>";
        $pagina .= "<body>";
        $pagina .= "<div id='marcoPrincipal'>";
        $pagina .= $this->miCruder->formSeleccionarAccion ();
        $pagina .= $this->miRegistrador->formRegistrarBloque ();
        $pagina .= $this->miRegistrador->formRegistrarPagina ();
        $pagina .= $this->miRegistrador->formAsociarBloque ();
        $pagina .= "</div>";
        $pagina .= "</body>";
        $pagina .= "</html>";
        
        echo $pagina;
    
    }
    
    function procesarFormulario() {
        
        $this->miRegistrador->procesarFormulario ( $_REQUEST ["action"] );
    
    }
    
    function __construct() {
        
        $this->miCruder = new Cruder();
        if (isset ( $_REQUEST ["action"] )) {
            $this->procesarFormulario ();
        }
        
        $this->iniciarRegistro ();
    
    }

}

$miIniciador = new Disparador ();