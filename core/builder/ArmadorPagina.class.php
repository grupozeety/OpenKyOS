<?php

require_once ("core/manager/Configurador.class.php");
require_once ("core/builder/builderSql.class.php");

class ArmadorPagina {

    var $miConfigurador;
    var $generadorClausulas;
    var $host;
    var $sitio;
    var $raizDocumentos;
    var $bloques;
    var $seccionesDeclaradas;

    const SECCION = 'seccion';
    const GRUPO = 'grupo';
    const BLOQUEGRUPO = 'bloqueGrupo';
    const NOMBRE = 'nombre';
    const ARCHIVOBLOQUE = '/bloque.php';
    const CARPETABLOQUES = '/blocks/';

    function __construct() {

        $this->miConfigurador = Configurador::singleton();
        $this->generadorClausulas = BuilderSql::singleton();
        $this->host = $this->miConfigurador->getVariableConfiguracion("host");
        $this->sitio = $this->miConfigurador->getVariableConfiguracion("site");
        $this->raizDocumentos = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
    }

    function armarHTML($registroBloques) {

        $this->bloques = $registroBloques;

        if ($this->miConfigurador->getVariableConfiguracion("cache")) {

            // De forma predeterminada las paginas del aplicativo no tienen cache
            header("Cache-Control: cache");
        } else {
            if (!(isset($_REQUEST ['opcion']) && $_REQUEST ['opcion'] == 'mostrarMensaje')) {
                header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");
            }
        }

        $this->raizDocumento = $this->miConfigurador->getVariableConfiguracion("raizDocumento");

        echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> ';
        echo "\n<html lang='es'>\n";
        $this->encabezadoPagina();
        $this->cuerpoPagina();
        echo "</html>\n";
    }

    private function encabezadoPagina() {

        $htmlPagina = "<head>\n";
        $htmlPagina .= "<title>" . $this->miConfigurador->getVariableConfiguracion("nombreAplicativo") . "</title>\n";
        $htmlPagina .= "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' >\n";
        $htmlPagina .= "<link rel='shortcut icon' href='" . $this->host . $this->sitio . "/" . "favicon.ico' >\n";
        echo $htmlPagina;

        // Incluir estilos
        include_once ("theme/basico/Estilo.php");

        // Enlazar los estilos definidos en cada bloque
        foreach ($this->bloques as $unBloque) {
            $this->incluirEstilosBloque($unBloque);
        }
        echo "</head>\n";
    }

    private function cuerpoPagina() {

        $this->seccionesDeclaradas = array(
            0,
            0,
            0,
            0,
            0
        );

        foreach ($this->bloques as $unBloque) {

            $posicion = ord($unBloque [self::SECCION]) - 65;
            $this->seccionesDeclaradas [$posicion] = $unBloque [self::SECCION];
        }

        echo "<body>\n";
        echo "<div id='marcoGeneral'>\n";

        if (in_array("A", $this->seccionesDeclaradas, true)) {
            $this->armarSeccionAmplia("A");
        }

        if (in_array("B", $this->seccionesDeclaradas, true)) {
            $this->armarSeccionLateral("B");
        }

        if (in_array("C", $this->seccionesDeclaradas, true)) {
            $this->armarSeccionCentral();
        }
        if (in_array("D", $this->seccionesDeclaradas, true)) {
            $this->armarSeccionLateral("D");
        }
        if (in_array("E", $this->seccionesDeclaradas, true)) {
            $this->armarSeccionAmplia("E");
        }

        echo "</div>\n";
        $this->piePagina();
        echo "</body>\n";
    }

    private function piePagina() {
        // Funciones javascript globales del aplicativo
        include_once ("plugin/scripts/Script.php");

        // Insertar las funciones js definidas en cada bloque
        foreach ($this->bloques as $unBloque) {
            $this->incluirFuncionesBloque($unBloque);
        }

        // Para las páginas que requieren jquery
        if (isset($_REQUEST ["jquery"])) {

            $this->incluirFuncionReady($unBloque);
        }
    }

    private function armarSeccionAmplia($seccion) {
        // Este tipo de secciones ocupan el ancho de la página
        echo "<div class='seccionAmplia'>\n";
        foreach ($this->bloques as $unBloque) {
            if ($unBloque [self::SECCION] == $seccion) {
                $this->incluirBloque($unBloque);
            }
        }
        echo "</div>\n";
    }

    private function armarSeccionLateral($seccion) {

        if ($seccion == 'B') {
            $otraSeccion = 'D';
        } else {
            $otraSeccion = 'B';
        }

        // Este tipo de secciones ocupan un ancho variable dependiendo si las otras secciones están declaradas
        // Si ninguna de las otras secciones están declaradas entonces ocupa el ancho de la página
        if (!in_array('C', $this->seccionesDeclaradas) && !in_array($otraSeccion, $this->seccionesDeclaradas)) {

            echo "<div class='seccionAmplia'>\n";
        } else {
            // Si la otra sección está declarada pero la sección central no, entonces ocupa la mitad de la página

            if (!in_array('C', $this->seccionesDeclaradas) && in_array($otraSeccion, $this->seccionesDeclaradas)) {
                echo "<div class='seccionMitad'>\n";
            } else {
                echo "<div class='seccion" . $seccion . "'>\n";
            }
        }

        foreach ($this->bloques as $unBloque) {
            if ($unBloque [self::SECCION] == $seccion) {
                $this->incluirBloque($unBloque);
            }
        }

        echo "</div>\n";
    }

    private function armarSeccionCentral() {

        // Si las secciones laterales no están definidas entonces la sección central ocupa el ancho de la página
        if (!in_array("B", $this->seccionesDeclaradas, true) && !in_array("D", $this->seccionesDeclaradas, true)) {

            echo "<div class='seccionAmplia'>\n";
        } else {

            if ((in_array("B", $this->seccionesDeclaradas, true) && !in_array("D", $this->seccionesDeclaradas, true)) || (!in_array("B", $this->seccionesDeclaradas, true) && in_array("D", $this->seccionesDeclaradas, true))) {
                echo "<div class='seccionCentralAmpliada'>\n";
            } else {
                echo "<div class='seccionCentral'>\n";
            }
        }

        foreach ($this->bloques as $unBloque) {
            if ($unBloque [self::SECCION] == "C") {
                $this->incluirBloque($unBloque);
            }
        }

        echo "</div>\n";
    }

    private function incluirBloque($unBloque) {

        foreach ($unBloque as $clave => $valor) {
            $unBloque [$clave] = trim($valor);
        }

        if (!isset($_REQUEST ['actionBloque']) || (isset($_REQUEST ['actionBloque']) && $unBloque [self::NOMBRE] != $_REQUEST ['actionBloque'] )) {
            if ($unBloque [self::GRUPO] == '') {
                $archivo = $this->raizDocumentos . self::CARPETABLOQUES . $unBloque [self::NOMBRE] . self::ARCHIVOBLOQUE;
            } else {
                $archivo = $this->raizDocumentos . self::CARPETABLOQUES . $unBloque [self::GRUPO] . "/" . $unBloque [self::NOMBRE] . self::ARCHIVOBLOQUE;
            }
            include ($archivo);

            return true;
        } else {

            $carpeta = '';
            if (isset($_REQUEST [self::BLOQUEGRUPO]) && $_REQUEST [self::BLOQUEGRUPO] != "") {
                $carpeta = $_REQUEST [self::BLOQUEGRUPO] . '/';
                $unBloque ['grupo'] = $carpeta;
            }
            if (isset($_REQUEST ["bloque"])) {
                $unBloque [self::NOMBRE] = $_REQUEST ['actionBloque'];
                $_REQUEST ['action'] = $_REQUEST ['actionBloque'];
                $unBloque ["id_bloque"] = $_REQUEST ["bloque"];
                include_once ($this->raizDocumentos . self::CARPETABLOQUES . $carpeta . $unBloque [self::NOMBRE] . self::ARCHIVOBLOQUE);
                unset($_REQUEST ['action']);
            } elseif (isset($_REQUEST ["procesarAjax"])) {

                include_once ($this->raizDocumentos . self::CARPETABLOQUES . $carpeta . $_REQUEST ["bloqueNombre"] . self::ARCHIVOBLOQUE);
            }
        }
    }

    private function incluirEstilosBloque($unBloque) {

        foreach ($unBloque as $clave => $valor) {
            $unBloque [$clave] = trim($valor);
        }

        if ($unBloque [self::GRUPO] == "") {
            $archivo = $this->raizDocumentos . self::CARPETABLOQUES . $unBloque [self::NOMBRE] . "/css/Estilo.php";
        } else {
            $archivo = $this->raizDocumentos . self::CARPETABLOQUES . $unBloque [self::GRUPO] . "/" . $unBloque [self::NOMBRE] . "/css/Estilo.php";
        }

        if (file_exists($archivo)) {
            include_once ($archivo);
        }
    }

    private function incluirFuncionesBloque($esteBloque) {

        foreach ($esteBloque as $clave => $valor) {
            $esteBloque [$clave] = trim($valor);
        }

        if ($esteBloque [self::GRUPO] == "") {
            $archivo = $this->raizDocumentos . self::CARPETABLOQUES . $esteBloque [self::NOMBRE] . "/script/Script.php";
        } else {
            $archivo = $this->raizDocumentos . self::CARPETABLOQUES . $esteBloque [self::GRUPO] . "/" . $esteBloque [self::NOMBRE] . "/script/Script.php";
        }

        if (file_exists($archivo)) {

            include_once ($archivo);
        }
    }

    function incluirFuncionReady($unBloque) {

        /**
         * Esta función registra funciones las opciones de la función ready (jquery) para la página
         * Tales funciones están declaradas en cada bloque y pueden venir directamente en un archivo
         * llamado ready.js o en un archivo ready.php.
         *
         *
         * El archivo ready.php se utiliza cuando se tenga que crear de manera dinámica el js.
         */
        echo "<script type='text/javascript'>\n";
        echo "$(document).ready(function(){\n";

        foreach ($this->bloques as $unBloque) {

            foreach ($unBloque as $clave => $valor) {
                $unBloque [$clave] = trim($valor);
            }

            if ($unBloque [self::GRUPO] == "") {
                $archivo = $this->raizDocumentos . self::CARPETABLOQUES . $unBloque [self::NOMBRE] . "/script/ready.js";
                $archivoPHP = $this->raizDocumentos . self::CARPETABLOQUES . $unBloque [self::NOMBRE] . "/script/ready.php";
            } else {
                $archivo = $this->raizDocumentos . self::CARPETABLOQUES . $unBloque [self::GRUPO] . "/" . $unBloque [self::NOMBRE] . "/script/ready.js";
                $archivoPHP = $this->raizDocumentos . self::CARPETABLOQUES . $unBloque [self::GRUPO] . "/" . $unBloque [self::NOMBRE] . "/script/ready.php";
            }

            if (file_exists($archivo)) {
                include ($archivo);
                echo "\n";
            }

            if (file_exists($archivoPHP)) {
                include ($archivoPHP);
                echo "\n";
            }
        }
        echo "});\n";
        echo "</script>\n";
    }

    private function armar_no_pagina($seccion, $cadena) {

        $this->la_cadena = $cadena . ' AND ' . $this->miConfigurador->configuracion ["prefijo"] . 'bloque_pagina.seccion="' . $seccion . '" ORDER BY ' . $this->miConfigurador->configuracion ["prefijo"] . 'bloque_pagina.posicion ASC';
        $this->base->registro_db($this->la_cadena, 0);
        $this->armar_registro = $this->base->getRegistroDb();
        $this->total = $this->base->obtener_conteo_db();
        if ($this->total > 0) {

            for ($this->contador = 0; $this->contador < $this->total; $this->contador ++) {

                $this->id_bloque = $this->armar_registro [$this->contador] [0];
                $this->incluir = $this->armar_registro [$this->contador] [4];
                include ($this->miConfigurador->configuracion ["raiz_documento"] . $this->miConfigurador->configuracion ["bloques"] . "/" . $this->incluir . self::ARCHIVOBLOQUE);
            }
        }
        return TRUE;
    }

    private function campoSeguro($campo = '') {

        if (isset($_REQUEST['tiempo'])) {
            return $this->miConfigurador->fabricaConexiones->crypto->codificar($campo . $_REQUEST ['tiempo']);
        }
        return false;
    }

}
