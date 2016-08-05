<?php 

$_REQUEST['tiempo']=time();


?>


setTimeout(function() {
    $('#divMensaje').hide( "drop", { direction: "up" }, "slow" );
}, 4000); // <-- time in milliseconds