<?php

$id=0;
$entro=0;
$con=0;
$prePub="";

	$ima = Imagen::model()->findByAttributes(array('tbl_producto_id'=>$data->id,'orden'=>'1'));
	
	$segunda = Imagen::model()->findByAttributes(array('tbl_producto_id'=>$data->id,'orden'=>'2'));
	
	// limitando a que se muestren los status 1 y estado 0
	
	   	if($data->precios){
	   	foreach ($data->precios as $precio) {
	   		$prePub = Yii::app()->numberFormatter->formatDecimal($precio->precioDescuento);
			}
		}
		
		echo ' <input id="productos" value="'.$data->id.'" name="ids" class="ids" type="hidden" >';
		if(isset($ima)){
			
			if($prePub!="")
			{
				//echo"<tr>";
				$like = UserEncantan::model()->findByAttributes(array('user_id'=>Yii::app()->user->id,'producto_id'=>$data->id));
            	
            	if(isset($like)) // le ha dado like
				{
					$a = CHtml::image($ima->getUrl(), "Imagen ", array("class"=>"img_hover","width" => "270", "height" => "270",'id'=>'img-'.$data->id));
					$b = '';
					if(isset($segunda))
						//echo "<input id='img2-".$data->id."' value='".$segunda->getUrl()."' type='hidden' >";
						//$b = CHtml::image($segunda->getUrl(), "Segunda ", array("width" => "270", "height" => "270",'display'=>'none','id'=>'img2-'.$data->id));
						$b = CHtml::image($segunda->getUrl(), "Imagen ", array("class"=>"img_hover_out","style"=>"display:none","width" => "270", "height" => "270"));
						echo("<td><article class='span3'><div class='producto'> 
						<input id='idprod' value='".$data->id."' type='hidden' ><a href='../producto/detalle/".$data->id."'>
						".$a.$b." 
						
						".CHtml::link("Vista Rápida",
						    $this->createUrl('modal',array('id'=>$data->id)),
						    array(// for htmlOptions
						      'onclick'=>' {'.CHtml::ajax( array(
						      'url'=>CController::createUrl('modal',array('id'=>$data->id)),
						           'success'=>"js:function(data){ $('#myModal').html(data);
											$('#myModal').modal(); }")).
						         'return false;}',
						    'class'=>'btn btn-block btn-small vista_rapida hidden-phone',
						    'id'=>'prodencanta')
						)."		
												
						</a>
						<header><h3><a href='../producto/detalle/".$data->id."' title='".$data->nombre."'>".$data->nombre."</a></h3>
						<a href='../producto/detalle/".$data->id."' class='ver_detalle entypo icon_personaling_big' title='Ver detalle'>&#128269;</a></header>
						<span class='precio'>Bs. ".Yii::app()->numberFormatter->format("#,##0.00",$prePub)."</span>
						<a id='like".$data->id."' onclick='encantar(".$data->id.")' style='cursor:pointer' title='Me encanta' class='entypo like icon_personaling_big like-active'>&hearts;</a></div></article></td>");
						
						$con=$id;
						$entro=1;
				}
				else
				{
					$a = CHtml::image($ima->getUrl(), "Imagen ", array("class"=>"img_hover","width" => "270", "height" => "270",'id'=>'img-'.$data->id));	
					$b = '';
					if(isset($segunda))
						//echo "<input id='img2-".$data->id."' value='".$segunda->getUrl()."' type='hidden' >";
					//	$b = CHtml::image($segunda->getUrl(), "Segunda ", array("width" => "270", "height" => "270",'display'=>'none','id'=>'img2-'.$data->id));
					$b = CHtml::image($segunda->getUrl(), "Imagen ", array("class"=>"img_hover_out","style"=>"display:none","width" => "270", "height" => "270"));
					echo("<article class='span3'><div class='producto' >
					<input id='idprod' value='".$data->id."' type='hidden' ><a href='../producto/detalle/".$data->id."'>
					".$a.$b." 
						
					".CHtml::link("Vista Rápida",
					    $this->createUrl('modal',array('id'=>$data->id)),
					    array(// for htmlOptions
					      'onclick'=>' {'.CHtml::ajax( array(
					      'url'=>CController::createUrl('modal',array('id'=>$data->id)),
					           'success'=>"js:function(data){ $('#myModal').html(data);
										$('#myModal').modal(); }")).
					         'return false;}',
					    'class'=>'btn btn-block btn-small vista_rapida hidden-phone',
					    'id'=>'pago')
					)."		
						 
					</a>
					<header><h3><a href='../producto/detalle/".$data->id."' title='".$data->nombre."'>".$data->nombre."</a></h3>
					<a href='../producto/detalle/".$data->id."' class='ver_detalle entypo icon_personaling_big' title='Ver detalle'>&#128269;</a></header>
					<span class='precio'>Bs. ".Yii::app()->numberFormatter->format("#,##0.00",$prePub)."</span>
					<a id='like".$data->id."' onclick='encantar(".$data->id.")' style='cursor:pointer' title='Me encanta' class='entypo like icon_personaling_big'>&#9825;</a></div></article>");
					
					$con=$id;
						
				}
				
				//echo("</tr>");
			}
		
		}




?>
<script>


function encantar(id)
   	{
   		var idProd = id;
   		//alert("id:"+idProd);		
   		
   		$.ajax({
	        type: "post",
	        url: "../producto/encantar", // action Tallas de Producto
	        data: { 'idProd':idProd}, 
	        success: function (data) {
				
				if(data=="ok")
				{					
					var a = "♥";
					
					//$("#meEncanta").removeClass("btn-link");
					$("a#like"+id).addClass("like-active");
					$("a#like"+id).text(a);
					
				}
				
				if(data=="no")
				{
					alert("Debes ingresar con tu cuenta de usuario o registrarte antes de dar Me Encanta a un producto");
					//window.location="../../user/login";
				}
				
				if(data=="borrado")
				{
					var a = "♡";
					
					//alert("borrando");
					
					$("a#like"+id).removeClass("like-active");
					//$("#meEncanta").addClass("btn-link-active");
					$("a#like"+id).text(a);

				}
					
	       	}//success
	       })
   		
   		
   	}  
   	
   	
</script>