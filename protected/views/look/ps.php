<?php
	$this->breadcrumbs=array(
		'Mis Looks',
	);

?>
<div class="container margin_top">
    <div class="page-header">
        <h1>Administrar Mis Looks</small></h1>
    </div>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table ">
        <tr>
            <th scope="col" colspan="6"> Totales </th>
        </tr>
        <tr>
            <td><p class="T_xlarge margin_top_xsmall">120 </p>
                Totales</td>
            <td><p class="T_xlarge margin_top_xsmall"> 144 </p>
                Activos</td>
            <td><p class="T_xlarge margin_top_xsmall"> 156</p>
                Inactivos</td>
            <td><p class="T_xlarge margin_top_xsmall">150</p>
                Enviados</td>
            <td><p class="T_xlarge margin_top_xsmall"> 1120</p>
                En tránsito </td>
            <td><p class="T_xlarge margin_top_xsmall"> 182 </p>
                Devueltos</td>
        </tr>
    </table>
    <hr/>
    <div class="row margin_top margin_bottom ">
        <div class="span4">
            <div class="input-prepend"> <span class="add-on"><i class="icon-search"></i></span>
                <input class="span3" id="prependedInput" type="text" placeholder="Buscar">
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
        <div class="span3"><a href="#" class="btn">Crear nuevo filtro</a></div>
        <div class="span2">
        
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType' => 'link',
			'label'=>'Crear Look',
			'type'=>'success', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
			'size'=>'normal', // null, 'large', 'small' or 'mini'
			'url' => 'create',
		)); ?>        	
        </div>
    </div>
    <hr/>
<?php
$template = '{summary}
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-hover table-striped">
        <tr>
            <th scope="col"></th>
            <th colspan="2" scope="col">Look</th>
            <th scope="col">Precio</th>
            <th scope="col">Vendidos</th>
            <th scope="col">Ventas Bs.</th>
            <th scope="col">Estado</th>
            <th scope="col">Fecha de Carga</th>
            <th scope="col">Progreso de la campaña</th>
            <th scope="col">Acción</th>
        </tr>
    {items}
    </table>
    {pager}
	';

		$this->widget('zii.widgets.CListView', array(
	    'id'=>'list-auth-items',
	    'dataProvider'=>$dataProvider,
	    'itemView'=>'_view_ps',
	    'template'=>$template,
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
                <option>Seleccionar opción</option>
                <option>Lorem</option>
                <option>Ipsum 2</option>
                <option>Lorem</option>
            </select>
        </div>
        <div class="span1"><a href="#" title="procesar" class="btn btn-danger">Procesar</a></div>
        <div class="span2"><a href="#" title="Exportar a excel" class="btn btn-info">Exportar a excel</a></div>
    </div>
</div>
<!-- /container --> 

<!------------------- MODAL WINDOW ON -----------------> 
<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'myModal')); ?>
<?php $this->endWidget(); ?>

<!------------------- MODAL WINDOW OFF ----------------->
