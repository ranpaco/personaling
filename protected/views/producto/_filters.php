<div class="row margin_top margin_bottom" id="filters-view" style="display: none">
<!--<div class="row margin_top margin_bottom" id="filters-view" >-->

<div class="span12">
  <div class="alert in" id="alert-msg" style="display: none">
    <button type="button" class="close" >&times;</button> 
    <!--data-dismiss="alert"-->
    <div class="msg"></div>
  </div>
</div>          
    
<?php
    echo CHtml::dropDownList('estados', '', array('0' => 'Activo',
    '1' => 'Inactivo'), array('style' => 'display:none'));
    
    echo Chtml::dropDownList('Operadores', '', array('>' => '>', '>=' => '>=',
                            '=' => '=', '<' => '<', '<=' => '<=', '<>' => '<>'), 
                                array('empty' => 'Operador',
                                    'style' => 'display:none'));
    
    echo CHtml::dropDownList('marcas', '', CHtml::listData(Marca::model()->findAll(), 'id', 'nombre'),
                            array('style' => 'display:none'));
							
	echo CHtml::dropDownList('destacados', '', array('1'=>'Si', '0'=>'No' ),
                            array('style' => 'display:none'));
							
	echo CHtml::dropDownList('080', '', array('1'=>'Si', '0'=>'No' ),
                            array('style' => 'display:none'));
	
	echo CHtml::dropDownList('100', '', array('1'=>'Si', '0'=>'No' ),
                            array('style' => 'display:none'));
    
    echo CHtml::dropDownList('outlet', '', array('1'=>'Si', '0'=>'No' ),
                            array('style' => 'display:none'));
							
	echo CHtml::dropDownList('producto_externo', '', array('1'=>'Si', '0'=>'No' ),
                            array('style' => 'display:none'));
							
	echo CHtml::dropDownList('descuento', '', array('1'=>'Si', '0'=>'No' ),
                            array('style' => 'display:none'));
							
	echo CHtml::dropDownList('tipoDescuento', '', array('1'=>'Monto', '0'=>'Porcentaje' ),
                            array('style' => 'display:none'));						
    
    /*Filtro de tiendas*/
    $allBrands = CHtml::listData(Tienda::model()->findAll(), 'id', 'name');
    $allBrands['NULL'] = "Personaling";
    echo CHtml::dropDownList('tiendas', '', $allBrands,
                            array('style' => 'display:none'));
    
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl."/js/filters.js");
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl."/js/filtersProductos.js");
    
    
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    //'action' => Yii::app()->createUrl($this->route),
    'method' => 'post',
    'htmlOptions' => array('class' => 'form-stacked span12'),
    'id' => 'form_filtros'
    ));
	
	if(Yii::app()->language=="es_es")
	{
		$var='080';	
		$var_name='080 Barcelona';	
	}
	else 
	{
		$var='100';	
		$var_name='100% Chic';	
	}
	
?>
    
    <h4>Nuevo Filtro:</h4>
    
    <fieldset>
        <div id="filters-container" class="clearfix">
            <div id="filter">
                <div class="control-group">
                    <div class="controls" >
                        <div class="span3" >
                            <?php echo Chtml::dropDownList('dropdown_filter[]', '', array(
                                'categoria' => 'Categoría',
                                'sku' => 'SKU',
                                'codigo' => 'Referencia',
                                'precioVenta' => 'Precio de Venta',
                                'precioDescuento' => 'Precio de Descuento',
                                'total' => 'Cantidad Total',
                                'disponible' => 'Cantidad Disponible',
                                'vendida' => 'Cantidad Vendida',
                                'ventas' => 'Ventas',
                                'estado' => 'Estado',
                                'fecha' => 'Fecha de Carga',
                                'marca_id' => 'Marca',
                                'tienda_id' => 'Tienda',
                                'view_counter' => 'Visitas',
                                'destacado' => 'Destacados',
                                'outlet'=>'Outlet',
                                $var=> $var_name,
                                'producto_externo'=>'Producto Externo',
                                'descuento'=>'Descuento',
                                'tipoDescuento'=>'Tipo de Descuento',
                                'valorTipo'=>'Valor de Descuento',
                                 ),
                            array('empty' => '-- Seleccione --', 'class' => 'dropdown_filter span3')); ?> 
                        </div>
                        <div class="span2" >
                            <?php echo Chtml::dropDownList('dropdown_operator[]', '', array('>' => '>', '>=' => '>=',
                            '=' => '=', '<' => '<', '<=' => '<=', '<>' => '<>'), 
                                array('empty' => 'Operador', 'class' => 'dropdown_operator span2')); ?>
                        </div>
                        <div class="span2" >
                            <?php echo Chtml::textField('textfield_value[]', '', array('class' => 'textfield_value span2')); ?>  
                        </div>
                        <div class="span2" >
                           <?php
                        echo Chtml::dropDownList('dropdown_relation[]', '', array('AND' => 'Y', 'OR' => 'O'),
                                array('class' => 'dropdown_relation span2', 'style' => 'display:none'));
                        ?> 
                        </div>
                        
                            <a href="#" class="btn span_add" style="float: right" title="Agregar nuevo campo"> + </a>
                            <a href="#" class="btn btn-danger span_delete" style="display:none; float: right" title="Eliminar campo"> - </a> 
                        
                        
                        
                       
                        
                        

                    </div>
                </div>    
            </div>    
        </div>  
    </fieldset>
    
   <?php $this->endWidget(); ?>

    <div class="span2 pull-right">
        <a href="#" id="filter-remove" class="btn" title="Borrar Filtro">Borrar</a>
    </div>
    <div class="span3 pull-right">
        <a href="#" id="filter-save" class="btn" title="Buscar con el filtro actual y guardarlo">Buscar y Guardar</a> 
    </div>
    <div class="span2 pull-right" style="display: none">
        <a href="#" id="filter-save2" class="btn" title="Guardar filtro actual">Guardar</a> 
    </div>
    <div class="span1 pull-right">
        <a href="#" id="filter-search" class="btn btn-danger" title="Buscar con el filtro actual">Buscar</a>  
    </div>
    
    
    
    
</div>
<script type="text/javascript">
/*<![CDATA[*/
   
   //Buscar      
    $('#filter-search').click(function(e) { 
        
        e.preventDefault(); 
        
        search('<?php echo CController::createUrl('producto/admin') ?>');
        
    });
    
    //Buscar y guardar nuevo
    $('#filter-save').click(function(e) {
        
        e.preventDefault(); 
        
        searchAndSave('<?php echo CController::createUrl('producto/admin') ?>', true);
            
    });
    
    //Buscar y guardar filtro actual
    $('#filter-save2').click(function(e) {
        
        e.preventDefault(); 
        
        searchAndSave('<?php echo CController::createUrl('producto/admin') ?>', false);
            
    });
    
    //Seleccionar un filtro preestablecido
    $("#all_filters").change(function(){
	
        getFilter('<?php echo CController::createUrl('orden/getFilter') ?>', $(this).val(), '<?php echo CController::createUrl('producto/admin') ?>');        	
	
    });
    
    $("#filter-remove").click(function(e){

             e.preventDefault();
             removeFilter('<?php echo CController::createUrl('orden/removeFilter') ?>',$("#all_filters").val());        	

    });    
    
    
/*]]>*/
</script>