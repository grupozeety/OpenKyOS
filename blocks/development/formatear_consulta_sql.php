<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<form name="cadenaSql" action="formatear_consulta_sql.php" method="POST">
    
    <textarea name="sql" rows="12" cols="100">
    </textarea>
    <br>
    <input type="submit" value="Enviar" name="enviar" />
    <input type="reset" value="Borrar" name="Borrar" />
    
</form >
<?

if( !$_REQUEST['sql']){echo 'Inserte cadena';exit;};

$cadena= nl2br($_REQUEST['sql']);
                 
            
  $linea=explode('<br />',$cadena);  
  
  foreach ($linea as $key => $value) {
      echo '$cadenaSql';
          if($key!=0)
              {echo '.';}
              echo '=" '.$value.'";<br>';
  }



?>
