<div class="container margin_top">
  <div class="page-header">
    <h1>Panel de Looks</h1>
  </div>
     <!-- SUBMENU ON -->
  
  <div class="navbar margin_top">
  <div class="navbar-inner">
    <ul class="nav">
  	<li><a href="#" class="nav-header">CATALOGOS POR:</a></li>
      	<li class="active" ><a title="Looks" href="">Looks</a></li>
      	<li><a title="Productos" href="<?php echo Yii::app()->baseUrl."/controlpanel/productos"; ?>">Productos</a></li>
    </ul>
  </div>
</div>
 
<?php

$ventas = Orden::model()->count();
$enviados = Orden::model()->countByAttributes(array('estado'=>4)); // enviados

$sql = "select sum(cantidad) from tbl_orden a, tbl_orden_has_productotallacolor b where a.estado = 4 and a.id = b.tbl_orden_id ";
$productos_enviados = Yii::app()->db->createCommand($sql)->queryScalar();

	// el total de ordenes pagas o enviadas
	$totalpromedio = Orden::model()->countByAttributes(array(), 'estado = :valor1 or estado = :valor2 or estado = :valor3', array(':valor1'=>3,':valor2'=>4,':valor3'=>8));
	// cada una de esas ordenes
	$ordenes = Orden::model()->findAllByAttributes(array(), 'estado = :valor1 or estado = :valor2 or estado = :valor3', array(':valor1'=>3,':valor2'=>4,':valor3'=>8));
	$sumatoria = 0;
	$impuestos = 0;
	
	foreach($ordenes as $uno)
	{
		$sumatoria = $sumatoria + $uno->total;	
		$impuestos = $impuestos + $uno->iva;
	}

$a = substr($sumatoria,0,-1);
$b = substr($impuestos,0,-1);

/* forma anterior */	
$sql = "SELECT sum(total) as total FROM tbl_orden";
$dinero_ventas = Yii::app()->db->createCommand($sql)->queryScalar();

if($totalpromedio != 0)
	$promedio = $sumatoria / $totalpromedio;
else
	$promedio = 0;

$c = substr($promedio,0,-1);

	$pago_pendientes = 0;
	$pend = Orden::model()->findAllByAttributes(array('estado'=>1));
	$envios = 0;
	
	foreach($pend as $cada){
		$pago_pendientes = $pago_pendientes + $cada->total;
		$envios = $envios + $cada->envio + $cada->seguro;
	}

$d = substr($pago_pendientes,0,-1);

$pendiente = Orden::model()->countByAttributes(array('estado'=>1));

if($pendiente != 0)
	$e = $pago_pendientes / $pendiente;
else 
	$e = 0;

$f = substr($e,0,-1);
$g = substr($envios,0,-1);

$sql = "select sum(cantidad) from tbl_orden a, tbl_orden_has_productotallacolor b where a.estado = 1 and a.id = b.tbl_orden_id";
$productos_pendientes = Yii::app()->db->createCommand($sql)->queryScalar();

?>  
  
  <!-- SUBMENU OFF -->
  <div class="row">
    <div class="span12">
    	<div class="bg_color3 margin_bottom_small padding_small box_1"> 
    		
    		 <ul class="nav nav-tabs" id="myTab">
              <li class="active"><a href="#mes" data-toggle="tab">Mensual</a></li>
              <li><a href="#semana" data-toggle="tab">Semanal</a></li>
              <li><a href="#dia" data-toggle="tab">Diario</a></li>
            </ul>
            
            <div class="tab-content">
            	
              <div class="tab-pane active" id="mes" >
                <div class="clearfix" style="width: 100%;height: 100%;">   
                 	<?php
 
					$ya = date('Y-m-d', strtotime('now'));
					      	
					$sql = "select fecha from tbl_orden limit 1";
					$primera = Yii::app()->db->createCommand($sql)->queryScalar();
					
					// de dos meses a un mes como primer punto de ventas	      	
					$sql = "select sum(total) from tbl_orden where fecha between '".date('Y-m-d', strtotime($ya. ' -2 month'))."' and '".date('Y-m-d', strtotime($ya. ' -1 month'))."' ";
					$monthago = (int) Yii::app()->db->createCommand($sql)->queryScalar();
					
					//$monthago = Yii::app()->numberFormatter->formatDecimal($monthago);
					
					/*
					$sql = "select count(*) from tbl_orden where fecha between '".$primera."' and '".date('Y-m-d H:i:s', strtotime($ya. ' -1 month'))."' ";
					$monthago = (int) Yii::app()->db->createCommand($sql)->queryScalar();
					*/
					
					// de un mes hasta hoy		
					$sql = "select sum(total) from tbl_orden where fecha between '".date('Y-m-d', strtotime($ya. ' -1 month'))."' and '".$ya."' ";
					$ahora = (int) Yii::app()->db->createCommand($sql)->queryScalar();
					
					//$ahora = Yii::app()->numberFormatter->formatDecimal($ahora);
					
					$uno = date('d-m-Y', strtotime($ya. ' -1 month'));
					$dos = date('d-m-Y', strtotime('now'));
					
					      	$this->Widget('ext.highcharts.HighchartsWidget', array(
							   'options'=>array(
							   	  'chart' => array('type' =>'areaspline','width'=>1100), // column, area, line, spline, areaspline, bar, pie, scatter
							      'title' => array('text' => 'Ventas en el último mes.'),
							      'xAxis' => array(
							         'categories' => array($uno, $dos)
							      ),
							      'yAxis' => array(
							         'title' => array('text' => 'Bs.')
							      ),
							      'series' => array(
							        // array('name' => 'Jane', 'data' => array(1, 0, 4)),
							         array('name' => 'Total', 'data' => array($monthago,$ahora))
							      )
							   )
							));
					
					
					?>
        		</div>
              </div>
              
              <div class="tab-pane" id="semana">
                <div class="clearfix">
					<?php
					
					$ya = date('Y-m-d', strtotime('now'));
					      	
					$sql = "select fecha from tbl_orden limit 1";
					$primera = Yii::app()->db->createCommand($sql)->queryScalar();
					      	
					// un mes y una semana a un mes
					      	
					$sql = "select sum(total) from tbl_orden where fecha between '".date('Y-m-d', strtotime($ya. ' -1 month -1 week'))."' and '".date('Y-m-d', strtotime($ya. ' -1 month'))."' ";
					$cuatrosem = (int) Yii::app()->db->createCommand($sql)->queryScalar();
					
					//  a 3 semanas
					      	
					$sql = "select sum(total) from tbl_orden where fecha between '".date('Y-m-d', strtotime($ya. ' -1 month'))."' and '".date('Y-m-d', strtotime($ya. ' -3 week'))."' ";
					$tressem = (int) Yii::app()->db->createCommand($sql)->queryScalar();
					
					// a 2 semanas
					      	
					$sql = "select sum(total) from tbl_orden where fecha between '".date('Y-m-d', strtotime($ya. ' -3 week'))."' and '".date('Y-m-d', strtotime($ya. ' -2 week'))."' ";
					$dossem = (int) Yii::app()->db->createCommand($sql)->queryScalar();
					
					// a una semana
					      	
					$sql = "select sum(total) from tbl_orden where fecha between '".date('Y-m-d', strtotime($ya. ' -2 week'))."' and '".date('Y-m-d', strtotime($ya. ' -1 week'))."' ";
					$unosem = (int) Yii::app()->db->createCommand($sql)->queryScalar();
					
					// de la primera venta hasta hoy
					
					$sql = "select sum(total) from tbl_orden where fecha between '".date('Y-m-d', strtotime($ya. ' -1 week'))."' and '".$ya."' ";
					$ahora = (int) Yii::app()->db->createCommand($sql)->queryScalar(); 	
					
					
					$uno = date('d-m-Y', strtotime($ya. ' -1 month'));
					$dos = date('d-m-Y', strtotime('now'));
					$tres = date('d-m-Y', strtotime('-3 week'));
					$cuatro = date('d-m-Y', strtotime('-2 week'));
					$cinco = date('d-m-Y', strtotime('-1 week'));
					
					
						$this->Widget('ext.highcharts.HighchartsWidget', array(
							'options'=>array(
								'chart' => array('type' =>'areaspline','width'=>1100), // column, area, line, spline, areaspline, bar, pie, scatter
								'title' => array('text' => 'Ventas por semanas'),
								'xAxis' => array(
									'categories' => array($uno, $tres, $cuatro, $cinco, $dos)
									),
								'yAxis' => array(
										'title' => array('text' => 'Bs.')
									),
								'series' => array(
										array('name' => 'Total', 'data' => array($cuatrosem, $tressem, $dossem, $unosem, $ahora))
									)
							)
						));
						
						
						?>
              	</div>
              </div>
              
              <div class="tab-pane" id="dia">
	            <div class="clearfix">
	            	<?php
					
					$ya = date('Y-m-d', strtotime('now'));
					      	
					$sql = "select fecha from tbl_orden limit 1";
					$primera = Yii::app()->db->createCommand($sql)->queryScalar();
					       	
					// un mes y un dia	
					$sql = "select sum(total) from tbl_orden where fecha between '".date('Y-m-d', strtotime($ya. ' -1 month -1 day'))."' and '".date('Y-m-d', strtotime($ya. ' -1 month'))."' ";
					$treintaun = (int) Yii::app()->db->createCommand($sql)->queryScalar();
					
					// de aqui en adelante diario
					$sql = "select sum(total) from tbl_orden where fecha between '".date('Y-m-d', strtotime($ya. ' -1 month'))."' and '".date('Y-m-d', strtotime($ya. '-1 month +1 day'))."' ";
					$treinta = (int) Yii::app()->db->createCommand($sql)->queryScalar();
					
					$todos = Array();
					$fecha = Array();
					
					for($i=30 ; $i>0 ; $i--)
					{		
						$sql = "select sum(total) from tbl_orden where fecha between '".date('Y-m-d', strtotime($ya. '-'.$i.' day'))."' and '".date('Y-m-d', strtotime($ya. '-'.($i-1).' day'))."' ";
						array_push($todos, (int) Yii::app()->db->createCommand($sql)->queryScalar());
						
						//echo $sql."<br>";
						
						array_push($fecha,date('d-m', strtotime($ya. '-'.($i-1).' day')));
					}				
					
					$sql = "select sum(total) from tbl_orden where fecha between '".date('Y-m-d', strtotime($ya. ' -1 day'))."' and '".$ya."' ";
					$ahora = (int) Yii::app()->db->createCommand($sql)->queryScalar(); 	
					
					$uno = date('d-m', strtotime($ya. ' -1 month'));
					$tres = date('d-m', strtotime('-1 month +1 day'));

						$this->Widget('ext.highcharts.HighchartsWidget', array(
							'options'=>array(
								'chart' => array('type' =>'areaspline','width'=>1100), // column, area, line, spline, areaspline, bar, pie, scatter
								'title' => array('text' => 'Ventas por día en el mes.'),
								'xAxis' => array(
									'categories' => array($uno,$tres,$fecha[0],$fecha[1],$fecha[2],$fecha[3],$fecha[4],$fecha[5],$fecha[6],$fecha[7],$fecha[8],$fecha[9],$fecha[10],
															$fecha[11],$fecha[12],$fecha[13],$fecha[14],$fecha[15],$fecha[16],$fecha[17],$fecha[18],$fecha[19],$fecha[20],
															$fecha[21],$fecha[22],$fecha[23],$fecha[24],$fecha[25],$fecha[26],$fecha[27],$fecha[28],$fecha[29])
									),
								'yAxis' => array(
										'title' => array('text' => 'Bs.')
									),
								'series' => array(
									array('name' => 'Total', 'data' => array($treintaun, $treinta,$todos[0],$todos[1],$todos[2],$todos[3],$todos[4],$todos[5],$todos[6],$todos[7],$todos[8],$todos[9],$todos[10]
																			,$todos[11],$todos[12],$todos[13],$todos[14],$todos[15],$todos[16],$todos[17],$todos[18],$todos[19],$todos[20]
																			,$todos[21],$todos[22],$todos[23],$todos[24],$todos[25],$todos[26],$todos[27],$todos[28],$todos[29]))
								)
							)
						));
						
						
					?>
	            </div>
	      </div>
	          
            </div>
      </div>

<!--      <div class="row margin_top">
        <div class="span6 ">
          <h4 class="CAPS braker_bottom margin_bottom_small">Estadisticas</h4>
          <table width="100%" border="0" class="table table-bordered  table-striped table-condensed"  cellspacing="0" cellpadding="0">
            <tr>
              <td><strong>Ventas Totales</strong>:</td>
              <td><?php echo Yii::app()->numberFormatter->format("#,##0.00",$a); ?> Bs.</td>
            </tr>
            <tr>
              <td><strong>Promedio de Ventas</strong>:</td>
              <td><?php echo Yii::app()->numberFormatter->format("#,##0.00",$c); ?> Bs.</td>
            </tr>
            <tr>
              <td><strong>Impuestos</strong>:</td>
              <td><?php echo $b; ?> Bs.</td>
            </tr>
            <tr>
              <td><strong>Envíos</strong>:</td>
              <td><?php echo $enviados; ?></td>
            </tr>
            <tr>
              <td><strong>Numero de Productos envíos</strong>:</td>
              <td><?php echo $productos_enviados; ?></td>
            </tr>
          </table>
        </div>
        <div class="span6">
          <h4 class="CAPS braker_bottom margin_bottom_small">VENTAS PENDIENTES</h4>
          <table width="100%" border="0" class="table table-bordered table-striped table-condensed"  cellspacing="0" cellpadding="0">
            <tr>
              <td><strong>Total Pagos Pendientes:</strong></td>
              <td><?php echo Yii::app()->numberFormatter->format("#,##0.00",$d); ?> Bs.</td>
            </tr>
            <tr>
              <td><strong>Promedio en Pagos Pendientes:</strong></td>
              <td><?php echo Yii::app()->numberFormatter->formatDecimal($f); ?> Bs.</td>
            </tr>
            <tr>
              <td><strong>Impuestos:</strong></td>
              <td>870 Bs.</td>
            </tr>
            <tr>
              <td><strong>Envios:</strong></td>
              <td><?php echo Yii::app()->numberFormatter->format("#,##0.00",$g); ?> Bs.</td>
            </tr>
            <tr>
              <td><strong>Numero de Productos Pendientes:</strong></td>
              <td><?php echo $productos_pendientes; ?></td>
            </tr>
          </table>
        </div>
      </div>-->
      
      
      
      
      <h2 class="braker_bottom margin_bottom_small">Looks</h2>
      <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#tab1">Número de visitas</a></li>
        <li><a data-toggle="tab" href="#tab2">Status</a></li>
        
      </ul>
      <div class="tab-content">
      
        <div class="tab-pane active" id="tab1" >
            <table width="100%" border="0" class="table table-bordered table-striped table-condensed"  cellspacing="0" cellpadding="0">
              <tr>
                <th scope="col">Nombre del Look</th>
                <th scope="col">Número de visitas</th>
                <th scope="col">Cantidad vendida</th>
                <th scope="col">Total de ventas</th>
              </tr>

          <?php
                 
          foreach($views->getData() as $record) {
                  if (isset($record)){
          ?>
                    <tr>
                        <td><a href="<?php echo $record->getUrl(); ?>" title="Ver Look"><?php echo $record->title; ?></a></td>
                        <td><?php echo $record->view_counter; ?></td>
                        <td><?php echo $record->getCantVendidos(); ?></td>
                        <td>Bs. <?php echo ""; ?></td>
                    </tr>
          <?php
                  }
          }
          ?>  

          </table>
        </div>
              
        <div class="tab-pane" id="tab2">
          <table width="100%" border="0" class="table table-bordered table-striped table-condensed"  cellspacing="0" cellpadding="0">
              <tr>
                <th scope="col">Status</th>
                <th scope="col">Cantidad de looks</th>
                <th scope="col">% de Looks</th>
              </tr>	
            <?php
                foreach($status as $record) {
                    if (isset($record)){
            ?>      
                <tr>
                  <td><?php echo $record['nombre']; ?></td>
                  <td><?php echo $record['total']; ?></td>
                  <td><div class="pull-right margin_left_small"><?php echo round($record['porcentaje'], 2); ?> % 
                    </div><div class="progress progress-danger margin_bottom_xsmall">
                      <div class="bar" style="width: <?php echo (int)$record['porcentaje']; ?>%;"></div>
                    </div>
                  </td>
                  
                </tr>
           <?php      
                     }
                 }
           ?>     
            </table>
        </div>

       
      </div>
    </div>
  </div>
</div>
<!-- /container -->