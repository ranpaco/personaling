<?php
$profile = Profile::model()->findByAttributes(array('user_id'=>$factura->orden->user_id));
$direccion_fiscal = Direccion::model()->findByPk($factura->direccion_fiscal_id);
$direccion_envio = Direccion::model()->findByPk($factura->direccion_envio_id);
?>
<div class="container-fluid">
  <div class="row-fluid">
    <div class="span12 bg_color3">
      <section class="margin_bottom_small padding_small ">
        <h1>Factura</h1>
        <table width="100%" cellspacing="0" cellpadding="0" border="0" class="table table-bordered">
          <tr>
            <td width="50%">  </td>
            <td width="50%"><div class="text_align_right"><strong> Número de factura: <span style="color:#F00"><?php echo str_pad($factura->id, 4, '0', STR_PAD_LEFT); ?></span> </strong>
            
           <br/><strong> Fecha de emisión:</strong> <?php echo date('d/m/Y', strtotime($factura->fecha)); ?>
            
            </div></td>
          </tr>
          <tr>
          <tr>
            <td><strong>Cliente / Razón Social:</strong> 
              <?php
			  //echo $profile->first_name.' '.$profile->last_name;
			  echo $direccion_fiscal->nombre.' '.$direccion_fiscal->apellido;
              ?>
              <br/>
              <strong>Domicilio fiscal:</strong> <?php echo $direccion_fiscal->dirUno.' '.$direccion_fiscal->dirDos; ?><br/>
              <?php echo $direccion_fiscal->ciudad.' - '.$direccion_fiscal->estado.'. '.$direccion_fiscal->pais; ?><br/>
              <strong>RIF:</strong> <?php echo $direccion_fiscal->cedula; ?></td>
            <td><p><strong>Enviar a: </strong><?php echo $direccion_envio->nombre.' '.$direccion_envio->apellido; ?><br/>
                <strong>Dirección de envio: </strong><?php echo $direccion_envio->dirUno.' '.$direccion_envio->dirDos.'. '.$direccion_envio->ciudad.' - '.$direccion_envio->estado.'. '.$direccion_envio->pais; ?></p></td>
          </tr>
          <tr>
            <td colspan="2"><strong>Estado</strong>: 
            	<?php
            	switch ($factura->estado) {
					case '1':
						echo 'Pendiente';
						break;
					case '2':
						echo 'Pagada';
						break;
					default:
						echo 'Desconocido';
						break;
				}
            	?>
            </td>
          </tr>
          <tr>
            <td colspan="2"><table width="100%" cellspacing="0" cellpadding="0" border="0" class="table table-bordered table-hover table-striped">
                <tr>
                  <th scope="col">Código</th>
                  <th scope="col">Nombre del Producto</th>
                  <th scope="col">Cantidad</th>
                  <th scope="col">Precio Unitario</th>
                  <th scope="col">Total</th>
                </tr>
                <?php
                foreach ($$factura->orden->productos as $ptc) {
                    ?>
                    <tr>
	                  <td>COD100</td>
	                  <td>Camisa Lazo semitransparente de Rayas</td>
	                  <td>1</td>
	                  <td>Bs. 800</td>
	                  <td>Bs. 800</td>
	                </tr>
					<?php
                }
                ?>
                <tr>
                  <td colspan="4"><div class="text_align_right"><strong>Subtotal</strong>:</div></td>
                  <td>Bs. 600</td>
                </tr>
                <tr>
                  <td colspan="4"><div class="text_align_right"><strong>Descuento</strong>:</div></td>
                  <td>Bs. 600</td>
                </tr>
                <tr>
                  <td colspan="4"><div class="text_align_right"><strong>I.V.A. sobre base imponible Bs</strong>.</div></td>
                  <td>Bs. 600</td>
                </tr>
                <tr>
                  <th colspan="4"><div class="text_align_right">TOTAL</div></th>
                  <th>Bs. 600</th>
                </tr>
              </table></td>
          </tr>
        </table>
        <hr/>
      </section>
    </div>
  </div>
</div>
<!-- /container -->