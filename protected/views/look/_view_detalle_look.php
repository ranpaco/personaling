<!-- Modal 1 -->

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel"><?php echo $model->title; ?></h3>
    </div>
    <div class="modal-body">
      <div class="text_align_center"><img src="http://placehold.it/300" class="img-polaroid"/></div>
      <hr/>
        <div >
        <h4>Productos</h4>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-condensed">      
<tr> <th scope="row">Mango - Pantalon X</th>
 <td>10 disponibles</td>
<td>              700,00 Bs.</td>

 
</tr><tr> <th scope="row">Mango - Camisa Y</th>
 <td>20 disponibles</td>
 <td>650,00 Bs.</td>
</tr>
<tr> <th scope="row">Aldo - Zapatos Z</th>
 <td> 8 disponibles</td>
<td>715,00 Bs</td>
</tr><tr>
<th scope="row">Accessorize - Accesorios A,B y C</th>
<td>30 disponibles</td>

<td>  50,00 Bs.</td>
  </tr>
</table>
        
        
            <h4>Precios</h4>
            
         
            
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-condensed">
                <tr>
                    <th scope="row">Precio base</th>
                    <td> Bs. 700,00 </td>
                </tr>
                <tr>
                    <th scope="row">Precio con descuento</th>
                    <td> Bs. 650,0</td>
                </tr>
                <tr>
                    <th scope="row">Descuento %</th>
                    <td>7.15%</td>
                </tr>
                <tr>
                    <th scope="row">Descuento Bs.</th>
                    <td>Bs. 50,00</td>
                </tr>
            </table>
            <hr/>
            <h4>Estadísticas</h4>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-condensed">
                <tr>
                    <th scope="row">Vistas</th>
                    <td>120</td>
                </tr>
                <tr>
                    <th scope="row">Looks que lo usan</th>
                    <td>18</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="modal-footer"> 
    	<a href="#" title="eliminar" class="btn"><i class="icon-trash"></i> Eliminar</a> 
    	<a href="#" title="Exportar" class="btn"><i class="icon-share-alt"></i> Exportar</a> 
    <?php $this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Editar',
			'icon'=>'edit',
			'url' => CController::createUrl('look/edit',array('id'=>$model->id)),
			//'type'=>'primary', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
			//'size'=>'large', // null, 'large', 'small' or 'mini'
		)); ?>

    	
    	
    	<a href="" title="ver" class="btn btn-info" target="_blank"><i class="icon-eye-open icon-white"></i> Ver</a> 
    </div>