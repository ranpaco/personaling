<?php
$this->breadcrumbs=array(
  'Todos los looks'=>array('tienda/look'),
  'Look'
);

Yii::app()->session['look_id']=$model->id;
$this->pageTitle=Yii::app()->name . " - " . $model->title;;
  Yii::app()->clientScript->registerMetaTag('Personaling - '.$model->title.' - '.$model->getPrecio().' '.Yii::t('contentForm', 'currSym'), null, null, array('property' => 'og:title'), null); // registro del meta para facebook
  Yii::app()->clientScript->registerMetaTag($model->description.' Creado por: '.$model->user->profile->first_name.' '.$model->user->profile->last_name, null, null, array('property' => 'og:description'), null);
  Yii::app()->clientScript->registerMetaTag(Yii::app()->request->hostInfo.Yii::app()->request->url , null, null, array('property' => 'og:url'), null);
  Yii::app()->clientScript->registerMetaTag('Personaling.com', null, null, array('property' => 'og:site_name'), null); 

  //Metas de Twitter CARD ON
  Yii::app()->clientScript->registerMetaTag('product', 'twitter:card', null, null, null);
  Yii::app()->clientScript->registerMetaTag('@personaling', 'twitter:site', null, null, null);
  Yii::app()->clientScript->registerMetaTag($model->title, 'twitter:title', null, null, null);
  Yii::app()->clientScript->registerMetaTag($model->description, 'twitter:description', null, null, null);
  Yii::app()->clientScript->registerMetaTag(Yii::app()->getBaseUrl(true)."/look/getImage/".$model->id, 'twitter:image', null, null, null); //IMAGEN DE TWITTER CARD, QUITAR EN CASO DE QUE NO FUNCIONE EN PRODUCCION
  Yii::app()->clientScript->registerMetaTag($model->getPrecio().' '.Yii::t('contentForm', 'currSym'), 'twitter:data1', null, null, null);
  Yii::app()->clientScript->registerMetaTag('Subtotal', 'twitter:label1', null, null, null);
  Yii::app()->clientScript->registerMetaTag($model->user->profile->first_name.' '.$model->user->profile->last_name, 'twitter:data2', null, null, null);  
  Yii::app()->clientScript->registerMetaTag('Creado por', 'twitter:label2', null, null, null);
  Yii::app()->clientScript->registerMetaTag('personaling.com', 'twitter:domain', null, null, null);

  //Metas de Twitter CARD OFF
   /* foreach ($model->lookhasproducto as $pr){
        $mod = Preciotallacolor::model()->findByAttributes(array('color_id'=>$pr->color_id,'producto_id'=>$pr->producto_id));
        echo $mod->updateLooksAvailability()."<br/>";
    }
   break;*/
?>



<div class="container margin_top_small" id="carrito_compras">
  <div class="row">
    <div class="span12">
      <div class="row detalle_look">
        <!-- Columna Principal ON-->
        <article class="span8 columna_principal">
          <div class="row">
            <div class="span6">
                <input id="idLook" type="hidden" value="<?php echo $model->id ?>" />
              <h1><?php echo $model->title; ?></h1>
              <p class="margin_top_small_minus"> <!-- <small>Look <a href="#" title="playero">Playero</a>,   --> <?php echo Yii::t('contentForm','Style'); ?> <a href="#" title="casual"><?php echo $model->getTipo(); ?></a> <!-- | 100% Disponible --></small></p>
            </div>
            <div class="span2 share_like">
              <div class="pull-right">

                  <?php
                $entro = 0;

                $like = LookEncantan::model()->findByAttributes(array('user_id'=>Yii::app()->user->id,'look_id'=>$model->id));

                if(isset($like)) // le ha dado like al look
                {
                    //echo "p:".$like->producto_id." us:".$like->user_id;
                    $entro=1;
                    ?>

                        <button id="meEncanta" onclick='encantar()' title="Me encanta" class="btn-link btn-link-active">
                            <span id="like" class="entypo icon_personaling_big">&hearts;</span>
                        </button>
                       <?php

                } 
 
                    if($entro==0) // no le ha dado like
                    {
                        echo "<button id='meEncanta' onclick='encantar()' title='Me encanta' class='btn-link'>
                           <span id='like' class='entypo icon_personaling_big'>&#9825;</span>
                           </button>";
                    }
                   ?>


              </div>
            </div>
          </div>
          <div class="row-fluid">
            <?php Yii::app()->clientScript->registerMetaTag(Yii::app()->request->hostInfo.Yii::app()->createUrl('look/getImage',array('id'=>$model->id,'w'=>770,'h'=>770)), null, null, array('property' => 'og:image'), null);  // Registro de <meta> para compartir en Facebook
                  //Yii::app()->clientScript->registerMetaTag(Yii::app()->request->hostInfo.Yii::app()->createUrl('look/getImage',array('id'=>$model->id)), 'twitter:image:src', null, null, null); //Registro de meta para Card de Twitter

            ?>
            <div class="span12" ><div class="imagen_principal"> <span class="label label-important margin_top_medium"><?php echo Yii::t('contentForm','Promotion'); ?></span>
            		 <?php echo CHtml::image(Yii::app()->createUrl('look/getImage',array('id'=>$model->id,'w'=>770,'h'=>770)), "Personaling - ".$model->title , array('class'=>'img_1')); ?> 
            		 </div></div>

          </div>
          <div class="hidden-phone row-fluid vcard">
            <div class="span2 avatar ">
            <a href="<?php $perfil = $model->user->profile; echo $perfil->getUrl(); ?>" title="perfil" class="url">
            <?php echo CHtml::image($model->user->getAvatar(),"Personaling - ".$model->user->profile->first_name.' '.$model->user->profile->last_name,array("width"=>"84", "class"=>"pull-left photo  img-circle")); //,"height"=>"270" ?>
            </a>
            </div>
            <div class="span5 braker_right row-fluid">
            <div class="span9">
            <span class="muted"><?php echo Yii::t('contentForm' , 'Look created by'); ?>: </span>
              <h5><a href="<?php echo $perfil->getUrl(); ?>" title="perfil" class="url"><span class="fn"> <?php echo $model->user->profile->first_name.' '.$model->user->profile->last_name; ?></span> <i class="icon-chevron-right"></i></a></h5>
              <p  class="note"><strong><?php echo Yii::t('contentForm','Biography'); ?></strong>: <?php echo $model->user->profile->bio; ?> </p>
            </div>
            <div class="span3">
              <span class="muted" ><?php echo Yii::t('contentForm' , 'On this look'); ?></span>
            </div>
            </div>
            <!-- Marcas en el look ON -->
            <div class="span5 marcas">
              
              
              <ul class="unstyled">
                <?php foreach ($model->getMarcas() as $marca){ ?>
	                 <li >  
	                  	<?php echo CHtml::image($marca->getImageUrl(true),$marca->nombre, array('width'=>60, 'height'=>60,'title'=>$marca->nombre));
                      ?>
	                </li>                	
                <?php } ?>              
                                                      
              </ul>
              
              
            </div>
            <!-- Marcas en el look OFF -->            
          </div>
          <hr/>
          <h3><?php echo Yii::t('contentForm' , 'Look description'); ?></h3>
          <p><?php echo $model->description; ?> </p>
        </article>
        <!-- Columna Principal OFF -->

        <!-- Columna Secundaria ON-->
        <div class="span4 columna_secundaria">
          <!-- Boton de comprar  -->
          <div class="row-fluid call2action">
            <div class="span6">
               <?php
                if(!is_null($model->tipoDescuento) && $model->valorDescuento > 0){
                  ?>
                  <h4 class="precio" ><div id="price"><span><?php echo Yii::t('contentForm' , 'Look Completo'); ?></span><?php echo Yii::t('contentForm', 'currSym').' '.$model->getPrecioDescuento(); ?></div></h4>
                  <h5 class="precio" ><small><div id="price"><span><?php echo Yii::t('contentForm' , 'Piezas Separadas'); ?></span><?php echo Yii::t('contentForm', 'currSym').' '.$model->getPrecioProductosDescuento(); ?></div></small></h5>
                  
                  <?php
                }else{
                  ?>
                  <h4 class="precio" ><div id="price"><span><?php echo Yii::t('contentForm' , 'Subtotal'); ?></span><?php echo Yii::t('contentForm', 'currSym').' '.$model->getPrecioDescuento(); ?></div></h4>
                  
                  <?php 
                  
                  }
                ?>
              
            </div>
            <div class="span6">
              <div class="">
                <!--    <a href="bolsa_de_compras.php" title="agregar a la bolsa" class="btn btn-danger"> Añadir a la bolsa</a> -->
                <?php

                // verificar si el look tiene productos de terceros y cambiar el texto del boton de compra
                $button_text = 'Buy';
                if($model->hasProductosExternos()){
                  $button_text = 'Load to shopping cart';
                }

         $this->widget('bootstrap.widgets.TbButton', array(
                    'buttonType'=>'ajaxButton',
                    'id'=>'btn-compra', 
                    'type'=>'warning',
                    'label'=>Yii::t('contentForm', $button_text),
                    'block'=>'true',
                       'size'=> 'large',
                       
                     //Si es invitado enviar los productos a una
                     //URL distinta por ajax
                   'url'=> Yii::app()->user->isGuest ? CController::createUrl('producto/agregarBolsaGuest'):
                         CController::createUrl('bolsa/agregar2'),
             
                    'htmlOptions'=>array('id'=>'buttonGuardar'),
                    'ajaxOptions'=>array(
                            'type' => 'POST',
                            'dataType' => 'json',
                            'data'=> "js:$('#producto-form').serialize()",

                            'beforeSend' => "function( request )n
                                 {
                                   
                                   
                                                                      var entro = true;	
                                   if ( $(\"input[name='producto[]']:checked\").length <= 0 ){
                                   		entro = false;
                                        alert('".Yii::t('contentForm' , 'Must select at least one item')."');
                                        return false;
                                   }

                                   $('.tallas').each(function(){
                                           if ($(this).val()==''){

                                               if ($(this).parent().prev('input').prop('checked')){
                                               		entro = false;
                                                		
                                                   $('#alertSizes').show();
                                                   return false;
                                               }
                                           }

                                   });
                                   if (entro){
                                   		if ($('#buttonGuardar').attr('disabled')==true)
                                   			return false;
                                   		$('#buttonGuardar').attr('disabled', true);
								   }else{
                                        return false;
                                    }
                                   
                                 }",


                             'success' => "function( data )
                                  {
                                    console.log(data);
                                    var invitado = ".(Yii::app()->user->isGuest ? "true":"false")."
                                     if(invitado){
                                        agregarBolsaGuest(data);
                                     }else{
                                        
                                         if(data.status == 'ok')
                                        {
                                          ga('ec:addProduct', {
                                            'id': data.id,
                                            'name': data.name,
                                            'category': data.category,
                                            'brand': data.brand,
                                            'variant': data.variant,
                                            'price': data.price,
                                            'quantity': data.quantity,
                                          });
                                          ga('ec:setAction', 'add');
                                          ga('send', 'event', 'UX', 'click', 'add to cart');     // Send data using an event.
                                          window.location='".$this->createUrl('bolsa/index')."';
                                        }                                     
                                    }
                                  }",
                    ),
                ));

                ?>
              </div>
            </div>
          </div>
          <p class="muted t_small CAPS braker_bottom"><?php echo Yii::t('contentForm' , 'Select the size'); ?> </p> 
          <p class="muted t_small "><?php echo Yii::t('contentForm' , 'You can buy separate clothes that you like'); ?></p>

          <!-- Productos del look ON -->
          <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
            'id'=>'producto-form',
            'enableAjaxValidation'=>false,
            'type'=>'horizontal',
        )); ?>
          <?php echo CHtml::hiddenField('look_id',$model->id); ?>
          <div class="productos_del_look">
            <div class="row-fluid">
              <?php 
              if($model->productos)
                foreach ($model->lookhasproducto as $lookhasproducto){
                  // $imagen = Imagen::model()->findByAttributes(array('tbl_producto_id'=>$lookhasproducto->producto_id,'orden'=>'1'));
                  $image_url = $lookhasproducto->producto->getImageUrl($lookhasproducto->color_id,array('type'=>'thumb'));
                  Yii::app()->clientScript->registerMetaTag(Yii::app()->request->hostInfo.$image_url, null, null, array('property' => 'og:image'), null);  // Registro de <meta> para compartir en Facebook                              
                  ?>
                  <div class="producto span6"> 
                    <a href="pagina_producto.php" title="Nombre del Producto">
                      <!-- <img width="170" height="170" src="<?php echo Yii::app()->getBaseUrl(true) . '/'; ?>/images/producto_sample_1.jpg" title="Nombre del producto" class="imagen_producto" />
                      -->
                      <?php      
                      $prod = Producto::model()->findByPk($lookhasproducto->producto_id);
                      ?>

                      <?php $image = CHtml::image($image_url, "Personaling - ".$prod->nombre, array('class'=>'imagen_producto'));  ?>
                      <?php echo CHtml::link($image, $prod->getUrl() ); ?>
                      <?php //$color_id = @LookHasProducto::model()->findByAttributes(array('look_id'=>$model->id,'producto_id'=>$lookhasproducto->producto_id))->color_id ?>
                      <?php $color_id = $lookhasproducto->color_id; ?>
                    </a>
                    <?php 
                    if ( $lookhasproducto->producto->getCantidad(null,$color_id) > 0 && $lookhasproducto->producto->estado == 0){ 
                      ?>
                      <?php echo CHtml::checkBox("producto[]",true,array('onclick'=>'js:updatePrice();','value'=>$lookhasproducto->producto_id.'_'.$color_id)); ?>
                      <?php } else { ?>
                      <?php echo CHtml::checkBox("producto[]",false,array('readonly'=>true,'disabled'=>true,'value'=>$lookhasproducto->producto_id.'_'.$color_id)); ?>

                      <?php 
                    } 
                    ?>

                    <div class="metadata_top">
                      <?php // echo Chtml::hiddenField("color[]",$color_id); ?>
                      <?php // echo Chtml::hiddenField("producto[]",$producto->id); ?>
                      <?php 
                      //if($lookhasproducto->producto->tipo == 0){
                        if(is_null($lookhasproducto->producto->tienda))    
                            if($lookhasproducto->producto->estado == 0){
                              echo CHtml::dropDownList('talla'.$lookhasproducto->producto_id.'_'.$color_id,'0',$lookhasproducto->producto->getTallas($color_id),array('onchange'=>'js:updateCantidad(this);','prompt'=>Yii::t('contentForm' , 'Size'),'class'=>'span5 tallas')); 
                            }else{
    
                              echo CHtml::dropDownList('talla'.$lookhasproducto->producto_id.'_'.$color_id,'0',array(),array('onchange'=>'js:updateCantidad(this);','prompt'=>Yii::t('contentForm' , 'Size'),'class'=>'span5 tallas')); 
    
                            }
                        else{
                          $keys = array_keys($lookhasproducto->producto->getTallas($color_id));
                            echo CHtml::dropDownList('talla'.$lookhasproducto->producto_id.'_'.$color_id,array_shift($keys),$lookhasproducto->producto->getTallas($color_id),array('onchange'=>'js:updateCantidad(this);','prompt'=>Yii::t('contentForm' , 'Size'),'class'=>'span5 tallas'));
                          }
                      /*}else{
                        echo $lookhasproducto->producto->tienda->name;
                      }*/
                      ?>
                    </div>
                    <div class="metadata_bottom">
                      <h5><?php echo $lookhasproducto->producto->nombre; ?></h5>
                      <div class="row-fluid">
                        <div class="span6"><span> <?php echo Yii::t('contentForm', 'currSym'); ?>
                        <?php foreach ($lookhasproducto->producto->precios as $precio) {
                        echo Yii::app()->numberFormatter->formatDecimal($precio->precioDescuento); // precio
                        }

                        ?>

                        </span></div>
                        <div class="span6 text_align_right"> <span id="cantidad<?php echo $lookhasproducto->producto_id.'_'.$color_id; ?>">
                        <?php 
                        if($lookhasproducto->producto->estado == 0 && $lookhasproducto->producto->status == 1){                        

                        echo $lookhasproducto->producto->getCantidad(null,$color_id)." unds.";

                        }else{

                        echo Yii::t('contentForm','Unavailable');

                        }

                        ?> </span></div>
                      </div>
                    </div>
                  </div>
                  <?php
                }
                ?>
            </div>
          </div>
          <?php $this->endWidget(); ?>
          <!-- Productos del look OFF -->
          <div class="row call2action">
                <?php

         $this->widget('bootstrap.widgets.TbButton', array(
                    'buttonType'=>'ajaxButton',
                    'id'=>'btn-compra', 
                    'type'=>'warning',
                    'label'=>Yii::t('contentForm', $button_text),
                    'block'=>'true',
                       'size'=> 'large',
                  //Si es invitado enviar los productos a una
                     //URL distinta por ajax
                   'url'=> Yii::app()->user->isGuest ? CController::createUrl('producto/agregarBolsaGuest'):
                         CController::createUrl('bolsa/agregar2'),
             
                    'htmlOptions'=>array('id'=>'buttonGuardar','class'=>'span4'),
                    'ajaxOptions'=>array(
                            'type' => 'POST',
                            'data'=> "js:$('#producto-form').serialize()",
                            'dataType' => 'json',
                            'beforeSend' => "function( request )
                                 {                                  
                                   
                                  var entro = true; 
                                   if ( $(\"input[name='producto[]']:checked\").length <= 0 ){
                                      entro = false;
                                        alert('".Yii::t('contentForm' , 'Must select at least one item')."');
                                        return false;
                                   }

                                   $('.tallas').each(function(){
                                           if ($(this).val()==''){

                                               if ($(this).parent().prev('input').prop('checked')){
                                                  entro = false;
                                                 
                                                   $('#alertSizes').show();
                                                   return false;
                                               }
                                           }

                                   });
                                   if (entro){
                                     
                                      if ($('#buttonGuardar').attr('disabled')==true)
                                        return false;
                                      $('#buttonGuardar').attr('disabled', true);
                                   }else{
                                        return false;
                                    }
                                   
                                 }",


                             'success' => "function( data )
                                  {
                                    var invitado = ".(Yii::app()->user->isGuest ? "true":"false")."
                                     if(invitado){
                                        agregarBolsaGuest(data);
                                     }else{
                                        if(data.status == 'ok')
                                        {
                                          for (var index = 0; index < data.productos.length; ++index) {
                                              console.log(data.productos[index]);
                                              ga('ec:addProduct', {
                                                'id': data.productos[index].id,
                                                'name': data.productos[index].name,
                                                'category': data.productos[index].category,
                                                'brand': data.productos[index].brand,
                                                'variant': data.productos[index].variant,
                                                'price': data.productos[index].price,
                                                'quantity': data.productos[index].quantity,
                                              });
                                              ga('ec:setAction', 'add');
                                              ga('send', 'event', 'UX', 'click', 'add to cart');     // Send data using an event.
                                          }
                                          ga('ec:addProduct', {
                                            'id': data.id,
                                            'name': data.name,
                                            'category': data.category,
                                            'brand': data.brand,
                                            'variant': data.variant,
                                            'price': data.price,
                                            'quantity': data.quantity,
                                          });
                                          ga('ec:setAction', 'add');
                                          ga('send', 'event', 'UX', 'click', 'add to cart');     // Send data using an event.
                                          window.location='".$this->createUrl('bolsa/index')."';
                                        }                                     
                                    }
                                  }",
                                //  'data'=>array('id'=>$model->id),
                    ),
                ));

                ?>
          </div>
          <div class="braker_horz_top_1">            
           
            <span class="entypo icon_personaling_medium">&#128197;</span> <?php echo Yii::t('contentForm' , 'Date estimated delivery'); ?>: <?php echo date("d/m/Y", strtotime('+1 day')); ?> - <?php echo date('d/m/Y', strtotime('+1 week'));  ?>                  
          </div>
          <div class="braker_horz_top_1 addthis clearfix row-fluid">  
          <?php
            // total de likes 
            $cuantos = LookEncantan::model()->countByAttributes(array('look_id'=>$model->id));
                       
            if(isset($like)) // le ha dado like 
				    { ?>
          
          		<div class="span6"><a class="btn btn-danger_modificado" id="btn-encanta" onclick="encantar()" style="cursor: pointer;"><span class="entypo icon_personaling_medium">&nbsp;</span> <?php echo Yii::t('contentForm' , 'Like'); ?> </a>
            <?php
				    }
			      else {?>
			       
			       <div class="span6"><a class="btn lighted" id="btn-encanta" onclick="encantar()" style="cursor: pointer;"><span class="entypo icon_personaling_medium">&nbsp;</span><?php echo Yii::t('contentForm' , 'Like'); ?> </a> 
      			<?php
      				}
      			?>	
      &nbsp; <span id="total-likes" class="lighted" ><?php echo $cuantos; ?></span>
          	
          
          	
          </div>            
          <!-- AddThis Button BEGIN -->
            <script src="//platform.twitter.com/widgets.js" type="text/javascript"></script>
         <div align="right">
          <?php
                  // link to share
                  echo CHtml::link(
                      CHtml::image(Yii::app()->baseUrl.'/images/icon_compartir_2.png', 'Compartir en twitter', array('width'=>30, 'height'=>30, 'class'=>'social')),'#',array('data-toggle'=>'modal',
                          'data-target'=>'#dialogLook'.$model->id)

                  );
#echo Yii::app()->getBaseUrl(true)."/look/".$model->id;
                // twitter button
                echo CHtml::link(
                  CHtml::image(Yii::app()->baseUrl.'/images/icon_twitter_2.png', 'Compartir en twitter', array('width'=>30, 'height'=>30, 'class'=>'social')),
                  'https://twitter.com/intent/tweet?url='.Yii::app()->getBaseUrl(true)."/look/".$model->id.'&text='.$model->title.'&lang=es&via=Personaling'
                );

                 echo CHtml::link(
                  CHtml::image(Yii::app()->baseUrl.'/images/icon_facebook_2.png', 'Compartir en facebook', array('width'=>30, 'height'=>30, 'class'=>'social')),
                  Yii::app()->getBaseUrl(true)."/look/".$model->id,
                  array(
                    'data-image'=>Yii::app()->language.'/look/'.$model->id.'.png',
                    'data-title'=>$model->title,
                    'data-desc'=>$model->description,
                    'class'=>'facebook_share'
                  )
                );

                  echo CHtml::link(
                  CHtml::image(Yii::app()->baseUrl.'/images/icon_pinterest_2.png', 'Compartir en pinterest', array('width'=>30, 'height'=>30, 'class'=>'social')),
                  '//pinterest.com/pin/create/button/?url='.Yii::app()->getBaseUrl(true)."/look/".$model->id.'&description='.$model->title.'&media='.Yii::app()->getBaseUrl(true).'/images/'.Yii::app()->language.'/look/'.$model->id.'.png',
                  array(
                    'target'=>'_blank'
                  )
                );

                echo CHtml::link(
                  CHtml::image(Yii::app()->baseUrl.'/images/icon_polyvore_2.png', 'Compartir en polyvore', array('width'=>30, 'height'=>30, 'class'=>'social')),
                  'http://www.polyvore.com?url='.Yii::app()->getBaseUrl(true)."/look/".$model->id.'&description='.$model->title.'&media='.Yii::app()->getBaseUrl(true).'/images/look/'.$model->id.'.png',
                  array(
                    'target'=>'_blank',
                    'name'=>'addToPolyvore',
                    'id'=>'addToPolyvore',
                    'data-product-url'=>Yii::app()->getBaseUrl(true).'/look/'.$model->id,
                    'data-image-url'=>Yii::app()->getBaseUrl(true).'/images/'.Yii::app()->language.'/look/'.$model->id.'.png',
                    'data-name'=>$model->title,
                    //'data-price'=>$look->getPrecioDescuento(),
                  )
                );
            ?>


         </div>

          <script type="text/javascript" src="http://akwww.polyvorecdn.com/rsrc/add_to_polyvore.js"></script>
          <!-- AddThis Button END --> 
            
            </div>
        </div>
        <!-- Columna secundaria OFF -->
<!--         <div>
            <img src="<?php // echo Yii::app()->getBaseUrl(); ?>/images/banner-night_non_stop.jpg" width="180" height="150" alt="Banner Accesorize" /> 
          </div>        -->
      </div>

      <?php if($dataProvider->getItemCount() > 0){ //si hay looks que te puedan gustar para mostrar  ?>

        <div class="braker_horz_top_1" id="tienda_looks">
          <h3>Otros Looks que te pueden gustar</h3>
              <div class="row">
        <?php        
        $cont=0;
        foreach($dataProvider->getData() as $record)
        {
          $lookre = Look::model()->findByPk($record['id']);

          if($lookre->matchOcaciones(User::model()->findByPk(Yii::app()->user->id)) && $lookre->activo=="1" && $lookre->status=="2" && $lookre->id!=$model->id&& $lookre->available=="1"){ 
              if($cont<3){

              //<div class="span4"><img src="<?php echo Yii::app()->getBaseUrl(true) . '/'; /images/look_sample_pequeno_1.jpg" width="370" height="370" alt="Nombre del Look"></div>

                     $like = LookEncantan::model()->findByAttributes(array('user_id'=>Yii::app()->user->id,'look_id'=>$lookre->id));

                     if(!isset($like)) // no le ha dado like al look
                  {
                      $cont++;
        ?>

              <div class="span4 look">
                  <article class="item" >
                      <?php echo CHtml::image('../images/loading.gif','Loading',array('id'=>"imgloading".$lookre->id)); ?>
                        <?php $image = CHtml::image(Yii::app()->createUrl('look/getImage',array('id'=>$lookre->id,'w'=>'368','h'=>'368')), $lookre->title, array("style"=>"display: none","id" => "imglook".$lookre->id,"width" => "368", "height" => "368", 'class'=>'')); ?>

                        <?php echo CHtml::link($image,$lookre->getUrl()); //array('look/view', 'id'=>$lookre->id ?>
                        <?php
                      //"style"=>"display: none",
                          $script = "
										var load_handler = function() {
										    $('#imgloading".$lookre->id."').hide();
										    $(this).show();
										}
										$('#"."imglook".$lookre->id."').filter(function() {
										    return this.complete;
										}).each(load_handler).end().load(load_handler);						 
									 ";									 
              						Yii::app()->clientScript->registerScript('img_script'.$lookre->id,$script);
                        ?>
                    <div class="hidden-phone margin_top_small vcard row-fluid">
                      <div class="span2 avatar ">

                          <?php echo CHtml::image($lookre->user->getAvatar(),'Avatar',array("width"=>"40", "class"=>"photo img-circle")); //,"height"=>"270" ?>
                      </div>
                      <div class="span4"> <span class="muted"><?php echo Yii::t('contentForm' , 'Look created by'); ?>:  </span>
                        <h5><a class="url" title="profile" href="#"><span class="fn">
                          <?php //echo $look->title; ?>
                          <?php echo $lookre->user->profile->first_name; ?> </span></a></h5>
                      </div>
                      <div class="span6"><span class="precio"> <small><?php echo Yii::t('contentForm' , 'currSym'); ?></small> <?php echo $lookre->getPrecio(); ?></span></div>
                    </div>
                    <div class="share_like">
                      <button href="#" title="Me encanta" class="btn-link"><span class="entypo icon_personaling_big">&#9825;</span></button>
                      <div class="btn-group">
                        <button class="dropdown-toggle btn-link" data-toggle="dropdown"><span class="entypo icon_personaling_big">&#59157;</span></button>
                        <ul class="dropdown-menu addthis_toolbox addthis_default_style ">
                          <!-- AddThis Button BEGIN -->

                          <li><a class="addthis_button_facebook_like" fb:like:layout="box_count"></a> </li>
                          <li><a class="addthis_button_tweet"></a></li>
                          <li><a class="addthis_button_pinterest_pinit"></a></li>
                        </ul>
                        <script type="text/javascript">var addthis_config = {"data_track_addressbar":false};</script>
                        <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=juanrules"></script>
                        <!-- AddThis Button END -->

                      </div>
                    </div>
                    <span class="label label-important"><?php echo Yii::t('contentForm' , 'Promotion'); ?></span> </article>
                </div>
      <?php
                  } // like
              } // contador
          } // match
      } // foreach
      ?>
                </div>
        </div>
      <?php } //  off si hay looks que te puedan gustar para mostrar ?>

      <div class="braker_horz_top_1">
        <div class="row">
<!--           <div class="span6">
            <h3>Otros Productos que te pueden gustar</h3>
            <div class="row">
              <div class="span2"> <a href="#" ><img width="170" height="170" src="<?php echo Yii::app()->getBaseUrl(true) . '/'; ?>/images/producto_sample_7.jpg" ></a></div>
              <div class="span2"> <a href="#" ><img width="170" height="170" src="<?php echo Yii::app()->getBaseUrl(true) . '/'; ?>/images/producto_sample_8.jpg" ></a></div>
              <div class="span2"> <a href="#" ><img width="170" height="170" src="<?php echo Yii::app()->getBaseUrl(true) . '/'; ?>/images/producto_sample_9.jpg" ></a></div>
            </div>
          </div> -->
          <div class="span11">
            <h3><?php echo Yii::t('contentForm' , 'Recently viewed'); ?></h3>
            <div class="row">
                        <?php
                             //$iterator = new CDataProviderIterator($ultimos_vistos);
                            //foreach($iterator as $view):
                             //    if (isset($view)):
                        foreach($ultimos_vistos->getData() as $record) :
     $producto = Producto::model()->findByPk($record['producto_id']);
     if (isset($producto)):
                         ?>
              <div class="span2">
                  <?php $image = CHtml::image($producto->getImageUrl(), "Personaling - ".$producto->nombre, array("width" => "170", "height" => "170"));    ?>
                            <?php echo CHtml::link($image, $producto->getUrl()); ?>

              </div>
                          <?php
                                  endif;
                              endforeach;
                          ?>

            </div>
          </div>
        </div>
      </div>
     <div class="text_align_center">
           <a href="http://personaling.com/magazine"><img src="<?php echo Yii::app()->getBaseUrl(); ?>/images/banner-grande.gif" width="970" height="90" alt="Banner blanco" /></a> 
      </div>      
    </div>

    <!-- /container -->
  </div>
</div>
<!-- Modal Window -->

<div class="modal hide fade" id="myModal">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4><?php echo Yii::t('contentForm' , 'Details'); ?></h4>
  </div>
  <div class="modal-body">
    <div class="row">
      <div class="span3"> <img src="http://placehold.it/400x450"/>
        <p class="margin_top_small"><a href="#" title="looks relacionados">12 looks</a> creados con esta prenda</p>
      </div>
      <div class="span2"><span class="label label-important margin_top_medium">ON SALE</span>
        <h3>Nombre de la prenda </h3>
        <p class="muted">Marca / Diseñador <br/>
          2 und. disponibles</p>
        <h4>Precio: Bs. 3.500</h4>
        <strong>Descripción</strong>:
        Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolor </div>
    </div>
  </div>
  <div class="modal-footer"> <a href="#" class="btn btn-warning">Añadir al Look</a> </div>
</div>

<div id="alertSizes" class="modal hide" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" >
 <div class="modal-header">
    <button type="button" class="close closeModal" data-dismiss="modal" aria-hidden="true">×</button>
     <h3 ><?php echo Yii::t('contentForm','Remember');?></h3>
 
  </div>
  <div class="modal-body">
 		 <h4><?php echo Yii::t('contentForm','You should set the sizes for the items.');?></h4>
 		 
  </div>
  <!--<div class="modal-footer">   
 		<button class="btn closeModal" data-dismiss="modal" aria-hidden="true">Aceptar</button>
  </div>-->
</div>

<div id="alertSizes" class="modal hide" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" >
 <div class="modal-header">
    <button type="button" class="close closeModal" data-dismiss="modal" aria-hidden="true">×</button>
     <h3 ><?php echo Yii::t('contentForm','Remember');?></h3>
 
  </div>
  <div class="modal-body">
 		 <h4><?php echo Yii::t('contentForm','You should set the sizes for the items.');?></h4>
 		 
  </div>
  <!--<div class="modal-footer">   
 		<button class="btn closeModal" data-dismiss="modal" aria-hidden="true">Aceptar</button>
  </div>-->
</div>

<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'dialogLook'.$model->id)); ?>
        <div class="modal-header">
            <a class="close" data-dismiss="modal">&times;</a>
            <h4><?php echo Yii::t('contentForm', 'Share Link'); ?></h4>
        </div>

        <div class="modal-body">
            <p><?php echo Yii::app()->getBaseUrl(true)."/"."look/".$model->id; ?></p>
        </div>
        <div class="modal-footer">

            <?php $this->widget('bootstrap.widgets.TbButton', array(
                'label'=>Yii::t('contentForm', 'Close'),
                'url'=>'#',
                'htmlOptions'=>array('data-dismiss'=>'modal'),
            )); ?>
        </div>
        <?php $this->endWidget(); ?>

<!-- // Modal Window -->
<script>
var ruta= "<?php echo Yii::app()->getBaseUrl(true);?>";
var token= "<?php echo Yii::app()->params['fb_appId']; ?>";
    window.fbAsyncInit = function(){ 
    FB.init({
        appId: token, status: true, cookie: true, xfbml: true });
  };
  (function(d, debug){var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];if   (d.getElementById(id)) {return;}js = d.createElement('script'); js.id = id; js.async = true;js.src = "//connect.facebook.net/es_ES/all" + (debug ? "/debug" : "") + ".js";ref.parentNode.insertBefore(js, ref);}(document, /*debug*/ false));
  function postToFeed(title, desc, url, image){
    var obj = {method: 'feed',link: url, picture: ruta+'/images/'+image,name: title,description: desc};
    function callback(response){}
  FB.ui(obj, callback);
  }

  $('.facebook_share').click(function(){
    elem = $(this);
    postToFeed(elem.data('title'), elem.data('desc'), elem.prop('href'), elem.data('image'));

    return false;
  });


	$('.closeModal').click(function(event) {
			$('#alertSizes').hide();
		});
    function updateCantidad(object){
        //alert(object.id.substring(5));
        //alert(object.value);
        //var talla = this.val();
        //var prendas = $(this).attr('id');
        //alert(talla);
        //alert(prendas);
        <?php
        //'colores'=>'js:colores',
        echo CHtml::ajax(array(
            'url'=>array('producto/updateCantidad'),
            'data'=> array('talla'=>'js:object.value','prenda'=>'js:object.id.substring(5)'),
            'type'=>'post',
            'dataType'=>'json',
            'success'=>"function(data)
            {
                if (data.status == 'success')
                {
                      //$('#price').html('".Yii::t('contentForm', 'currSym')." '+data.div);
                      $('#'+data.id).fadeOut(400,function() { $(this).html(data.div+ ' unds.').fadeIn(400); });
                  //alert(data.div);

                }


            } ",
            ))
        ?>
    }
    function updatePrice(){
        var prendas = '';
        //var colores = '';
        $("input[name='producto[]']:checked").each(function(){
            //tempo = $(this).val().split('_');
            //prendas += tempo[0]+',';
            //colores += tempo[1]+',';
            prendas += $(this).val()+',';
        });
        //alert(prendas);
        <?php
        //'colores'=>'js:colores',
        echo CHtml::ajax(array(
            'url'=>array('look/updatePrice'),
            'data'=> array('prendas'=>'js:prendas','look_id'=>'js:$("#look_id").val()'),

            'type'=>'post',
            'dataType'=>'json',
            'success'=>"function(data)
            {
                if (data.status == 'success')
                {
                      //$('#price').html('".Yii::t('contentForm', 'currSym')." '+data.div);
                      $('#price').fadeOut(400,function() { $(this).html('".Yii::t('contentForm', 'currSym')." '+data.div).fadeIn(400); });
                  //alert(data.div);

                }


            } ",
            ))
        ?>

    }



       function encantar()
       {
           var idLook = $("#idLook").attr("value");
           //alert("id:"+idLook);

           $.ajax({
            type: "post",
            dataType:"json",
            url: "encantar", // action Tallas de look
            data: { 'idLook':idLook},
            success: function (data) {
			 
			//alert(data );
			
                if(data.mensaje=="ok")
                {
                    var a = "♥";

                    //$("#meEncanta").removeClass("btn-link");
                    $("#meEncanta").addClass("btn-link-active");
                    $("span#like").text(a);
					
					$("#total-likes").text(data.total);
					
					
					    $('#btn-encanta').addClass('btn-danger_modificado');
					    $('#btn-encanta').removeClass('lighted');
					
					   

                }

                if(data.mensaje=="no") 
                {
                    alert("Debe primero ingresar como usuario");
                    //window.location="../../user/login";
                }

                if(data.mensaje=="borrado")
                {
                    var a = "♡";

                    $("#meEncanta").removeClass("btn-link-active");
                    $("span#like").text(a);
                    
                    $("#total-likes").text(data.total);
           
                        $('#btn-encanta').addClass('lighted');
                        $('#btn-encanta').removeClass('btn-danger_modificado');
                    

                }

               }//success
           })


       }

/*Agregar el look completo a la bolsa*/
function agregarBolsaGuest(data){     
    
    if(data.status == "success"){ 

        //mostrar el popover de nuevo.
        desplegarBolsaGuest(data);
    }
       
}

</script>
