<?php
/**
 *
 * Importante: Si se desean los datos del bloque estos se encuentran en el arreglo $esteBloque
 */
//Datos del Bloque actual
$esteBloque=$this->miConfigurador->getVariableConfiguracion("esteBloque");

//URL base
$url=$this->miConfigurador->getVariableConfiguracion("host");
$url.=$this->miConfigurador->getVariableConfiguracion("site");
$url.="/index.php?";

//Variables
$cadenaACodificar="pagina=".$this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar.="&procesarAjax=true";
$cadenaACodificar.="&action=index.php";
$cadenaACodificar.="&bloqueNombre=".$esteBloque["nombre"];
$cadenaACodificar.="&bloqueGrupo=".$esteBloque["grupo"];
$cadenaACodificar.=$cadenaACodificar."&funcion='nombre'";

//Codificar las variables
$enlace=$this->miConfigurador->getVariableConfiguracion("enlace");
$cadena=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar,$enlace);

//URL definitiva
$urlFinal=$url.$cadena;

?>
 $('#example').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "<?php echo $urlFinal  ?>"
    } );
    