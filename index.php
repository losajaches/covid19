<?php
function ProcesarFichero($file_name,$fileContent=""){
	$data1=array();
	if($file_name==""){
		$f=$fileContent;
	}else{
		$f=file($file_name);
	}
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
	include("get.spain.data.php");
	$SpainData=GetSpainData();
	$data_date_update=$SpainData["date_update"];
	$data_fallecidos=ProcesarFichero("",$SpainData["fallecidos"]);
	$data_contagiados=ProcesarFichero("",$SpainData["contagiados"]);
}else{
	$data_date_update=date("Y-m-d",strtotime("-1 days"))." 00:00:00";
	$data_fallecidos=ProcesarFichero("https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_19-covid-Deaths.csv");
	$data_contagiados=ProcesarFichero("https://raw.githubusercontent.com/CSSEGISandData/COVID-19/master/csse_covid_19_data/csse_covid_19_time_series/time_series_19-covid-Confirmed.csv");
}

$bigdata=array();
$fecha_minima=null;
$fecha_maxima=null;
	
foreach($data_fallecidos as $pais=>$data){
	$bigdata[$pais]=array(
		"t"=>array(
			"c"=>0,/*contagiados*/
			"f"=>0/*fallecidos*/
		),
		"d"=>array()
	);
	
	$f=strtotime("2020-01-22");
	$ff=strtotime(date("Y-m-d"));
	$i=0;
	$acum_contagiados=0;
	$acum_fallecidos=0;
	
	
	
	while($f<$ff){
		$k=date("Y-m-d",$f);
		if((isset($data_contagiados[$pais][$k]))||(isset($data_fallecidos[$pais][$k]))){
			if((is_null($fecha_minima))||($f<$fecha_minima)){
				$fecha_minima=$f;
			}
			if((is_null($fecha_maxima))||($fecha_maxima<$f)){
				$fecha_maxima=$f;
			}
		
			$contagiados=(isset($data_contagiados[$pais]) && isset($data_contagiados[$pais][$k]))?$data_contagiados[$pais][$k]:0;
			$fallecidos=(isset($data_fallecidos[$pais]) && isset($data_fallecidos[$pais][$k]))?$data_fallecidos[$pais][$k]:0;
			
			$last_acum_contagiados=$acum_contagiados;
			$last_acum_fallecidos=$acum_fallecidos;
			
			$acum_contagiados+=$contagiados;
			$acum_fallecidos+=$fallecidos;
			
			$crecimiento_contagiados=($last_acum_contagiados==0)? 0 : round((100*$acum_contagiados/$last_acum_contagiados)-100,2);
			$crecimiento_fallecidos=($last_acum_fallecidos==0)? 0 : round((100*$acum_fallecidos/$last_acum_fallecidos)-100,2);
			
			if(($acum_contagiados>0)||($acum_fallecidos>0)){
				$bigdata[$pais]["d"][$k]=array(
					"c"=>$contagiados,
					"f"=>$fallecidos,
					"sc"=>$acum_contagiados,/*acumulados contagiados*/
					"sf"=>$acum_fallecidos, /*acumulados fallecidos*/
					"if"=>($acum_contagiados==0)?0:round(100*$acum_fallecidos/$acum_contagiados,2), /*tasa de fallecidos por contagiados*/
					"cc"=>$crecimiento_contagiados,/*crecimiento contagiados*/
					"cf"=>$crecimiento_fallecidos /*crecimiento fallecidos*/
				);
				$bigdata[$pais]["t"]["c"]+=$contagiados;
				$bigdata[$pais]["t"]["f"]+=$fallecidos;
				$bigdata[$pais]["t"]["if"]=($bigdata[$pais]["t"]["c"]==0)?0:round(100*$bigdata[$pais]["t"]["f"]/$bigdata[$pais]["t"]["c"],2);
				$bigdata[$pais]["t"]["cc"]=$crecimiento_contagiados;
				$bigdata[$pais]["t"]["cf"]=$crecimiento_fallecidos;
			}
		}
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
		table .d{
			min-width:80px;
			font-size:1em;
		}
		table .d span{
			text-align:right;
		}
		table .d .r{
			display:block;
		}
		table .d .r.r1{
			font-size:1em;
			height:1.2em;
			line-height:1.3em;
			font-weight:bold;
		}
		table .d .r.r2{
			font-size:.7em;
			height:1.4em;
			line-height:1.3em;
		}
		table .d .r.r3{
			font-size:.8em;
			height:1.3em;
			line-height:1.3em;
		}
		table .d .r.r4{
			font-size:.7em;
			height:1.2em;
			line-height:1.3em;
		}
		table .d .r .c{
			display:inline-block;
			
		}
		table .d .c1{
			width:40%;
			color:#ff4d4d;
			font-size:.8em;
		}
		table .d .c2{
			width:60%;
		}
		table .d .c3{
			width:40%;
			color:#8c8c8c;
		}
		table .d .c4{
			width:60%;
			color:#8c8c8c;
		}
		table .d .c5{
			width:40%;
			
		}
		table .d .c6{
			font-weight:bold;
			font-size:1.2em;
			width:60%;
			color:#e68a00;
		}
		table .d .c7{
			width:40%;
			color:#ffad33;
		}
		table .d .c8{
			width:60%;
			color:#ffad33;
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
					<a class="btn btn-outline-secondary mr-1 <?php echo (isset($_GET["ccaa"]))?"":"active";?>" href="?">PAISES</span></a>
				    <a class="btn btn-outline-secondary mr-1 <?php echo (isset($_GET["ccaa"]))?"active":"";?>" href="?ccaa=1">ESPAÑA</span></a>
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
				<table class="table table-bordered table-sm table-striped table-hover">
					<thead>
						<th  class='text-center' colspan='2'>Leyenda (datos actualizados a fecha <?php echo date("d/m/Y H:i",strtotime($data_date_update));?>)</th>
					</thead>
					<tbody>
						<tr>
							<td class='d' style='width:100px;'>
								<span class='r r1'><span class='c c1'>9,9%</span><span class='c c2'>9.999</span></span>
								<span class='r r2'><span class='c c3'>9,9%</span><span class='c c4'>99.999</span></span>
								<span class='r r3'><span class='c c5'></span><span class='c c6'>9.999</span></span>
								<span class='r r4'><span class='c c7'>9,9%</span><span class='c c8'>99.999</span></span>
							</td>
							<td class='d'>
								<span class='r r1'><span class='c c1'>Tasa de fallecidos</span><span class='c c2'>Nuevos fallecidos</span></span>
								<span class='r r2'><span class='c c3'>% Nuevos fallecidos</span><span class='c c4'>Fallecidos totales</span></span>
								<span class='r r3'><span class='c c5'></span><span class='c c6'>Nuevo contagiados</span></span>
								<span class='r r4'><span class='c c7'>% Nuevos Contagiados</span><span class='c c8'>Contagiados totales</span></span>
							</td>
						</tr>
					</tbody>
				</table>
				
				<div class='table-responsive'>
				<table class="table table-bordered table-sm table-striped table-hover">
					<?php
					$i=0;
					foreach($bigdata as $pais=>$pais_data){
						if($pais_data["t"]["f"]>0){
							if($i==0){
								$Nombre=(isset($_GET["ccaa"]))?"ESPAÑA":"País";
								echo "<thead>";
								echo "<th class='text-center'><input type='checkbox' checked></th>";
								echo "<th>$Nombre</th>";
								echo "<th class='text-center'>Total</th>";
								
								$f=$fecha_maxima;
								while($f>=$fecha_minima){
									$s=sprintf("%s<small>/%s</small>",date("d",$f),date("m",$f));
									echo "<th class='text-center'>$s</th>";
									
									$f=strtotime('-1 day', $f);	
								}
								echo "</thead>";
								echo "<tbody>";
							}
							
							echo "<tr>";
							echo sprintf("<td class='text-center'><input type='checkbox' %s data-pais='$pais'></td>",($i<5)?"checked":"");
							echo sprintf("<td >%s</td>",substr(str_replace(" ","·",$pais),0,15));
							
							$fallecidos=($pais_data["t"]["f"]==0) ? "*" : number_format($pais_data["t"]["f"],0,",",".");
							$contagiados=($pais_data["t"]["c"]==0) ? "*" : number_format($pais_data["t"]["c"],0,",",".");
							
							$crecimiento_fallecidos= ($pais_data["t"]["cf"]==0) ? "" : number_format($pais_data["t"]["cf"],1,",",".")."%";
							$crecimiento_contagiados=($pais_data["t"]["cc"]==0) ? "" : number_format($pais_data["t"]["cc"],1,",",".")."%";
							$indice_fallecidos=      ($pais_data["t"]["if"]==0) ? "" : number_format($pais_data["t"]["if"],1,",",".")."%";
							
							echo "<td class='d'>
									<span class='r r1'><span class='c c1'>$indice_fallecidos</span><span class='c c2'>$fallecidos</span></span>
									<span class='r r2'><span class='c c3>$crecimiento_fallecidos</span><span class='c c4'></span></span>
									<span class='r r3'><span class='c c5'></span><span class='c c6'>$contagiados</span></span>
									<span class='r r4'><span class='c c7'>$crecimiento_contagiados</span><span class='c c8'></span></span>
								</td>";
								
								
							$f=$fecha_maxima;
							while($f>=$fecha_minima){
								$k=date("Y-m-d",$f);
								if($pais_data["d"][$k]){
									$pd=$pais_data["d"][$k];
									
									$fallecidos             = ($pd["f"]==0) ? "" : number_format($pd["f"],0,",",".");
									$contagiados            = ($pd["c"]==0) ? "" : number_format($pd["c"],0,",",".");
									$acum_fallecidos        = ($pd["sf"]==0) ? "" : number_format($pd["sf"],0,",",".");
									$acum_contagiados       = ($pd["sc"]==0) ? "" : number_format($pd["sc"],0,",",".");
									
									$crecimiento_fallecidos = ($pd["cf"]==0) ? "" : number_format($pd["cf"],1,",",".")."%";
									$crecimiento_contagiados= ($pd["cc"]==0) ? "" : number_format($pd["cc"],1,",",".")."%";
									$indice_fallecidos      = ($pd["if"]==0) ? "" : number_format($pd["if"],1,",",".")."%";
								}else{
									$fallecidos="";	
									$contagiados="";	
									$acum_fallecidos="";	
									$acum_contagiados="";	
									$crecimiento_fallecidos="";	
									$crecimiento_contagiados="";	
									$indice_fallecidos="";	
								}
								$fallecidos=isset($pais_data["d"][$k]) ? $pais_data["d"][$k]["f"] : 0;
								$fallecidos=($fallecidos==0) ? "" : number_format($fallecidos,0,",",".");
								
								echo "<td class='d'>
									<span class='r r1'><span class='c c1'>$indice_fallecidos</span><span class='c c2'>$fallecidos</span></span>
									<span class='r r2'><span class='c c3'>$crecimiento_fallecidos</span><span class='c c4'>$acum_fallecidos</span></span>
									<span class='r r3'><span class='c c5'></span><span class='c c6'>$contagiados</span></span>
									<span class='r r4'><span class='c c7'>$crecimiento_contagiados</span><span class='c c8'>$acum_contagiados</span></span>
								</td>";
								
								$f=strtotime('-1 day', $f);	
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
		
		function GetValuesArrayObject(a){
			var b=[];
			$.each(a,function(i,v){
				b.push(v);
			});
			return b;
			return Math.min.apply(Math, b);
		}	
		function MostrarGrafica(){
			var series=[];
			var categorias=[];
			var index_color=0;
			var ShowAcumulado=$("#ShowAcumulado").prop("checked");
			var ShowDiario=$("#ShowDiario").prop("checked");
			//buscamos el primer y ultimo día con datos de cualquiera de los paises seleccionados y se crean las categorías
			var primer_dia={};
			var ultimo_dia={};
			$("table tbody input:checked").each(function(){
				var pais=$(this).data("pais");
				primer_dia[pais]=null;
				ultimo_dia[pais]=null;
				$.each(DATA[pais]["d"],function(i,v){
					if((v["f"]!=0)&&(primer_dia[pais]==null)){
						primer_dia[pais]=1*i.replace(/\-/g,'');
					}
					ultimo_dia[pais]=1*i.replace(/\-/g,'');
				});
			});
			
			primer_dia=Math.min.apply(Math, GetValuesArrayObject(primer_dia)).toString();
			ultimo_dia=Math.min.apply(Math, GetValuesArrayObject(ultimo_dia)).toString();
			
			primer_dia=primer_dia.substring(0,4)+"-"+primer_dia.substring(4,6)+"-"+primer_dia.substring(6,10);
			ultimo_dia=ultimo_dia.substring(0,4)+"-"+ultimo_dia.substring(4,6)+"-"+ultimo_dia.substring(6,10);
			
			primer_dia = new Date(primer_dia);
			ultimo_dia = new Date(ultimo_dia);
			
			var categorias=[];
			var loop = new Date(primer_dia);
			while(loop <= ultimo_dia){
			   categorias.push(loop.toISOString().split('T')[0]);
			   loop = new Date(loop.setDate(loop.getDate() + 1));
			}
			
			//creamos las series
			$("table tbody input:checked").each(function(){
				var pais=$(this).data("pais");
				var d=[];
				var dT=[];
				$.each(categorias,function(i,fecha){
					if(DATA[pais]["d"][fecha]){
						d.push(DATA[pais]["d"][fecha]["f"]);	
						dT.push(DATA[pais]["d"][fecha]["sf"]);	
					}else{
						d.push(null);
						dT.push(null);
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
