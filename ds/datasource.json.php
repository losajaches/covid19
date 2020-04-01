<?php
header('Content-Type: application/json');
include("../bin/config.inc.php");



function LoadData($params){	
	$response=new stdClass();
	try{
		if($mySQL=ConectarBDMain()){
			$meses=array("","En","Fb","Mr","Ab","My","Jn","Jl","Ag","Sp","Oc","Nv","Dc");
			
			$field_order=$params["field_order"];
			$zona=       $params["zona"];
			$factor=     $params["factor_estimacion"];
			
			$response->data=array();
			$response->zona=$zona;
			
			if($zona==""){
				$sql_total_global="select sum(contagiados) as c,sum(fallecidos)as f,sum(curados) as s from paises_total where pais<>'España MS'";
				$sql_total_global_detalle="select fecha, sum(contagiados) as c,sum(fallecidos)as f,sum(curados) as s from paises_detalle where pais<>'España MS' group by fecha";
				
				$sql_num_registros="select count(*) from paises_total";
				$sql_paises="select pais as p,contagiados as c,fallecidos as f,curados as s from paises_total";
				$sql_detalle="select fecha,contagiados as c,fallecidos as f,curados as s from paises_detalle where pais='%s'";
			}else{
				$sql_total_global="select sum(contagiados) as c,sum(fallecidos)as f,sum(curados) as s from estados_total where pais='$zona'";
				$sql_total_global_detalle="select fecha, sum(contagiados) as c,sum(fallecidos)as f,sum(curados) as s from estados_detalle where pais='$zona' group by fecha";
				
				$sql_num_registros="select count(*) from estados_total where pais='$zona'";
				$sql_paises="select estado as p,contagiados as c,fallecidos as f,curados as s from estados_total where pais='$zona'";
				$sql_detalle="select fecha,contagiados as c,fallecidos as f,curados as s from estados_detalle where pais='$zona' and estado='%s'";
			}
			
			
			//datos globales
			if($params["pagina"]==1){
				$response->globales=$mySQL->query($sql_total_global)->fetch_assoc();
				
				$response->globales["c"]=(int)$response->globales["c"];
				$response->globales["f"]=(int)$response->globales["f"];
				$response->globales["s"]=(int)$response->globales["s"];
				$response->globales["e"]=round(100*$response->globales["f"]/$factor);
				
				$e=$response->globales["c"];
					$response->globales["fc_c"]=0;
					$response->globales["fc_f"]=($e==0) ? 0 : round(100 * $response->globales["f"] / $e,1);
					$response->globales["fc_s"]=($e==0) ? 0 : round(100 * $response->globales["s"] / $e,1);
					$response->globales["fc_e"]=0;
				
				$e=$response->globales["e"];
					$response->globales["fe_c"]=0;
					$response->globales["fe_f"]=($e==0) ? 0 : round(100 * $response->globales["f"] / $e,1);
					$response->globales["fe_s"]=($e==0) ? 0 : round(100 * $response->globales["s"] / $e,1);
					$response->globales["fe_e"]=0;
				
				$response->globales["d"]=array();
				$c=$mySQL->query($sql_total_global_detalle);
				while($r=$c->fetch_assoc()){
					$response->globales["d"][$r["fecha"]]=array(
						"c"=>(int)$r["c"],
						"e"=>round(100*$r["f"]/$factor),
						"f"=>(int)$r["f"],
						"s"=>(int)$r["s"]
					);
				}
				
				
			}
			
			//El intervalo de fechas y la lista de estas
			$response->min_fecha=$mySQL->query("select min(fecha) from datos")->fetch_row()[0];
			$response->max_fecha=$mySQL->query("select max(fecha) from datos")->fetch_row()[0];
			
			$min_f=new DateTime($response->min_fecha);
			$max_f=new DateTime($response->max_fecha);
			$max_f = $max_f->modify('+1 day'); 
			$int_f=DateInterval::createFromDateString('1 day');
			
			$response->fechas=array();
			$response->fechas_display=array();
			$fechas_content=array();
			$graph_content=array();
			$graph_content_fecha_index=array();
			$periodo=new DatePeriod($min_f,$int_f,$max_f);
			$index=0;
			foreach ($periodo as $dt) {
				$response->fechas[]=$dt->format("Y-m-d");
				$response->fechas_display[]=sprintf("%s %s",$dt->format("d"),$meses[(int)$dt->format("m")]);
				$fechas_content[$dt->format("Y-m-d")]=array("c"=>0,"e"=>0,"f"=>0,"s"=>0);
				$graph_content[]=0;
				$graph_content_fecha_index[$dt->format("Y-m-d")]=$index;
				$index++;
			}
				
			//numero de registros
			$response->num_regs=(int) $params["num_regs"];
			$response->pagina=(int) $params["pagina"];
			$response->regs_totales=(int) $mySQL->query($sql_num_registros)->fetch_row()[0];
			$response->paginas_totales=($response->num_regs==0)? 1 : ceil($response->regs_totales/$response->num_regs);
			
			//Consulta
			$limit=($response->num_regs==0) ? "" : sprintf("limit %s,%s",$response->num_regs*($response->pagina-1),$response->num_regs);
			
			$c=$mySQL->query("$sql_paises order by $field_order $limit");
			if($c){
				while($r=$c->fetch_assoc()){
					if($zona==""){
						$r["zonas"]=$mySQL->query(sprintf("select count(*) from nombres where pais='%s'",addslashes($r["p"])))->fetch_row()[0];
					}else{
						$r["zonas"]=0;	
					}
					$r["acumulados"]=array("c"=>0,"e"=>0,"f"=>0,"s"=>0);
					$r["d"]=$fechas_content;
					$r["graph"]=array(
						"c"=>$graph_content,"e"=>$graph_content,"f"=>$graph_content,"s"=>$graph_content,
						"ac"=>$graph_content,"ae"=>$graph_content,"af"=>$graph_content,"as"=>$graph_content
					);
					
					$c2=$mySQL->query(sprintf($sql_detalle,addslashes($r["p"])));
					if($c2){
						while($r2=$c2->fetch_assoc()){
							$r["d"][$r2["fecha"]]=array(
								"c"=>(int)$r2["c"],
								"e"=>round(100*$r2["f"]/$factor),
								"f"=>(int)$r2["f"],
								"s"=>(int)$r2["s"]
							);
							
							$index=$graph_content_fecha_index[$r2["fecha"]];
							foreach($r["acumulados"] as $k=>$valor){
								$r["graph"][$k][$index]=$r["d"][$r2["fecha"]][$k];
								$r["acumulados"][$k]+=$r["d"][$r2["fecha"]][$k];
								$r["graph"]["a$k"][$index]=$r["acumulados"][$k];
							}
							
						}
						
						$ec=$r["c"];
						$ee=round(100*$r["f"]/$factor);
						
						$response->data[]=array(
							"p"=>$r["p"],
							"c"=>(int)$r["c"],
							"e"=>round(100*$r["f"]/$factor),
							"f"=>(int)$r["f"],
							"s"=>(int)$r["s"],
							
							"fc_c"=>0,
							"fc_f"=>($ec==0) ? 0 : round(100 * $r["f"] / $ec,1),
							"fc_s"=>($ec==0) ? 0 : round(100 * $r["s"] / $ec,1),
							"fc_e"=>0,
							
							"fe_c"=>0,
							"fe_f"=>($ee==0) ? 0 : round(100 * $r["f"] / $ee,1),
							"fe_s"=>($ee==0) ? 0 : round(100 * $r["s"] / $ee,1),
							"fe_e"=>0,
							
							"z"=>(int)$r["zonas"],
							"d"=>$r["d"],
							"g"=>$r["graph"]
						);
					}
				}
			}
			//los datos para las graficas
			
		}else{
			throw new Exception($GLOBALS["msg.error_conect_db"]);
		}
		
		$response->result="ok";	
		echo json_encode($response);
		exit;
	}catch(Exception $e) {		
		$response->result="nok";		
		$response->msg=$e->getMessage();	
		echo json_encode($response);
		exit;
	}
}

//enrutador de las peticiones
if (isset($_POST["func"])){
	if (isset($_POST["params"])){
		call_user_func($_POST["func"],$_POST["params"]);
	}else{
		call_user_func($_POST["func"]);	
	}
}



?>
