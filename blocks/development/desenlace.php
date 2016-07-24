<?
if (isset ( $_REQUEST ["aceptarA"] )) {
    
    require_once ("../config/configDesenlace.class.php");
    require_once ("../core/connection/dbms.class.php");
    $esta_configuracion = ConfiguracionDesenlace::singleton ();
    
    $configuracion ["prefijo"] = $esta_configuracion->conf ["dbprefijo"];
    
    $gestorDb = new dbms ( $esta_configuracion->conf );
    $recurso = $gestorDb->getRecursoDb ();
    
    $esta_configuracion->cripto->decodificar_url ( $_POST ["pagina"], $configuracion );
    
    echo "<b>Variables</b><br>";
    foreach ( $_REQUEST as $key => $value ) {
        if ($key != "aceptarA") {
            echo $key . "=>" . $value . "<br>";
        }
    }
    echo "<hr>";
    $pagina = $_REQUEST ["pagina"];
    echo "<b>P&aacute;gina</b><br><b>" . $pagina . "</b><br>";
    $cadenaSql = "SELECT id_pagina,parametro FROM " . $configuracion ["prefijo"] . "pagina WHERE nombre='" . $pagina . "' LIMIT 1";
    $registro = $recurso->ejecutarAcceso ( $cadenaSql, "busqueda" );
    if ($registro) {
        echo "id_pagina: " . $registro [0] [0] . "<br>";
        echo "parametros: " . $registro [0] [1] . "<br><hr>";
        echo "Bloques que componen esta p&aacute;gina:<br>";
        $cadenaSql = "SELECT ";
        $cadenaSql .= "" . $configuracion ["prefijo"] . "bloque_pagina.id_bloque, ";
        $cadenaSql .= "" . $configuracion ["prefijo"] . "bloque_pagina.seccion, ";
        $cadenaSql .= "" . $configuracion ["prefijo"] . "bloque_pagina.posicion, ";
        $cadenaSql .= "" . $configuracion ["prefijo"] . "bloque.nombre ";
        $cadenaSql .= "FROM ";
        $cadenaSql .= "" . $configuracion ["prefijo"] . "bloque_pagina,";
        $cadenaSql .= "" . $configuracion ["prefijo"] . "bloque ";
        $cadenaSql .= "WHERE ";
        $cadenaSql .= "" . $configuracion ["prefijo"] . "bloque_pagina.id_pagina='" . $registro [0] [0] . "' ";
        $cadenaSql .= "AND ";
        $cadenaSql .= "" . $configuracion ["prefijo"] . "bloque_pagina.id_bloque=" . $configuracion ["prefijo"] . "bloque.id_bloque";
        
        $registro = $recurso->ejecutarAcceso ( $cadenaSql, "busqueda" );
        if ($registro) {
            ?>
<table border="0" align="center" cellpadding="5" cellspacing="1">
	<tr bgcolor="#ECECEC">
		<td align="center">id</td>
		<td align="center">nombre</td>
		<td align="center">secci&oacute;n</td>
		<td align="center">posici&oacute;n</td>
	</tr>	
                <?
            for($contador = 0; $contador < count ( $registro ); $contador ++) {
                ?>
                    <tr bgcolor="#ECECEC">
		<td><? echo $registro[$contador][0] ?></td>
		<td><? echo $registro[$contador][3] ?></td>
		<td><? echo $registro[$contador][1] ?></td>
		<td><? echo $registro[$contador][2] ?></td>
	</tr>	
                    <?
            }
            ?>
            </table>
<hr>
P&aacute;gina generada autom&aacute;ticamente el: <? echo @date("d/m/Y", time()) ?><br>
Ambiente de desarrollo para aplicaciones web. - Software amparado por
licencia GPL. Copyright (c) 2004-2006.
<br>
Paulo Cesar Coronado - Universidad Distrital Francisco Jos&eacute; de
Caldas.
<br>
<hr>

<?
        }
    } else {
        echo "La p&aacute;gina no se encuentra registrada en el sistema<br>";
        ?>
<hr>
P&aacute;gina generada autom&aacute;ticamente el: <? echo @date("d/m/Y", time()) ?><br>
Ambiente de desarrollo para aplicaciones web. - Software amparado por
licencia GPL. Copyright (c) 2004-2006.
<br>
Paulo Cesar Coronado - Universidad Distrital Francisco Jos&eacute; de
Caldas.
<br>
<hr>
<?
    }
}
?><form method="post" action="desenlace.php" name="desenlazar">
	<table class="bloquelateral" align="center" cellpadding="0"
		cellspacing="0" width="100%">
		<tbody>
			<tr>
				<td align="center" valign="middle"><input size="40" tabindex="1"
					name="pagina"></td>
			</tr>
			<tr>
				<td align="center" valign="middle"><input value="aceptar"
					name="aceptarA" type="submit"></td>
			</tr>
		
		
		<tbody>
	
	</table>
	<form>