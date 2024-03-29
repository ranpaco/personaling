<?php
/* @var $form TbActiveForm */
 $this->pageTitle = Yii::app()->name . ' - Personal Shoppers';
//$this->breadcrumbs=array(
//'Usuarios',
//);
?>

<style>
    h5 a.link-ps{
        text-decoration: underline;
    }
</style>
<div class="container margin_top">

    <?php
    $this->widget('bootstrap.widgets.TbAlert', array(
        'block' => true, // display a larger alert block?
        'fade' => true, // use transitions?
        'closeText' => '&times;', // close link text - if set to false, no close link is displayed
        'alerts' => array(// configurations per alert type
            'success' => array('block' => true, 'fade' => true, 'closeText' => '&times;'), // success, info, warning, error or danger
            'error' => array('block' => true, 'fade' => true, 'closeText' => '&times;'), // success, info, warning, error or danger
        ),
            )
    );
    ?> 

    <div class="page-header">
        <h1>Pago de comisiones por afiliación a productos de terceros</h1>
    </div>    
    
    <div class="row">
        <div class="span6">
           <fieldset>
               <legend>Datos del último pago realizado</legend>
               <?php if($lastPayment){ ?>
                <ul class="no_bullets no_margin_left">                    
                    <li><strong>Fecha y hora: </strong>
                        <?php echo date("d-m-Y h:m:i a", strtotime($lastPayment->created_at)); ?>
                    </li>
                    <li><strong>Monto pagado: </strong>
                        <?php echo $lastPayment->getAmount() . " " .
                        Yii::t('contentForm', 'currSym'); ?>
                    </li>                    
                </ul>
               <?php }else{ ?>
               <h4>No se ha hecho ningún pago hasta el momento</h4>
               <?php } ?>
            </fieldset>
        </div>
        <div class="span6">
            
             <fieldset>                
                <legend>Ingresa el monto para el periodo actual</legend>
                    <?php
                        $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                            'id'=>'pago-form',
                            "htmlOptions" => array(
                                "class" => "text_align_center"                                                                
                            )
                        ));     
                    ?>
                
                    <div class="control-group input-prepend margin_left_small">                        
                        <div class="controls">
                            <span class="add-on"><?php echo Yii::t('contentForm', 'currSym'); ?></span>                            
                            
                            <?php
                                echo TbHtml::numberField('monthlyEarning', 0, array(                                
                                    'step' => 'any',
                                    'min' => "1",
                                    'max' => 25000,                                
                                    'class' => "span2",                                
                                ));
                            
                                echo TbHtml::submitbutton("Pagar", array(
                                    "id" => "pay",
                                    "color" => "warning",
                                    "class" => "margin_left_small",
                                ));   
                               
                            ?>
                            
                        </div>
                    </div>
                    <?php
                        $this->endWidget();
                    ?>
            </fieldset>
        </div>
    </div>
    <div class="row">
	    <div class="span10">
	    	<fieldset>
	    		<legend>Filtrar por fechas</legend>
	    	<div class="control-group margin_left_medium">
	    		<div class="controls">
	    	<?php
	    		echo TbHtml::dateField('first', 0, array('class' => "",));
				echo TbHtml::dateField('second', 0, array('class' => "margin_left_small",));
				echo TbHtml::button('Filtrar', array('color' => TbHtml::BUTTON_COLOR_SUCCESS,'class' => "margin_left_small",'id'=>'filtrar','name'=>'filtrar')); 
			?>
				</div>
			</div>
			</fieldset>              		
	    </div>
	 </div>
    <div class="row margin_top margin_bottom ">
        <div class="span4">
            <?php
            
//            echo CHtml::beginForm(CHtml::normalizeUrl(array('index')), 'get', array('id' => 'search-form'))
//            . '<div class="input-prepend"> <span class="add-on"><i class="icon-search"></i></span>'
//            . CHtml::textField('nombre', (isset($_GET['string'])) ? $_GET['string'] : '', array('id' => 'textbox_buscar', 'class' => 'span3', 'placeholder' => 'Buscar'))
//            . CHtml::endForm();
            ?>
        </div>
    </div>
    <div class="span3">
        <?php // echo CHtml::dropDownList("Filtros", "", Chtml::listData(Filter::model()->findAll('type = 8'), "id_filter", "name"), array('empty' => '-- Búsquedas avanzadas --', 'id' => 'all_filters'))
        ?>
    </div>
    <!--<div class="span3 "><a href="#" class="btn  crear-filtro">Crear búsqueda avanzada</a></div>-->
    
</div>

<?php // $this->renderPartial("_filters"); ?>
<!--<hr/>-->
<?php 
$template = '{summary}
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-hover table-striped table-condensed">
    <tr>      
      <th colspan="3" rowspan="2" scope="col">Personal Shopper</th>
      <th colspan="2" scope="col" style="text-align: center">Visitas Generadas</th>
      <th rowspan="2" scope="col" style="text-align: center">Porcentaje<br>de<br>Comisión</th>
      
      <th rowspan="2" scope="col" style="text-align: center">Monto a pagar ('.Yii::t('contentForm', 'currSym').')</th>            
    </tr>
    <tr>
      <th scope="col">Totales</th>
      <th scope="col">Per. Actual</th>      
    </tr>
    {items}
    </table>
    {pager}';
	
		 $pagerParams=array(
        'header'=>'',
        'prevPageLabel' => Yii::t('contentForm','Previous'),
        'nextPageLabel' => Yii::t('contentForm','Next'),
        'firstPageLabel'=> Yii::t('contentForm','First'),
        'lastPageLabel'=> Yii::t('contentForm','Last'),
        'htmlOptions'=>array(
            'class'=>'pagination pagination-right'));   

$this->widget('zii.widgets.CListView', array(
    'id' => 'list-auth-items',
    'dataProvider' => $dataProvider,
    'itemView' => '_view_ps_comision',
    'template' => $template,
    'summaryText' => 'Mostrando {start} - {end} de {count} Resultados',                
    'afterAjaxUpdate'=>" function(id, data) {

        

      } ",
     'pager' =>$pagerParams, 
));


	Yii::app()->clientScript->registerScript('filtrar', "
        var ajaxUpdateTimeout;

        $('#filtrar').click(function(){
            	inicio = $('#first').attr('value');
				final = $('#second').attr('value');
					
				// alert(inicio+' '+final);	
            	datos = $('#first, #second').serialize();
            	
            	ajaxUpdateTimeout = setTimeout(
                    function () {
                		$.fn.yiiListView.update(
                        // this is the id of the CListView
                            'list-auth-items',
                            {data: datos}
                        )
					},
                    // this is the delay
                    300); 
                                 
        });"
	);
?> 

</div>

<!-- /container -->
<script>

var validSubmit = false;

/* Funcion para cambiar los montos que le corresponden a cada PS de acuerdo al 
 * monto ingresado en el campo #monthlyEarning
 * */
function cambiarMontosEnTabla(e){
    
    
    var monthlyEarning = $("#monthlyEarning").val();
    
    $("input[name ^= 'amount']").each(function(index, element){

        var id = $(element).attr("id");
        var percentage = $("#percentage-"+id).val();
        /* Asign corresponding amount to each PS*/
        $(element).val(monthlyEarning * percentage);

    });    
    
}

function filtrarFechas(){
    
//	inicio = $("#first").attr("value");
//	final = $("#second").attr("value");
	
	alert("Va el otro alert");
}

/*Action when the form pago-form is submitted*/
function formSubmit(e){
    
    if(!validSubmit){
        
        e.preventDefault();    
        bootbox.confirm("Se realizará el pago a todas las personal shoppers en\n\
            Personaling, ¿Deseas continuar?",
            function(result) {

                if(result){
                    validSubmit = true;
                    //disable the button, start the loading animation
                    //and submit the form
                    
                    $('#pay').attr("disabled", true);
                    $('body').addClass("aplicacion-cargando");
                    $('form#pago-form').submit();

                }

            }
        );
    }
        
}


$(document).ready(function(){

    $('#monthlyEarning').change(cambiarMontosEnTabla).keypress(cambiarMontosEnTabla);            
//    $('button#pay').click(accionBotonPagar);
    $('form#pago-form').submit(formSubmit);
 
});    
</script>