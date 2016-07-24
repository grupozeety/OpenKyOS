<?php

if (isset ( $_REQUEST ['botonAceptar'] )) {
	
	$this->miConfigurador->fabricaConexiones->crypto->decodificar_url ( $_REQUEST ['campoCadena'] );
	
	echo "<div style='padding:20px;font-family:arial'> <span class='textoElegante textoEnorme textoAzul'>Variables</span><hr>";
	echo '<table  style="table-layout: fixed; width: 100%">';
	foreach ( $_REQUEST as $key => $value ) {
		if ($key != 'botonAceptar') {
			echo '<tr><td style="width:20%"><span class="textoAzul">' . $key . "</span> </td><td>
                    <div style='width: 100%; word-wrap: break-word'><span class='textoGris'>" . $value . "</span></div></td></tr>";
		}
	}
	echo '</table>';
	echo '<br>';
	if (isset ( $_REQUEST ["pagina"] )) {
		$pagina = $_REQUEST ["pagina"];
		
		$conexion = "configuracion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		echo "<span class='textoElegante textoEnorme textoAzul'>P&aacute;gina</span><hr>";
		echo '<table style="width:100%">';
		echo '<tr><td style="width:20%"><span class="textoAzul">Nombre</td><td><span class="textoGris">' . $pagina . "</span></td></tr>";
		$cadenaSql = "SELECT id_pagina,parametro FROM " . $this->miConfigurador->getVariableConfiguracion ( 'prefijo' ) . "pagina WHERE nombre='" . $pagina . "' LIMIT 1";
		$registro = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		if ($registro) {
			
			echo "<tr><td style='width:20%'><span class='textoAzul'>id_pagina</span></td><td><span class='textoGris'>" . $registro [0] [0] . "</span></td></tr>";
			echo "<tr><td style='width:20%'><span class='textoAzul'>parametros</span></td><td><span class='textoGris'>" . $registro [0] [1] . "</span></td></tr>";
			echo '</table><br><br>';
			
			echo "<span class='textoElegante textoEnorme textoAzul'>Bloques que componen esta p&aacute;gina:</span><hr>";
			$prefijo = $this->miConfigurador->getVariableConfiguracion ( 'prefijo' );
			
			$cadenaSql = "SELECT ";
			$cadenaSql .= "" . $prefijo . "bloque_pagina.id_bloque, ";
			$cadenaSql .= "" . $prefijo . "bloque_pagina.seccion, ";
			$cadenaSql .= "" . $prefijo . "bloque_pagina.posicion, ";
			$cadenaSql .= "" . $prefijo . "bloque.nombre ";
			$cadenaSql .= "FROM ";
			$cadenaSql .= "" . $prefijo . "bloque_pagina,";
			$cadenaSql .= "" . $prefijo . "bloque ";
			$cadenaSql .= "WHERE ";
			$cadenaSql .= "" . $prefijo . "bloque_pagina.id_pagina='" . $registro [0] [0] . "' ";
			$cadenaSql .= "AND ";
			$cadenaSql .= "" . $prefijo . "bloque_pagina.id_bloque=" . $prefijo . "bloque.id_bloque";
			
			$registro = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
			if ($registro) {
				?>
<table border="0" align="center" cellpadding="5" cellspacing="1">
	<tr bgcolor="#ECECEC">
		<td align="center">id</td>
		<td align="center">nombre</td>
		<td align="center">secci&oacute;n</td>
		<td align="center">posici&oacute;n</td>
	</tr>	
                <?php
				for($contador = 0; $contador < count ( $registro ); $contador ++) {
					?>
                    <tr bgcolor="#ECECEC">
		<td><? echo $registro[$contador][0] ?></td>
		<td><? echo $registro[$contador][3] ?></td>
		<td><? echo $registro[$contador][1] ?></td>
		<td><? echo $registro[$contador][2] ?></td>
	</tr>	
                    <?php
				}
				?>
                                </table>
</div>

<?php
			}
		}
	}
}
?>