<?php
$data1=array();
$f=file("https://covid.ourworldindata.org/data/new_deaths.csv");

$data=array();
foreach($f as $i=>$l){
	$s=str_replace(array("\r","\n"),array("",""),$l);
	
	if($i==0){
		$keys=explode(",",$s);	
	}else{
		$values=explode(",",$s);
		$linea=array_combine($keys,$values);
		foreach($linea as $k=>$v){
			if($k!="date"){
				if(!isset($data[$k])){
					$data[$k]=array("total"=>0,"dias"=>array());
				}
				$data[$k]["dias"][$linea["date"]]=($v=="")?0:1*$v;
				$data[$k]["total"]+=($v=="")?0:1*$v;
			}
		}
	}
}

//ordenamos por total de fallecidos
uasort($data,function($a,$b){
	if ($a["total"] == $b["total"]) {
        return 0;
    }
    return ($a["total"] > $b["total"]) ? -1 : 1;
});

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
	</style>
  </head>
  <body>
    <header>
		<nav id='MainNav' class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
			<a class="navbar-brand" href="#">COVID19 - Fallecimientos País/Día</a>
			
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mr-auto">
			    </ul>
			    <small style='color:#ffcc80;font-size:.6em;' class='text-right'>José Manuel Rodríguez Sánchez<br><a href='mailto:adharis.net@gmail.com' style='color:#ffebcc;'>adharis.net@gmail.com</a></small>
			</div>
		</nav>
	</header>
	<div id='MainForm' class='container-fluid pt-2' style='min-height:500px;'>
		<div id='MainFormTop' class='row info'>
			<div class='col'>
				<div class='text-center'>
					<small style='font-size:.7em;'>
						<a href='https://covid.ourworldindata.org/data/new_deaths.csv' target='_blank'>fichero csv</a> | 
						<a href='https://www.who.int/emergencies/diseases/novel-coronavirus-2019/situation-reports/' target='_blank'>Ir a https://www.who.int/emergencies/diseases/novel-coronavirus-2019/situation-reports/</a> | 
						<a href='https://github.com/losajaches/covid19' target='_blank'>Fuente del programa en GitHub</a>
					</small>
					<br>
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
					foreach($data as $pais=>$d){
						if($d["total"]>0){
							if($i==0){
								echo "<thead>";
								echo "<th class='text-center'><input type='checkbox' checked></th>";
								echo "<th>País</th>";
								echo "<th>Total</th>";
								
								foreach(array_reverse($d["dias"],true) as $k=>$v){
									$s=explode("-",$k);
									$s=sprintf("%02d<br><small>%s</small>",$s[2],$meses[1*$s[1]]);
									echo "<td class='text-center'>$s</td>";
								}
								echo "</thead>";
								echo "<tbody>";
							}
							
							
							echo "<tr>";
							echo sprintf("<td class='text-center'><input type='checkbox' %s data-pais='$pais'></td>",(($i>0)&&($i<6))?"checked":"");
							echo sprintf("<td >%s</td>",substr(str_replace(" ","·",$pais),0,15));
							echo sprintf("<td class='text-right %s'>%s</td>",($k=="total")?"font-weight-bold":"",number_format($d["total"],0,",","."));
							foreach(array_reverse($d["dias"],true) as $k=>$v){
								echo sprintf("<td class='text-right %s'>%s</td>",($k=="total")?"font-weight-bold":"",($v==0)?"":number_format($v,0,",","."));
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
	<script src="https://code.highcharts.com/modules/exporting.js"></script>
	<script src="https://code.highcharts.com/modules/export-data.js"></script>
	<script src="https://code.highcharts.com/modules/accessibility.js"></script>
	<script>
		var DATA=<?php echo json_encode($data);?>;
		
			
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
				$.each(DATA[pais]["dias"],function(i,v){
					if((i!="total")&&(v!=0)&&(primer_dia[pais]==null)){
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
				var t=$.map(DATA[pais]["dias"], function(n, i) { return i; }).length;
				
				var d=[];
				var dT=[];
				categorias=[];
				vt=0;
				$.each(DATA[pais]["dias"],function(i,v){
					if(i!="total"){
						var ii=1*i.replace(/\-/g,'');
						if(ii>=primer_dia){
							d.push(v);
							vt+=v;
							dT.push(vt);
							var fecha=i.split("-")
							categorias.push(fecha[2]+"/"+fecha[1]+"/"+fecha[0]);
						}
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
			        text: 'Fallecimientos'
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
			    "series": series
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
