<?php

$user = User::model()->findByPk(Yii::app()->user->id);


?>
<div class="container margin_top">
  <div class="row">
    <div class="span8 offset2">
      <div class="clearfix margin_bottom margin_top margin_left">
        <div class="first-done"></div>
        <div class="middle-done "></div> 
        <div class="middle-done "></div>
        <div class="last-done"></div> 
      </div>
      
      <?php
      
      if($orden->estado==1) // pendiente de pago
	  {
      ?>
      
      <section class="bg_color3 margin_top  margin_bottom_small padding_small box_1">
        <p class="alert alert-success"><strong>Tu Pedido ha sido recibido con éxito.</strong> <br/>
          A continuación encontrarás las instrucciones para completar tu compra. (También las hemos enviado a tu correo electrónico: <?php echo $user->email; ?>)</p>
        <h1 class="error">Siguiente paso</h1>
        <p><strong>Para completar tu comprar debes:</strong></p>
        <ol>
          <li> <strong>Realizar el pago</strong>: de Bs. <?php echo $orden->total; ?> via transferencia electrónica o depósito bancario antes del D-mm-YYYY en una de las siguientes cuentas: <br>
            <br>
            <ul>
              <li><strong>Banesco</strong><br>
                Cuenta Corriente Nº XXXXX-YYY-ZZZ<br>
                PERSONALING C.A<br>
                RIF Nº J-RRRRR<br>
                <br>
              </li>
            </ul>
            <ul>
              <li><strong>Mercantil<br>
                </strong>Cuenta Corriente Nº XXXXX-YYY-ZZZ<br>
                PERSONALING C.A<br>
                RIF Nº J-RRRRR<br>
                <br>
              </li>
              <li> <strong>Provincial<br>
                </strong>Cuenta Corriente Nº XXXXX-YYY-ZZZ<br>
                PERSONALING C.A<br>
                RIF Nº J-RRRRR<br>
                <br>
              </li>
            </ul>
          </li>
          <li><strong>Registra tu pago</strong>: a través del link enviado a tu correo ó ingresa a Tu Cuenta - > Mis compras,  selecciona el pedido que deseas Pagar y la opción Registrar Pago.</li>
          <li><strong>Proceso de validación: </strong>usualmente toma de 1 y 5 días hábiles y consiste en validar tu transferencia o depósito con nuestro banco. Puedes consultar el status de tu compra en tu perfil.</li>
          <li><strong>Envio:</strong> Luego de validar el pago te enviaremos el producto :)</li>
        </ol>
        <hr/>
        <div class="clearfix"><div class="pull-left"><a onclick="window.print();" class="btn"><i class="icon-print"></i> Imprime estas instrucciones</a></div> <div class="pull-right">
        	Si ya has realizado el deposito <a href="#myModal" role="button" class="btn btn-mini" data-toggle="modal" >haz click aqui</a></div></div>

      </section>
      <?php
      }// caso 1
      
      if($orden->estado==2) // pendiente por confirmar
	  {
	  	echo "
	  	<section class='bg_color3 margin_top  margin_bottom_small padding_small box_1'>
        <div class='alert'>
          <h1>Pedido en proceso</h1>
          <p>Hemos recibido los datos de pedido asi como de tu pago con transferencia o depósito bancario</p>
        </div>
         
        <p>Estaremos verificando la transferencia o depósito en los próximos 2 a 3 días hábiles y te notificaremos cuándo haya sido aprobado</p>
	  	</section>
	  	";
		
	  }
      
      ?>
      <section class="bg_color3 margin_top  margin_bottom_small padding_small box_1">
        <h3>Resumen del pedido </h3>
        <p class="well well-small"><strong>Número de confirmación:</strong> <?php echo $orden->id; ?></p> 
        <p> <strong>Fecha estimada de entrega</strong>: 01/01/2013</p>
        <hr/>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <th class="text_align_left">Subtotal:</th>
            <td><?php echo $orden->subtotal; ?> Bs.</td>
          </tr>
          <tr>
            <th class="text_align_left">Descuento:</th>
            <td><?php echo $orden->descuento; ?> Bs.</td>
          </tr>
          <tr>
            <th class="text_align_left">Envío:</th>
            <td><?php echo $orden->envio; ?> Bs.</td>
          </tr>
          <tr>
            <th class="text_align_left">I.V.A. (12%):</th>
            <td><?php echo $orden->iva; ?> Bs.</td>
          </tr>
          <tr>
            <th class="text_align_left"><h4>Total:</h4></th>
            <td><h4><?php echo $orden->total; ?> Bs.</h4></td>
          </tr>
        </table>
        <hr/>
        <p>Hemos enviado un resumen de la compra a tu correo electrónico: <strong><?php echo $user->email; ?></strong> </p>
        <?php
        
        $s1 = "select count( * ) as total from tbl_orden_has_productotallacolor where look_id != 0 and tbl_orden_id = ".$orden->id."";
		$look = Yii::app()->db->createCommand($s1)->queryScalar();
        
		$s2 = "select count( * ) as total from tbl_orden_has_productotallacolor where look_id = 0 and tbl_orden_id = ".$orden->id."";
		$ind = Yii::app()->db->createCommand($s2)->queryScalar();
			
        ?>
        <h3 class="margin_top">Detalles del Pedido</h3>
        <!-- Look ON -->
        
        <?php
        
        if($look!=0) // hay looks
		{
		/*	
			 <h4 class="braker_bottom">Nombre del Look 1</h4>
        <div class="padding_left">
          <table class="table" width="100%" >
            <thead>
              <tr>
                <th colspan="2">Producto</th>
                <th>Precio por 
                  unidad </th>
                <th >Cantidad</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><img src="http://placehold.it/70x70"/ class="margin_bottom"></td>
                <td><strong>Vestido Stradivarius</strong> <br/>
                  <strong>Color</strong>: azul<br/>
                  <strong>Talla</strong>: M</td>
                  </td>
                <td >Bs. 3500</td>
                <td> 1</td>
              </tr>
              <tr>
                <td><img src="http://placehold.it/70x70"/ class="margin_bottom"></td>
                <td><strong>Vestido Stradivarius</strong> <br/>
                  <strong>Color</strong>: azul<br/>
                  <strong>Talla</strong>: M</td>
                  </td>
                <td >Bs. 3500</td>
                <td> 1</td>
              </tr>
            </tbody>
          </table>
          <hr/>
          <p class="muted"><i class="icon-user"></i> Creado por: <a href="#" title="ir al perfil">Nombre del personal shopper</a></p>
        </div>
        <!-- Look OFF --> 
        
			*/
		}
        
		if($ind!=0) // si hay individuales
		{
			echo "<h4 class='braker_bottom margin_top'></h4>
				        <div class='padding_left'>
				          <table class='table' width='100%' >
				            <thead>
				              <tr>
				                <th colspan='2'>Producto</th>
				                <th>Precio por 
				                  unidad </th>
				                <th >Cantidad</th>
				                </tr>
				                </thead>
            					<tbody>
				                ";
			
			$ordenprod =  OrdenHasProductotallacolor::model()->findAllByAttributes(array('tbl_orden_id'=>$orden->id));
			
			foreach ($ordenprod as $individual) {
				
				$todo = PrecioTallaColor::model()->findByPk($individual->preciotallacolor_id);
						
				$producto = Producto::model()->findByPk($todo->producto_id);
				$talla = Talla::model()->findByPk($todo->talla_id);
				$color = Color::model()->findByPk($todo->color_id);
							
				$imagen = Imagen::model()->findByAttributes(array('tbl_producto_id'=>$producto->id,'orden'=>'1'));
								
				echo "<tr>";		
							
				if($imagen){					  	
					$aaa = CHtml::image(Yii::app()->baseUrl . str_replace(".","_thumb.",$imagen->url), "Imagen ", array("width" => "70", "height" => "70",'class'=>'margin_bottom'));
					echo "<td>".$aaa."</td>";
				}else
					echo"<td><img src='http://placehold.it/70x70'/ class='margin_bottom'></td>";

				echo "
					<td>
					<strong>".$producto->nombre."</strong> <br/>
					<strong>Color</strong>: ".$color->valor."<br/>
					<strong>Talla</strong>: ".$talla->valor."</td>
					</td>
					";	
				
				// precio
				foreach ($producto->precios as $precio) {
					$pre = Yii::app()->numberFormatter->formatDecimal($precio->precioDescuento);
				}
						
					echo "<td>Bs. ".$pre."</td>";
					echo "<td>".$individual->cantidad."</td>
					</tr>";

              
				
			}// foreach de productos		
		}// si hay indiv
		
        ?>

            </tbody>
          </table>
          <hr/>
        </div>
        
      </section>
      <hr/>
      <a href="../../tienda/index" class="btn" title="seguir comprando">Seguir comprando</a> </div>
  </div>
</div>
<!-- /container -->

<!-- Modal Window -->
<?php 
$detPago = Detalle::model()->findByPk($orden->detalle_id);
?>
<div class="modal hide fade" id="myModal">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4>Agregar Depósito o Transferencia bancaria ya realizada</h4>
  </div>
  <div class="modal-body">
    <form class="">
      <div class="control-group"> 
        <!--[if lte IE 7]>
            <label class="control-label required">Nombre del Depositante <span class="required">*</span></label>
<![endif]-->
        <div class="controls">
          <?php echo CHtml::activeTextField($detPago,'nombre',array('id'=>'nombre','class'=>'span5','placeholder'=>'Nombre del Depositante')); ?>
          <div style="display:none" id="RegistrationForm_email_em_" class="help-inline"></div>
        </div>
      </div>
      <div class="control-group"> 
        <!--[if lte IE 7]>
            <label class="control-label required">Número o Código del Depósito<span class="required">*</span></label>
<![endif]-->
        <div class="controls">
        	<?php echo CHtml::activeTextField($detPago,'nTransferencia',array('id'=>'numeroTrans','class'=>'span5','placeholder'=>'Número o Código del Depósito')); ?>
          <div style="display:none" class="help-inline"></div>
        </div>
      </div>
      <div class="controls controls-row"> 
        <!--[if lte IE 7]>
            <label class="control-label required">Fecha del depósito DD/MM/YYY<span class="required">*</span></label>
<![endif]-->
<?php echo CHtml::TextField('dia','',array('id'=>'dia','class'=>'span1','placeholder'=>'Día')); ?>
<?php echo CHtml::TextField('mes','',array('id'=>'mes','class'=>'span1','placeholder'=>'Mes')); ?>
<?php echo CHtml::TextField('ano','',array('id'=>'ano','class'=>'span2','placeholder'=>'Año')); ?>
      </div>
      <div class="control-group"> 
        <!--[if lte IE 7]>
            <label class="control-label required">Comentarios (Opcional) <span class="required">*</span></label>
<![endif]-->
        <div class="controls">
        	<?php echo CHtml::activeTextArea($detPago,'comentario',array('id'=>'comentario','class'=>'span5','rows'=>'6','placeholder'=>'Comentarios (Opcional)')); ?>
          <div style="display:none" class="help-inline"></div>
        </div>
      </div>
      <div class="form-actions"> <a onclick="enviar()" class="btn btn-danger">Confirmar Deposito</a> </div>
      <p class="well well-small"> <strong>Terminos y Condiciones de Recepcion de pagos por Deposito y/o Transferencia</strong><br/>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ul </p>
    </form>
  </div>
</div>

<input type="hidden" id="idDetalle" value="<?php echo($orden->detalle_id); ?>" />

<!-- // Modal Window -->

<script>
	
	function enviar()
	{	
		var idDetalle = $("#idDetalle").attr("value");
		var nombre= $("#nombre").attr("value");
		var numeroTrans = $("#numeroTrans").attr("value");
		var dia = $("#dia").attr("value");
		var mes = $("#mes").attr("value");
		var ano = $("#ano").attr("value");
		var comentario = $("#comentario").attr("value");


 		$.ajax({
	        type: "post", 
	        url: "../cpago", // action 
	        data: { 'nombre':nombre, 'numeroTrans':numeroTrans, 'dia':dia, 'mes':mes, 'ano':ano, 'comentario':comentario, 'idDetalle':idDetalle}, 
	        success: function (data) {
				
				if(data=="ok")
				{
					window.location.reload();
					//alert("guardado"); 
					// redireccionar a donde se muestre que se ingreso el pago para luego cambiar de estado la orden 
				}
	       	}//success
	       })
 			
		
		
	}
	
</script>