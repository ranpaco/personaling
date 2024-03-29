
<style>
    div.infinite_navigation{
        display:none;
    }
</style>


<?php
Yii::app()->clientScript->registerScriptFile('http://akwww.polyvorecdn.com/rsrc/add_to_polyvore.js', CClientScript::POS_END);
?>

  <div class="row" id="looks">
  	
	<?php foreach($looks as $look): 

                if(!$look->getIsVisible()){
                    continue;
                }
		?>
		
		<?php  //echo $this->renderPartial('_look',array('look'=>$look),true,true); ?>
<div class="span4 look">
      <article > 
      	<?php
        $mod_time = '';
        if($look->modified_on){
            $mod_time = '?lastmod='.strtotime($look->modified_on);
        }
        ?>
      	<?php if($look->has_100chic){ ?>
		<!--	<div class="has_100chic"></div> -->
      	<?php }?>
      	<?php if ($pages->currentPage > 0){ ?>
      	<?php $image = CHtml::image(Yii::app()->createUrl('look/getImage',array('id'=>$look->id,'w'=>'368','h'=>'368')).$mod_time, "Personaling - ".$look->title, array("id" => "imglook".$look->id,"width" => "368", "height" => "368", 'class'=>'imglook')); ?>
      	<?php }else{ ?>
      	<?php echo CHtml::image(Yii::app()->baseUrl .'/images/loading.gif','Loading',array('class'=>'imgloading','id'=>"imgloading".$look->id)); ?>                            	
        <?php $image = CHtml::image(Yii::app()->createUrl('look/getImage',array('id'=>$look->id,'w'=>'368','h'=>'368')).$mod_time, "Personaling - ".$look->title, array("style"=>"display: none","id" => "imglook".$look->id,"width" => "368", "height" => "368", 'class'=>'imglook')); ?>
        <?php } ?>
        	         
                  	<?php echo CHtml::link($image,$look->getUrl()); ?>
                  	
                  	<?php
                    /*
                    //"style"=>"display: none",              	
                        $script = "$('#"."imglook".$look->id."').load(function(){
									//alert('cargo');
									$('#imgloading".$look->id."').hide();
									$(this).show();
									//$('#loader_img').hide();
						});";
  						Yii::app()->clientScript->registerScript('img_ps_script'.$look->id,$script);
					 * 
					 */
					 
					 /*
					 echo "<script>
					 $('#"."imglook".$look->id."').load(function(){
									$('#imgloading".$look->id."').hide();
									$(this).show();
						});
					 </script>";
					 */
						 $script = "
							var load_handler = function() {
							    $('#imgloading".$look->id."').hide();
							    $(this).show();
							}
							$('#"."imglook".$look->id."').filter(function() {
							    return this.complete;
							}).each(load_handler).end().load(load_handler);
						 ";		
						 	Yii::app()->clientScript->registerScript('img_ps_script'.$look->id,$script);			 
  					?>
        <div class="hidden-phone margin_top_small vcard row-fluid">
          <div class="span12 hidden-tablet">
            <div class="mis_looks">
              <div class="mis_looks_titulo"><?php echo $look->title; ?></div>
              <div class="mis_looks_descripcion"><?php echo $look->description; ?></div>
              <script src="//platform.twitter.com/widgets.js" type="text/javascript"></script>
              <div align="right">
                  <?php
                  // link to share


                  echo CHtml::link(

                      CHtml::image(Yii::app()->baseUrl.'/images/icon_compartir_2.png', '', array('width'=>30, 'height'=>30, 'class'=>'social')),'',array('class'=>'pointer','onclick'=>'callModal('.$look->id.')')

                      #CHtml::image(Yii::app()->baseUrl.'/images/icon_compartir_2.png', 'Compartir', array('width'=>30, 'height'=>30, 'class'=>'social')),'#',array('onclick'=>'share_link_modal('.$look->id.')')


                  );
                  ?>
                <?php
                // twitter button
                echo CHtml::link(
                  CHtml::image(Yii::app()->baseUrl.'/images/icon_twitter_2.png', 'Compartir en twitter', array('width'=>30, 'height'=>30, 'class'=>'social')),
                  'https://twitter.com/intent/tweet?url='.Yii::app()->getBaseUrl(true).'/l/'.$look->encode_url("123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ").'&text='.$look->title.'&lang=es&via=Personaling'
                );
                

                // facebook button
                echo CHtml::link(
                  CHtml::image(Yii::app()->baseUrl.'/images/icon_facebook_2.png', 'Compartir en facebook', array('width'=>30, 'height'=>30, 'class'=>'social')),
                  Yii::app()->getBaseUrl(true).'/l/'.$look->encode_url("123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ"),
                  array(
                    'data-image'=>Yii::app()->language.'/look/'.$look->id.'.png'."?v=".time(),
                    'data-title'=>$look->title,
                    'data-desc'=>$look->description,
                    'class'=>'facebook_share'
                  )
                );

                // pinterest button
                echo CHtml::link(
                  CHtml::image(Yii::app()->baseUrl.'/images/icon_pinterest_2.png', 'Compartir en pinterest', array('width'=>30, 'height'=>30, 'class'=>'social')),
                  '//pinterest.com/pin/create/button/?url='.Yii::app()->getBaseUrl(true).'/l/'.$look->encode_url("123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ").'&description='.$look->title.'&media='.Yii::app()->getBaseUrl(true).'/images/'.Yii::app()->language.'/look/'.$look->id.'.png',
                  array(
                    'target'=>'_blank'
                  )
                );

                // polyvore button
                echo CHtml::link(
                  CHtml::image(Yii::app()->baseUrl.'/images/icon_polyvore_2.png', 'Compartir en polyvore', array('width'=>30, 'height'=>30, 'class'=>'social')),
                  'http://www.polyvore.com?url='.Yii::app()->getBaseUrl(true).'/l/'.$look->encode_url("123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ").'&description='.$look->title.'&media='.Yii::app()->getBaseUrl(true).'/images/'.Yii::app()->language.'/look/'.$look->id.'.png',
                  array(
                    #'target'=>'_blank', 
                    'name'=>'addToPolyvore',
                    'id'=>'addToPolyvore',
                    'data-product-url'=>Yii::app()->getBaseUrl(true).'/look/'.$look->id,
                    'data-image-url'=>Yii::app()->getBaseUrl(true).'/images/'.Yii::app()->language.'/look/'.$look->id.'.png',
                    'data-name'=>$look->title,
                    //'data-price'=>$look->getPrecioDescuento(),
                  )
                );
                ?>
                
              </div>
              
            </div>
          </div>
        <div class="share_like">
         <?php if(!Yii::app()->user->isGuest){?>
          <button id="meEncanta<?php echo $look->id; ?>" onclick='encantar(<?php echo $look->id; ?>)' title="Me encanta" class="btn-link <?php echo $look->meEncanta()?"btn-link-active":""; ?>">
          	<span id="like<?php echo $look->id; ?>" class="entypo icon_personaling_big"><?php echo $look->meEncanta()?"♥":"♡"; ?></span>
          </button>
          <?php }?>
          <div class="btn-group">
            <button data-toggle="dropdown" class="dropdown-toggle btn-link"><span class="entypo icon_personaling_big"></span></button>
            <ul class="dropdown-menu addthis_toolbox addthis_default_style ">
            </ul>
            
            <!-- AddThis Button END --> 
            
          </div>
        </div>
        <span class="label label-important"><?php echo Yii::t('contentForm','Promotion'); ?></span> 
        </article>
    </div>
        
	<?php endforeach; ?>
	<script>
	var ruta= "<?php echo Yii::app()->getBaseUrl(true);?>";
	var token= "<?php echo Yii::app()->params['fb_appId']; ?>";
	$('.imglook').on("load",function(){
		//console.log('clicking');
		$(this).parent().prev("img").hide();
		$(this).show();
	});
	$(document).on('click','.imgloading', function(){
    console.log('clicking');
   // FB.Canvas.scrollTo(0,0);        
	});
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

  $('.facebook_share').live('click',function(){
    elem = $(this);
    postToFeed(elem.data('title'), elem.data('desc'), elem.prop('href'), elem.data('image'));

    return false;
  });


     function callModal(id)
        {        
                         $.ajax({
                        type: "post", 
                        url: "<?php echo Yii::app()->createUrl('Look/setVar') ?>",
                        data:{
                            id:id,
                         },
                        success: function (data) {
                             $('#toEnable').val(data);
                             $('#nombre').html($('#toEnable').val());        
          					 $('#dialogLook').modal();
                         }

                   });
                   
         
          

        }

  /*function share_link_modal(id){
    console.log('dialogLook'+id);
    $('#dialogLook'+id).modal();
  }*/

</script>


	</div>
<?php $this->widget('ext.yiinfinite-scroll.YiinfiniteScroller', array(
    'contentSelector' => '#looks',
    'itemSelector' => 'div.look',
    'loadingText' => 'Cargando Looks...',
    'donetext' => ' ',
    //'afterAjaxUpdate' => 'alert("hola");',
    'contentLoadedCallback' => "js:function(){        
        var jsElm= document.createElement('script');
        jsElm.type = 'application/javascript';
        jsElm.src = 'http://akwww.polyvorecdn.com/rsrc/add_to_polyvore.js';
        document.body.appendChild(jsElm);
    }",  
    'pages' => $pages, 
    //'debug' => true,
)); ?>