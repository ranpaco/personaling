<?php
/* @var $this SiteImageController */
/* @var $model SiteImage */
/* @var $form CActiveForm */
?>

<div class="form horizontal">
<div class="pointer text_align_right margin_small" title="Cerrar" onclick="$('#toLoad').modal('toggle'); "><i class="icon-remove"></i></div>
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id'=>'site-image-form',
   
    'enableClientValidation'=>true,
   // 'type'=>'horizontal',
    'clientOptions'=>array(
        'validateOnSubmit'=>true, 
    ),
    'htmlOptions' => array(
        'enctype' => 'multipart/form-data',
    ),
)); 

?>
    <div class="row-fluid margin_bottom">
       <h3 class="margin_left">Carga una imagen</h3>
       <div class="span12 text_align_center">
           <p class="note"><small>Los campos con <span class="required">*</span> son requeridos.</small></p>
            <?php echo $form->errorSummary($model, Funciones::errorMsg()); ?>
       </div>
       <div class="span4 no_margin_left text_align_right">
             <?php echo $form->labelEx($model,'alt'); ?>         
       </div>
       <div class="span8">
           <?php echo $form->textField($model,'alt'); ?>
		   <?php echo $form->error($model,'alt'); ?>
       </div>
       
       
       <div class="span4 no_margin_left text_align_right">
            <?php echo $form->labelEx($model,'title'); ?>         
       </div>
       <div class="span8">           
            <?php echo $form->textField($model,'title'); ?>
            <?php echo $form->error($model,'title'); ?>
       </div>
       
       
       <div class="span4 no_margin_left text_align_right">
         <?php echo $form->labelEx($model,'copy'); ?>            
       </div>
       <div class="span8">           
        <?php echo $form->textArea($model,'copy',array('style'=>'resize:none','rows'=>'6')); ?>
        <?php echo $form->error($model,'copy'); ?>
       </div>
       
       
       <div class="span4 no_margin_left text_align_right">
        <?php echo $form->labelEx($model,'path'); ?>          
       </div>
       <div class="span8">
           <?php echo CHtml::activeFileField($model, 'path', array('required'=>'required','accept'=>'image/*'));?>
		  <?php echo $form->error($model,'path'); ?>
       </div>
       
       <div class="span4 no_margin_left text_align_right">
        <?php echo $form->labelEx($model,'link'); ?>          
       </div>
       <div class="span8">
           <?php echo $form->textField($model,'link'); ?>
            <?php echo $form->error($model,'link'); ?>
       </div>
       
       <?php
        
              echo CHtml::activeHiddenField($model,'index',array('value'=>$index));
              echo CHtml::activeHiddenField($model,'type',array('value'=>$type));
              echo CHtml::activeHiddenField($model,'group',array('value'=>$group));
              echo CHtml::activeHiddenField($model,'name',array('value'=>$name));  
         ?>               
        
    </div>
	

	<div class="buttons row-fluid">
		<?php echo CHtml::submitButton('Submit',array('class'=>'btn btn-danger btn-large span6 offset3')); ?>
	</div>
    
<?php $this->endWidget(); ?>

</div><!-- form -->