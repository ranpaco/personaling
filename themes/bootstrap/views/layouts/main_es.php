<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
         <meta http-equiv="X-UA-Compatible" content="IE=9" />
    <meta name="language" content="es" />
    <meta charset="utf-8">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <?php 
    
    Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl.'/css/style.css',null);
    if (($this->id=='tienda')&&($this->action->id=='index'))
        Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . '/css/tienda.css');    
    // Yii::app()->clientScript->registerLinkTag('stylesheet','text/css','http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,400,300,600,700',null,null);
    Yii::app()->clientScript->registerLinkTag('shortcut icon','image/x-icon',Yii::app()->getBaseUrl().'/favicon.ico?v=3',null,null);  
    Yii::app()->getClientScript()->registerCoreScript( 'jquery.ui' );
    ?>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,400,300,600,700' rel='stylesheet' type='text/css'>
    <?php  Yii::app()->clientScript->registerScriptFile( Yii::app()->theme->baseUrl."/js/jquery.hoverIntent.minified.js" ); ?>
    <noscript><img height='1' width='1' alt='' style='display:none' src='https://www.facebook.com/offsite_event.php?id=6016397659254&amp;value=0.01&amp;currency=EUR' /></noscript>
<!-- start Mixpanel --><script type="text/javascript">(function(f,b){if(!b.__SV){var a,e,i,g;window.mixpanel=b;b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");
for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,e,d])};b.__SV=1.2;a=f.createElement("script");a.type="text/javascript";a.async=!0;a.src="//cdn.mxpnl.com/libs/mixpanel-2.2.min.js";e=f.getElementsByTagName("script")[0];e.parentNode.insertBefore(a,e)}})(document,window.mixpanel||[]);
mixpanel.init("da3a06a70248326e132ae8c873390868");</script><!-- end Mixpanel -->
</head>
 
<body class="<?php echo $this->getBodyClasses(); ?>">
  <div class="barra-carga"></div>
  <div id="navegacion_principal">
<?php

$total = 0; //variable para llevar el numero de notificaciones
$cont_productos = 0 ; //variable para llevar el numero de productos
$contadorMensaje = 0;
//<i class="icon-shopping-cart"></i> <span class="badge badge-important">2</span>



     
if (Yii::app()->user->id?UserModule::isAdmin():false){
        
    $this->widget('bootstrap.widgets.TbNavbar',array(
    'type'=> 'inverse',
    'items'=>array(
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'htmlOptions'=>array('class'=>'pull-right'),
            'items'=>array(

                //array('label'=>'Personaling', 'url'=>array('/site/index')),
                array('label'=>'Panel de Control', 'url'=>'#', 'items'=>array(
                							 array('label'=>'Script', 'url'=>array('/Orden/ordenFinalizada')),
                                            array('label'=>'General', 'url'=>array('/controlpanel/index')),
                                            array('label'=>'Ventas', 'url'=>array('/controlpanel/ventas')),
                                            array('label'=>'Productos', 'url'=>array('/controlpanel/productos')),
                                            array('label'=>'Looks', 'url'=>array('/controlpanel/looks')),
                                            array('label'=>'Usuarios', 'url'=>array('/controlpanel/usuarios')),
                                            array('label'=>'Personal Shoppers', 'url'=>array('/controlpanel/personalshoppers')),
                                            array('label'=>'Seguimiento de Bugs', 'url'=>array('/bug/admin')),
                                            array('label'=>'Metricas', 'url'=>array('/shoppingMetric/admin')),                                             
                                            array('label'=>'Acciones', 'url'=>array('#')),
                                         
                    )),

                array('label'=>'Usuarios', 'url'=>array('/user/admin')),

                array('label'=>'Looks', 'url'=>'#', 'items'=>array(
                                            array('label'=>'Todos los Looks', 'url'=>array('/look/admin')),
                                            array('label'=>'Campañas', 'url'=>array('/campana/index')),
                                            array('label'=>'Importar Descuentos', 'url'=>array('/look/importarDescuentos')),
                                            array('label'=>'Elementos Gráficos', 'url'=>array('/adorno/index')),
                                        )), 

                array('label'=>'Productos', 'url'=>'#', 'items'=>array(
                                            array('label'=>'Todos los Productos', 'url'=>array('/producto/admin')),
                                            array('label' => 'Egresos de Mercancía','url'=>array('/movimiento/adminEgresos')),
                                            array('label'=>'Inventario','url'=>'#',
                                                'items' => array(
                                                    array('label' => 'Reporte de Inventario',
                                                        'url'=>array('/producto/reporte'),),                                                   
                                                    array('label' => 'Reporte de Defectuosos',
                                                        'url'=>array('/movimiento/defectuosos'),),
                                                    array('label' => 'Ver MasterDatas',
                                                        'url'=>array('/masterData/admin'),),
                                                    array('label' => 'Ver Inbounds',
                                                        'url'=>array('/inbound/admin'),),
                                                )),
                                            array('label'=>'Importar','url'=>'#',
                                                'items' => array(

                                                    array('label' => 'Productos Internos',
                                                        'url'=>array('/producto/importar'),),
                                                    array('label' => 'Productos Externos',
                                                        'url'=>array('/producto/importarExternos'),), 
                                                    array('label' => 'Descuentos',
                                                        'url'=>array('/producto/importarPrecios'),),
                                                    
                                                )),                                        
                                           
                                            
                    )
                ),
                array('label'=>'Ventas', 'url'=>'#', 'items'=>array(
                    array('label'=>'Órdenes Registradas', 'url'=>array('/orden/admin')),
                    array('label'=>'Devoluciones', 'url'=>array('/orden/adminDevoluciones')),
                    array('label'=>'Reporte de Ventas', 'url'=>array('/orden/reporte')),
                   ) 
                ),
                array('label'=>'PS', 'url'=>'#', 'items'=>array(
                                        
                    array('label'=>'Pagos a Personal Shoppers', 'url'=>array('/pago/admin')),
                      array('label'=>'Comisiones por venta directa', 'url'=>array('/controlpanel/personalshoppers')),
                     array('label'=>'Comisiones por afiliación PS', 'url'=>array('/pago/comisionAfiliacion')),
                    array('label'=>'Comisiones por clics', 'url'=>array('/pago/comisionClick')),
                    )
                ),
                
                array('label'=>'Promociones', 'url'=>'#', 'items'=>array(
                    array('label'=>'Gift Cards', 'url'=>array('/giftcard/index')),
                    array('label'=>'Códigos de Descuento', 'url'=>array('/codigoDescuento/index')),

                                ),
                    ),
                    
                 array('label'=>'Configuración', 'url'=>'#', 'items'=>array(
                     array('label'=>'Home', 'url'=>array('/site/homeConf')),
                    array('label'=>'Colores', 'url'=>array('/color/admin')),
                    array('label'=>'Marcas', 'url'=>array('/marca/admin')),
                    array('label'=>'Categorías', 'url'=>array('/categoria/admin')),
                    array('label'=>'Tiendas', 'url'=>array('/tiendaExterna/admin')),
                    array('label'=>'Activos Graficos', 'url'=>array('/site/activos_graficos')),
                    array('label'=>'SEO', 'url'=>array('/controlpanel/seo')),
                    

                                )
                    ),   
                //array('label'=>'Sistema', 'url'=>array('/site/logout')),
                array('label'=>'Tu Cuenta', 'url'=>'#', 'items'=>array(
                    array('label'=>'Tu Cuenta', 'url'=>array('/user/profile/micuenta')),
                    array('label'=>'Perfil', 'url'=>'#'),
                    array('label'=>'Soporte', 'url'=>'#'),
                    array('label'=>'Salir', 'url'=>array('/site/logout')),
                ),
               ),
            ),
        ),
    ),
));
} else {
	$cont_productos = 0;
        $cantProductosGuest = 0;

        if(Yii::app()->getSession()->contains("Bolsa")){

            $cantProductosGuest = count(Yii::app()->getSession()->get("Bolsa"));
        }



		$sql = "select count( * ) as total from tbl_orden where user_id=".Yii::app()->user->id." and estado < 5";
		$total = Yii::app()->db->createCommand($sql)->queryScalar();

    //Consulta de mensajes
    $mensajes = Mensaje::model()->findAllByAttributes(array('user_id'=>Yii::app()->user->id,'visible'=>1,'admin'=>NULL));
    if(count($mensajes) > 0){
      foreach($mensajes as $msj)
      {
        if( $msj->estado == 0)
          $contadorMensaje++;
      }
    }

  // Buscar usuario para avatar en el menu
	if (Yii::app()->user->id){
		$profile = Profile::model()->findByAttributes(array('user_id'=>Yii::app()->user->id));
		$user = User::model()->findByPk(Yii::app()->user->id);
    $avatar ='';
    if($user){
      $file = explode('.',$user->getAvatar());
      $avatar = "<img  src='".$file[0]."_x30.".$file[1]."' class='img-circle avatar_menu' width='30' height='30' />   ";
    }

    $Arraynombre = explode(" ",$profile->first_name);
    if(strlen($Arraynombre[0]) > 0)
      $nombre = $Arraynombre[0];
    else
     $nombre = $profile->first_name;

		$bolsa = Bolsa::model()->findByAttributes(array('user_id'=>Yii::app()->user->id));

		if(isset($bolsa))
			$cont_productos = count($bolsa->bolsahasproductos);

	} else {
		$nombre = 'N/A';
    $avatar = '';

	}
    if(!UserModule::isPersonalShopper())
        $itemsUser = array(
                    array('label'=>'Tus Looks', 'url'=>array('/user/profile/looksencantan')),
                    array('label'=>'Tus Compras', 'url'=>array('/orden/listado')),
                    array('label'=>'Invita a tus Amig@s', 'url'=>array('/user/profile/invitaciones')),
                    array('label'=>'Comprar GiftCard', 'url'=>array('/giftcard/comprar')),
                   
                    array('label'=>'Tu Cuenta', 'url'=>array('/user/profile/micuenta')),
										                     
                    array('label'=>'Ayuda', 'url'=>array('/site/preguntas_frecuentes')),
                    '---',
                    array('label'=>'¿Comprando para alguién más?'),
                    //array('label'=>'<a href="#" class="sub_perfil_item"><img width="30" height="30" class="img-circle avatar_menu" src="/develop/images/avatar_provisional_2_x30.jpg">Elise</a>',
//                    array('label'=>'<img width="30" height="30" class="img-circle avatar_menu" src="/develop/images/avatar_provisional_2_x30.jpg">Elise',
//                        'url'=>array(''), 'linkOptions' => array('class' => 'sub_perfil_item'),),

                );
    else
        $itemsUser = array(
                    array('label'=>'Tu Cuenta', 'url'=>array('/user/profile/micuenta')),
                    array('label'=>'Tus Compras', 'url'=>array('/orden/listado')),
                    array('label'=>'Tus Ventas', 'url'=>array('/user/profile/misventas'),
                                            'visible' => UserModule::isPersonalShopper()),   
                    array('label'=>'Tus Pagos', 'url'=>array('/pago/index'), 'visible' => UserModule::isPersonalShopper()),
                    array('label'=>'Looks Favoritos', 'url'=>array('/user/profile/looksencantan')),
                    array('label'=>'Comprar GiftCard', 'url'=>array('/giftcard/comprar')),
                    array('label'=>'Invita a tus Amig@s', 'url'=>array('/user/profile/invitaciones')),
                    array('label'=>'Ayuda', 'url'=>array('/site/preguntas_frecuentes')),
                    '---',
                    array('label'=>'¿Comprando para alguién más?'),
                 
                );

        $otrosPerfiles = Filter::model()->findAllByAttributes(array('type' => '0', 'user_id' => Yii::app()->user->id),array('order' => 'id_filter DESC'));

        $verMas = count($otrosPerfiles) > 2;

        $cont = 0;

        foreach($otrosPerfiles as $perfil){
            $cont++;
            if(strlen($perfil->name) > 15){
                $perfil->name = substr_replace($perfil->name, " ...", 15);
            }

            $itemsUser[] = array('label'=>'<img width="30" height="30" class="img-circle avatar_menu" src="'.Yii::app()->baseUrl.'/images/'.Yii::app()->language.'/avatar_provisional_2_x30.jpg">'.$perfil->name,
                'url'=>'#',
                'linkOptions' => array('class' => 'sub_perfil_item', 'id' => $perfil->id_filter),
                //'itemOptions' => array('id' => $perfil->id_filter),
                );

            if($cont >= 2){
                break;
            }
        }
        $todos = count($otrosPerfiles);
        if($verMas){
           $itemsUser[] =  array('label'=>"Ver todos los perfiles ...",
                                    'url'=>'#', 'linkOptions' => array('class' => 'sub_perfil_item ver_todos'), //array('/site/preguntas_frecuentes')
                                    );
        }


        array_push($itemsUser, array('label'=>'Añadir un nuevo perfil <i class="icon icon-plus"></i>',
                                    'url'=>'#modalFiltroPerfil', 'linkOptions' => array('data-toggle' => 'modal', 'id' => 'agregar-perfil'), //array('/site/preguntas_frecuentes')
                                    ),
                                '---',
                                array('label'=>'Salir', 'url'=>array('//site/logout')));


$this->widget('bootstrap.widgets.TbNavbar',array(
    'collapse' => true,
    'items'=>array(
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'htmlOptions'=>array('class'=>'pull-right'),
            'encodeLabel'=>false,
            'items'=>array(

                //array('label'=>'Personaling', 'url'=>array('/site/index')),

                // array('label'=>'¿Cómo funciona?', 'url'=>array('/site/comofunciona')),
                array('label'=>'Looks', 'url'=>array('/tienda/look'),'visible'=>!UserModule::isPersonalShopper()),
                // array('label'=>'Top', 'url'=>array('//site/top'),'visible'=>!Yii::app()->user->isGuest),
                //array('label'=>'Tu personal Shopper', 'url'=>array('/site/personal'),'visible'=>Yii::app()->user->isGuest?false:!UserModule::isPersonalShopper()),
                array('label'=>'Mis Looks', 'url'=>'#', 'visible'=>Yii::app()->user->isGuest?false:UserModule::isPersonalShopper(), 'items'=>array(
                    array('label'=>'Ver Looks', 'url'=>array('/look/listarLooks')),
                    array('label'=>'Administrar Looks', 'url'=>array('/look/mislooks')),
                )),
                array('label'=>'Crear Look', 'url'=>array('/look/create'), 'visible'=>Yii::app()->user->isGuest?false:UserModule::isPersonalShopper()),
                array('label'=>'Looks', 'url'=>array('/tienda/look'),'visible'=>UserModule::isPersonalShopper()),
                array('label'=>'Tienda', 'url'=>array('/tienda/index'), 'itemOptions'=>array('id'=>'tienda_menu')),
                array('label'=>'Outlet', 'url'=>array('/outlet'), 'itemOptions'=>array('id'=>'outlet_menu'), 'visible'=>Yii::app()->params['outlet']),
                array('label'=>'Magazine', 'url'=>'http://personaling.com/magazine','itemOptions'=>array('id'=>'magazine'),'linkOptions'=>array('target'=>'_blank')),
                array('label'=>'','icon'=>'icon-gift', 'url'=>array('/giftcard/comprar'), 'itemOptions'=>array('id'=>'btn-gift','class'=>'hidden-phone to-white-icon', 'data-html'=>"true"), 'visible'=>!Yii::app()->user->isGuest,),
				array('label'=>$contadorMensaje,'icon'=>'icon-exclamation-sign', 'url'=>array('/site/notificaciones'), 'itemOptions'=>array('id'=>'btn-notifications','class'=>'hidden-phone to-white-icon'), 'visible'=>!Yii::app()->user->isGuest&&$total>0),
                //array('label'=>$cont_productos,'icon'=>'icon-exclamation-sign', 'url'=>array('/orden/listado'), 'visible'=>!Yii::app()->user->isGuest),
                array('label'=>$cont_productos,'icon'=>'icon-shopping-cart', 'itemOptions'=>
                    array('id'=>'btn-shoppingcart','class'=>'hidden-phone to-white-icon') ,
                    'url'=>array('/bolsa/index') ,'visible'=>!Yii::app()->user->isGuest),
                array('label'=>$cantProductosGuest,'icon'=>'icon-shopping-cart', 'itemOptions'=>
                    array('id'=>'btn-shoppingBag','class'=>'hidden-phone to-white-icon') ,
                    'url'=>array('/user/login') ,'visible'=>Yii::app()->user->isGuest),
                array('label'=>'Accede', 'url'=>array('/user/login'), 'itemOptions'=>array('id'=>'ingresa'),'visible'=>Yii::app()->user->isGuest),
                //******* MODIFICACION EN TbBaseMenu.php PARA PODERLE COLOCAR CLASE AL BOTON *******//
                array('label'=>"Regístrate", 'url'=>array('/user/registration'), 'htmlOptions'=>array('class'=>'btn btn-rectangle'),'visible'=>Yii::app()->user->isGuest),
                //array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
                //array('label'=>$avatar.$nombre, 'url'=>'#','itemOptions'=>array('id'=>'dropdownUser'), 'items'=> $itemsUser,
                array('label'=>$avatar."<span id='userName'>{$nombre}</span>", 'url'=>'#','itemOptions'=>array('id'=>'dropdownUser'), 'items'=> $itemsUser,
                'visible'=>!Yii::app()->user->isGuest,
				),
            ),
        ),

    ),
));


}

 


    if(Yii::app()->params['environment']): 
?>
 
        <div class="navbar-fixed-top" id="entorno" style="background: #A23; top:71px; color:#FFF;z-index:999;"">
           Estás en:
           <b><?php echo  Yii::app()->params['environment'];?></b>
           <?php echo Yii::app()->params['zohoActive']?'<span class="pull-right">Zoho: <b>'.Yii::app()->params['zohoToken'].'</b> '.Yii::app()->params['zohoAccount'].'</span>':"Zoho esta Desactivado"; ?>
          </br>
           <?php echo "Database: ".Yii::app()->db->connectionString; ?> 
        </div>
         <script>
            $('#page').css('margin-top','90px'); //aumentar si se agregan mas cosas al cintillo
        </script>
      
<?php endif; ?>



</div>

<?php

//si es invitado agregar el estilo del link de vaciar bolsa
if(Yii::app()->user->isGuest){
?>
  <style>
      .popover .link-vaciar{
          font-size: 11px;
      }
  </style>  
<?php
}
?>


  
  
<!-- Mensaje Cookies ON -->
<div class="header_notification" id="cookies_notification" style="margin-top: 88px; display: none;">
    Esta web utiliza <strong>cookies</strong> para mejorar tu experiencia de usuario y para recopilar información estadística sobre tu navegación. Si continúas navegando, consideramos que aceptas su uso. <a href="<?php echo Yii::app()->baseUrl; ?>/site/politicas_de_cookies" style="color: #0000FF">Más información</a> | <a id="accept_cookies" href="#" style="color: #0000FF">No mostrar de nuevo</a>
    <button id="buttomCookies" type="button" class="close" aria-hidden="true">&times;</button>

</div>
<!-- Mensaje Cookies OFF -->

<?php
if(!Yii::app()->user->isGuest){
	$user = User::model()->findByPk(Yii::app()->user->id);
	
	if($user->status == 0){
		?>
		<div id="notificacion_validar" class="alert-block alert-error text_align_center">
			Tu cuenta no ha sido validada. 
			<?php
			echo CHtml::ajaxLink(
				'Reenviar correo de validación.', 
				$this->createUrl('user/registration/sendValidationEmail'), 
				array('success'=>'function(data){
					$("#notificacion_validar").html(data);
					$("#notificacion_validar").removeClass();
					$("#notificacion_validar").addClass("alert alert-success margin_top padding_top text_align_center");
				}'), 
				array()
			);
			?>
		</div>
                <div style="height: 48px"></div>
		<?php
	}
}
?>
<!-- Popovers ON -->

 <?php   
    if(!Yii::app()->user->isGuest){
        $bolsa = Bolsa::model()->findByAttributes(array('user_id'=>Yii::app()->user->id));

        if (!is_null($bolsa)){
	        // Consulta si hay Looks
	        $sql = "select count( *   ) as total from tbl_bolsa_has_productotallacolor where look_id != 0 and bolsa_id = ".$bolsa->id."";
	        $cantidadLooks = Yii::app()->db->createCommand($sql)->queryScalar();
	 
	        //Consulta si hay productos individuales
	        $sql = "select count( * ) as total from tbl_bolsa_has_productotallacolor where look_id = 0 and bolsa_id = ".$bolsa->id."";
	        $cantidadProductosIndiv = Yii::app()->db->createCommand($sql)->queryScalar();        
	
	        $bptcolor = BolsaHasProductotallacolor::model()->findAllByAttributes(array('bolsa_id'=>$bolsa->id,'look_id'=> 0));
        } else {
        	$cantidadLooks = 0;
        	$cantidadProductosIndiv = 0;
        }

    }
  ?> 
 


<!-- <div class="alert alert-error margin_top padding_top">Estas en el sitio de Pruebas T1</div> -->
<div class="container" id="page">
  <?php if(isset($this->breadcrumbs)):?>
  <?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?>
  <!-- breadcrumbs -->
  <?php endif?>
  <?php echo $content; ?> </div>
<!-- page -->
<div id="modalAjax"></div>

<footer>
    <div id="wrapper_footer">
        <div class="main_footer">
            <div class="container">
                <div class="row hidden-phone">
                    <div class="span3">
                        <h4>Información de Interés</h4>
                        <ul>
                          <li><a href="<?php echo Yii::app()->getBaseUrl(); ?>/acerca-personaling" title="Acerca de">Acerca de Personaling</a></li>
                          <li><a href="<?php echo Yii::app()->getBaseUrl(); ?>/formas-de-pago" title="Formas de Pago">Formas de Pago</a></li>
                          <li><a href="<?php echo Yii::app()->getBaseUrl(); ?>/envios" title="Envíos y Encomiendas">Envíos</a></li>
                          <li><a href="<?php echo Yii::app()->getBaseUrl(); ?>/site/politicas_de_devoluciones" title="Políticas de Devoluciones">Políticas de Devoluciones</a></li>
                          <li><a href="<?php echo Yii::app()->getBaseUrl(); ?>/site/politicas_y_privacidad" title="Políticas de Privacidad">Políticas de Privacidad</a></li>
                          <li><a href="<?php echo Yii::app()->getBaseUrl(); ?>/site/politicas_de_cookies" title="Políticas de Cookies">Políticas de Cookies</a></li>
                          <li><a href="<?php echo Yii::app()->getBaseUrl(); ?>/preguntas_frecuentes" title="Preguntas Frecuentes">Preguntas Frecuentes</a></li>
                          <li><a href="<?php echo Yii::app()->getBaseUrl(); ?>/site/terminos_de_servicio" title="Términos de Servicio">Términos de Servicio</a></li>
                           <li><a href="<?php echo Yii::app()->getBaseUrl(); ?>/user/registration/aplicarPS" title="Aplicar para Personal Shopper">Aplicar para Personal Shopper</a></li>
                           <li><a href="<?php echo Yii::app()->getBaseUrl(); ?>/site/terminos_condiciones_ps" title="Terminos y condiciones PS">Términos y condiciones para Personal Shoppers</a></li>
                            <li><a href="<?php echo Yii::app()->getBaseUrl(); ?>/site/contacto" title="Contacto">Contáctanos</a></li>
                        </ul> 
                      </div>
                      
                      <div class="span3">
                        <h4>Lo que encontrarás</h4>
                        <ul>
                          <li><a href="<?php echo Yii::app()->getBaseUrl(); ?>/looks-personalizados" title="Looks Personalizados">Looks</a></li>
                          <li><a href="<?php echo Yii::app()->getBaseUrl(); ?>/tienda-ropa-personalizada" title="Ropa">Ropa</a></li>
                          <li><a href="<?php echo Yii::app()->getBaseUrl(); ?>/tienda-ropa-personalizada" title="Ropa">Zapatos</a></li>
                          <li><a href="<?php echo Yii::app()->getBaseUrl(); ?>/tienda-ropa-personalizada" title="Ropa">Accesorios</a></li>
                          <li><a href="<?php echo Yii::app()->getBaseUrl(); ?>/user/profile/listado" title="Personal Shoppers">Personal Shoppers</a></li>
                        </ul>
                      </div>                      
                      
                      <div class="span3">
                        <h4>Sigue nuestras redes sociales</h4>
                            <ul>
                               <li>  <a title="Facebook" href="https://www.facebook.com/Personaling">Facebook</a></li>  
                               <li>  <a title="Instagram" href="http://instagram.com/personaling">Instagram </a></li>  
                               <li>  <a title="Twitter" href="https://twitter.com/personaling">Twitter </a></li>  
                               <li>  <a title="Pinterest" href="https://pinterest.com/personaling/">Pinterest   </a></li>  
                               <li> <a title="Youtube" href="http://www.youtube.com/channel/UCe8aijeIv0WvrZS-G-YI3rQ">Youtube </a> </li>  
                          
                        </ul>
                      </div>
                      <div class="span3">
                            <h4>Métodos de Pago</h4>
                            <div class="row-fluid">
                                <a class="span3">
                                    <img alt="MasterCard" src="<?php echo Yii::app()->theme->baseUrl.'/images/home/logos/mc.png';?>" height="25px"/>
                                </a>
                                <a class="span3">
                                    <img style="margin-top:7px" alt="Visa" src="<?php echo Yii::app()->theme->baseUrl.'/images/home/logos/visa.png';?>" height="25px"/>
                                </a>
                                <a class="span3">
                                    <img style="margin-top:12px" alt="Paypal" src="<?php echo Yii::app()->theme->baseUrl.'/images/home/logos/pp.png';?>" height="25px"/>
                                </a>
                            </div>
                            <h4 class="margin_top">Seguridad de Datos</h4>
                            <div class="row-fluid">
                               <a class="span2">
                                                     <img alt="SSL Security" src="<?php echo Yii::app()->theme->baseUrl.'/images/home/logos/ssl.png';?>" height="25px"/>
                                                </a>
                                                <div class="span8">
                                                    <script type="text/JavaScript">
                                                    //<![CDATA[
                                                    // var sealServer=document.location.protocol+"//seals.websiteprotection.com/sealws/525d3892-d158-46f3-aacd-5777cbdd56cb.gif";var certServer=document.location.protocol+"//certs.websiteprotection.com/sealws/?sealId=525d3892-d158-46f3-aacd-5777cbdd56cb";var hostName="personaling.com";document.write(unescape('<div style="text-align:center;margin:0 auto;"><a target="_blank" href="'+certServer+'&pop=true" style="display:inline-block;"><img src="'+sealServer+'" alt="Website Protection&#153; Site Scanner protects this website from security threats." title="This Website Protection site seal is issued to '+ hostName +'. Copyright &copy; 2013, all rights reserved."oncontextmenu="alert(\'Copying Prohibited by Law\'); return false;" border="0" /></a><div id="bannerLink"><a href="https://www.godaddy.com/" target="_blank">Go Daddy</a></div></div>'));
                                                    
                                                    //]]>
                                                    </script>
                                             
                                                </div>
                               
                            </div>
                        
                      
                      </div>
                      
                    
                    
                </div>
                
            </div>
            
        </div>
        
        
        <div>
            <div class="row-fluid margin_top_medium padding_bottom">
                  <div class="span12 text_align_center creditos"><?php echo Yii::app()->params['clientName']; ?> Nuestro RIF <?php echo Yii::app()->params['clientIdentification']; ?> | Todos los derechos reservados<br/>
                   Desarrollado por: <br/>
                   <!-- <a class="to_add" href="http://cooltribes.com" title="Connecting true fans" target="_blank">Cooltribes.com</a> -->
                    <a  href="http://cooltribes.com" title="Connecting true fans" target="_blank">
                        <img src="<?php echo Yii::app()->baseUrl ?>/images/cooltribes100.png" width="100" />
                        
                    </a> 
                   </div>
                </div>        
            
        </div>
        
    </div>
<script type="text/javascript">
setTimeout(function(){var a=document.createElement("script");
var b=document.getElementsByTagName("script")[0];
a.src=document.location.protocol+"//dnn506yrbagrg.cloudfront.net/pages/scripts/0027/3531.js?"+Math.floor(new Date().getTime()/3600000);
a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);
</script>    
    
</footer>
<script >
  
  $(document).on('ready',HandlerReady);

  <?php 
		$url="'".Yii::app()->baseUrl."/giftcard/comprar'";
		echo 'var gift = ""; ';
		
		'<p class="padding_small"><strong>Tu carrito todavía esta vacío</strong>, ¿Qué esperas? Looks y prendas increíbles esperan por ti.</p>';
   
		
		$gift="<p class='padding_left_small padding_top_xsmall'><span class='gifts-menu'>Tu Balance:<strong> ".Yii::app()->numberFormatter->format("#,##0.00",Profile::model()->getSaldo(Yii::app()->user->id,true))." ".Yii::t('contentForm','currSym').
		"</strong></span><br/><div class='padding_right_xsmall padding_left_xsmall padding_bottom_xsmall'><a href='".Yii::app()->baseUrl."/giftcard/comprar"."' class='btn btn-block btn-small btn-danger'>Comprar Giftcard</a></div>";
		 echo 'gift = "'.$gift.'";';
        $htmlMensaje = '';
         echo 'var contenidoMensajes = ""; ';
        // Si el usuario no es administrador buscar mensajes para mostrar
        if(! (Yii::app()->user->id?UserModule::isAdmin():false) ){
          if(count($mensajes) > 0){
            $mensajes_Reverse = array_reverse( $mensajes ); // volveo el array para mostrar en orden cronologico
            array_splice( $mensajes_Reverse ,4);
            $htmlMensaje='<ul>';
            foreach( $mensajes_Reverse  as $msj)
            {
              if ($msj->estado == 0)
                $htmlMensaje=$htmlMensaje." <li class='bg_color10' >De: <strong>Admin</strong> <br/><strong>Asunto:</strong>  ".$msj->asunto.'</li> ';
              else
                $htmlMensaje=$htmlMensaje.' <li >De: <strong>Admin</strong> <br/><strong>Asunto:</strong> '.$msj->asunto.'</li> ';                
            }
            $htmlMensaje=$htmlMensaje.'</ul>';
          }

          echo 'contenidoMensajes = "'.$htmlMensaje.'";';

        }


  ?>


  function HandlerReady () {
    // //Boton Notificaciones

    contenidoMensajes = contenidoMensajes + '<div class="padding_right_xsmall padding_left_xsmall padding_bottom_xsmall"><a href="<?php echo Yii::app()->baseUrl; ?>/site/notificaciones"  class="btn btn-block btn-small btn-danger">Ver notificaciones</a></div>';
	
    $('#btn-notifications').popover(
    {
      title: '<strong>Notificaciones ('+ <?php echo $contadorMensaje ?>+')</strong>',
      content: contenidoMensajes,
      placement: 'bottom',
      trigger: 'manual',
      html: true,
    });

    $('#btn-notifications').hoverIntent(function(){
        $(this).popover('show');
        $(this).addClass('bg_color10');
        $('.popover').addClass('active_two');
      },
      function(){
        $('.active_two').hover(function(){},function(){
          $('#btn-notifications').popover('hide');
          $('#btn-notifications').removeClass('bg_color10');
        });   

      });
      
       $('#btn-gift').popover(
    {
     
      title:'<strong>Balance y Giftcards</strong>',
      content: gift,
      placement: 'bottom',
      trigger: 'manual',
      html: true,
    });
 
    $('#btn-gift').hoverIntent(function(){
        $(this).popover('show');
        $(this).addClass('bg_color10');
        $('.popover').addClass('active_two');
      },
      function(){
        $('.active_two').hover(function(){},function(){
          $('#btn-gift').popover('hide');
          $('#btn-gift').removeClass('bg_color10');
        });   

      });
      
      
      
        $('.active_two').hover(function(){},function(){
          $('#btn-notifications').popover('hide');
          $('#btn-notifications').removeClass('bg_color10');
          });  

        $('#magazine').hover(function(){
          $('#btn-notifications').popover('hide');          
          $('#btn-notifications').removeClass('bg_color10');
          $('#btn-gift').popover('hide');          
          $('#btn-gift').removeClass('bg_color10');

        },function(){});

        $('#btn-shoppingcart').hover(function(){
          $('#btn-notifications').popover('hide');          
          $('#btn-notifications').removeClass('bg_color10');          
        },function(){});
		
		 $('#btn-gift').hover(function(){
          $('#btn-notifications').popover('hide');          
          $('#btn-notifications').removeClass('bg_color10');          
        },function(){});
    
    var listaCarrito;

    //------------Generar html para poner en Popover ON---------------//
    <?php if(!Yii::app()->user->isGuest){

      $contadorItems = 0 ;

      //Si hay Looks en la bolsa del usuario
      if($cantidadLooks!=0){

          $clases = '" unstyled clearfix"';
          echo "listaCarrito = '<ul class=".$clases." >";
          $bolsa_Reverse = array_reverse($bolsa->looks());
          
          foreach ($bolsa_Reverse as $look_id) {

              if($contadorItems > 5){
                break;
              }

              $bolsahasproductotallacolor = BolsaHasProductotallacolor::model()->findAllByAttributes(array('bolsa_id'=>$bolsa->id,'look_id' => $look_id));
              $look = Look::model()->findByPk($look_id);
        if (isset($look)){
                echo '<li>';
                echo '<a class="btn-link" href="'.$look->getUrl().'" >'.addslashes($look->title).'</a>';
                echo '<div class="row-fluid">';
  
                //invertir array para mostrar en orden cronológico de compras
  
                foreach ($bolsahasproductotallacolor as $productotallacolor) {
                    $color = Color::model()->findByPk($productotallacolor->preciotallacolor->color_id)->valor;
                    $talla = Talla::model()->findByPk($productotallacolor->preciotallacolor->talla_id)->valor;
                    $producto = Producto::model()->findByPk($productotallacolor->preciotallacolor->producto_id);
                    $imagen = Imagen::model()->findByAttributes(array('tbl_producto_id'=>$producto->id,'orden'=>'1'));
                    if($imagen){
                        $htmlimage = CHtml::image(Yii::app()->baseUrl .'/images/'.Yii::app()->language.'/producto/'. str_replace(".","_x30.",$imagen->url), "Imagen ", array("width" => "30", "height" => "30"));
                        echo '<div class="span2">'.$htmlimage.'</div>';
                    }
                }
                echo '</div>';
                echo "</li>";
                $contadorItems ++;
              }
          }
          if($cantidadProductosIndiv!=0){
              echo "';";
          }
      }
      elseif($cantidadProductosIndiv!=0){
          echo "listaCarrito = '<ul>';";
      }

      //Si hay producto individuales en la bolsa del usuario
      if( $cantidadProductosIndiv != 0 ){
          if(isset($bptcolor)){ 
            echo "\n    listaCarrito = listaCarrito + '";

            $bptcolor_Rev = array_reverse($bptcolor);

            foreach($bptcolor_Rev as $productoBolsa){ // cada producto en la bolsa

              if($contadorItems >= 5){
                break;
              }

                $todo = Preciotallacolor::model()->findByPk($productoBolsa->preciotallacolor_id);                
                $producto = Producto::model()->findByPk($todo->producto_id);
                $talla = Talla::model()->findByPk($todo->talla_id);
                $color = Color::model()->findByPk($todo->color_id);                  
                $imagen = Imagen::model()->findByAttributes(array('tbl_producto_id'=>$producto->id,'orden'=>'1'));
                echo "<li>";
                echo '<a class="btn-link" href="'.$producto->getUrl().'" >'.$producto->nombre.'</a>';
                echo '<div class="row-fluid">';
                 
                if($imagen){
                    $htmlimage = CHtml::image(Yii::app()->baseUrl .'/images/'.Yii::app()->language.'/producto/'. str_replace(".","_x30.",$imagen->url), "Imagen ", array("width" => "30", "height" => "30"));
                    echo '<div class="span2">'.$htmlimage.'</div>';
                }
                echo '</div>';
                echo "</li>";
                $contadorItems ++;

            }
            echo "</ul>';";    
          }  
      }
      elseif( $cantidadLooks != 0 ){
          echo "</ul>';";
      }


    }
    ?>

    //------------Generar html para poner en Popover OFF---------------//

    textShoppingCart = '<div class="padding_right_xsmall padding_left_xsmall padding_bottom_xsmall"><a href="<?php
    echo Yii::app()->baseUrl; ?>/bolsa/index" class="btn btn-block btn-small btn-danger">Ver carrito</a></div>';

    if( listaCarrito != "" ){
        textShoppingCart = listaCarrito + textShoppingCart;
    }  
    if(<?php echo $cont_productos ?> == 0){
      textShoppingCart = '<p class="padding_small"><strong>Tu carrito todavía esta vacío</strong>, ¿Qué esperas? Looks y prendas increíbles esperan por ti.</p>';
    }
 
    //Boton Shopping Cart
    $('#btn-shoppingcart').popover(
    {
      html: true,
      title: '<strong>Tu Carrito ('+ <?php echo $cont_productos  ?>+')</strong>',
      content: textShoppingCart,
      placement: 'bottom',
      trigger: 'manual',
      offset: 10
    });
    


    $('#btn-shoppingcart').hoverIntent(
      function(){

        $(this).popover('show');
        $(this).addClass('bg_color10 color3');
        $('.popover').addClass('active_one');        

      },
      function(){

        $('.active_one').hover(function(){},function(){
          $('#btn-shoppingcart').popover('hide');
          $('#btn-shoppingcart').removeClass('bg_color10 color3');
        });        

      });



    /********Para la bolsa de Guest ON******/
<?php if(Yii::app()->user->isGuest){ ?>

    function clickVaciar(){
        
        $('.popover #link-vaciar a').click(function(e){
            $("body").addClass("aplicacion-cargando");
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '<?php echo Yii::app()->createUrl('bolsa/vaciarGuest'); ?>',
                dataType: 'JSON',
                data: {},
                success: function(data){
                    
                    if(data.status == 'success'){
                        location.reload();
                    }else if(data.status == 'error'){
                        location.reload();                        
                    }
                }
            }); 
        });

    }    
    var textShoppingBag = '<?php echo Yii::app()->user->isGuest?
            Bolsa::textoBolsaGuest($cantProductosGuest):""; ?>';    


    $('#btn-shoppingBag').popover(
    {
      html: true,
      title: '<strong>Tu Carrito</strong>',
      content: textShoppingBag,
      placement: 'bottom',
      trigger: 'manual',
      offset: 10
    });

    $('#btn-shoppingBag').hoverIntent(
      function(e){

          $(this).popover('show');
          $('.popover').addClass('active_one'); 
          $(this).addClass('bg_color5');
          
          clickVaciar();
          

      },
      function(e){
//            console.log(e.offsetX + " : " + e.offsetY);
//            var x = e.offsetX;
//            var y = e.offsetY;
//            
//            if(x > -2){
////                console.log("vale");
//            }
          //revisar si no ubicó el cursor dentro de la bolsa            
//          if(!$('.active_one').is(":hover")){
//              $('#btn-shoppingBag').popover('hide'); 
//              $('#btn-shoppingBag').removeClass('bg_color5');
//          }
          
          $('.active_one').hover(function(){},function(){
              $('#btn-shoppingBag').popover('hide');
              $('#btn-shoppingBag').removeClass('bg_color5');
        });

      });      
      
<?php } ?>
/*Shopping bag guest OFF*/   
   
   
   
    $('#dropdownUser, #btn-notifications,#magazine').hover(function(){
      $('#btn-shoppingcart').popover('hide');      
      $('#btn-shoppingcart').removeClass('bg_color10');      
      $('#btn-gift').popover('hide');          
      $('#btn-gift').removeClass('bg_color10');        
    },function(){});
    
    $('#dropdownUser').hoverIntent(function(){
        if( !($(this).attr('class') =='dropdown open') ){          
          $(this).addClass('open');
        }
    },function(){
      // $('#dropdownUser').removeClass('open');
      $('#page').hover(function(){
        $('#dropdownUser').removeClass('open');
      },function(){});        
    });

    $('#btn-shoppingcart, #btn-notifications').hover(function(){
      $('#dropdownUser').removeClass('open');
    },function(){});        

    $('#dropdownUser').on('click',function(){
        $(this).removeClass('open');      
    });
 
 
     //Elemento li del menu de usuario para agregar un nuevo filtro
    $('#agregar-perfil').click(function(e){
                
        var urlActual = "<?php echo CController::createUrl(""); ?>";
        var tiendaLooks = "<?php echo CController::createUrl("/tienda/look"); ?>";        
        var redirect = "<?php echo CController::createUrl("/tienda/redirect"); ?>";        
        //si esta en tienda de looks
        if(urlActual === tiendaLooks){
            clickAgregar();
        }else{
        
        //Llevar a tienda de looks
            
            $.ajax({
                type: 'POST',
                url: redirect,
                dataType: 'JSON',
                data: {agregar : 1},
                success: function(data){

                    if(data.status == 'success'){

                      window.location = tiendaLooks;  

                    }else if(data.status == 'error'){
                        

                    }
                }
            });
        }
        
    });
    
    //Click para seleccionar un peril de la lista que esta en el dropdown User
    $("#dropdownUser a.sub_perfil_item:not(.ver_todos), #modalPerfilesOcultos li a").click(function(e){
        e.preventDefault();
        var urlActual = "<?php echo CController::createUrl(""); ?>";
        var tiendaLooks = "<?php echo CController::createUrl("/tienda/look"); ?>";        
        var redirect = "<?php echo CController::createUrl("/tienda/redirect"); ?>";        
        var tiendaGetfilter = "<?php echo CController::createUrl("/tienda/getFilter"); ?>";        
        //si esta en tienda de looks
        if(urlActual === tiendaLooks){
            clickPerfil($(this).prop("id"), tiendaGetfilter);
        }else{
        
        //Llevar a tienda de looks
            var datos = $(this).prop("id");
            $.ajax({
                type: 'POST',
                url: redirect,
                dataType: 'JSON',
                data: {perfil : datos},
                success: function(data){

                    if(data.status == 'success'){

                      window.location = tiendaLooks;  

                    }else if(data.status == 'error'){
                        

                    }
                }
            });
        }
       
    });
    
    //Click en el elemento del dropdown para ver todos los perfiles ocultos
    $("#dropdownUser .ver_todos").click(function(e){
        e.preventDefault();
        
        //Llevar a tienda de looks
            var urlModal = "<?php echo CController::createUrl("/tienda/modalAjax"); ?>";  
            
            
            $.ajax({
                type: 'POST',
                url: urlModal,
                dataType: 'JSON',
                data: {modal : "perfiles"},
                success: function(data){
                    $("#modalAjax").empty();
                    $("#modalAjax").html(data.data);
                    $("#modalPerfilesOcultos").modal("show");
                    
                }
            });
       
    });
 
 
  }



</script>

<!-- Popovers OFF -->

<!-- Google Analytics 
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-1015357-44']);
  _gaq.push(['_trackPageview']);
  _gaq.push(['_setDomainName', 'personaling.es']);
  _gaq.push(['_setAllowLinker', true]);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
-->
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    <?php
    // New Google Analytics code to set User ID.
    // $userId is a unique, persistent, and non-personally identifiable string ID.
    if (isset(Yii::app()->user->id)) {
        $gacode = "ga('create', 'UA-1015357-44', { 'userId': '%s' });";
        echo sprintf($gacode, Yii::app()->user->id);
        $setcode = "ga('set', '&uid', %s);";
        echo sprintf($setcode, Yii::app()->user->id);
    }else{
        $gacode = "ga('create', 'UA-1015357-44', 'auto');";
        echo sprintf($gacode);
    }
    ?>
    ga('require', 'displayfeatures');
    ga('send', 'pageview');
    ga('require', 'ec');
</script>

<script>
    $(document).ready(function(){
    	
        var accepted = readCookie('accept_cookies');
        if(!accepted){
            $('#cookies_notification').show();
        }

        // verificar si es outlet para arreglar las clases del menu
        if(document.URL.indexOf('outlet') != -1){
            $('#outlet_menu').addClass('active');
            $('#tienda_menu').removeClass('active');
        }
    });

    $('#buttomCookies').on('click', function(e){
        createCookie('accept_cookies', 'true', 365);
        $('#cookies_notification').hide();
    });

    $('#accept_cookies').on('click', function(e){
        createCookie('accept_cookies', 'true', 365);
        $('#cookies_notification').hide();
    });

    $('body').on('click', function(e){
        createCookie('accept_cookies', 'true', 365);
    });

    function createCookie(name,value,days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString();
        }
        else var expires = "";
        document.cookie = name+"="+value+expires+"; path=/";
    }

    function readCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }

    function eraseCookie(name) {
        createCookie(name,"",-1);
    }
    
    /********Para la bolsa de Guest ON******/
<?php if(Yii::app()->user->isGuest){ ?>
    
    function desplegarBolsaGuest(data){    
    
        $('#btn-shoppingBag').popover('destroy');
        $('#btn-shoppingBag').popover(
        {
          content: data.contenido,                      
          html: true,
          title: '<strong>Tu Carrito</strong>',
          placement: 'bottom',
          trigger: 'manual',
          offset: 10
        });

        //cambiar el numero de items en la bolsa
        var icono = $('#btn-shoppingBag a i');
        $('#btn-shoppingBag a').html(icono).append(" " + data.cantidad);

        //mostrar el popover del carrito
        $('#btn-shoppingBag').popover("show");
        //agregar el evento del link para vaciar
        //clickVaciar();
        
        
    }

<?php }
?>

</script>

<script type="text/javascript">//var $zoho= $zoho || {salesiq:{values:{},ready:function(){}}};var d=document;s=d.createElement("script");s.type="text/javascript";s.defer=true;s.src="https://salesiq.zoho.com/personalingenterprise/float.ls?embedname=chatdepersonaling";t=d.getElementsByTagName("script")[0];t.parentNode.insertBefore(s,t);</script>

</body>
</html>
