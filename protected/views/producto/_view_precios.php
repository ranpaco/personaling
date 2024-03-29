<?php
$this->breadcrumbs=array(
	'Productos'=>array('admin'),
	'Precios',
);
 if($precio->gananciaImpuesto)
 	$checked=" checked='checked' ";
 else {
     $checked="";
 }
?>

<div class="container margin_top">
  <div class="page-header">
    <h1>Editar Producto - Precio</small></h1>
    <h2 ><?php echo $model->nombre."  [<small class='t_small'>Ref: ".$model->codigo."</small>]"; ?></h2>
  </div>
  <?php echo $this->renderPartial('menu_agregar_producto', array('model'=>$model,'opcion'=>2)); ?>
  <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'producto-form',
	'enableAjaxValidation'=>false,
	'type'=>'horizontal',
)); ?>
  <?php echo $form->errorSummary($model, Funciones::errorMsg()); ?>
  
  
<?php
	echo CHtml::hiddenField('accion','def', array('id' => 'accion'));
	echo CHtml::hiddenField('iva',Yii::app()->params['IVA']);
	//<input id="accion" type="hidden" value="" />	
?>

  
  <div class="row margin_top">
    <div class="span9">
      <div class="bg_color3   margin_bottom_small padding_small box_1">
        <form method="post" action="/aiesec/user/registration?template=1" id="registration-form"   class="form-stacked form-horizontal" enctype="multipart/form-data">
          <fieldset>
            <legend >Precios: </legend>
            <!--
            <div class="control-group"> <?php echo $form->radioButtonListRow($precio, 'combinacion', array(0 => 'Aplicar datos a todas las combinaciones', 1 => 'Aplicar datos por separado a cada combinación',)); ?> <?php echo $form->error($precio,'combinacion'); ?> </div>
            -->
            <div class="control-group">
              <?php  echo $form->textFieldRow($precio, 'costo', array('class'=>'span5')); ?>
              <div class="controls">
                <div class=" muted">Precio al que compró este producto como mayorista </div>
              </div>
            </div>
            <div class="control-group"> <?php echo $form->textFieldRow($precio, 'precioVenta',
                    array('class'=>'span5')); ?>
              <div class="controls">
                <div class=" muted">Precio de venta de este producto (sin IVA)</div>
              </div>
            </div>
            <?php 
            if(Yii::app()->language=="es_es")
			{
			?>
				<div class="control-group"> <?php echo $form->dropDownListRow($precio, 'impuesto', array(0 => 'Sin IVA (Zona Libre)', 1 => 'Con IVA '.Yii::app()->params['IVAtext'].' (Tierra Firme)',2 => 'Ambos'), array('disabled'=>'disabled', 'options' => array('1'=>array('selected'=>true)))); ?> <?php echo $form->error($precio,'impuesto'); ?> </div>	
			<?php
			}
			else 
			{
			?>	
				<div class="control-group"> <?php echo $form->dropDownListRow($precio, 'impuesto', array(0 => 'Sin IVA (Zona Libre)', 1 => 'Con IVA '.Yii::app()->params['IVAtext'].'',2 => 'Ambos'), array('disabled'=>'disabled', 'options' => array('1'=>array('selected'=>true)))); ?> <?php echo $form->error($precio,'impuesto'); ?> </div>
			<?php
			}
            ?>
            
            <div class="control-group"> <?php echo $form->textFieldRow($precio, 'precioImpuesto', array('class'=>'span5')); ?> </div>
            
            
            <!--Tipo descuento-->
            <div class="control-group"> <?php echo $form->dropDownListRow($precio,
                    'tipoDescuento', array(0 => 'Porcentaje %', 1 => 'Monto en '.Yii::t('contentForm','currSym'),)); ?> 
                        <?php echo $form->error($precio,'tipoDescuento'); ?> 
            </div>
            
            <!--Valor del descuento-->
            <div class="control-group"> <?php echo $form->textFieldRow($precio,
                    'valorTipo', array('class'=>'span5','id'=>'valordescuento')); ?>
              <div class="controls">
                <div class=" muted">Si el producto no tendrá descuento ingrese 0</div>
              </div>
            </div>
            
            <div class="control-group"> <?php echo $form->textFieldRow($precio, 'ahorro', array('class'=>'span5','readonly'=>true)); ?> </div>
            <div class="control-group"> <?php echo $form->labelEx($precio,'precioDescuento', array('class' => 'control-label required')); ?>
              <div class="controls"> <?php echo $form->textField($precio, 'precioDescuento', array('class'=>'span5','readonly'=>true)); ?> <?php echo $form->error($precio,'precioDescuento'); ?> </div>
            </div>
            
            
            
            
            
             <div class="control-group"> <?php echo $form->textFieldRow($precio, 'ganancia', array('class'=>'span5')); ?> 
              <?php echo "<div class='span5'><input type='checkbox' id='ganImp' name='ganImp' ".$checked.">".$precio->getAttributeLabel('gananciaImpuesto')."</div>";
              echo CHtml::activeHiddenField($precio, 'gananciaImpuesto', array('value'=>'0')) ?></div>
          </fieldset>
        </form>
        <?php if(count($precio->anteriores)){ ?>
        <legend>Histórico de Precios</legend>
        <table width="100%" cellspacing="0" cellpadding="0" border="0" class="table table-bordered ta table-hover table-striped" align="center">
        	<thead>
        		<tr>
        			<th>Usuario</th>
        			<th>Costo</th>
        			<th>Precio Venta</th>
        			<th>Precio con Descuento</th>
        			<th>Precio con Impuesto</th>
        			<th>Tipo de Descuento</th>
        			<th>Valor de Descuento</th>
        			<th>Guardado en</th>
        		</tr>        		
        	</thead>
        	<tbody>
        		<?php foreach ($precio->anteriores as $historico) {
     					
						$ahorro=$historico->precioImpuesto-$historico->precioDescuento;
                         ?>
						
        		<tr>
        			<td><?php echo User::model()->getUsername($historico->user_id); ?></td>
        			<td><?php echo Yii::app()->numberFormatter->format("#,##0.00",$historico->costo); ?></td>
        			<td><?php echo Yii::app()->numberFormatter->format("#,##0.00",$historico->precioVenta); ?></td>
        			<td><?php echo Yii::app()->numberFormatter->format("#,##0.00",$historico->precioDescuento); ?></td>
        			<td><?php echo Yii::app()->numberFormatter->format("#,##0.00",$historico->precioImpuesto); ?></td>
        			
        			<td><?php 
        			 if($historico->tipoDescuento=="1")
        			 { 
        				 echo "Monto en €"; 
					 }
					 else
					 {
					 	if($historico->tipoDescuento=="0")
						{
							echo "Porcentaje %";
						}
						else 
						{
							echo "-";
						}	
					 	
					 } ?></td>
        			<td><?php echo Yii::app()->numberFormatter->format("#,##0.00",$ahorro); ?></td>
        			
        			<td width="23%"><?php echo date("d/m/Y h:i:s a",strtotime($historico->fecha)); ?></td>
        		</tr>
        		<?php } ?>
        	</tbody>
        	
        </table>
        	<?php } ?>
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
          <?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'danger',
			'size' => 'large',
			'block'=>'true',
			'htmlOptions' => array('id'=>'normal'),
			'label'=>'Guardar',
		)); ?>
          <ul class="nav nav-stacked nav-tabs margin_top">
            <li><a id="avanzar" style="cursor: pointer" title="Guardar y Siguiente" id="limpiar">Guardar y avanzar</a></li>
            <li><a style="cursor: pointer" title="Restablecer" id="limpiar">Limpiar Formulario</a></li>
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
</div>
<!-- /container -->
<?php $this->endWidget(); ?>
<script type="text/javascript">

var precioDeVenta = $("#Precio_precioVenta");
var precioConImpuesto = $("#Precio_precioImpuesto");
var tipoDeDescuento = $("#Precio_tipoDescuento");
var valorDelDescuento = $("#valordescuento");
var ahorroUsuario = $("#Precio_ahorro");
var precioConDescuento = $("#Precio_ahorro");


/*Al cambiar el precio de Venta*/
$("#Precio_precioVenta").keyup(function(){

        var uno;
        var dos;
        var valor;
        var precioDescuento;

	
	uno = document.getElementById("Precio_tipoDescuento").value;
	dos = document.getElementById("valordescuento").value;	
	valor = document.getElementById("Precio_impuesto").value;
	
        //Cambiar el precio con impuesto
        if(valor==0)
        {
        	$("#Precio_precioImpuesto").val(this.value);	
        }  
        else{                    		
            precioDescuento = parseFloat(this.value) * ( 1 + parseFloat($("#iva").val()));
            precioDescuento=redondeo2decimales(precioDescuento);			
            $("#Precio_precioImpuesto").val(precioDescuento);
        }
        
        //Cambiar el ahorro y el precio con descuento
	if(uno==0)
	{
            precio_ahorro=redondeo2decimales(precioDescuento * (dos/100));
            $("#Precio_ahorro").val(precio_ahorro);
            precio_descuento=redondeo2decimales(precioDescuento - (precioDescuento * (dos/100)));		
            $("#Precio_precioDescuento").val(precio_descuento);		
		
	}
	else
	{
            $("#Precio_ahorro").val(dos);
            $("#Precio_precioDescuento").val(redondeo2decimales(this.value - dos));
	}
    
});


$("#Precio_precioImpuesto").keyup(function(){

         var precio_impuesto; 
         var tipo;
         var valor;
         var descuento;
         var ahorro;   
        precio_impuesto = $("#Precio_precioImpuesto").val();
        
        tipo = document.getElementById("Precio_tipoDescuento").value;
		valor = document.getElementById("valordescuento").value;	
		
		 
		 ahorro = $("#Precio_ahorro").val(); 
		 descuento=$("#Precio_precioDescuento").val();
		 

		
		if(precio_impuesto=="0")
		{
			$("#Precio_precioVenta").val("0");
		}
		else
		{
			precio=precio_impuesto/(parseFloat($("#iva").val())+1);
		}
		
		
		precio=redondeo2decimales(precio);	
		$("#Precio_precioVenta").val(precio);
	
		
		if(tipo=="0")
		{
			ahorro=(precio_impuesto*valor)/100;
			ahorro=redondeo2decimales(ahorro);	
			$("#Precio_ahorro").val(ahorro);
			$("#Precio_precioDescuento").val(precio_impuesto-ahorro);
		}
		else
		{
			$("#Precio_ahorro").val(valor);
			$("#Precio_precioDescuento").val(precio_impuesto-valor);
			
		}
		//alert(tipo);
    
});


/*Al Cambiar el valor del descuento*/

$("#valordescuento").keyup(function(){

        var tipoDescuento;
	tipoDescuento = document.getElementById("Precio_tipoDescuento").value;
	var precioImpuesto = document.getElementById("Precio_precioImpuesto").value;
	console.log(precioImpuesto);
        /*Tipo de descuento 0-Porcentaje, 1-Fijo*/
	if(tipoDescuento == 0)
	{

            $("#Precio_ahorro").val(redondeo2decimales(precioImpuesto * (this.value/100)));		
            $("#Precio_precioDescuento").val(redondeo2decimales(precioImpuesto - (precioImpuesto * (this.value/100))));				
	}
	else
	{
            $("#Precio_ahorro").val(this.value);
            $("#Precio_precioDescuento").val(precioImpuesto - this.value);		
		
	}
	
    
});

/*Cambiar el tipo de iva - regla de impuestos*/
//$("#Precio_impuesto").click(function(){
//	
//var uno;
//var dos;
//var tres;
//
//uno= document.getElementById("Precio_impuesto").value;
//dos= document.getElementById("Precio_precioDescuento").value;	
//	
//	if(uno==0)
//		$("#Precio_precioImpuesto").val(dos);
//	
//	if(uno==1 || uno==2){
//		tres = parseFloat(dos) * parseFloat($("#iva").val());
//		
//		dos = parseFloat(dos)+parseFloat(tres);
//		
//		$("#Precio_precioImpuesto").val(dos);
//	}
//
//	
//});


$("#Precio_tipoDescuento").change(function(){

    /*Que buenos nombres de variables*/
    var uno;
    var dos;
    var tres;
    var cuatro;
    var cinco;
    var pre;
    var valor;

    uno = $("#Precio_precioVenta").val();
    dos = $("#valordescuento").val();	
    tres = $("#Precio_ahorro").val();
    cuatro = $("#Precio_precioDescuento").val();
    cinco = $("#Precio_tipoDescuento").val();

    var precioImpuesto = $("#Precio_precioImpuesto").val();

    if(cinco==0){

        $("#Precio_ahorro").val(redondeo2decimales(precioImpuesto * (dos/100)));		
        $("#Precio_precioDescuento").val(redondeo2decimales(precioImpuesto - (precioImpuesto * (dos/100))));

    }else{ 

        $("#Precio_ahorro").val(dos);		
        $("#Precio_precioDescuento").val(precioImpuesto - dos);

    }

	
});


    $('a#limpiar').on('click', function() {
			
			$('#producto-form').each (function(){
			  this.reset();
			});
			
			 $('#producto-form').find(':input').each(function() {
            switch(this.type) {
                case 'password':
                case 'select-multiple':
                case 'select-one':
                case 'text':
                case 'textarea':
                    $(this).val('');
                    break;
                case 'checkbox':
                case 'radio':
                    this.checked = false;
            }
        });

       });
	
	
    $('#normal').on('click', function(event) {
        event.preventDefault();

        // cambio el valor
        $("#accion").attr("value", "normal");
        //alert( $("#accion").attr("value") );

        // submit del form
        $('#producto-form').submit();

    });
	
	
    $('a#avanzar').on('click', function(event) {

            event.preventDefault();

            $("#accion").attr("value", "avanzar");
            //alert( $("#accion").attr("value") );

            // submit del form
            $('#producto-form').submit();

            }
    );
	
    $("#ganImp").click(function(){
    if($("#ganImp").is(':checked')) {
            $('#Precio_gananciaImpuesto').val('1');
            }
    else
            {$('#Precio_gananciaImpuesto').val('0');
    }
});

	function redondeo2decimales(numero)
	{
		var flotante = parseFloat(numero);
		var resultado = Math.round(flotante*100)/100;
		return resultado;
	}
	
</script>