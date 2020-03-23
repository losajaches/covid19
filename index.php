<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name='apple-mobile-web-app-capable' content='yes' />
    <meta name='apple-mobile-web-app-title' content='COVID-19'>
    <meta name="description" content="">
    <meta name="author" content="PpSoft">
	<link rel='icon' type='image/png' href='icon.png'/>
	<link rel='apple-touch-icon' sizes='96x96' href='icon.png'>
    
    <title>COVID-19</title>
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
	<style>
		:root {
			--ww:30px;
			--hh:30px;
		}
		@media (max-width:900px) {
			:root {
				--ww:42px;
				--hh:42px;
			}
		}
		
		body {
			padding-top:calc(var(--hh) + 6px);
		}
		
		#MainNav{
			padding:6px !important;
			margin:0 !important;
		}
		
		#MainNav .navbar-brand{
			padding-top:0 !important;
			padding-bottom:0 !important;
			height:var(--hh) !important;
		}
		#MainNav img{
			height:var(--hh);
			width:var(--ww);
		}
		#MainNav .titulo{
			font-weight:bold;
			display:inline-block;
			height:var(--hh);
		}
		#MainNav .titulo .t1{
			color:#ffcc80;	
			font-size:calc(var(--hh)*2/3);
			line-height:calc(var(--hh)*2/3);
			display:block;
		}
		
		#MainNav .titulo .t2{
			color:#ffebcc;	
			font-size:calc(var(--hh)/3);
			line-height:calc(var(--hh)/3);
			display:block;
			text-align:right;
		}
		#MainNav .btn-outline-secondary{
			color:#ffcc80;
			border-color:#ffcc80;
		}
		#MainNav .btn-outline-secondary:hover,#MainNav .btn-outline-secondary.active{
			background-color:#ffebcc;
			color:#e68a00;
		}
		#MainNav button,#MainNav a{
			height:var(--hh);
			width:var(--ww);
			font-size:calc(var(--hh)*2/3);
			line-height:var(--hh);
			text-align:center;
			padding:0;
		}
		
		
		table{
			font-size:.7em;
		}
		
		#infoTable thead th{
			font-weight: normal;
		}
		#infoTable label{
			margin-bottom:0;
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
    	<nav id='MainNav' class="navbar navbar-dark bg-dark fixed-top">
		  <a class="navbar-brand" href="#" class='p-0 m-0'>
		    <img src="icon.png" class="d-inline-block align-top">
		    <div class='titulo'>
		    	<span class='t1'>COVID-19</span>
		    	<span class='t2'>ESTADISTICAS</span>
		    </div>
		  </a>
		  <div class="form-inline p-0 m-0">
			<button class="btn btn-sm btn-outline-secondary mr-1" data-tipo='world' title='Todos los paises' onclick="LoadData(this);"><i class="fas fa-globe-europe"></i></button>
		    <button class="btn btn-sm btn-outline-secondary mr-1" data-tipo='spain' title='España' onclick="LoadData(this);">Es</button>
		    <a class="btn btn-sm btn-outline-secondary mr-1" title='Fuentes' href='https://github.com/losajaches/covid19' target='_blank'><i class="fab fa-github"></i></a>
		  </div>
		</nav>
	</header>
	<div id='MainForm' class='container-fluid pt-2' style='min-height:400px;'>
		<div class='row'>
			<div class='col-sm-7 pl-1 pr-0'>
				<div id="graph"></div>
			</div>
			<div class='col-sm-5 pl-0 pr-1'>
				<table id="infoTable" class="table table-bordered table-sm table-striped table-hover">
					<thead>
						<th class='text-center' colspan='2'></th>
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
								<span class='r r2'><span class='c c3'>Incremento fallecidos</span><span class='c c4'>Fallecidos totales</span></span>
								<span class='r r3'><span class='c c5'></span><span class='c c6'>Nuevo contagiados</span></span>
								<span class='r r4'><span class='c c7'>Incremento Contagiados</span><span class='c c8'>Contagiados totales</span></span>
							</td>
						</tr>
						<tr class='cb_graph'>
							<td><b>F</b>allecidos :</td>
							<td>
								<label class='mr-2'><input type='checkbox' data-tipograph='fall_diar' checked>Diarios</label>
								<label><input type='checkbox' data-tipograph='fall_acum' checked>Acumulados</label>
							</td>
						</tr>
						<tr class='cb_graph'>
							<td><b>C</b>ontagiados :</td>
							<td>
								<label class='mr-2'><input type='checkbox' data-tipograph='cont_diar' checked>Diarios</label>
								<label><input type='checkbox' data-tipograph='cont_acum' checked>Acumulados</label>
							</td>
						</tr>
					</tbody>
				</table>
				<div class='table-responsive'>
					<table id='dataTable' class="table table-bordered table-sm table-striped table-hover"></table>
				</div>
			</div>
		</div>
	</div>     
	<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
	<script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/modules/accessibility.js"></script>
	<script>
		var DATA=null;
		
		function LoadData(ui){
			$("#MainNav button").removeClass("active");
			
			var tipo=$(ui).data("tipo");
			var old_icon=$(ui).html();
			$(ui).html("<i class='fas fa-spinner fa-spin'></i>");
			
			$(ui).addClass("active");
			
			$("#dataTable").html("");
			$("#graph").html("");
			
			$("#infoTable thead th:eq(0)").html("<i class='fas fa-spinner fa-spin'></i> Cargando datos");
			
			
			$.getJSON("data."+tipo+".json", function(data_json) {
				var fecha=new Date(data_json.date_update.replace(' ', 'T'));
				var html="";
				html+=data_json.descripcion+"<br><b>"+fecha.toLocaleDateString()+" "+fecha.toLocaleTimeString()+"</b><br>";
				$.each(data_json.sources,function(i,source){
					html+="<a href='"+source.url+"' target='_blank'>"+source.name+"</a> | ";	
				});
				
				$("#infoTable thead th:eq(0)").html(html);
				DATA=data_json.data;
				ShowTable(tipo);
				ShowGraph();
				$(ui).html(old_icon);
			}).fail(function(e) {
			    $("#infoTable thead th:eq(0)").html("<span class='text-danger'>Se ha producido un error al cargar los datos</span>");
			}).always(function() {
			    
			});
 
		}
		
		function GetValuesArrayObject(a){
			var b=[];
			$.each(a,function(i,v){
				b.push(v);
			});
			return b;
			return Math.min.apply(Math, b);
		}	
	
		function ShowTable(tipo){
			//buscamos el primer y ultimo día con datos de cualquiera de los paises seleccionados y se crean las categorías
			var primer_dia={};
			var ultimo_dia={};
			$.each(DATA,function(pais,subdata){
				primer_dia[pais]=null;
				ultimo_dia[pais]=null;
				$.each(subdata["d"],function(i,v){
					if(((v["f"]!=0)||(v["c"]!=0))&&(primer_dia[pais]==null)){
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
			$("#dataTable").html("");
			//cabecera
			var html="";
			html+="<thead>";
			html+="<th class='text-center'><input type='checkbox' checked onclick='ShowGraph();'></th>";
			html+="<th>"+((tipo=="spain")?"ESPAÑA":"País")+"</th>";
			html+="<th class='text-center'>Total</th>";
			for(var i=categorias.length-1;i>=0;i--){
				var d=new Date(categorias[i]).toISOString();
				html+="<th class='text-center'>"+d.substr(8,2)+"<small>/"+d.substr(5,2)+"</small></th>";
			}
			html+="</thead>";
			$("#dataTable").append(html);
			
			//registros
			$("#dataTable").append("<tbody></tbody>");

			var n=0;
			$.each(DATA,function(k,subdata){
				if(subdata["t"]["f"]>0){
					//(input/1000).toFixed(3)
					const FormatPercent = new Intl.NumberFormat('es-ES', {
					  style: 'decimal',
					  minimumFractionDigits: 1,
					  maximumFractionDigits: 1
					});
					
					const FormatNum = new Intl.NumberFormat('de-DE', {
					  style: 'decimal',
					  minimumFractionDigits: 0,
					  maximumFractionDigits: 0,
					  useGrouping:true
					});
					
					var fallecidos=(subdata["t"]["f"]==0) ?  "*" : FormatNum.format(subdata["t"]["f"]);
					var contagiados=(subdata["t"]["c"]==0) ? "*" : FormatNum.format(subdata["t"]["c"]);
							
					var crecimiento_fallecidos= (subdata["t"]["cf"]==0) ? "" : FormatPercent.format(subdata["t"]["cf"])+"%";
					var crecimiento_contagiados=(subdata["t"]["cc"]==0) ? "" : FormatPercent.format(subdata["t"]["cc"])+"%";
					var indice_fallecidos=      (subdata["t"]["if"]==0) ? "" : FormatPercent.format(subdata["t"]["if"])+"%";
							
					
					
					var html="";
					html+="<tr>";
					html+="	<td class='text-center'><input type='checkbox' "+((n<2)?"checked":"")+" data-pais='"+k+"' onclick='ShowGraph();'></td>";
					html+="	<td>"+k.replace(/ /g,"·")+"</td>";
					html+="	<td class='d'>";
					html+="		<span class='r r1'><span class='c c1'>"+indice_fallecidos+"</span><span class='c c2'>"+fallecidos+"</span></span>";
					html+=" 	<span class='r r2'><span class='c c3>"+crecimiento_fallecidos+"</span><span class='c c4'></span></span>";
					html+="		<span class='r r3'><span class='c c5'></span><span class='c c6'>"+contagiados+"</span></span>";
					html+="		<span class='r r4'><span class='c c7'>"+crecimiento_contagiados+"</span><span class='c c8'></span></span>";
					html+="	</td>";
					
					for(var i=categorias.length-1;i>=0;i--){
						var k=categorias[i];
						if(subdata["d"][k]){
							var pd=subdata["d"][k];
							
							var fallecidos             = (pd["f"]==0) ? "" :  FormatNum.format(pd["f"]);
							var contagiados            = (pd["c"]==0) ? "" :  FormatNum.format(pd["c"]);
							var acum_fallecidos        = (pd["sf"]==0) ? "" : FormatNum.format(pd["sf"]);
							var acum_contagiados       = (pd["sc"]==0) ? "" : FormatNum.format(pd["sc"]);
							
							var crecimiento_fallecidos = (pd["cf"]==0) ? "" : FormatPercent.format(pd["cf"])+"%";
							var crecimiento_contagiados= (pd["cc"]==0) ? "" : FormatPercent.format(pd["cc"])+"%";
							var indice_fallecidos      = (pd["if"]==0) ? "" : FormatPercent.format(pd["if"])+"%";
						}else{
							var fallecidos="";	
							var contagiados="";	
							var acum_fallecidos="";	
							var acum_contagiados="";	
							var crecimiento_fallecidos="";	
							var crecimiento_contagiados="";	
							var indice_fallecidos="";	
						}
						html+="	<td class='d'>";
						html+="		<span class='r r1'><span class='c c1'>"+indice_fallecidos+"</span><span class='c c2'>"+fallecidos+"</span></span>";
						html+=" 	<span class='r r2'><span class='c c3>"+crecimiento_fallecidos+"</span><span class='c c4'>"+acum_fallecidos+"</span></span>";
						html+="		<span class='r r3'><span class='c c5'></span><span class='c c6'>"+contagiados+"</span></span>";
						html+="		<span class='r r4'><span class='c c7'>"+crecimiento_contagiados+"</span><span class='c c8'>"+acum_contagiados+"</span></span>";
					}
					html+="</tr>";
					
					$("#dataTable tbody").append(html);
					n++;
				}
			});
			redimensionar();
		}
		
		
		function ShowGraph(){
			var series=[];
			var categorias=[];
			var index_color=0;
			
			var fall_diar=$(".cb_graph input[data-tipograph='fall_diar']").prop("checked");
			var fall_acum=$(".cb_graph input[data-tipograph='fall_acum']").prop("checked");
			var cont_diar=$(".cb_graph input[data-tipograph='cont_diar']").prop("checked");
			var cont_acum=$(".cb_graph input[data-tipograph='cont_acum']").prop("checked");
			
			//buscamos el primer y ultimo día con datos de cualquiera de los paises seleccionados y se crean las categorías
			var primer_dia={};
			var ultimo_dia={};
			$("#dataTable tbody input:checked").each(function(){
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
			var categorias_lbl=[];
			var loop = new Date(primer_dia);
			while(loop <= ultimo_dia){
			   categorias.push(loop.toISOString().split('T')[0]);
			   categorias_lbl.push(loop.toLocaleDateString());
			   loop = new Date(loop.setDate(loop.getDate() + 1));
			}
			
			//creamos las series
			$("#dataTable tbody input:checked").each(function(){
				var pais=$(this).data("pais");
				var d_fall_diar=[];
				var d_fall_acum=[];
				var d_cont_diar=[];
				var d_cont_acum=[];
			
				$.each(categorias,function(i,fecha){
					if(DATA[pais]["d"][fecha]){
						d_fall_diar.push(DATA[pais]["d"][fecha]["f"]);	
						d_fall_acum.push(DATA[pais]["d"][fecha]["sf"]);	
						d_cont_diar.push(DATA[pais]["d"][fecha]["c"]);	
						d_cont_acum.push(DATA[pais]["d"][fecha]["sc"]);	
					}else{
						d_fall_diar.push(null);
						d_fall_acum.push(null);
						d_cont_diar.push(null);
						d_cont_acum.push(null);
					}
				});
				var s_fall_acum={
					"id":pais+"_s_fall_acum_"+index_color,
					"name": pais+" (F)",
					type: 'area',
					yAxis:1,
					"data":d_fall_acum,
					lineWidth: 0,
					color: Highcharts.getOptions().colors[index_color],
			        fillOpacity: 0.5,
			        zIndex: 0,
			        marker: {
			            enabled: false
			        }
				};
				var s_fall_diar={
					"id":pais+"_s_fall_diar_"+index_color,
					"name": pais+" (F)",
					type: 'spline',
					yAxis:0,
					"data":d_fall_diar,
					zIndex: 1,
					color: Highcharts.getOptions().colors[index_color],
					marker: {
			            enabled: false
			        }
				};
				index_color++;
				var s_cont_acum={
					"id":pais+"_s_cont_acum_"+index_color,
					"name": pais+" (C)",
					type: 'area',
					yAxis:1,
					"data":d_cont_acum,
					lineWidth: 0,
					color: Highcharts.getOptions().colors[index_color],
			        fillOpacity: 0.5,
			        zIndex: 0,
			        marker: {
			            enabled: false
			        }
				};
				var s_cont_diar={
					"id":pais+"_s_cont_diar_"+index_color,
					"name": pais+" (C)",
					type: 'spline',
					yAxis:0,
					"data":d_cont_diar,
					zIndex: 1,
					color: Highcharts.getOptions().colors[index_color],
					marker: {
			            enabled: false
			        }
				};
				
				
				if(fall_acum){
					series.push(s_fall_acum);
					s_fall_diar["linkedTo"]=s_fall_acum.id;
				}
				if(cont_acum){
					series.push(s_cont_acum);
					s_cont_diar["linkedTo"]=s_cont_acum.id;
				}
				
				if(fall_diar){
					series.push(s_fall_diar);		
				}
				
				if(cont_diar){
					series.push(s_cont_diar);		
				}
				
				
				index_color++;
			});
			Highcharts.chart('graph', {
			    chart: {
			        zoomType: 'xy',
			        spacingTop: 6,
			        spacingRight: 0,
			        spacingBottom: 0,
			        spacingLeft: 0
			    },
			    title: {
			        text: ''
			    },
			    xAxis: {
			        categories: categorias_lbl
			    },
			    yAxis:[
			    	{
			        	title: {
			            	text: 'Diarios'
			        	}
			    	},
			    	{
			        	title: {
			            	text: 'Acumulados'
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
			$("#MainForm").height($(window).innerHeight()-$("#MainNav").height()-18);
			$("#graph").height($("#MainForm").height());
			$(".table-responsive").height($("#MainForm").height()-$("#infoTable").height()-20);
		}
		
		$( document ).ready(function() {
    		$(".cb_graph input[type='checkbox']").click(function(){
    			ShowGraph();
    		});
    		
    		$(window).resize(function() {
			  redimensionar();
			});
			redimensionar();
			$("button[data-tipo='spain']").trigger("click");
		});
	</script>
</html>
