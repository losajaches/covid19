var ZONA="";
var MAINDATA=null;
var GRAFICAS={};

function LoadData(event,pagina,subzona){
	if(!is_null(event)){
		event.preventDefault();
	}
	
	ZONA= isset(subzona) ? subzona : "";
	if(ZONA!=""){
		$("#ButtonBack").removeClass("invisible");	
	}else{
		$("#ButtonBack").addClass("invisible");
	}
	if(pagina==1){
		MAINDATA=null;
	}
	
	$("#BtnSup").html("<i class='fas fa-spinner fa-spin fg_color_sys_light fa-2x mr-2'></i>");
	
	$.post("ds/datasource.json.php", {func:"LoadData",params:{"field_order":"fallecidos desc,pais","zona":ZONA,"pagina":pagina,"num_regs":(ZONA=="")?15:0,"factor_estimacion":1.6}},			
		function(r){
			if(r.result=="ok"){
				var i=0;
				if(MAINDATA==null){
					MAINDATA=r;
				}else{
					i=MAINDATA.data.length;
					$.each(r.data,function(i,v){
						MAINDATA.data.push(v);	
					});
				}
				console.log(MAINDATA);
				ShowDataTable(r,i);
				if(pagina==1){
					ShowGraph();
				}
			}else{
				ShowError(r.msg);
			}
		},"json").fail(function(jqXHR, textStatus, error) {
			console.log(error);
			console.log(textStatus);
			console.log(jqXHR);
		}).always(function() {
		    $("#BtnSup").html("");
		}
	);
}

function DisplayNumber(number,decimal){
	if(number==0){
		return "&nbsp;";	
	}else if (number<0){
		return "<span title='La fuente corrige datos anteriores'>"+number_format(number,0,",",".")+"<sup>[?]</sup></span>";
	}else{
		return number_format(number,isset(decimal)?decimal:0,",",".");
	}
}

function ShowDataTable(datos,i_inicial){
	var fechas=datos.fechas.reverse();
	if(datos.pagina==1){	
		$("#dataTable thead").html("");
		$("#dataTable tbody").html("");	
		
		$("#dataTableTotal thead").html("");
		$("#dataTableTotal tbody").html("");	
		$("#dataTableTotal thead").append(sprintf("<th field='pais'>%s</th>",(datos.zona=="") ? "País/Zona" : datos.zona));
		$("#dataTableTotal thead").append("<th>Totales</th>");
		$("#dataTableTotal thead").append("<th class='text-right'>%C</th>");
		$("#dataTableTotal thead").append("<th class='text-right'>%E</th>");
		$.each(fechas,function(i,fecha){
			var d=new Date(fecha).toISOString();
			$("#dataTable thead").append(sprintf("<th class='text-center'>%s<small>/%s</small></th>",d.substr(8,2),d.substr(5,2)));
		});
		
		//linea de totales
		
		$("#dataTableTotal tbody").append("<tr>%s%s%s</tr>",
			"<td class='totales text-left'><h1>Contagiados</h1><h2>Estimados</h2><h3>Curados</h3><h4>Fallecidos</h4></td>",
			sprintf("<td class='totales'><h1>%s</h1><h2>%s</h2><h3>%s</h3><h4>%s</h4></td>",DisplayNumber(datos.globales.c),DisplayNumber(datos.globales.e),DisplayNumber(datos.globales.s),DisplayNumber(datos.globales.f)),
			sprintf("<td class='totales'><h1>%s</h1><h2>%s</h2><h3>%s</h3><h4>%s</h4></td>",DisplayNumber(datos.globales.fc_c,1),DisplayNumber(datos.globales.fc_e,1),DisplayNumber(datos.globales.fc_s,1),DisplayNumber(datos.globales.fc_f,1)),
			sprintf("<td class='totales'><h1>%s</h1><h2>%s</h2><h3>%s</h3><h4>%s</h4></td>",DisplayNumber(datos.globales.fe_c,1),DisplayNumber(datos.globales.fe_e,1),DisplayNumber(datos.globales.fe_s,1),DisplayNumber(datos.globales.fe_f,1))
		);	
		
		html="<tr>";
		$.each(fechas,function(i,fecha){
			d=isset(datos.globales.d[fecha]) ? datos.globales.d[fecha] : {"c":0,"e":0,"s":0,"s":0};
			html+=sprintf("<td><h1>%s</h1><h2>%s</h2><h3>%s</h3><h4>%s</h4></td>",DisplayNumber(d.c),DisplayNumber(d.e),DisplayNumber(d.s),DisplayNumber(d.f));
		});
		html+="</tr>";
		$("#dataTable tbody").append(html);
	}
	
	$.each(datos.data,function(i,v){
		
		$("#dataTableTotal tbody").append("<tr>%s%s%s%s</tr>",
			sprintf("<td class='text-left font-weight-bold'><input type='checkbox' %s data-id='%s' onclick='ShowGraph();'>%s%s</td>",
				((datos.pagina==1)&&(i<2))?"checked":"",
				i_inicial+i,
				(v.z>1)?sprintf("<a href='#' onclick='LoadData(event,1,\"%s\");'>%s</a>",v.p,v.p): v.p,
				(v.p=="Spain") ? " <span class='leyenda'><a href='#leyenda'>(1)</a></span>" : ( (v.p=="España MS") ? " <span class='leyenda'><a href='#leyenda'>(2)</a></span>" : "")
			),
			sprintf("<td class='totales'><h1>%s</h1><h2>%s</h2><h3>%s</h3><h4>%s</h4></td>",DisplayNumber(v.c),DisplayNumber(v.e),DisplayNumber(v.s),DisplayNumber(v.f)),
			sprintf("<td class='totales'><h1>%s</h1><h2>%s</h2><h3>%s</h3><h4>%s</h4></td>",DisplayNumber(v.fc_c,1),DisplayNumber(v.fc_e,1),DisplayNumber(v.fc_s,1),DisplayNumber(v.fc_f,1)),
			sprintf("<td class='totales'><h1>%s</h1><h2>%s</h2><h3>%s</h3><h4>%s</h4></td>",DisplayNumber(v.fe_c,1),DisplayNumber(v.fe_e,1),DisplayNumber(v.fe_s,1),DisplayNumber(v.fe_f,1))
		);	
		
		html="<tr>";
		$.each(fechas,function(i,fecha){
			d=v.d[fecha];
			html+=sprintf("<td><h1>%s</h1><h2>%s</h2><h3>%s</h3><h4>%s</h4></td>",DisplayNumber(d.c),DisplayNumber(d.e),DisplayNumber(d.s),DisplayNumber(d.f));
		});
		html+="</tr>";
		$("#dataTable tbody").append(html);
	});
	
	$(".datos h3.titulo").html("Datos");
	if((datos.paginas_totales==1)||(datos.pagina==datos.paginas_totales)){
		if(ZONA==""){
			$("#data-footer").html("");
		}else{
			$("#data-footer").html("<button class='btn btn-sm btn-primary' onclick='LoadData(event,1,\"\");'>Ir a datos globales</button>");	
			$(".datos h3.titulo").html("<a class='btn btn-sm btn-outline-secondary' onclick='LoadData(event,1,\"\");'><i class='fas fa-backward'></i></a> Datos");
		}
	}else{
		$("#data-footer").html(sprintf("<small class='text-muted'>Mostrando %s de %s</small><br><button class='btn btn-sm btn-primary' onclick='LoadData(event,%s,\"%s\");'>Cargar siguientes registros</button>",
			$("#dataTable tbody tr").length-1,
			datos.regs_totales,
			datos.pagina+1,
			ZONA
		));
	}
}

function GetIndicePrimerValorNoZero(data_x,data_y){
	for(var i=0;i<data_x.length;i++){
		for(var j=0;j<data_y.length;j++){
			if(data_y[j].data[i]!=0){
				return i;
			}	
		}
	}
	return 0;
}

function NormalizarVectores(data_x,data_y){
	var index=GetIndicePrimerValorNoZero(data_x,data_y);
	for(var i=0;i<index;i++){
		data_x.shift();	
		for(var j=0;j<data_y.length;j++){
			data_y[j].data.shift();		
		}
	}
}

function ShowGraph(gid){
	if(isset(gid)){
		var grafica=gid;
	}else{
		var grafica=$(".graph",$("#graphCarousel .carousel-item.active")).attr("id");
	}
	
	$("#"+grafica).html("");
	if(grafica=="graph_1"){
		LoadGraphFallecidosAcumulados();	
	}else if(grafica=="graph_2"){
		LoadGraphFallecidosVsCurados("graph_2","Datos acumulados",true);	
	}else if(grafica=="graph_3"){
		LoadGraphFallecidosVsCurados("graph_3","Datos diarios",false);	
	}else if(grafica=="graph_4"){
		LoadGraphEstimadosFrenteAcumulado("graph_4","f","Fallecidos en la última semana frente a fallecidos totales");	
	}else if(grafica=="graph_5"){
		LoadGraphEstimadosFrenteAcumulado("graph_5","c","Contagios en la última semana frente a contagios totales");	
	}else if(grafica=="graph_6"){
		LoadGraphDatosSemanales("graph_6","f","Fallecidos semanales");	
	}
}



function LoadGraphFallecidosAcumulados(){
	var data_x=[];
	$.each(MAINDATA.fechas_display,function(i,v){
		data_x.push(v);	
	});
	var series_fallecidos=[];
	//paises seleccionados
	$("#dataTableTotal tbody input:checked").each(function(i,ui){
		var index=$(ui).data("id");
		var serie={name:MAINDATA.data[index].p,data:[]};
		$.each(MAINDATA.data[index].g["af"],function(i,v){
			serie.data.push({y:v});	
		});
		
		serie.data[serie.data.length-1]["dataLabels"]={
            enabled: true,
            formatter:function(){
            	return this.series.name;
            }
        };
        
		series_fallecidos.push(serie);
	});
	
	NormalizarVectores(data_x,series_fallecidos);
	
	Highcharts.chart("graph_1", {
		chart: {
	        type: "spline",
	        backgroundColor: 'transparent',
	        zoomType: 'xy',
	        spacingTop: 6,
			spacingRight: 0,
			spacingBottom: 2,
			spacingLeft: 0,
			animation: false
	    },
	    title: {text: "Fallecidos"},
	    subtitle: {text:"Escala logarítmica"},
	    xAxis: {
	        categories: data_x
	    },
	    yAxis: {
	        type:"logarithmic",
	        title:{
	        	enabled:false
	        },
	        margin:0
	    },
	    plotOptions: {
            series: {
                marker: {
                    enabled:false
                }/*,
                fillOpacity:0.4,
				lineWidth:0*/
            }
        },
	    series:series_fallecidos
	});
	
}

function LoadGraphFallecidosVsCurados(id_div,subtitulo,acumulados){
	var data_x=[];
	$.each(MAINDATA.fechas_display,function(i,v){
		data_x.push(v);	
	});
	var series_fallecidos=[];
	//paises seleccionados
	var index_color=0;
	$("#dataTableTotal tbody input:checked").each(function(i,ui){
		var index=$(ui).data("id");
		var serie_f={
			name:MAINDATA.data[index].p+" (F)",
			color: Highcharts.getOptions().colors[index_color],
			data:[]
		};
		var serie_s={
			name:MAINDATA.data[index].p+" (S)",
			color: Highcharts.getOptions().colors[index_color],
			dashStyle: 'shortdot',
			data:[]
		};
		if(acumulados){
			$.each(MAINDATA.data[index].g["af"],function(i,v){
				serie_f.data.push({y:v});	
			});
			$.each(MAINDATA.data[index].g["as"],function(i,v){
				serie_s.data.push({y:v});	
			});
		}else{
			$.each(MAINDATA.data[index].g["f"],function(i,v){
				serie_f.data.push({y:v});	
			});
			$.each(MAINDATA.data[index].g["s"],function(i,v){
				serie_s.data.push({y:v});	
			});
		}
		
		serie_f.data[serie_f.data.length-1]["dataLabels"]={
            enabled: true,
            formatter:function(){
            	return this.series.name;
            }
        };
        
        serie_s.data[serie_s.data.length-1]["dataLabels"]={
            enabled: true,
            formatter:function(){
            	return this.series.name;
            }
        };
        
		series_fallecidos.push(serie_f);
		series_fallecidos.push(serie_s);
		index_color++;
	});
	
	NormalizarVectores(data_x,series_fallecidos);
	
	Highcharts.chart(id_div, {
		chart: {
	        type: "spline",
	        backgroundColor: 'transparent',
	        zoomType: 'xy',
	        spacingTop: 6,
			spacingRight: 0,
			spacingBottom: 2,
			spacingLeft: 0,
			animation: false
	    },
	    title: {text: "Fallecidos (F) vs Curados (S)"},
	    subtitle: {text:subtitulo},
	    xAxis: {
	        categories: data_x
	    },
	    yAxis: {
	    	title:{
	        	enabled:false
	        },
	        margin:0
	    },
	    plotOptions: {
            series: {
                marker: {
                    enabled:false
                }
            }
        },
	    series:series_fallecidos
	});
	
}


function LoadGraphEstimadosFrenteAcumulado(id_div,variable,titulo){
	var series_data=[];
	//paises seleccionados
	index_color=0;
	$("#dataTableTotal tbody input:checked").each(function(i,ui){
		var index=$(ui).data("id");
		var serie={
			name:MAINDATA.data[index].p,
			color: Highcharts.getOptions().colors[index_color],
			data:[]
		};
		
		for(var i=0;i<MAINDATA.data[index].g[variable].length;i++){
			if(i>=7){
				var ult_sem=0;
				for(var j=0;j<7;j++){
					ult_sem+=MAINDATA.data[index].g[variable][i-j];	
				}
				serie.data.push({
					x:MAINDATA.data[index].g["a"+variable][i],
					y:ult_sem
				});	
			}	
			
		}
		serie.data[serie.data.length-1]["dataLabels"]={
                enabled: true,
                formatter:function(){
                	return this.series.name;
                }
            };
		series_data.push(serie);
		index_color++;
	});
	
	Highcharts.chart(id_div, {
		chart: {
	        type: "spline",
	        backgroundColor: 'transparent',
	        zoomType: 'xy',
	        spacingTop: 6,
			spacingRight: 0,
			spacingBottom: 2,
			spacingLeft: 0,
			animation: false
	    },
	    title: {text: titulo},
	    subtitle: {text:"Otra forma de ver cuándo empieza a caer la curva"},
	    xAxis: {
	        
	    },
	    yAxis: {
	    	title:{
	        	enabled:false
	        },
	        margin:0	
	    },
	    plotOptions: {
            series: {
                marker: {
                    enabled:false
                }
            }
        },
	    series:series_data
	});
	
}

function LoadGraphDatosSemanales(id_div,variable,titulo){
	var series_data=[];
	//paises seleccionados
	index_color=0;
	$("#dataTableTotal tbody input:checked").each(function(i,ui){
		var index=$(ui).data("id");
		var serie={
			name:MAINDATA.data[index].p,
			color: Highcharts.getOptions().colors[index_color],
			data:[],
			pepe:{}
		};
		
		var index_inicio=MAINDATA.data[index].g[variable].length - 7 * Math.floor(MAINDATA.data[index].g[variable].length / 7);
		
		var contador=0;
		var suma=0;
		for(var i=index_inicio;i<MAINDATA.data[index].g[variable].length;i++){
			contador++;
			suma+=MAINDATA.data[index].g[variable][i];
			if(contador==7){
				serie.data.push({y:suma});
				suma=0;
				contador=0;
			}	
			
		}
		serie.data[serie.data.length-1]["dataLabels"]={
                enabled: true,
                formatter:function(){
                	return this.series.name;
                }
            };
		series_data.push(serie);
		index_color++;
	});

	Highcharts.chart(id_div, {
		chart: {
	        type: "spline",
	        backgroundColor: 'transparent',
	        zoomType: 'xy',
	        spacingTop: 6,
			spacingRight: 0,
			spacingBottom: 2,
			spacingLeft: 0,
			animation: false
	    },
	    title: {text: titulo},
	    xAxis: {
	        
	    },
	    yAxis: {
	    	title:{
	        	enabled:false
	        },
	        margin:0	
	    },
	    plotOptions: {
            series: {
                marker: {
                    enabled:false
                }
            }
        },
	    series:series_data
	});
	
}
