<?php

$this->breadcrumbs = array(
    'Productos' => array('admin'),
    'Importar',
);
?>
<!-- FLASH ON --> 
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



if(isset( Yii::app()->session['contar']))
{
	 echo CHtml::link('Ver detalle de Inbound', $this->createUrl('/inbound/detalle/'.Yii::app()->session['contar']), array('class'=>'btn btn-success', 'role'=>'button'));
	 unset(Yii::app()->session['contar']);  
}

?>	
<!-- FLASH OFF --> 

<div class="row margin_top">
    <div class="span12">
        <?php
        if ($total > 0 || $actualizar > 0) {
            echo "<h3>Total de productos en el archivo: <b>" . ($total + $actualizar). "</b></h3>";            
            echo "<h4>Productos nuevos: <b>" . $total . "</b></h4>";
            echo "<h4>Productos actualizados: <b>" . $actualizar . "</b></h4><br><hr><br>";
            //echo $tabla. "<br/><br/>";
        }
        ?>
        <?php
        if ($totalInbound > 0) {
            echo "<h3>Total de productos en el archivo: <b>" . $totalInbound. "</b></h3>";            
            echo "<h4>Productos actualizados: <b>" . $actualizadosInbound . "</b></h4><br><hr><br>";            
        }
        ?>
        <div class="page-header">
            <h1>Importar Productos</h1>
        </div>        
        <div class="bg_color3 margin_bottom_small padding_small box_1">
            <?php
            $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                    'action' => CController::createUrl('importar'),
                    'id' => 'form-validar',
                    'enableAjaxValidation' => false,
                    'type' => 'horizontal',
                    'htmlOptions' => array('enctype' => 'multipart/form-data'),
                ));
                echo TbHtml::hiddenField("valido", 1);
            
            ?>
            <fieldset>
                <legend>1.- Realizar la validación previa del archivo: <?php echo " (".
   CHtml::link("Descargar plantilla MasterData",Yii::app()->getBaseUrl()."/docs/PlantillaMasterData.xlsx",
      array('class'=>'donwload_link')
   )
  .")";?></legend>
                
                <div class="well span5">
                    
                    <?php
                    $this->widget('CMultiFileUpload', array(
                        'name' => 'validar',
                        'accept' => 'xls|xlsx', // useful for verifying files
                        'duplicate' => 'El archivo está duplicado.', // useful, i think
                        'denied' => 'Tipo de archivo inválido.', // useful, i think
                    ));
                    ?>
                    
                    <div class="margin_top_small">	              		    
                        <?php
                        $this->widget('bootstrap.widgets.TbButton', array(
                            'buttonType' => 'submit',
                            'type' => 'danger',
                            'label' => 'Validar',
                            'icon' => 'ok white',
                            'htmlOptions' => array(
                                'name' => 'validar'
                            ),
                        ));
                        ?>
                    </div>
                    
                </div>
                
                <legend>2.- Subir archivo previamente validado: </legend>
                <div class="well span5">
                   
                    <?php
                    $this->widget('CMultiFileUpload', array(
                        'name' => 'url',
                        'accept' => 'xls|xlsx', // useful for verifying files
                        'duplicate' => 'El archivo está duplicado.', // useful, i think
                        'denied' => 'Tipo de archivo inválido.', // useful, i think
                    ));
                    ?>

                    <div class="margin_top_small">	              		    
                        <?php
                        $this->widget('bootstrap.widgets.TbButton', array(
                            'buttonType' => 'submit',
                            'type' => 'warning',
                            'icon' => 'upload white',
                            'label' => 'Cargar MasterData',
                            'loadingText'=>'Cargando ...',
                            'htmlOptions' => array(
                                'name' => 'cargar',
                                'id'=>'buttonCargaMD',
                            ),
                        ));
                        ?>
                    </div>
                </div>
                <legend>3.- Descargar archivo Excel para generar el Inbound: </legend>
                <div class="well span5">
                    <div class="row-fluid">
                        <div class="span7">
                            <?php 
                            echo TbHtml::dropDownList("Marca", "",
                            TbHtml::listData(Marca::model()->findAll(array(
                                "order" => "nombre",
                            )), "id", "nombre"),
                                    array(
                                        'empty' => "-Seleccione-"
                                    )
                                    );
                            ?>
                        </div>
                        <div class="span5">
                            <?php
                            
                            $this->widget('bootstrap.widgets.TbButton', array(
                                'buttonType' => 'submit',
                                'type' => 'info',
                                'icon' => 'download-alt white',
                                'label' => 'Descargar Archivo',
                                'htmlOptions' => array(
                                    'name' => 'generar'
                                ),
                            ));
                            ?>
                        </div>
                    </div>                    
                        
                </div>
                <legend>4.- Subir Excel para Inbound: </legend>
                <div class="well span5">
                    <?php
                    $this->widget('CMultiFileUpload', array(
                        'name' => 'inbound',
                        'accept' => 'xls|xlsx', // useful for verifying files
                        'duplicate' => 'El archivo está duplicado.', // useful, i think
                        'denied' => 'Tipo de archivo inválido.', // useful, i think
                    ));
                    ?>

                    <div class="margin_top_small">	              		    
                        <?php
                        $this->widget('bootstrap.widgets.TbButton', array(
                            'buttonType' => 'submit',
                            'type' => 'warning',
                            'icon' => 'upload white',
                            'label' => 'Cargar Inbound',
                            'loadingText'=>'Cargando ...',
                            'htmlOptions' => array(
                                'name' => 'cargarIn',
                                'id' => 'buttonCargaIB',
                            ),
                        ));
                        ?>
                    </div>
                    

                </div>
            </fieldset>
            
            <?php $this->endWidget(); ?>

        </div>	
    </div>
</div>
<script type="text/javascript">

$('#buttonCargaMD').click(function(e) {
    var btn = $(this);
    var res = confirm("El archivo será cargado.\n¿Está seguro de que ha sido validado ya?");
    if (res == true) {
        btn.button('loading'); // call the loading function
        $("body").addClass("aplicacion-cargando");
       
    } else {
       e.preventDefault();
    }
    
});

$('#buttonCargaIB').click(function(e) {
    var btn = $(this);
    var res = confirm("El archivo será cargado.\n¿Está seguro de que no contiene errores?");
    if (res == true) {
        btn.button('loading'); // call the loading function
        $("body").addClass("aplicacion-cargando");
       
    } else {
       e.preventDefault();
    }
    
});



</script>


