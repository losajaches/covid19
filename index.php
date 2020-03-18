<?php
$data1=array();
$f=file("https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_19-covid-Deaths.csv");
foreach($f as $i=>$l){
	$s=str_replace(array("\r","\n"),array("",""),$l);
	if($i==0){
		$keys=explode(",",$s);
	}else{
		$valores=explode(",",$s);
		$a=array_combine($keys,$valores);
		unset($a["Lat"]);
		unset($a["Long"]);
		$data1[]=$a;
	}
}
//agrupamos por pais
$data=array();
foreach($data1 as $d){
	
	$pais=$d["Country/Region"];
	
	unset($d["Province/State"]);
	unset($d["Country/Region"]);
	
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
			font-size:.8em;
		}
	</style>
  </head>
  <body>
    <header>
		<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
			<a class="navbar-brand" href="#">COVID19 - Fallecimientos País/Día</a>
		</nav>
	</header>
	<div class='container-fluid pt-2'>
		<div class='row info'>
			<div class='col'>
				<div class='text-center'>
					<small style='font-size:.7em;'>
						<a href='https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_19-covid-Deaths.csv' target='_blank'>fichero csv</a> | 
						<a href='https://systems.jhu.edu/research/public-health/ncov/' target='_blank'>Ir a https://systems.jhu.edu/research/public-health/ncov/</a>
					</small>
					<br>
					<label><input type='checkbox' id='ShowDiario' checked>Mostrar fallecimientos diarios</label>&nbsp;&nbsp;
					<label><input type='checkbox' id='ShowAcumulado' checked>Mostrar fallecimientos acumulados diarios</label>
				</div>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm-6'>
				<div id="container" style='min-height:500px;'></div>
			</div>
			<div class='col-sm-6'>
				<div class='table-responsive'>
				<table class="table table-bordered table-sm table-striped table-hover">
					<?php
					$meses=array("","Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
					$i=0;
					foreach($data as $pais=>$d){
						if($d["total"]>0){
							if($i==0){
								echo "<thead>";
								echo "<th><label><input type='checkbox' checked>País</label></th>";
								foreach(array_reverse($d,true) as $k=>$v){
									if($k=="total"){
										$s="Total";
									}else{
										$s=explode("/",$k);
										$s=sprintf("%02d<br><small>%s</small>",$s[1],$meses[$s[0]]);
									}
									echo "<td class='text-center'>$s</td>";
								}
								echo "</thead>";
								echo "<tbody>";
							}
							
							
							echo "<tr>";
							echo sprintf("<td><label><input type='checkbox' %s data-pais='$pais'>$pais</label></td>",($i<5)?"checked":"");
							foreach(array_reverse($d,true) as $k=>$v){
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
		
		function normalizar(vector){
			var v=[];
			var radio = Math.max.apply(this, vector) / 100;	
			for(var i = 0; i<vector.length;i++) {
			    v.push(Math.round(vector[i] / radio));
			}
			return v;
		}
			
		function plotear(){
			var series=[];
			var categorias=[];
			var index_color=0;
			var ShowAcumulado=$("#ShowAcumulado").prop("checked");
			var ShowDiario=$("#ShowDiario").prop("checked");
			
			$("table tbody input:checked").each(function(){
				var pais=$(this).data("pais");
				var t=$.map(DATA[pais], function(n, i) { return i; }).length;
				
				var d=[];
				var dT=[];
				categorias=[];
				vt=0;
				$.each(DATA[pais],function(i,v){
					if(i!="total"){
						d.push(v);
						vt+=v;
						dT.push(vt);
						var fecha=i.split("/")
						categorias.push(fecha[1]+"/"+fecha[0]+'/'+fecha[2]);
					}
				})
				
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
				/*
				series.push({
					"name": pais+"(N)",	
					"data":normalizar(dT)
				});*/
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
			$("#container").height($(window).innerHeight()-$("header").height()-$("div.info").height()-60);
		}
		
		$( document ).ready(function() {
    		$("table thead input[type='checkbox']").click(function(){
    			$("table tbody input[type='checkbox']").prop("checked",$(this).prop("checked"));
    			plotear();
    		});
    		$("table tbody input[type='checkbox']").click(function(){
    			plotear();
    		});
    		$("#ShowDiario").click(function(){
    			plotear();
    		});
    		$("#ShowAcumulado").click(function(){
    			plotear();
    		});
    		
    		$(window).resize(function() {
			  redimensionar();
			});
			redimensionar();
    		plotear();
		});
	</script>
</html>
