<?php
/* @var $this OrdenController */

$this->breadcrumbs=array(
	'Pedidos',
);
?>

<div class="container margin_top">
  <div class="page-header">
    <h1>Administrar Pedidos</h1>
  </div>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table ">
    <tr>
      <th scope="col" colspan="6"> Totales </th>
    </tr>
    <tr>
      <td><p class="T_xlarge margin_top_xsmall">
      	
      	<?php
$sql = "select count( * ) as total from tbl_orden";
$num = Yii::app()->db->createCommand($sql)->queryScalar();
echo $num;
?>
      </p>
        Totales</td>
      <td><p class="T_xlarge margin_top_xsmall"> 
      	<?php
$sql = "SELECT count( * ) as total FROM tbl_orden where estado=1";
$num = Yii::app()->db->createCommand($sql)->queryScalar();
echo $num;
?>    	 </p>
        En espera de pago</td>
      <td><p class="T_xlarge margin_top_xsmall">
      	      	<?php
$sql = "SELECT count( * ) as total FROM tbl_orden where estado=2";
$num = Yii::app()->db->createCommand($sql)->queryScalar();
echo $num;
?> 
      </p>
        Esperando<br/>
        Confirmación</td>
      <td><p class="T_xlarge margin_top_xsmall">150</p>
        Pago Recibido</td>
      <td><p class="T_xlarge margin_top_xsmall"> 1120</p>
        Preparandose <br/>
        para enviar </td>
      <td><p class="T_xlarge margin_top_xsmall"> 182 </p>
        Recibidos</td>
      <td><p class="T_xlarge margin_top_xsmall"> 182 </p>
        Devuelto</td>
    </tr>
  </table>
  <hr/>
  <div class="row margin_top margin_bottom ">
    <div class="span4">
      <div class="input-prepend"> <span class="add-on"><i class="icon-search"></i></span>
        <input class="span3" id="prependedInput" type="text" placeholder="Buscar por palabras clave">
      </div>
    </div>
    <div class="span3">
      <select class="span3">
        <option>Filtros prestablecidos</option>
        <option>Filtro 1</option>
        <option>Filtro 2</option>
        <option>Filtro 3</option>
      </select>
    </div>
    <div class="span3">
      <a href="#" class="btn " title="Crear nuevo filtro">Crear filtro</a>
    </div>
  </div>
  <hr/>

  <?php
  
$template = '{summary}
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-hover table-striped">
    <tr>
      <th scope="col"><input type="checkbox"></th>
      <th scope="col">ID del pedido</th>
      <th scope="col">Usuaria</th>
      <th scope="col">Fecha de Compra</th>
      <th scope="col">Mercancia Comprada</th>
      <th scope="col">Monto (Bs)</th>
      <th scope="col">Método de pago</th>
      <th scope="col">Estado</th>
      <th scope="col">Acciones</th>
    </tr>
    {items}
    </table>
    {pager}
	';
    
  $this->widget('zii.widgets.CListView', array(
	    'id'=>'list-auth-items',
	    'dataProvider'=>$dataProvider,
	    'itemView'=>'_datos',
	    'template'=>$template,
	    'enableSorting'=>'true',
	    'afterAjaxUpdate'=>" function(id, data) {
						    	
							$('#todos').click(function() { 
				            	inputs = $('table').find('input').filter('[type=checkbox]');
				 
				 				if($(this).attr('checked')){
				                     inputs.attr('checked', true);
				               	}else {
				                     inputs.attr('checked', false);
				               	} 	
							});
						   
							} ",
		'pager'=>array(
			'header'=>'',
			'htmlOptions'=>array(
			'class'=>'pagination pagination-right',
		)
		),					
	));
	
	?>

  <hr/>
  <div class="row">
    <div class="span3">
      <select class="span3">
        <option>Acciones en lote</option>
        <option>Activar</option>
        <option>Inactivar</option>
        <option>Borrar usuarios</option>
      </select>
    </div>
    <div class="span1"><a href="#" title="procesar" class="btn btn-danger">Procesar</a></div>
    <div class="span2"><a href="#" title="Exportar a excel" class="btn btn-info">Exportar a excel</a></div>
  </div>
</div>
<!-- /container --> 