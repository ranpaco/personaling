<?php
/* @var $this OrdenController */

$this->breadcrumbs=array(
	'Pedidos'=>array('admin'),
	'Detalle',
);

$usuario = User::model()->findByPk($orden->user_id); 

?>

<div class="container margin_top">
  <div class="page-header">
    <h1>PEDIDO #<?php echo $orden->id; ?></h1>
  </div>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table ">
    <tr>
      <th scope="col" colspan="4"> PEDIDO #<?php echo $orden->id; ?> - <span class="color1"><?php echo $usuario->profile->first_name." ".$usuario->profile->last_name; ?></span> </th>
      <th scope="col" colspan="2"><div class="text_align_right">
      	<?php
      	
      	if($orden->fecha!="")
   			echo date("d-m-Y H:i:s",strtotime($orden->fecha));
      	
      	?>
      </div></th>
    </tr>
    <tr>
   	<td><p class="T_xlarge margin_top_xsmall color1">
<?php
//----------------------Estado
	if($orden->estado == 1)
		echo "En espera de pago"; 
	
	if($orden->estado == 2)
		echo "En espera de confirmación";
	
	if($orden->estado == 3)
		echo "Pago Confirmado";
	
	if($orden->estado == 6)
		echo "Pago Rechazado";
	
	// agregar demas estados
?>
	</p>
        Estado actual</td>
      <td><p class="T_xlarge margin_top_xsmall"> 1 </p>
        Documentos</td>
      <td><p class="T_xlarge margin_top_xsmall"> 2</p>
        Mensajes<br/></td>
      <td><p class="T_xlarge margin_top_xsmall">150</p>
        Prendas</td>
      <td><p class="T_xlarge margin_top_xsmall"><?php echo Yii::app()->numberFormatter->formatDecimal($orden->total); ?></p>
        Bolivares (Bs.)</td>
      <td><a onclick="window.print();" class="btn margin_top pull-right"><i class="icon-print"></i> Imprimir pedido</a></td>
    </tr>
  </table>
  <hr/>
  <div class="row">
    <div class="span7">
      <h3 class="braker_bottom margin_top"> Información del cliente</h3>
      <div class="row">
        <div class="span1"><img src="http://placehold.it/90" title="Nombre del usuario"></div>
        <div class="span6">
          <h2><?php echo $usuario->profile->first_name." ".$usuario->profile->last_name; ?><small> C.I. <?php echo $usuario->profile->cedula; ?></small></h2>
          <div class="row">
            <div class="span3">
              <ul class="no_bullets no_margin_left">
                <li><strong>eMail</strong>: <?php echo $usuario->email; ?></li>
                <li><strong>Telefono</strong>:<?php echo $usuario->profile->tlf_celular; ?> </li>
                <li><strong>Ciudad</strong>:<?php echo $usuario->profile->ciudad; ?> </li>
              </ul>
            </div>
            <div class="span3">
              <ul class="no_bullets no_margin_left">
                <li><strong>Cuenta registrada</strong>:<?php echo $usuario->create_at; ?></li>
                <li><strong>Pedidos validos realizados</strong>: 0</li>
                <li><strong>Total comprado desde su registro</strong>: 0,00 Bs. </li>
              </ul>
            </div>
          </div>
        </div>
      </div>      
        
          	<?php
          	
          	$detalle = Detalle::model()->findByPk($orden->detalle_id);
          	$pago = Pago::model()->findByAttributes(array('id'=>$orden->pago_id));
			
			if($detalle->estado == 1) // si fue aceptado
			{
				echo("
          	<div id='pago' class='well well-small margin_top well_personaling_small'>
          	<h3 class='braker_bottom '> Método de Pago</h3>
        		<table width='100%' border='0' cellspacing='0' cellpadding='0' class='table table-bordered table-hover table-striped'>
          		<tr>
            		<th scope='col'>Fecha</th>
            		<th scope='col'>Método de pago</th>
            		<th scope='col'>ID de Transaccion</th>
            		<th scope='col'>Monto</th>
            		<th scope='col'></th>
          		</tr>
          	<tr>
          	");	
				echo("<td>".date("d/m/Y",strtotime($detalle->fecha))."</td>");
				
				if($pago->tipo == 1)
					echo("<td>Deposito o Transferencia</td>");
					//hacer los demas tipos
						
				echo("<td>".$detalle->nTransferencia."</td>");	
				echo("<td>".Yii::app()->numberFormatter->formatDecimal($detalle->monto)."</td>");
				echo("<td><a href='#' title='Ver'><i class='icon-eye-open'></i></a></td>");
				echo("
				</tr>
        		</table> </div>
				");
			}
			else if($detalle->estado == 2) // rechazado
				{
						echo("
          	<div id='pago' class='well well-small margin_top well_personaling_small'>
          	<h3 class='braker_bottom '> Método de Pago</h3>
        		<table width='100%' border='0' cellspacing='0' cellpadding='0' class='table table-bordered table-hover table-striped'>
          		<tr>
            		<th scope='col'>Fecha</th>
            		<th scope='col'>Método de pago</th>
            		<th scope='col'>ID de Transaccion</th>
            		<th scope='col'>Monto</th>
            		<th scope='col'></th>
          		</tr>
          	<tr>
          	");	
				echo("<td>".date("d/m/Y",strtotime($detalle->fecha))."</td>");
				
				if($pago->tipo == 1)
					echo("<td>Deposito o Transferencia</td>");
					//hacer los demas tipos
						
				echo("<td> PAGO RECHAZADO </td>");	
				echo("<td>".Yii::app()->numberFormatter->formatDecimal($detalle->monto)." Bs.</td>");
				echo("<td><a href='#' title='Ver'><i class='icon-eye-open'></i></a></td>");
				echo("
				</tr>
        		</table></div>
				");
					
				}

		  	?>    
     
      <div class="well well-small margin_top well_personaling_small">
        <h3 class="braker_bottom "> Transporte </h3>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-hover table-striped">
          <tr>
            <th scope="col">Fecha</th>
            <th scope="col">Tipo</th>
            <th scope="col">Transportista</th>
            <th scope="col">Peso</th>
            <th scope="col">Costo de envio</th>
            <th scope="col">Numero de seguimiento</th>
            <th scope="col"></th>
          </tr>
          <tr>
            <td>21/12/2012 - 12:21 PM</td>
            <td>Delivery</td>
            <td>DHL</td>
            <td>0,00 Kg.</td>
            <td>100,00 Bs.</td>
            <td>1234567891012345</td>
            <td><a href="#" title="Editar"><i class="icon-edit"></i></a></td>
          </tr>
        </table>
      </div>
      <div class="row-fluid">
        <div class="span6">
          <h3 class="braker_bottom margin_top">Dirección de envío</h3>
          <div class="vcard">
            <div class="adr">
            	<?php
            	$direccionEnvio = DireccionEnvio::model()->findByPk($orden->direccionEnvio_id);
            	?>
              <div class="street-address"><i class="icon-map-marker"></i><?php echo $direccionEnvio->nombre." ".$direccionEnvio->apellido.". "; echo $direccionEnvio->dirUno.", ".$direccionEnvio->dirDos;  ?></div>
              <span class="locality"><?php echo $direccionEnvio->ciudad ?>, <?php echo $direccionEnvio->estado; ?>.</span>
              <div class="country-name"><?php echo $direccionEnvio->pais; ?></div>
            </div>
            <div class="tel margin_top_small"> <span class="type"><strong>Telefono</strong>:</span><?php echo $direccionEnvio->telefono; ?></div>
            <div><strong>Email</strong>: <span class="email"><?php echo $usuario->email; ?></span> </div>
          </div>
          <a href="#" class="btn"><i class="icon-edit"></i></a> </div>
        <div class="span6">
          <h3 class="braker_bottom margin_top">Dirección de Facturación</h3>
          <div class="vcard">
            <div class="adr">
              <div class="street-address"><i class="icon-map-marker"></i> Av. 5ta. Edificio Los Mirtos  Piso 3. Oficina 3-3</div>
              <span class="locality">San Cristobal, Tachira 5001</span>
              <div class="country-name">Venezuela</div>
            </div>
            <div class="tel margin_top_small"> <span class="type"><strong>Telefono</strong>:</span> 0276-341.47.12 </div>
            <div class="tel"> <span class="type"><strong>Celular</strong>:</span> 0414-724.80.43 </div>
            <div><strong>Email</strong>: <span class="email">info@commerce.net</span> </div>
            <a href="#" class="btn"><i class="icon-edit"></i></a> </div>
        </div>
      </div>
    </div>
    <div class="span5">
      <div class="well well_personaling_big">
        <h3 class="braker_bottom"><strong>Acciones pendientes</strong></h3>
        
        	<?php
        	
        	if($detalle->estado == 0 && $detalle->nTransferencia!="") // si esta en default
			{
				echo("<div class='alert alert-block '>");
				echo(" <h4 class='alert-heading '>Confirmar Pago:</h4>");
				echo("<ul class='padding_bottom_small padding_top_small'>");
				echo("<li>Banco: ".$detalle->banco."</li>");
				echo("<li>Numero: ".$detalle->nTransferencia."</li>");
				echo("<li>Monto: ".Yii::app()->numberFormatter->formatDecimal($detalle->monto)." Bs.</li>");
				echo("<li>Fecha: ".date("d/m/Y",strtotime($detalle->fecha))."</li>");
			
				echo("
				
				</ul>
          		<p> <a onclick='aceptar(".$detalle->id.")' class='btn' title='Aceptar pago'>
          		<i class='icon-check'></i> Aceptar</a>
          		<a onclick='rechazar(".$detalle->id.")' class='btn' title='Rechazar pago'>Rechazar</a> </p>
        		</div>
        		
				");
			
			}
        	
        	?>

        <div class="alert alert-block form-inline ">
          <h4 class="alert-heading "> Enviar pedido:</h4>
          <p>
            <input name="" type="text" placeholder="Numero de Tracking">
            <a href="#" class="btn" title="Rechazar pago">Enviar</a> </p>
        </div>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-hover table-striped">
          <tr>
            <th scope="col">Estado</th>
            <th scope="col">Usuario</th>
            <th scope="col">Fecha</th>
            <th scope="col">&nbsp;</th>
          </tr>

          <?php
          
          $estados = Estado::model()->findAllByAttributes(array('orden_id'=>$orden->id),array('order'=>'id DESC'));
          
		  	foreach ($estados as $est)
		  	{
		  		echo("<tr>");
				
				if($est->estado==1)
					echo("<td>Pendiente de Pago</td>");
				
				if($est->estado==2)
					echo("<td>Pendiente por confirmar</td>");
				
				if($est->estado==3)
					echo("<td>Pago Confirmado</td>");
				
				if($est->estado==6)
					echo("<td>Pago Rechazado</td>");
				
				$usu = User::model()->findByPk($est->user_id);
				echo ("<td>".$usu->profile->first_name." ".$usu->profile->last_name."</td>");
				
				$fecha = date("d/m/Y",strtotime($est->fecha));
				echo("<td>".$fecha." </td>");
            	echo("<td><a tabindex='-1' href='#'><i class='icon-edit'></i></a></td>");		
            	echo("</tr>");
		  	}
		  
          ?>
          <tr>
            <td>Nuevo Pedido</td>
            <td><?php echo $usuario->profile->first_name." ".$usuario->profile->last_name; ?></td>
            <td><?php echo date("d/m/Y",strtotime($orden->fecha)); ?></td>
            <td><a tabindex="-1" href="#"><i class="icon-edit"></i></a></td>
          </tr>
        </table>
      </div>
  <div class="well well-small margin_top well_personaling_small">  <h3 class="braker_bottom margin_top"> Documentos</h3>
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-hover table-striped">
        <tr>
          <th scope="col">Fecha</th>
          <th scope="col">Documento</th>
          <th scope="col">Número</th>
        </tr>
        <tr>
          <td>21/12/2012 </td>
          <td>Factura</td>
          <td>12345</td>
        </tr>
        <tr>
          <td>21/12/2012 </td>
          <td>Recibo de Pago</td>
          <td>23123</td>
        </tr>
        <tr>
          <td>21/12/2012 </td>
          <td>Etiqueta de direccion</td>
          <td>1231234</td>
        </tr>
        <tr>
          <td>21/12/2012 </td>
          <td>Orden de devolucion</td>
          <td>45648</td>
        </tr>
        <tr>
          <td>21/12/2012 </td>
          <td>Tarjeta de regalo</td>
          <td>123546</td>
        </tr>
      </table></div>
    </div>
  </div>
  <hr/>
  <!-- INFORMACION DEL PEDIDO ON -->
  <div class="row">
    <div class="span7">
   <div class="well well-small margin_top well_personaling_small">   <h3 class="braker_bottom margin_top">Productos</h3>
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-hover table-striped">
        <tr>
          <th scope="col">Nombre de la prenda</th>
          <th scope="col">Cant. en Existencia</th>
          <th scope="col">Cant. en Pedido</th>
          <th scope="col">Precio Unitario</th>
          <th scope="col">Subtotal</th>
          <th scope="col">Descuento</th>
          <th scope="col">Impuesto</th>
          <th scope="col">Accion</th>
        </tr>
        <?php
        
        	$productos = OrdenHasProductotallacolor::model()->findAllByAttributes(array('tbl_orden_id'=>$orden->id)); // productos de la orden
        	
			foreach ($productos as $prod) {
				
				if($prod->look_id != 0) // si es look
				{
					
				}
				else // individual
				{
					$ptc = PrecioTallaColor::model()->findByAttributes(array('id'=>$prod->preciotallacolor_id)); // consigo existencia actual
					$indiv = Producto::model()->findByPk($ptc->producto_id); // consigo nombre
					$precio = Precio::model()->findByAttributes(array('tbl_producto_id'=>$ptc->producto_id)); // precios
					
					echo("<tr>");
					echo("<td>".$indiv->nombre."</td>"); // nombre
					echo("<td>".$ptc->cantidad."</td>"); // cantidad en existencia
					echo("<td>".$prod->cantidad."</td>"); // cantidad en pedido
					
					setlocale(LC_MONETARY, 've_VE');
					$a = money_format('%i', $precio->precioVenta);
					$c = money_format('%i', $precio->ahorro);
					
					$iva = $precio->precioDescuento * 0.12;
					
					echo("<td>".Yii::app()->numberFormatter->formatDecimal($a)."</td>"); //precio individual sin descuento
					
					$b = $a * $prod->cantidad;					
					echo("<td>".Yii::app()->numberFormatter->formatDecimal($b)."</td>"); // subtotal
					
					$d = $c * $prod->cantidad;					
					echo("<td>".Yii::app()->numberFormatter->formatDecimal($d)."</td>"); //descuento total
					
					$e = $iva * $prod->cantidad;
					echo("<td>".Yii::app()->numberFormatter->formatDecimal($e)."</td>"); // impuesto total
					
					echo("
						<td><div class='dropdown'> <a class='dropdown-toggle' id='dLabel' role='button' data-toggle='dropdown' data-target='#' href='/page.html'> <i class='icon-cog'></i></a> 
		              	<!-- Link or button to toggle dropdown -->
		              	<ul class='dropdown-menu' role='menu' aria-labelledby='dLabel'>
		                	<li><a tabindex='-1' href='#'><i class='icon-edit'></i> Editar</a></li>
		                	<li class='divider'></li>
		                	<li><a tabindex='-1' href='#'><i class='icon-trash'></i> Eliminar</a></li>
		              	</ul>
		            	</div></td>
					");
					
					echo("</tr>");				
				}				
				
			}
        
        ?>

      </table>
      <?php
      $individuales=0;
	  $looks=0;
      $compra = OrdenHasProductotallacolor::model()->findAllByAttributes(array('tbl_orden_id'=>$orden->id));
	
		foreach ($compra as $tot) {
			
			if($tot->look_id == 0)
			{
				$individuales++;
			}else{
				$looks++;
			}
			
		}
      
      ?>
      
      <a href="#" title="Añadir productos" class="btn btn-info"><i class="icon-plus icon-white"></i> Añadir productos</a></div> </div>
    <div class="span5">
   <div class="well well-small margin_top well_personaling_small"> <h3 class="braker_bottom margin_top"> Resumen del Pedido</h3>
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-hover table-striped">
        <tr>
          <th scope="col">No de Looks</th>
          <th scope="col"><?php echo($looks); ?></th>
        </tr>
        <tr>
          <td>No de Prendas</td>
          <td><?php echo($individuales); ?></td>
        </tr>
        <tr>
          <td>SubTotal</td>
          <td><?php echo Yii::app()->numberFormatter->formatDecimal($orden->subtotal); ?></td>
        </tr>
        <tr>
          <td>Descuento</td>
          <td><?php echo Yii::app()->numberFormatter->formatDecimal($orden->descuento); ?></td>
        </tr>
        <tr>
          <td>Envio y Transporte</td>
          <td><?php echo Yii::app()->numberFormatter->formatDecimal($orden->envio); ?></td>
        </tr>
        <tr>
          <td>Impuesto</td>
          <td><?php echo Yii::app()->numberFormatter->formatDecimal($orden->iva); ?></td>
        </tr>
        <tr>
          <td>Total</td>
          <td><?php echo Yii::app()->numberFormatter->formatDecimal($orden->total); ?></td>
        </tr>
      </table></div>
    </div>
  </div>
  <!-- INFORMACION DEL PEDIDO OFF -->
  <hr/>
  
  <!-- MENSAJES ON -->
  
  <div class="row">
    <div class="span7">
      <h3 class="braker_bottom margin_top">MENSAJES</h3>
      <form>
        <div class="control-group">
          <select>
            <option>Elija un mensaje estandar</option>
            <option>2</option>
            <option>3</option>
            <option>4</option>
            <option>5</option>
          </select>
        </div>
        <div class="control-group">
          <textarea name="Mensaje" cols="" class="span7" rows="4"></textarea>
        </div>
        <div class="control-group">
          <label class="checkbox">
            <input type="checkbox" value="">
            Notificar al Cliente por eMail </label>
          <label class="checkbox">
            <input type="checkbox" value="">
            Hacer visible en el Frontend</label>
        </div>
        <div class="form-actions"><a href="#" title="Enviar" class="btn btn-inverse">Enviar comentario</a> </div>
      </form>
    </div>
    <div class="span5">
      <h3 class="braker_bottom margin_top">Historial de Mensajes</h3>
      <ul class="media-list">
        <li class="media braker_bottom">
          <div class="media-body">
            <h4 class="color4"><i class=" icon-comment"></i> Asunto: XXX YYY ZZZ</h4>
            <p class="muted"> <strong>23/03/2013</strong> 12:35 PM <strong>| Recibido | Cliente: <strong>Notificado</strong> </strong></p>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate </p>
            <!-- Nested media object --> 
            
          </div>
        </li>
        <li class="media braker_bottom">
          <div class="media-body">
            <h4 class="color4"><i class=" icon-comment"></i> Asunto: XXX YYY ZZZ</h4>
            <p class="muted"> <strong>23/03/2013</strong> 12:35 PM <strong>| Recibido | Cliente: <strong>Notificado</strong> </strong></p>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate </p>
            <!-- Nested media object --> 
            
          </div>
        </li>
        <li class="media braker_bottom">
          <div class="media-body">
            <h4 class="color4"><i class=" icon-comment"></i> Asunto: XXX YYY ZZZ</h4>
            <p class="muted"> <strong>23/03/2013</strong> 12:35 PM <strong>| Recibido | Cliente: <strong>Notificado</strong> </strong></p>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate </p>
            <!-- Nested media object --> 
            
          </div>
        </li>
      </ul>
    </div>
    
    <!-- MENSAJES OFF --> 
    
  </div>
</div>
<!-- /container --> 

<!------------------- MODAL WINDOW ON -----------------> 

<!-- Modal 1 -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Desea aceptar este pago?</h3>
  </div>
  <div class="modal-body">
    <p><strong>Detalles</strong></p>
    <ul>
      <li><strong>Usuaria</strong>: Maria Perez</li>
      <li><strong>Fecha de compra</strong>: 18/10/1985</li>
      <li><strong>Monto</strong>: Bs. 6.500</li>
    </ul>
  </div>
  <div class="modal-footer"><a href="" title="ver" class="btn-link" target="_blank">Cancelar </a> <a href="#" title="Confirmar" class="btn btn-success">Aceptar el pago</a> </div>
</div>
<!------------------- MODAL WINDOW OFF ----------------->

<script>
	
	function aceptar(id){

		var uno = 'aceptar';
		
 		$.ajax({
	        type: "post", 
	        url: "../validar", // action 
	        data: { 'accion':uno, 'id':id}, 
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
	
	function rechazar(id){
		
		var uno = 'rechazar';
		
 		$.ajax({
	        type: "post", 
	        url: "../validar", // action 
	        data: { 'accion':uno, 'id':id}, 
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