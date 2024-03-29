	<?php 

$this->breadcrumbs=array(
	'Usuarios'=>array('admin'),
	'Editar',
);


function getMonthsArray()
    {
        
         $months['01'] = "Enero";
		 $months['02'] = "Febrero";
		 $months['03'] = "Marzo";
		 $months['04'] = "Abril";
		 $months['05'] = "Mayo";
		 $months['06'] = "Junio";
		 $months['07'] = "Julio";
		 $months['08'] = "Agosto";
		 $months['09'] = "Septiembre";
		 $months['10'] = "Octubre";
		 $months['11'] = "Noviembre";
		 $months['12'] = "Diciembre";
    

        return array(0 => 'Mes:') + $months;
    }

     function getDaysArray()
    {
		$days['01'] = '01';
		$days['02'] = '02';
		$days['03'] = '03';
		$days['04'] = '04';
		$days['05'] = '05';
		$days['06'] = '06';
		$days['07'] = '07';
		$days['08'] = '08';
		$days['09'] = '09';
        for($dayNum = 10; $dayNum <= 31; $dayNum++){
            $days[$dayNum] = $dayNum;
        }

        return array(0 => 'Dia:') + $days;
    }

     function getYearsArray()
    {
        $thisYear = date('Y', time());

        for($yearNum = $thisYear; $yearNum >= 1920; $yearNum--){
            $years[$yearNum] = $yearNum;
        }

        return array(0 => 'Año:') + $years;
    }
    $val=true;
	if($model->personal_shopper!=0)
		$profile->setScenario('PS');
		
	?>
<style>
    #userPS{
        padding-top: 8px;
        padding-bottom: 8px;
        padding-right: 12px;
        padding-left: 12px;
        border: 1px solid #ddd;
        
        
    }
    #userPS a{
        text-decoration: none;
        
    }
    #userPS a:hover{
        color:#000;
        background-color: #DDD;
    }
    #userPS.open{
        color:#888;        
    }
    #userPS>.dropdown-menu{
        width:100%;
        margin-top: 0px;
        border-radius: 0px;
        padding-top: 0px;
        border-top: 0px;
        margin-left:-1px;
    }
    
    .nav-tabs .open .dropdown-toggle{
        color:#444;
    }
    
    .nav .dropdown-toggle:hover .caret{
        border-top-color: black;
    }
    .nav .dropdown-toggle .caret{
        border-top-color: black;
    }
 
</style>
<div class="container margin_top">
  <div class="page-header">
    <h1>Editar Usuario</h1>
  </div>
  <!-- SUBMENU ON -->
  <?php $this->renderPartial('_menu', array('model'=>$model, 'activo'=>1)); ?>
  <!-- SUBMENU OFF -->
  <div class="row margin_top">
    <div class="span9">
      <div class="bg_color3   margin_bottom_small padding_small box_1">
        <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'registration-form',
	'htmlOptions'=>array('class'=>'form-horizontal','enctype'=>'multipart/form-data'),
    'type'=>'horizontal',
   // 'type'=>'inline',
	'enableClientValidation'=>$val,
	
	'clientOptions'=>array(
		'validateOnSubmit'=>$val,
	),
	
)); ?>          
          
        <fieldset>
          <legend >Datos de Usuario: </legend>
          <?php echo $form->textFieldRow($model,'email',array('class'=>'span5',)); ?>
<!--           <div class="control-group">
            <label for="" class="control-label ">Contraseña </label>
            <div class="controls">
              <input type="password" placeholder="Contraseña"  class="span5">
              <div style="display:none" class="help-inline">Ingrese una contraseña</div>
            </div>
          </div> -->
          
          
          <div class="control-group"> 
              <label for="" class="control-label ">Administrador</label>  
              <div class="controls"> 
        <?php   echo CHtml::textField('superuser', 
          $model->superuser == 1?"Si":"No" ,
                        array('disabled'=>'disabled',
                              'id' => 'User_status',
                              'class' => 'span2')); ?> 
               </div>               
                              
          
          </div>
          
          
          
          
          <div class="control-group"> 
              <label for="" class="control-label ">Personal Shopper</label>  
              <div class="controls"> 
        <?php   echo CHtml::textField('personal_shopper', 
          $model->personal_shopper == 1?"Si":($model->personal_shopper == 2?"Aplicante":"No") ,
                        array('disabled'=>'disabled',
                              'id' => 'User_status',
                              'class' => 'span2')); ?> 
               </div>               
                              
              <?php if($model->personal_shopper == 1){ ?>
              <span class="label label-warning" 
                    style="margin-left: 180px;padding: 4px 10px;font-size: 12px;
                    <?php echo ($model->ps_destacado)? '':'display:none;'; ?>">
                  
                    Personal Shopper Destacado
                    
              </span>
              <?php } ?>
          </div>
          <div class="control-group">
            <label for="" class="control-label ">Estado </label>  
            <div class="controls">  
                <?php echo CHtml::textField('Status', User::getStatus($model->status),
                        array('disabled'=>'disabled',
                              'id' => 'User_status',
                              'class' => 'span2'));
                //$form->textFieldRow($model,'status',array('class'=>'span5', 'value' => User::getStatus($model->status)));
                //$form->dropDownListRow($model,'status', User::getStatus(),array('class'=>'span2')); 
                 	            ?> 
            </div></div>
            <div class="control-group">
            		
            <?php  
 
            		 echo $form->dropDownListRow($model,'interno',array(0=>'No',1=>'Si'),array('class'=>'span2')); ?>
     
            </div>
            
          <legend >Datos básicos: </legend>
          <?php 
		$profileFields=$profile->getFields();
		if ($profileFields) {
			foreach($profileFields as $field) {
				//echo $field->varname;
			?>
          <?php 
		if ($widgetEdit = $field->widgetEdit($profile)) {
			echo $widgetEdit;
		} elseif ($field->range) {
			if ($field->varname == 'sex')
				echo $form->radioButtonListInlineRow($profile,$field->varname,Profile::range($field->range));
			else
				echo $form->dropDownListRow($profile,$field->varname,Profile::range($field->range));
						
			//echo $form->dropDownListRow($profile,$field->varname,Profile::range($field->range));
			//echo $form->radioButtonListRow($profile,$field->varname,Profile::range($field->range));
		} elseif ($field->field_type=="TEXT") {
			echo $form->textArea($profile,$field->varname,array('rows'=>6, 'cols'=>50));
		} elseif ($field->field_type=="DATE") {
			echo '<div class="control-group">';
			echo '<div class="controls">';

			echo $form->labelEx($profile,$field->varname);	
			echo $form->DropDownList($profile,'day',getDaysArray(),array('class'=>'span1'));
			echo ' ';
			echo $form->DropDownList($profile,'month',getMonthsArray(),array('class'=>'span2'));
			echo ' ';
			echo $form->DropDownList($profile,'year',getYearsArray(),array('class'=>'span1'));
			echo '</div></div> ';
			echo $form->hiddenField($profile,$field->varname);
			//echo $form->textFieldRow($profile,$field->varname,array('class'=>'span5','maxlength'=>(($field->field_size)?$field->field_size:255)));
			echo '';
				 
				
		} else {
			echo $form->textFieldRow($profile,$field->varname,array('class'=>'span5','maxlength'=>(($field->field_size)?$field->field_size:255)));
		}	
		 ?>
          <?php echo $form->error($profile,$field->varname); ?>
          <?php
			}
		}
?>
        </fieldset>
        <?php $this->endWidget(); ?>
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
							top: "70px",
						});
					} else {
						if(st <= ot) {
							s.css({
								position: "relative",
								top: "0",
							});
						}
					}
				};
				$(window).scroll(move);
				move();
			}
		</script>
        <div>
          <div id="scroller-anchor"></div>
          <div id="scroller">
            <?php $this->widget('bootstrap.widgets.TbButton', array(
            				'buttonType'=>'button',
						    'label'=>'Guardar Cambios',
						    'type'=>'danger', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
						    'size'=>'large', // null, 'large', 'small' or 'mini'
						    'block'=>'true',
						    'htmlOptions'=>array('onclick'=>'js:$("#registration-form").submit();')
						)); ?>
            <ul class="nav nav-stacked nav-tabs margin_top">
              <li><a href="#" title="Limpiar Formulario" id="limpiar"><i class="icon-repeat"></i> Limpiar Formulario</a></li>
              <li>
              	<?php
              	if($model->superuser==1)
				{
					$message="<i class='icon-user'></i> Quitar Administrador";
				}
              	else{
              		$message="<i class='icon-user'></i> Hacer Administrador";
              	} 

				echo CHtml::ajaxLink(
					  $message,
					  Yii::app()->createUrl( 'user/admin/toggle_admin' ,array('id'=>$model->id)),
					  array( // ajaxOptions
					    'type' => 'POST',
					    'dataType'=>'json',
					    'beforeSend' => "function( request )
					                     {
					                       // Set up any pre-sending stuff like initializing progress indicators
					                     }",
					    'success' => "function( data )
					                  {
					                    // handle return data
					                   // alert( data.status );
					                   // alert(data.admin);
					                    if (data.status == 'success'){
					                    	$('#User_superuser').val(data.admin);
					                    	 var text;
											 var msj;
					                    	 if(data.admin==1)
					                    	 {		
					                    	 	 text='Quitar Administrador';
												 msj='Es Administrador';
					                    	 }
											 else 
											 {
												text='Hacer Administrador';
												msj='Ya no es Administrador'; 
											 }
					                    	 $('#messagePS').html(''+msj);
											$('#alertPS').show();
											               
                                             var child = $('#ps_admi').children(); 
                                            $('#ps_admi').html(child).append(' '+text);
					                    }
					                    	
					                  }",
					  //  'data' => array( 'val1' => '1', 'val2' => '2' )
					  ),
					  array( //htmlOptions
					    'href' => Yii::app()->createUrl( 'user/admin/toggle_admin' ),
					    'id' => "ps_admi",
					    //'class' => $class
					  )
					);
?>
              	              	
              </li>
              
              	<?php
                  /*  
                    if($model->personal_shopper == 1){
                        $titulo = 'Quitar Personal Shopper';
                    }else if($model->personal_shopper == 2){
                        $titulo = 'Aprobar como Personal Shopper';
                    }else{
                        $titulo = 'Hacer Personal Shopper';

                    }
				echo CHtml::ajaxLink(
					  "<i class='icon-user'></i> {$titulo}",
					  Yii::app()->createUrl( 'user/admin/toggle_ps' ,array('id'=>$model->id)),
					  array( // ajaxOptions
					    'type' => 'POST',
					    'dataType'=>'json',
					    'beforeSend' => "function( request )
					                     {
					                       // Set up any pre-sending stuff like initializing progress indicators
					                     }",
					    'success' => "function( data )
					                  {
					                    // handle return data
					                   // alert( data.status );
					                   // alert(data.personal_shopper);
					                    if (data.status == 'success'){
					                    	$('#User_personal_shopper').val(data.personal_shopper);
					                    	
					                    	var text;
											 var msj;
					                    	 if(data.personal_shopper == 1)
					                    	 {		
					                    	 	 text='Quitar Personal Shopper';
												 msj='Es Personal Shopper';
					                    	 }
											 else 
											 {
												text='Hacer Personal Shopper';
												msj='Ya no es Personal Shopper'; 
											 }
					                    	 $('#messagePS').html(''+msj);
											 $('#alertPS').show();                       
                                            var child = $('#ps_link').children(); 
                                            $('#ps_link').html(child).append(' '+text);
                                         }
					                  }",
					  //  'data' => array( 'val1' => '1', 'val2' => '2' )
					  ),
					  array( //htmlOptions
					    'href' => Yii::app()->createUrl( 'user/admin/toggle_ps' ),
					    'id' => "ps_link",
					  )
					);
*/?>
            
                 
             	 
              	 
              	 
              	 
              	 
              	 
              	 
              	 
              	 
              	 
              	              	
           
              <?php if($model->personal_shopper == 1){ ?>
              <li>
              <?php 
                    if($model->ps_destacado){
                        $titulo = 'Quitar PS destacado';
                        $icon = 'down';
                    }else{
                        $titulo = 'Destacar Personal Shopper';
                        $icon = 'up';
                    }
                    echo CHtml::ajaxLink(
                              "<i class='icon-thumbs-{$icon}'></i> {$titulo}",
                              Yii::app()->createUrl( 'user/admin/toggleDestacado' ,array('id'=>$model->id)),
                              array( // ajaxOptions
                                'type' => 'POST',
                                'dataType'=>'json',
                                'success' => "function( data )
                                              {
                                                // handle return data
                                               // alert( data.status );
                                               // alert(data.personal_shopper);
                                                if (data.status == 'success'){
                                                    //$('#User_personal_shopper').val(data.personal_shopper);

                                                    //bootbox.alert(\"¡Se ha aprobado la solicitud para Personal Shopper!\");
                                                    var text = data.personal_shopper == 1? 'Quitar PS destacado':'Destacar Personal Shopper';
                                                    console.log(data);
                                                    var child = $('#psDestacado_link').children(); 
                                                    $('#psDestacado_link').html(child).append(' '+text);
                                                    $('#psDestacado_link').children('i').toggleClass('icon-thumbs-down');
                                                    $('#psDestacado_link').children('i').toggleClass('icon-thumbs-up');
                                                    $('span.label-warning').toggle();                                                   

                                                 }
                                              }",
                              //  'data' => array( 'val1' => '1', 'val2' => '2' )
                              ),
                              array( //htmlOptions
                                'href' => Yii::app()->createUrl( 'user/admin/toggleDestacado' ),
                                'id' => "psDestacado_link",
                              )
                            );
                ?> 
                  </li>
              <?php }//Fin si es personalshopper ?>
              <li><a href="#" title="Guardar"><i class="icon-bell"></i> Reenviar Invitación</a></li>
              
              <li>
                  
                  <?php
                  $text = ($model->status == User::STATUS_BANNED)? 'Desbloquear':'Bloquear';
                  echo CHtml::ajaxLink(
                      "<i class='icon-off'></i> {$text}",
                      Yii::app()->createUrl('user/admin/toggle_banned', array('id' => $model->id)), 
                      array(// ajaxOptions
                      'type' => 'POST',
                      'dataType' => 'json',
                      'beforeSend' => "function( request )
                                     {
                                       // Set up any pre-sending stuff like initializing progress indicators
                                     }",
                      'success' => "function( data )
                                  {     
                                    
                                    if (data.status == 'success'){
                                        $('#User_status').val(data.user_status);
                                        
                                        var text = data.user_status == 'Bloqueado'? 'Desbloquear':'Bloquear';
                                        var child = $('#ban_link').children();                                      
                                        
                                        $('#ban_link').html(child).append(' '+text);                                        
                                    }
                                    else if(data.status == 'error')
                                    {
                                        console.log(data.error);
                                    }
                                  }",
                         
                          ), 
                          array(//htmlOptions
                                'href' => Yii::app()->createUrl('user/admin/toggle_banned'),
                                 'id' => 'ban_link'
                          )
                  );
                  ?>
              </li>
               
              <li>
              	<?php 
              	
				echo CHtml::link("<i class='icon-user'></i> Historial de Personal Shopper", array('historial', 'id'=>$model->id));

              	//echo CHtml::button("Historial de Personal Shopper", array('submit'=>array('historial')));
              	/*echo CHtml::ajaxLink(
					  "<i class='icon-user'></i> Ver Historial de Personal Shopper",
					  Yii::app()->createUrl( 'user/admin/todmin' ,array('id'=>$model->id)));*/
					/* echo CHtml::ajaxLink(
					  "<i class='icon-user'></i> Ver Historial de Personal Shopper",array('id'=>'botonedespues', 'submit'=>array('historial'))); */
				/*echo CHtml::ajaxLink(
					  "<i class='icon-user'></i> Ver Historial de Personal Shopper",
					  Yii::app()->createUrl( 'user/admin/todmin' ,array('id'=>$model->id)));*/
              	 ?>
              </li>
              <li>
               <div class="dropdown" id="userPS">
                    <a class="dropdown-toggle" data-toggle="dropdown" >
                        <div class="dropinput" >
                               <i class='icon-user'></i><span> Status PS</span>
                            <small> 
                                    <b class="caret pull-right" ></b>
                            </small>
                                
                            <span id="successPS" style="display:none">
                                <i class="icon-ok"></i>
                            </span>
                            <span id="errorPS" style="display:none">
                                <i class="icon-remove"></i>
                            </span>
                                
                            
                        </div>
                    </a>
                    <ul class="dropdown-menu" >
                        
                    <li id="ps0" class="pointer userPS <?php echo $model->personal_shopper==0?"active":"";?> option" >
                        <a onclick="statusPS('<?php echo $model->id;?>','0')">Ninguno</a>
                    </li>
                    <li id="ps2" class="pointer userPS <?php echo $model->personal_shopper==2?"active":"";?> option" >
                        <a onclick="statusPS('<?php echo $model->id;?>','2')">Aplicante a PS</a>
                    </li>
                    <li id="ps1" class="pointer userPS <?php echo $model->personal_shopper==1?"active":"";?> option">
                        <a onclick="statusPS('<?php echo $model->id;?>','1')">Personal Shopper</a>
                    </li>
                           
                    </ul>  
                </div>
                <div class="muted" id="messagePS" style="display:none">
                    
                </div>
                </li>
            </ul>
          </div>
        </div>
        <script type="text/javascript"> 
		// Script para dejar el sidebar fijo Parte 2
			$(function() {
				moveScroller();
			 });

		$('a#limpiar').on('click', function() {
			
			$('#registration-form').each (function(){
			  this.reset();
			});
			
			 $('#registration-form').find(':input').each(function() {
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
        function statusPS(id, status) {
            

            $.ajax({
                type: "post",
                'url' :"../../statusPs",
                dataType:'json',
                data: { 
                'id':id,'status':status}, 
                'success': function(data){
                     if(data.status=='success'){
                            
                            $('#ps'+data.personal_shopper).addClass('active');
                            $('#ps'+data.previous).removeClass('active');
                            $('#successPS').show();
                            $('#successPS').fadeOut(2000);
                            $('#messagePS').html(data.alert);
                            $('#messagePS').show();
                            $('#messagePS').fadeOut(6000);
                           
                            console.log(data);
                     } else{
                         $('#errorPS').show();
                         $('#errorPS').fadeOut(2000);
                     }                          
                },
                'cache' :false
            });
        }
       
       
		</script> 
        <!-- SIDEBAR OFF --> 
        
      </div>
    </div>
  </div>
</div>

<div id="alertPS" class="modal hide" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" >
 <div class="modal-header">
    <button type="button" class="close closeModal" data-dismiss="modal" aria-hidden="true">×</button>
     <h3 ><?php echo Yii::t('contentForm','Done');?></h3>
 
  </div>
  <div class="modal-body">
         <h4 id="messagePS"></h4>
         
  </div>
  <!--<div class="modal-footer">   
        <button class="btn closeModal" data-dismiss="modal" aria-hidden="true">Aceptar</button>
  </div>-->
</div>
<script>
    $('.closeModal').click(function(event) {
            $('#alertPS').hide();
           // javascript:location.reload();
        });
</script>
<!-- /container --> 
