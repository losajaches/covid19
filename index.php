<?php
function ProcesarFichero($file_name){
	$data1=array();
	$f=file($file_name);
	foreach($f as $i=>$l){
		$s=str_replace(array("\r","\n"),array("",""),$l);
		if($i==0){
			$keys=explode(",",$s);
		}else{
			$valores=explode(",",$s);
			$a=array_combine($keys,$valores);
			
			unset($a["Lat"]);
			unset($a["Long"]);
			unset($a["Province/State"]);
			$a["pais"]=$a["Country/Region"];
			unset($a["Country/Region"]);
			
			foreach($a as $k=>$v){
				if($k!="pais"){
					list($m,$d,$y)=explode("/",$k);
					$fec=sprintf("20%d-%02d-%02d",$y,$m,$d);
					$a[$fec]=$v;
					unset($a[$k]);
				}
			}
			
			$data1[]=$a;
		}
	}
	//agrupamos por pais
	$data=array();
	foreach($data1 as $d){
		
		$pais=$d["pais"];
		unset($d["pais"]);
		
		if(!isset($data[$pais])){
			$data[$pais]=$d;
		}else{
			foreach($d as $i=>$v){
				$data[$pais][$i]+=$v;	
			}
		}
	}
	//cambiamos a fallecidos distintos diarios, no fallecidos totales acumulados diarios
	foreach($data as $pais=>$d){
		$ff=0;
		foreach($d as $dia=>$fallecidos){
			$data[$pais][$dia]=$fallecidos-$ff;
			$ff+=$data[$pais][$dia];
		}
	}
	
	//sumamos
	foreach($data as $pais=>$d){
		$data[$pais]["total"]=array_sum($d);
	}
	//ordenamos por total de fallecidos
	uasort($data,function($a,$b){
		if ($a["total"] == $b["total"]) {
	        return 0;
	    }
	    return ($a["total"] > $b["total"]) ? -1 : 1;
	});	
	return $data;
}
if(isset($_GET["ccaa"])){
	$data_fallecidos=ProcesarFichero("spain-ccaa-death.csv");
	$data_contagiados=ProcesarFichero("spain-ccaa-infec.csv");
	
}else{
	$data_fallecidos=ProcesarFichero("https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_19-covid-Deaths.csv");
	$data_contagiados=ProcesarFichero("https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_19-covid-Confirmed.csv");
}

$bigdata=array();
foreach($data_fallecidos as $pais=>$data){
	$total_contagios=$data["total"];
	$total_contagios=isset($data_contagiados[$pais])?$data_contagiados[$pais]["total"]:0;
	$total_fallecidos=isset($data_fallecidos[$pais])?$data_fallecidos[$pais]["total"]:0;
	$bigdata[$pais]=array(
		"contagiados"=>array("total"=>$total_contagios,"dias"=>array()),
		"fallecidos"=>array("total"=>$total_fallecidos,"dias"=>array()),
		"acum_contagiados"=>array("total"=>$total_contagios,"dias"=>array()),
		"acum_fallecidos"=>array("total"=>$total_fallecidos,"dias"=>array()),
		"tasa"=>array("total"=>($total_contagios==0)?0:round(100*$total_fallecidos/$total_contagios,2),"dias"=>array())
	);
	$f=strtotime("2020-01-22");
	$ff=strtotime(date("Y-m-d"));
	$i=0;
	$acum_contagiados=0;
	$acum_fallecidos=0;
	
	while($f<$ff){
		$k=date("Y-m-d",$f);
		$k=date("Y-m-d",$f);
		$contagiados=(isset($data_contagiados[$pais]) && isset($data_contagiados[$pais][$k]))?$data_contagiados[$pais][$k]:0;
		$fallecidos=(isset($data_fallecidos[$pais]) && isset($data_fallecidos[$pais][$k]))?$data_fallecidos[$pais][$k]:0;
		
		$acum_contagiados+=$contagiados;
		$acum_fallecidos+=$fallecidos;
		
		$bigdata[$pais]["contagiados"]["dias"][$k]= $contagiados;
		$bigdata[$pais]["fallecidos"]["dias"][$k]=  $fallecidos;
		
		$bigdata[$pais]["acum_contagiados"]["dias"][$k]= $acum_contagiados;
		$bigdata[$pais]["acum_fallecidos"]["dias"][$k]=  $acum_fallecidos;
		
		$bigdata[$pais]["tasa"]["dias"][$k]=  ($acum_contagiados==0)?0:round(100*$acum_fallecidos/$acum_contagiados,2);
		$f=strtotime('+1 day', $f);
	}

}

?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="PpSoft">
    <title>COVID-19</title>
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<style>
		body {
			padding-top:52px;
		}
		table{
			font-size:.7em;
		}
		.nav-link{
			
		}
		.titulo_1{
			font-weight:bold;
			color:#ffcc80;	
			font-size:1em;
		}
		.titulo_2{
			font-weight:bold;
			color:#ffebcc;	
			font-size:.4em;
		}
		.nav-link1{
			font-weight:bold;
			font-size:1.2em;
		}
		.nav-link1.active{
			text-decoration: underline;
			font-weight:bold;
		}
	</style>
  </head>
  <body>
    <header>
		<nav id='MainNav' class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
			<a class="navbar-brand" href="#">
				<span class='titulo_1'>COVID-19</span>
				<span class='titulo_2'>ESTADISTICAS</span>
			</a>
			
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mr-auto">
				</ul>
				<ul class="navbar-nav">
					<a class="btn btn-outline-secondary mr-1 <?php echo (isset($_GET["ccaa"]))?"":"active";?>" href="index.php">PAISES</span></a>
				    <a class="btn btn-outline-secondary mr-1<?php echo (isset($_GET["ccaa"]))?"active":"";?>" href="index.php?ccaa=1">ESPAÑA</span></a>
				    <a class="btn btn-outline-warning" href="https://github.com/losajaches/covid19" target='_blank'>&copy;</span></a>
			    </ul>
			</div>
		</nav>
	</header>
	<div id='MainForm' class='container-fluid pt-2' style='min-height:500px;'>
		<div id='MainFormTop' class='row info'>
			<div class='col'>
				<div class='text-center'>
					<label><input type='checkbox' id='ShowDiario' checked>Mostrar fallecimientos diarios</label>&nbsp;&nbsp;
					<label><input type='checkbox' id='ShowAcumulado' checked>Mostrar fallecimientos acumulados diarios</label>
				</div>
			</div>
		</div>
		<div id='MainFormData' class='row'>
			<div class='col-sm-7 pr-0'>
				<div id="container"></div>
			</div>
			<div class='col-sm-5 pl-0'>
				<div class='table-responsive'>
				<table class="table table-bordered table-sm table-striped table-hover">
					<?php
					$meses=array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
					$i=0;
					foreach($bigdata as $pais=>$d){
						if($d["fallecidos"]["total"]>0){
							
							if($i==0){
								$Nombre=(isset($_GET["ccaa"]))?"ESPAÑA":"País";
								echo "<thead>";
								echo "<th class='text-center'><input type='checkbox' checked></th>";
								echo "<th>$Nombre</th>";
								echo "<th>Total<br>Fallecidos</th>";
								foreach(array_reverse($d["fallecidos"]["dias"],true) as $k=>$v){
									$s=explode("-",$k);
									$s=sprintf("%02d<br><small>%s</small>",$s[2],$meses[1*$s[1]]);
									echo "<td class='text-center'>$s</td>";
								}
								echo "</thead>";
								echo "<tbody>";
							}
							
							
							echo "<tr>";
							echo sprintf("<td class='text-center'><input type='checkbox' %s data-pais='$pais'></td>",($i<5)?"checked":"");
							echo sprintf("<td >%s</td>",substr(str_replace(" ","·",$pais),0,15));
							echo sprintf("<td class='text-right font-weight-bold'>%s</td>",$d["fallecidos"]["total"]);
							foreach(array_reverse($d["fallecidos"]["dias"],true) as $k=>$v){
								$contagiados=isset($d["contagiados"]["dias"][$k])?$d["contagiados"]["dias"][$k]:0;
								$tasa=isset($d["tasa"]["dias"][$k])?$d["tasa"]["dias"][$k]:0;
								echo sprintf("<td class='text-right'>%s<br><small title='Nuevos casos y tasa de fallecidos por contagiados'>%s%s</small></td>",
									($v==0)?"":number_format($v,0,",","."),
									($contagiados==0)?"":"+".number_format($contagiados,0,",","."),
									($tasa==0)?"":" i:".number_format($tasa,2,",",".")."%"
								);
							}
							echo "</tr>";	
							$i++;
						}
					}
					echo "</tbody>";
					?>
				</table>
				</div>
			</div>
		</div>
	</div>     
	<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
	<script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/modules/accessibility.js"></script>
	<script>
		var DATA=<?php echo json_encode($bigdata);?>;
		
			
		function MostrarGrafica(){
			var series=[];
			var categorias=[];
			var index_color=0;
			var ShowAcumulado=$("#ShowAcumulado").prop("checked");
			var ShowDiario=$("#ShowDiario").prop("checked");
			//buscamos el primer día con datos de cualquiera de los paises seleccionados
			var primer_dia={};
			$("table tbody input:checked").each(function(){
				var pais=$(this).data("pais");
				primer_dia[pais]=null;
				$.each(DATA[pais]["fallecidos"]["dias"],function(i,v){
					if((v!=0)&&(primer_dia[pais]==null)){
						primer_dia[pais]=1*i.replace(/\-/g,'');
					}
				});
			});
			var p="99999999";
			$.each(primer_dia,function(i,v){
				if(v<p){
					p=v;
				}
			});
			primer_dia=p;
			//creamos las series
			$("table tbody input:checked").each(function(){
				var pais=$(this).data("pais");
				var t=$.map(DATA[pais]["fallecidos"]["dias"], function(n, i) { return i; }).length;
				
				var d=[];
				var dT=[];
				categorias=[];
				vt=0;
				$.each(DATA[pais]["fallecidos"]["dias"],function(i,v){
					
					var ii=1*i.replace(/\-/g,'');
					if(ii>=primer_dia){
						d.push(v);
						vt+=v;
						dT.push(vt);
						var fecha=i.split("-")
						categorias.push(fecha[2]+"/"+fecha[1]+"/"+fecha[0]);
					}
					
				});
				
				var s_acumulado={
					"name": pais,
					type: 'area',
					yAxis:1,
					"data":dT,
					
					lineWidth: 0,
					color: Highcharts.getOptions().colors[index_color],
			        fillOpacity: 0.3,
			        zIndex: 0,
			        marker: {
			            enabled: false
			        }
				};
				var s_diario={
					"name": pais,
					type: 'spline',
					yAxis:0,
					"data":d,
					zIndex: 1,
					color: Highcharts.getOptions().colors[index_color],
					marker: {
			            enabled: false
			        }
				};
				
				if(ShowAcumulado){
					series.push(s_acumulado);
					s_diario["linkedTo"]=':previous';
				}
				if(ShowDiario){
					series.push(s_diario);	
				}
				
				index_color++;
			});
			
			Highcharts.chart('container', {
			    chart: {
			        
			        zoomType: 'xy'
			    },
			    title: {
			        text: ''
			    },
			    xAxis: {
			        categories: categorias
			    },
			    yAxis:[
			    	{
			        	title: {
			            	text: 'Fallecidos diarios'
			        	}
			    	},
			    	{
			        	title: {
			            	text: 'Fallecidos totales'
			        	},
			        	opposite: true
			    	}
			    ],
			    "series": series,
			    credits: {
				    enabled: false
				}
			});
		}
		
		
		function redimensionar(){
			$("#MainForm").height($(window).innerHeight()-$("#MainNav").height()-30);
			$("#MainFormData").height($("#MainForm").height()-$("#MainFormTop").height());
			$("#container").height($("#MainFormData").height());
			$(".table-responsive").height($("#MainFormData").height());
		}
		
		$( document ).ready(function() {
    		$("table thead input[type='checkbox']").click(function(){
    			$("table tbody input[type='checkbox']").prop("checked",$(this).prop("checked"));
    			MostrarGrafica();
    		});
    		$("table tbody input[type='checkbox']").click(function(){
    			MostrarGrafica();
    		});
    		$("#ShowDiario").click(function(){
    			MostrarGrafica();
    		});
    		$("#ShowAcumulado").click(function(){
    			MostrarGrafica();
    		});
    		
    		$(window).resize(function() {
			  redimensionar();
			});
			redimensionar();
    		MostrarGrafica();
		});
	</script>
</html>
