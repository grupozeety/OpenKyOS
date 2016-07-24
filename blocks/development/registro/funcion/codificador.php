<?php
/**
 * 1. Toma el texto del área e ingresa cada línea en una posición del arreglo
 */

$lineas=preg_split('/\r\n|[\r\n]/', $_REQUEST['campoCadena']);

echo '<div style="padding:20px; font-family:arial">';
echo '<span class="textoEnorme textoAzul">Cadenas de Texto Procesadas</span><hr>';
echo '<table>';
if ( $_REQUEST ['botonCodificar'] =='true') {
    
    
    foreach ($lineas as $clave=>$valor){
        echo '<tr><td>'.$this->miConfigurador->fabricaConexiones->crypto->codificar ($valor).'</td></tr>';
    }
    

}else{

    foreach ($lineas as $clave=>$valor){
        echo '<tr><td>'.$this->miConfigurador->fabricaConexiones->crypto->decodificar ($valor).'</td></tr>';
    }

}
echo '</table></div>';


?>