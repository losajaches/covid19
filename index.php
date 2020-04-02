<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-title" content="COVID-19">
    <meta name="description" content="">
    <meta name="author" content="PpSoft">
	<link rel="icon" type="image/png" href="icon.png"/>
	<link rel="apple-touch-icon" sizes="96x96" href="icon.png">
    
    <title>COVID-19</title>
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
	<link rel="stylesheet" href="css/main.css?v=<?php echo filemtime("css/main.css");?>">
  </head>
  <body>
    <header>
		<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
			<a class="navbar-brand h-100 p-0 titulo" href="#">
				<img src="icon.png" class="d-inline-block h-100" style="vertical-align:top;">
				<div class="d-inline-block h-100 fg_color_sys font-weight-bold text-right">
					<span class="d-block ">COVID-19</span>
					<small class="d-block fg_color_sys_light">ESTADISTICAS</small>
				</div>
			</a>
			
				<ul class="navbar-nav mr-auto">
					
				</ul>
				<form id='BtnSup' class="form-inline mt-2 mt-md-0">
					
				</form>
		
		</nav>
	</header>

	<main role="main">
		<!-- CARRUSEL DE GRAFICAS -->
		<div id="graphCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
		    <ol class="carousel-indicators">
		      <li data-target="#graphCarousel" data-slide-to="0" class="active"></li>
		      <li data-target="#graphCarousel" data-slide-to="1"></li>
		      <li data-target="#graphCarousel" data-slide-to="2"></li>
		      <li data-target="#graphCarousel" data-slide-to="3"></li>
		      <li data-target="#graphCarousel" data-slide-to="4"></li>
		      <li data-target="#graphCarousel" data-slide-to="5"></li>
		      <li data-target="#graphCarousel" data-slide-to="6"></li>
		    </ol>
		    <div class="carousel-inner">
		      <div class="carousel-item active" >
		      	<div id='graph_0' class='graph border'></div>
		      </div>
		      <div class="carousel-item">
		        <div id='graph_1' class='graph'></div>
		      </div>
		      <div class="carousel-item">
		        <div id='graph_2' class='graph'></div>
		      </div>
		      <div class="carousel-item">
		        <div id='graph_3' class='graph'></div>
		      </div>
		      <div class="carousel-item">
		        <div id='graph_4' class='graph'></div>
		      </div>
		      <div class="carousel-item">
		        <div id='graph_5' class='graph'></div>
		      </div>
		      <div class="carousel-item">
		        <div id='graph_6' class='graph'></div>
		      </div>
		    </div>
		    <a class="carousel-control-prev" href="#graphCarousel" role="button" data-slide="prev">
		      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
		      <span class="sr-only">Anterior</span>
		    </a>
		    <a class="carousel-control-next" href="#graphCarousel" role="button" data-slide="next">
		      <span class="carousel-control-next-icon" aria-hidden="true"></span>
		      <span class="sr-only">Siguiente</span>
		    </a>
		</div>
		
		<!-- TABLA DE DATOS -->
		<div class="container datos mb-3">
			<h3 class='titulo'>Datos</h3>
			<small class='text-muted'>Tasa actual de fallecimientos estimados en 1.6% (Coreal del Sur). <b>%C</b> factor sobre contagios reportados. <b>%E</b> factor sobre contagios estimados.</small>
			<div class='d-flex flex-row'>
				<div>
					<table id="dataTableTotal" class="table table-bordered table-sm table-striped table-hover">
						<thead></thead>
						<tbody></tbody>
					</table>
				</div>
				<div class="table-responsive">
					<table id="dataTable" class="table table-bordered table-sm table-striped table-hover">
						<thead></thead>
						<tbody></tbody>
					</table>
				</div>	
			</div>
			<div id='data-footer' class='text-center'></div>
		</div>

		<!-- EXPLICACION DE FUENTES DE DATOS-->
		<a name='leyenda'>
		<div class="container fuentes">
			<div class="row">
				<div class="col-lg-4 text-center">
					<div class="icono"><i class="fas fa-database"></i></div>
					<h3>JHU CSSE</h3>
					<p class='text-justify'>Los datos globales son obtenidos de los publicados por <a href="https://systems.jhu.edu/research/public-health/ncov/" target="_blank">Johns Hopkins University Center for Systems Science and Engineering (JHU CSSE)</a> y se actualizan diariamente a las 00:00.</p>
					<p><a class="btn btn-secondary" href="https://github.com/CSSEGISandData/COVID-19" target="_blank" role="button">Acceder &raquo;</a></p>
				</div>
				
				<div class="col-lg-4 text-center">
					<div class="icono"><i class="fas fa-landmark"></i></div>
					<h3>Ministerio de sanidad</h3>
					<p class='text-justify'>Los datos concernientes a <b>España <span class='leyenda'>(2)</span></a></b> y que en la tabla aparecen diferenciados como <b>España MS <span class='leyenda'>(1)</span></b>, frente a <b>Spain</b> de los datos globales, son publicados por el <a href="https://www.isciii.es/Paginas/Inicio.aspx" target="_blank">Instituto de Salud Carlos III</a> y se actualizan diariamente a las 21:00, de ahí su diferencia con los globales.</p>
					<p><a class="btn btn-secondary" href="https://covid19.isciii.es/" role="button" target="_blank">Acceder &raquo;</a></p>
				</div>
				
				<div class="col-lg-4 text-center">
					<div class="icono"><i class="fab fa-github"></i></div>
					<h3>Código Fuente</h3>
					<p class='text-justify'>Los códigos fuentes de esta utilidad estan disponible para su descarga en el repositorio <a href="https://github.com" target="_blank">Github</a>. Si tiene alguna consulta no dude en ponerse en contacto.</p>
					<p><a class="btn btn-secondary" href="https://github.com/losajaches/covid19" target="_blank" role="button">Acceder &raquo;</a></p>
				</div>
				
			</div>
		</div>

    	<!-- EXPLICACION DE LOS DATOS Y CALCULOS -->
		<div class="container">
	    	<hr class="explicaciones-divider">
	
		    <div class="row explicaciones">
		      <div class="col-md-7">
		        <h3>Contagios estimados. <small class="text-muted">El caso de Corea del Sur.</small></h3>
		        <p class="text-justify">
		        	En este baile de datos y formas dispares de contabilización, Corea del Sur se presenta como el país que más test ha realizado reportando, de esa manera, la tasa de letalidad más fiable. Actualmente ese valor está en torno al 1,6%.<br>
		        	Este artículo del 28 de Marzo aparecido en <b></b>El Confidencial</b> lo explica detalladamente <a href='https://www.elconfidencial.com/mundo/europa/2020-03-28/coronavirus-italia-tasa-letalidad-infectados-mito_2522695/' target='_blank'> "Italia no tiene la tasa de letalidad al 10%. Lo que tiene son 500.000 infectados."</a>
		        </p>
		      </div>
		      <div class="col-md-5">
		        <img src='https://www.ecestaticos.com/imagestatic/clipping/d10/164/d10164ea7059b7be8063a8ae8b11b72d/italia-no-tiene-la-tasa-de-letalidad-al-10-lo-que-tiene-son-500-000-infectados.jpg?mtime=1585342724' class='w-100'>
		        <small>La UCI del Hospital San Giovanni Bosco en Turín, Italia. (Reuters)</small>
		      </div>
		    </div>
	
	    	<hr class="featurette-divider">
			
			<div class="row explicaciones">
		      <div class="col-md-7 order-md-2">
		        <h3>Datos diarios frente a datos acumulados. <small class="text-muted">Buscando el punto de inflexión.</small></h3>
		        <p class="text-justify">
		        	Este video es una colaboración de <a href='https://www.minutephysics.com/' target='_blank'>Minuto de Física</a> con <a href='https://aatishb.com/covidtrends/' target='_blank'>Aatish Bhatia</a> sobre cómo ver el punto de inflexión de COVID-19.
		        	Se presenta una mejor manera de representar los casos de coronavirus de COVID-19 utilizando una escala logarítmica en el "espacio de fase", 
		        	trazando la tasa de crecimiento frente a los casos acumulados, en lugar de cualquiera de estos contra el tiempo.
		        </p>
		      </div>
		      <div class="col-md-5 order-md-1">
		        <iframe width="100%"  height="345" src="https://www.youtube.com/embed/54XLXg4fYsc" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		        <small>MinutoDeFísica: Como saber si estamos venciendo al COVID-19</small>
		      </div>
		    </div>
		    
			<hr class="featurette-divider">
		</div>
		<div class="container">
			<footer class="row mr-4">
				<div class="col-md-8 text-center text-md-left">
					<a href="https://www.ppsoft.org" target='_blank'>ppsoft.org</a> // <b>Jose Manuel Rodríguez Sánchez, 2020</b> <small class="text-muted">/ Durante en confinamiento.</small><br>
					
		    	</div>
		    	<div class="col-md-4 text-center text-md-right">
		    		<a href="#">Volver arriba</a></p>
		    	</div>
			</footer>
		</div>
	</main>   
    
	<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
	<script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/modules/accessibility.js"></script>
	<script src="js/funciones.js"></script>
	<script src="js/main.js?v=<?php echo filemtime("js/main.js");?>"></script>
	<script>
		$( document ).ready(function() {
			$('#graphCarousel').on('slide.bs.carousel', function (event) {
				ShowGraph($(".graph",$(event.relatedTarget)).attr("id"),false);
			});
    		LoadData(null,1,"");
		});
	</script>
	
</html>
