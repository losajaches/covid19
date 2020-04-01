<?php 
date_default_timezone_set('Atlantic/Canary');

$GLOBALS["db.server"]="";
$GLOBALS["db.dbname"]="";
$GLOBALS["db.user"]="";
$GLOBALS["db.pass"]="";

$GLOBALS["msg.error_conect_db"]="Error al conectar a la base de datos";



function ConectarBDparams($server,$user,$pass,$db_name){
	$mySQL = @new mysqli($server,$user,$pass,$db_name);
	if ($mySQL->connect_error) {
    $GLOBAL["last_error_conexion"]=$mySQL->connect_error;
    return false;		
	}else{
		$mySQL->set_charset("utf8");
		return $mySQL;		
	}
}

function ConectarBDMain(){
	return ConectarBDparams($GLOBALS["db.server"],$GLOBALS["db.user"],$GLOBALS["db.pass"],$GLOBALS["db.dbname"]);
}
?>
