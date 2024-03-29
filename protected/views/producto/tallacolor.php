<script language="JavaScript">

//$(document).ready(function() {
	
//});

</script>
<?php
$this->breadcrumbs=array(
	'Productos'=>array('admin'),
	'Tallas y Colores',
);
?> 

<div class="container margin_top">
  <div class="page-header">
    <h1>Editar Producto - Tallas y Colores</small></h1>
    <h2 ><?php echo $model->nombre."  [<small class='t_small'>Ref: ".$model->codigo."</small>]"; ?></h2>
  </div>
  <!-- SUBMENU ON -->
  <?php echo $this->renderPartial('menu_agregar_producto', array('model'=>$model,'opcion'=>6)); ?>
  <!-- SUBMENU OFF -->
  <div class="row margin_top">
    <div class="span9">
      <div class="bg_color3   margin_bottom_small padding_small box_1">

          <fieldset class="margin_top">
            <legend>Elige las<span class="color1"> tallas y colores</span> disponibles para este producto: </legend>
            <div class="row">
              <div class="span6">
                <p class="margin_bottom muted">Utiliza el buscador para encontrar y seleccionar las tallas y colores que correspondan</p>
              </div>


			
			<?php $this->widget('bootstrap.widgets.TbButton', array(
				'icon'=>'plus',
			    'label'=>'Crear un nuevo color',
			    //'type'=>'primary', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
			    'size'=>'small', // null, 'large', 'small' or 'mini'
			    'htmlOptions'=> array(
				      'data-toggle'=>'modal',
						'data-target'=>'#dialogColor', 
				        'onclick'=>"{addColor();}"
				       ),
				       
			)); ?>	        
				        
              </div>
            <div class="row-fluid">
            
            <div class="span6">
            	 <label class="control-label required">Color</label>
              
              	<?php
              	/* $colores = Color::model()->findAll(array('order'=>'valor')); // ordena alfeticamente por nombre
				 foreach($colores as $i => $row){
					$data[$i]['text']= $row->valor;
					$data[$i]['id'] = $row->id;
				 }*/
				$this->widget('bootstrap.widgets.TbSelect2',array(
																'asDropDownList' => false,
																	'name' => 'clevertech',
																	'options' => array(
																						 'placeholder'=> "Seleccione un color",
		 																				  'multiple'=>true,
		 //'data'=>$data,
		 ////'data'=>array(array('id'=>1,'text'=>'rafa'),array('id'=>2,'text'=>'lore')),
		// 'data'=> CHtml::listData(Color::model()->findAll(),'id', 'valor'),
		 																				  'width' => '90%',
		  
																						  'ajax' => array(  
																				                                //'url'=> 'http://api.rottentomatoes.com/api/public/v1.0/movies.json',  
																				                                'url'=> CController::createUrl('color/getColores'),  
																				                            'dataType' => 'json',  
																				                            'data' => 'js: function (term,page) {  
																				                                        return {  
																				                                        //term: term, // Add all the query string elements here seperated by ,  
																				                                        search: term,
																				                                        page_limit: 10,  
																				                                               };  
																				                                                             }',        
																				                            'results' => 'js: function (data,page) {return {results: data};}',  
																				                            ),  
 
																									),
																				)
				);
				?>
            	
            	
            </div>
            
            <div class="span6">
            	
            <label class="control-label required">Talla</label>
<?php
				$this->widget('bootstrap.widgets.TbSelect2',array(
																'asDropDownList' => false,
																	'name' => 'clevertech2',
																	'options' => array(
																						 'placeholder'=> "Seleccione una talla",
		 																				  'multiple'=>true,
		 //'data'=>$data,
		 ////'data'=>array(array('id'=>1,'text'=>'rafa'),array('id'=>2,'text'=>'lore')),
		// 'data'=> CHtml::listData(Color::model()->findAll(),'id', 'valor'),
		 																				  'width' => '90%',
		  
																						  'ajax' => array(  
																				                                //'url'=> 'http://api.rottentomatoes.com/api/public/v1.0/movies.json',  
																				                                'url'=> CController::createUrl('talla/getTallas'),  
																				                            'dataType' => 'json',  
																				                            'data' => 'js: function (term,page) {  
																				                                        return {  
																				                                        //term: term, // Add all the query string elements here seperated by ,  
																				                                        search: term,
																				                                        page_limit: 10,  
																				                                               };  
																				                                                             }',        
																				                            'results' => 'js: function (data,page) {return {results: data};}',  
																				                            ),  
 
																									),
																				)
				);
				
?>	
            	
            </div>
             
			<?php $this->widget('bootstrap.widgets.TbButton', array(
		    'buttonType'=>'ajaxButton',
		    'type'=>'primary',
		    'label'=>'Generar',
		    'loadingText'=>'Cargando...',
		    'url'=>array('producto/addtallacolor'),
		    'htmlOptions'=>array('id'=>'buttonStateful', 'class'=>'span2 offset5 margin_top'),
		    'ajaxOptions'=>array(
		    	    'type' => 'POST',
		    	    
    				'beforeSend' => "function( request )
	                     {
	                     //  var tallas = '';
	                     //  $('.btn-group a.active').each(function(index){
	                       		//tallas += $(this).html()+',';
	                       //		tallas += $(this).attr('href');
	                      // });
						   //tallas = tallas.substring(0, tallas.length-1);
						   
						    
						    var tallas = '';
						    $('#s2id_clevertech2 .select2-choices .select2-search-choice div').each(function(index){
	                       		tallas +=$(this).html()+ ',';
	                       });
	                       tallas = tallas.substring(0, tallas.length-1);
						    
	                       var colores = '';
	                       $('#s2id_clevertech .select2-choices .select2-search-choice div').each(function(index){
	                       		colores +=$(this).html()+ ',';
	                       });
	                       colores = colores.substring(0, colores.length-1);
						   
						   
						   
	                       this.data += '&colores='+colores+'&tallas='+tallas;
	                     }",
                     'success' => "function( data )
		                  {
		                    // handle return data
		                    $('#fieldset_tallacolor').html(data);
							$('.sku').each(function(){
							    if($.trim($(this).val()) == ''){
							    	console.log( $(this).$(':parent').addClass('success'));
							        $(this).$(':parent').addClass('success');
							    }
							});			                    
		                  }",
		                  'data'=>array('id'=>$model->id),
			),
		)); ?>	
              	
              
           </div> 
          </fieldset>
          
          <fieldset class="margin_top" >
            <legend>Combinaciones: </legend>
            <div id="fieldset_tallacolor">
<?php 
	if (count($model->preciotallacolor))
		$this->renderPartial('_view_tallacolor',array('tallacolor'=>$model->preciotallacolor,'producto'=>$model)); 
?></div>
          </fieldset>
        <!--
        </form>
        -->
        <hr/>
      </div>
    </div>
    
    
    <div class="span3">
        <div class="padding_left"> 
            <!-- SIDEBAR OFF --> 
            <script > 
			// Script para dejar el sidebar fijo Parte 1
			function moveScroller() {
				var move = function() {
					var st = $(window).scrollTop();
					var ot = $("#scroller-anchor").offset().top;
					var s = $("#scroller");
					if(st > ot) {
						s.css({
							position: "fixed",
							top: "70px"
						});
					} else {
						if(st <= ot) {
							s.css({
								position: "relative",
								top: "0"
							});
						}
					}
				};
				$(window).scroll(move);
				move();
			}
		</script>
            <div id="scroller-anchor"></div>
            <div id="scroller">
                 <?php 
		 
		 $this->widget('bootstrap.widgets.TbButton', array(
				    'buttonType'=>'ajaxButton',
				    'type'=>'danger',
				    'label'=>'Guardar',
				    'block'=>'true',
				   	'size'=> 'large',
				    'url'=> CController::createUrl('producto/tallacolor',array('id'=>$model->id)) ,
				    'htmlOptions'=>array('id'=>'buttonGuardar'),
				    'ajaxOptions'=>array(
				    	    'type' => 'POST',
				    	    'data'=> "js:$('#Tallacolor-Form').serialize()",
		                    'success' => "function( data )
				                  {

				                   data = JSON.parse( data );
				                   if(data.status=='success'){
				                        $('.error').hide();
				                        //$('#yw0').html('<div class=\"alert in alert-block fade alert-success\">Se guardaron las cantidades</div>');
				                        $('#MensajeError').html('<div class=\"alert in alert-block fade alert-success\">Se guardaron las cantidades</div>');

									}else{
										id = data.id;
										delete data['id'];
				                        $.each(data, function(key, val) {
				                        	key_tmp = key.split('_');
											key_tmp.splice(1,0,id);
				                        	key = key_tmp.join('_');
					                        $('#Tallacolor-Form #'+key+'_em_').text(val);                                                    
					                        $('#Tallacolor-Form #'+key+'_em_').show();
				                        });
									}
				                  }",
					),
				)); 
		
				?> 
                <ul class="nav nav-stacked nav-tabs margin_top">
                    <li>
                    	
                 <?php 		 		 
				   
				   $this->widget('bootstrap.widgets.TbButton', array(
				    'buttonType'=>'ajaxButton',
				    'label'=>'Guardar y Avanzar',
				    'block'=>'true',
				 
				    'url'=> CController::createUrl('producto/tallacolor',array('id'=>$model->id)) ,
				   'htmlOptions'=>array('id'=>'saveandgo','class'=>'btn btn-block boton_link transition_all color11'),
				    'ajaxOptions'=>array(
				    	    'type' => 'POST',
				    	    'data'=> "js:$('#Tallacolor-Form').serialize()",
		                    'success' => "function( data )
				                  {

				                   data = JSON.parse( data );
				                   if(data.status=='success'){
				                        $('.error').hide();
				                        //$('#yw0').html('<div class=\"alert in alert-block fade alert-success\">Se guardaron las cantidades</div>');
				                        $('#MensajeError').html('<div class=\"alert in alert-block fade alert-success\">Se guardaron las cantidades</div>');
										window.location.href = '../imagenes/'+data.id+'';
									}
				                  }",
					),
				)); 
				   
				   
				   
			
		
				?>                     	
                    </li>
                    <li><a id="nuevo" style="cursor: pointer" title="Guardar y crear nuevo producto">Guardar y crear nuevo producto</a></li>
                    <li><a style="cursor: pointer" title="Restablecer" id="limpiar">Limpiar</a></li>
                    <li id='MensajeError'></li>
                    <!-- <li><a href="#" title="Duplicar">Duplicar Producto</a></li> -->
                    <!-- <li><a href="#" title="Guardar"><i class="icon-trash"> </i> Borrar Producto</a></li> -->
                </ul>
                
            </div>
           
            
        </div>
          <script type="text/javascript"> 
		// Script para dejar el sidebar fijo Parte 2
			$(function() {
				moveScroller();
			 });
		</script>
            <!-- SIDEBAR OFF --> 
       
    </div>
    
    
    
    
  </div>
</div>
<!-- /container --> 
<!-- MODAL WINDOW CONFIRMACION ON -->
<div id="myModalConfirmacion" class="modal hide " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Confirmación</h3>
  </div>
  <div class="modal-body">
    <p>Algunos campos de código SKU están vacios, las combinaciones sin SKU no se guardaran</p>
    <p>¿Desea continuar?</p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">No</button>
    <button class="btn btn-danger si" data-loading-text="Cargando...">Si</button>
  </div>
</div>
<!-- MODAL WINDOW CONFIRMACION OFF -->

<!------------------- MODAL WINDOW ON -----------------> 

<!-- Modal 1 -->
<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'dialogColor')); ?>
<div class="divForForm"></div>
<?php $this->endWidget(); ?>
<!--
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 
</div>
-->
<!------------------- MODAL WINDOW OFF ----------------->
<script type="text/javascript">
// here is the magic
function addColor()
{
    <?php echo CHtml::ajax(array(
            'url'=>array('color/create'),
            'data'=> "js:$(this).serialize()",
            'type'=>'post',
            'dataType'=>'json',
            'success'=>"function(data)
            {
                if (data.status == 'failure')
                {
                    $('#dialogColor div.divForForm').html(data.div);
                          // Here is the trick: on submit-> once again this function!
                    $('#dialogColor div.divForForm form').submit(addColor);
                }
                else
                {
                    $('#dialogColor div.divForForm').html(data.div);
                    setTimeout(\"$('#dialogColor').modal('hide') \",3000);
                }
 
            } ",
            ))?>;
    return false; 
 
}
 
</script>
<?php 
$script = "
	
	$('#div_tallas .btn-group').on('click', 'a', function(e) {
		if ($(this).attr('href') == '#0'){
			//alert('entro');
			$('#div_tallas a.active').each(function(){
				if ($(this).attr('href') != '#0')
					$(this).removeClass('active');
			});
			//$(this).siblings('.active').removeClass('active');
			//if (!($(this).hasClass('active')))
			
		}
		if ($('a[href=\"#0\"]').hasClass('active') && $(this).attr('href') != '#0')
			return false;
		//alert('rafa');
		//if (($(this).hasClass('active')))
				
		//if (($(this).hasClass('active')))
		//	$(this).removeClass('active');
		 //alert($(this).attr('href'));
		 /*
		 var ids = 0;
		 $(this).siblings('.active').each(function(){
		 	//alert($(this).attr('href').substring(1));
		 	ids += parseInt($(this).attr('href').substring(1));
			
		 });
		 if (!($(this).hasClass('active')))
		 	ids += parseInt($(this).attr('href').substring(1));
		
		 $(this).parent().next('input').val(ids);*/
		 //return false;
		 e.preventDefault();
	 });
";
?>
<?php Yii::app()->clientScript->registerScript('botones',$script); ?>


<!-- Script mensaje de confirmación ON -->
<script type="text/javascript">

$('#guardaravanzar').on('click',function(){

	var campoVacio = false;
	$('.sku').each(function(){
	    if($.trim($(this).val()) == ''){
	        campoVacio = true;
	    }
	});	

	if(campoVacio){
		$('#myModal').modal({
			 keyboard: false
		});
		$('#myModalConfirmacion').modal('toggle');

		$('#myModalConfirmacion .si').click(function(e){ 
			handlerAjax();		
		});
	}	
	else{
		handlerAjax();
	}

});

function handlerAjax(){
	$.ajax({
	'type':'POST',
	'success':function( data )
	{
       data = JSON.parse( data );
       if(data.status=='success'){
       		window.location.href = '/site/producto/imagenes/1';
            //$('.error').hide();
            //$('#yw0').html('<div class="alert in alert-block fade alert-success">Se guardaron las cantidades</div>');
		}else{
			id = data.id;
			delete data['id'];
            $.each(data, function(key, val) {
            	key_tmp = key.split('_');
				key_tmp.splice(1,0,id);
            	key = key_tmp.join('_');
                $('#Tallacolor-Form #'+key+'_em_').text(val);                                                    
                $('#Tallacolor-Form #'+key+'_em_').show();
            });
		}
	},
	'data':$('#Tallacolor-Form').serialize(),
	'url':'/site/producto/tallacolor/1',
	'cache':false});
	return false;
}
</script>
<!-- Script mensaje de confirmación OFF -->
