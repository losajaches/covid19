<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

date_default_timezone_set("Atlantic/Canary");

ini_set('memory_limit','2048M');
set_time_limit(600);

include("bin/config.inc.php");

$GLOBALS["max_id_pais"]=0;
$GLOBALS["paises"]=array();



function GetNombres($mySQL){
	//Consultamos códigos de paises***************************************************
	$c=$mySQL->query("select pais,estado,isla,id from nombres");
	while($r=$c->fetch_assoc()){
		$key=sprintf("%s·%s·%s",$r["pais"],$r["estado"],$r["isla"]);
		$GLOBALS["paises"][$key]=$r["id"];
		$GLOBALS["max_id_pais"]=max($GLOBALS["max_id_pais"],$r["id"]);
	}
}

function GetCCAA_ISOS(){
	$isos="ME-Melilla,CE-Ceuta,AN-Andalucía,AR-Aragón,AS-Asturias,CN-Canarias,CB-Cantabria,CM-Castilla La Mancha,CL-Castilla y León,CT-Cataluña,EX-Extremadura,GA-Galicia,IB-Islas Baleares,RI-La Rioja,MD-Madrid,MC-Murcia,NC-Navarra,PV-País Vasco,VC-Comunidad Valenciana";
	$iso_ccaa=array();
	foreach(explode(",",$isos) as $v){
		list($iso,$nombre)=explode("-",$v);
		$iso_ccaa[$iso]=$nombre;
	}
	return $iso_ccaa;
}

function GetCodeNombre($mySQL,$pais,$estado,$isla,$lat,$lon){
	$key=sprintf("%s·%s·%s",$pais,$estado,$isla);
	if(isset($GLOBALS["paises"][$key])){
		return $GLOBALS["paises"][$key];	
	}else{
		$GLOBALS["max_id_pais"]++;
		$sql=sprintf("insert into nombres(pais,estado,isla,lat,lon,id) values('%s','%s','%s',%s,%s,%s)",
			$mySQL->real_escape_string($pais),
			$mySQL->real_escape_string($estado),
			$mySQL->real_escape_string($isla),
			($lat=='')?'NULL':$lat,
			($lon=='')?'NULL':$lon,
			$GLOBALS["max_id_pais"]
		);
		if($mySQL->query($sql)){
			$GLOBALS["paises"][$key]=$GLOBALS["max_id_pais"];
			return $GLOBALS["max_id_pais"];
		}else{
			echo $mySQL->error."<br>";
			echo $sql."<br>";
			
			$GLOBALS["max_id_pais"]--;
			return false;
		}
		
	}
}



function ProcesarFicheroWorld($mySQL,$fieldDataName,$file_name,$fileContent=""){
	$sql_sentencias=0;
	//los datos **********************************************************************
	$csv = array_map(
		'str_getcsv',
		($file_name!="") ? file($file_name) : $fileContent
	);
	//obtenemos las claves del fichero del registro cero
	$keys= array_map(function($value){
			if(substr_count($value,"/")==2){
				list($m,$d,$y)=explode("/",$value);
				return sprintf("20%d-%02d-%02d",$y,$m,$d);
			}else{
				return $value;
			}
		},
		$csv[0]
	);
	
	
	//recorremos el fichero asignando los calores a sus claves (fieldnames)
	$mySQL->begin_transaction();
	
	for($i=1;$i<count($csv);$i++){
		$r=array_combine($keys,$csv[$i]);
		$id=GetCodeNombre($mySQL,trim($r["Country/Region"]),trim($r["Province/State"]),"",trim($r["Lat"]),trim($r["Long"]));
		if($id===false){
			echo "Error al procesar los nombres de los paises<br>";
			print_r($r);
			return false;
		}
		unset($r["Province/State"]);
		unset($r["Country/Region"]);
		unset($r["Lat"]);
		unset($r["Long"]);
		
		$last_valor=0;
		foreach($r as $fecha=>$valor){
			$acumulado=$valor;
			$dato_diario=$valor-$last_valor;
			$last_valor=$valor;
			
			if(($acumulado!=0)||($dato_diario!=0)){
				$sql="insert into datos(id,fecha,$fieldDataName) values($id,'$fecha',$dato_diario) on duplicate key update $fieldDataName=$dato_diario";
				if(!$mySQL->query($sql)){
					echo $mySQL->error."<br>";
					echo $sql."<hr>";	
				}else{
					$sql_sentencias++;
				}
			}
		}
	}
	$mySQL->commit();
	
	return $sql_sentencias;
}

function CheckData($data){
	return  preg_replace("/[^0-9]/", "",$data);
}
function ProcesarFicheroSpain($mySQL,$file_name,$fileContent=""){
	$ISOS=GetCCAA_ISOS();

	$sql_sentencias=0;
	//los datos **********************************************************************
	$csv = array_map(
		'str_getcsv',
		($file_name!="") ? file($file_name) : $fileContent
	);
	//obtenemos las claves del fichero del registro cero
	$keys= array_map(function($value){
			return trim($value);
		},
		$csv[0]
	);
	$matrix=array();
	for($i=1;$i<count($csv);$i++){
		if(count($csv[$i])==count($keys)){
			$r=array_combine($keys,$csv[$i]);
			if(trim($r["Fecha"])!=""){
				list($d,$m,$y)=explode("/",$r["Fecha"]);
				$matrix[$r["CCAA Codigo ISO"]]["$y-$m-$d"]=array(
					"acum_contagiados"=>(trim($r["Casos"])!="")?CheckData($r["Casos"]):0,
					"acum_fallecidos"=>(trim($r["Fallecidos"])!="")?CheckData($r["Fallecidos"]):0,
					"acum_curados"=>(trim($r["Recuperados"])!="")?CheckData($r["Recuperados"]):0,
					"acum_hospitalizados"=>(trim($r["Hospitalizados"])!="")?CheckData($r["Hospitalizados"]):0,
					"acum_uci"=>(trim($r["UCI"])!="")?CheckData($r["UCI"]):0	
				);
			}
		}
	}
	
	//recorremos el fichero asignando los calores a sus claves (fieldnames)
	$mySQL->begin_transaction();
	
	foreach($matrix as $iso=>$data){
		$id=GetCodeNombre($mySQL,"España MS",isset($ISOS[$iso])?$ISOS[$iso]:$iso,"","","");
		
		if($id===false){
			echo "Error al procesar los nombres de los paises<br>";
			print_r($r);
			return false;
		}
		$last_acum_contagiados=0;
		$last_acum_fallecidos=0;
		$last_acum_curados=0;
		$last_acum_hospitalizados=0;
		$last_acum_uci=0;
			
		foreach($data as $fecha=>$r){
			$acum_contagiados=$r["acum_contagiados"];
			$acum_fallecidos=$r["acum_fallecidos"];
			$acum_curados=$r["acum_curados"];
			$acum_hospitalizados=$r["acum_hospitalizados"];
			$acum_uci=$r["acum_uci"];
			
			$contagiados=$acum_contagiados-$last_acum_contagiados;
			$fallecidos=$acum_fallecidos-$last_acum_fallecidos;
			$curados=$acum_curados-$last_acum_curados;
			$hospitalizados=$acum_hospitalizados-$last_acum_hospitalizados;
			$uci=$acum_uci-$last_acum_uci;
			
			$last_acum_contagiados=$r["acum_contagiados"];
			$last_acum_fallecidos=$r["acum_fallecidos"];
			$last_acum_curados=$r["acum_curados"];
			$last_acum_hospitalizados=$r["acum_hospitalizados"];
			$last_acum_uci=$r["acum_uci"];
		
			
			$sql=sprintf("insert into datos(id,fecha,contagiados,fallecidos,curados,hospitalizados,uci) 
			              values(%s,'%s',%s,%s,%s,%s,%s) on duplicate key 
			              update contagiados=%s,fallecidos=%s,curados=%s,hospitalizados=%s,uci=%s",
				$id,
				$fecha,
				
				$contagiados,
				$fallecidos,
				$curados,
				$hospitalizados,
				$uci,
				
				$contagiados,
				$fallecidos,
				$curados,
				$hospitalizados,
				$uci
			);
			if(!$mySQL->query($sql)){
				echo $mySQL->error."<br>";
				echo $sql."<hr>";	
			}else{
				$sql_sentencias++;
			}
		}
	}
	$mySQL->commit();
	
	return $sql_sentencias;
}



echo "<pre>";

$mySQL=ConectarBDMain();
GetNombres($mySQL);

$sql_sentencias=ProcesarFicheroWorld($mySQL,"fallecidos","https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_covid19_deaths_global.csv");
echo "Datos mundiales: $sql_sentencias lineas procesadas de fallecidos<br>";

$sql_sentencias=ProcesarFicheroWorld($mySQL,"contagiados","https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_covid19_confirmed_global.csv");
echo "Datos mundiales: $sql_sentencias lineas procesadas de contagiados<br>";

$sql_sentencias=ProcesarFicheroWorld($mySQL,"curados","https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_covid19_recovered_global.csv");
echo "Datos mundiales: $sql_sentencias lineas procesadas de curados<br>";

$sql_sentencias=ProcesarFicheroSpain($mySQL,"https://covid19.isciii.es/resources/serie_historica_acumulados.csv");
echo "Datos España: $sql_sentencias lineas procesadas<br>";




?>
