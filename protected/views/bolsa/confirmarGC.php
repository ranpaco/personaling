<?php
Yii::app()->clientScript->registerLinkTag('stylesheet','text/css','https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,400,300,600,700',null,null);
Yii::import('application.components.*');
require_once "mercadopago-sdk/lib/mercadopago.php";
$mp = new MP ("8356724201817235", "vPwuyn89caZ5MAUy4s5vCVT78HYluaDk");
$mp->sandbox_mode(TRUE);
//$accessToken = $mp->get_access_token();
//var_dump($accessToken);

$this->setPageTitle(Yii::app()->name . " - " . Yii::t('contentForm', 'Purchase confirmation'));

if (!Yii::app()->user->isGuest) { // que este logueado
	
//Yii::app()->getSession()->add('descuento',$descuento);
//Yii::app()->getSession()->add('total',$total);	
	//echo 'Total: '.$total.' - Descuento: '.$descuento;
?>
<style>
        .progreso_compra_giftcard {
            width: 268px;
        }
        .progreso_compra_giftcard .last-done {
            text-align: center;
        }
    </style>
<div class="container margin_top">
  <div class="progreso_compra progreso_compra_giftcard">
    <div class="clearfix margin_bottom">
      <div class="first-past"><?php echo Yii::t('contentForm','Authentication'); ?></div>
      <div class="middle-past">
        <?php echo Yii::t('contentForm','Payment <br> method'); ?>
    </div>
      <div class="last-done">
        <?php echo Yii::t('contentForm','Confirm <br>purchase'); ?>
    </div>
    </div>
  </div>
  <div class="row">
    <div class="span12">
      <h1><?php echo Yii::t('contentForm','Purchase confirmation'); ?></h1>
    </div>
  </div>
  <input type="hidden" id="tarjeta" value="<?php echo(Yii::app()->getSession()->get('idTarjeta')); ?>" />
  <!-- <input type="hidden" id="idCard" value="0" /> -->

  <div class="row margin_top_medium">
    <section class="span4"> 
      <!-- Direcciones ON -->
      <div class="well">
        <h4 class="braker_bottom"><?php echo Yii::t('contentForm','Purchasing Information'); ?></h4>
        <?php 
        //datos de todas las giftcards de la bolsa
        //Temporalmente solo una
        //
        
        ?>
        <p> <strong><?php echo Yii::t('contentForm','Amount Gift Card'); ?></strong> <br/>
          <span class="muted small"><?php echo $giftcard->monto.' '.Yii::t('contentForm','currSym'); ?></span>
        </p>
        <p>
            <strong><?php echo Yii::t('contentForm','Effective date'); ?></strong> <br/>
            <?php echo  Yii::t('contentForm','From1').' '.date("d/m/Y"); ?> <br/>
            <?php echo Yii::t('contentForm','Until'); ?> 
            <?php $now = date('Y-m-d', strtotime('now'));
                  echo date("d/m/Y", strtotime($now." + 1 year")); ?>
        </p>
<!--        <p> <strong>Otros datos</strong>: <?php echo "otros"; ?> <br/>
        </p>-->
        
        <!-- Direcciones OFF --> 
        
      </div>
    </section>
    <section class="span4">
      <div class="well ">
        <h4> <?php echo Yii::t('contentForm','Payment Method Selected'); ?> </h4>
        <div class=" margin_bottom">
          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table">
            <?php 
              	if(Yii::app()->getSession()->get('tipoPago')==1){
                    
                    echo "<tr class='deptran'><td valign='top'><i class='icon-exclamation-sign'></i> ".Yii::t('contentForm','Deposit or Bank Transference').".</td></tr>";
                
                    
                }else if(Yii::app()->getSession()->get('tipoPago')==4){
                    
                    echo "<tr class='mp'><td valign='top'><i class='icon-exclamation-sign'></i>".Yii::t('contentForm','MercadoPago').".</td></tr>";
                    
                }else if(Yii::app()->getSession()->get('tipoPago')==2){
                    
                    echo "<tr class='mp'>
                            <td valign='top'><i class='icon-exclamation-sign'></i>  ".Yii::t('contentForm','Credit Card').".</td>
                          </tr>";

                    $tarjeta = TarjetaCredito::model()->findByPk($idTarjeta);

                    $rest = substr($tarjeta->numero, -4);
                    echo "<tr class='mp'><td>";
                        echo "<b> ".Yii::t('contentForm','Name').":</b> ".$tarjeta->nombre."
                        </br><b> ".Yii::t('contentForm','Number').":</b> XXXX - XXXX - XXXX - ".$rest."                            
                        </br><b> ".Yii::t('contentForm','Expiration').":</b> ".$tarjeta->vencimiento;
                    echo "</td></tr>";
                                        
                }else if(Yii::app()->getSession()->get('tipoPago') == 5){
                    
                    echo "<tr><td valign='top'><i class='icon-exclamation-sign'></i> ".
                            Yii::t('contentForm','Credit Card').".</td></tr>";
                
                    
                }else if(Yii::app()->getSession()->get('tipoPago') == 6){
                    
                    echo "<tr><td valign='top'><i class='icon-exclamation-sign'></i> ".
                            Yii::t('contentForm','PayPal').".</td></tr>";
                    
                }
              ?>
          </table>
        </div>
      </div>
    </section>
    <section class="span4"> 
      <!-- Resumen de Productos ON -->
      <div class="well well_personaling_big">
        <h5><?php echo Yii::t('contentForm','Total Gift Cards'); ?>: <?php echo 1;?>
        </h5>
        <hr/>
        <div class="margin_bottom">
          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-condensed ">
            <tr>
              <th class="text_align_left"><?php echo Yii::t('contentForm','Subtotal'); ?> :</th>
              <td><?php echo Yii::t('contentForm','currSym').'. '.Yii::app()->numberFormatter->formatCurrency($monto, ''); ?></td>
            </tr>
            <tr>
              <th class="text_align_left"><h4><?php echo Yii::t('contentForm','Total'); ?> :</h4></th>
              <td><h4><?php echo Yii::t('contentForm','currSym').'. '.Yii::app()->numberFormatter->formatCurrency($monto, ''); ?></h4></td>
            </tr>
          </table>
          <?php
          $tipo_pago = Yii::app()->getSession()->get('tipoPago');
          /*Para mercadopago*/
              if($tipo_pago == 4){
              	$user = User::model()->findByPk(Yii::app()->user->id);
				$profile = Profile::model()->findByPk(Yii::app()->user->id);
              	$preference = array (
                    "items" => array (
                        array (
                            "title" => "Look seleccionado + productos individuales",
                            "quantity" => 1,
                            "currency_id" => "VEF",
                            "unit_price" => $total
                            //"unit_price" => 23
                        )
                    ),
                    "payer" => array(
                                "name" => $profile->first_name,
                                "surname" => $profile->last_name,
                                "email" => $user->email
                        ),
                        "back_urls" => array(
                                "success" => 'http://personaling.com'.Yii::app()->baseUrl.'/bolsa/successMP',
                                "failure" => 'http://personaling.com'.Yii::app()->baseUrl.'/bolsa/successMP',
                                "pending" => 'http://personaling.com'.Yii::app()->baseUrl.'/bolsa/successMP'
                        ),
                );
                $preferenceResult = $mp->create_preference($preference);
                ?>
              <a href="<?php echo $preferenceResult['response']['sandbox_init_point']; ?>" name="MP-Checkout" id="boton_mp" class="blue-L-Rn-VeAll" mp-mode="modal"><?php echo Yii::t('contentForm','Pay MercadoPago'); ?></a>
          <?php
            
              }
              /*DEPOSITO O TRANSFERENCIA*/ 
              else if($tipo_pago == 1  || $tipo_pago == 2){ 
                  
              	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
			    'id'=>'form-Comprar',
			    'action'=>Yii::app()->createUrl('bolsa/comprarGC'),
			    'htmlOptions'=>array('class'=>'well'),
			)); 
                
                $tipo_pago = Yii::app()->getSession()->get('tipoPago');
                echo CHtml::hiddenField('codigo_randon',rand());
                
                $this->widget('bootstrap.widgets.TbButton', array(
                    'type'=>'warning',
                    'buttonType'=>'submit',
                    //'buttonType'=>'button',
                    'size'=>'large',
                    'label'=>$tipo_pago==2?Yii::t('contentForm','Pay with credit card') :Yii::t('contentForm','Complete purchase'),
                    //'url'=>Yii::app()->createUrl('bolsa/comprar'), // action
                    'icon'=>'lock white',
                    'loadingText'=>'Cargando',
                    'htmlOptions'=>array(
                        //'onclick'=>'js:enviar_pago();',
                        'id'=>'btnPagarTDC',
                        )
                )); 
		
                $this->endWidget(); 
                
                /*Si es en españa bankCard o Paypal*/    
              }else if($tipo_pago == 5  || $tipo_pago == 6){ 
          	
                      if($tipo_pago == 5){                          
                        $this->widget('ext.fancybox.EFancyBox', array(
                            'target'=>'#btn-ComprarEsp',
                            'config'=>array(
                                "type" => "iframe",                        
                                "height" => "100%",                        
                                "width" => "65%",                        
                                "autoScale" => false,                        
                                "transitionIn" => "none",                        
                                "transitionOut" => "none",  
                                
                                "hideOnOverlayClick" => false,
                                "enableEscapeButton" => false,
                                "showCloseButton" => false,

                                ),
                            )
                        );
                      }
                        
                        echo "<div class='well text_align_center'>";
                        $this->widget('bootstrap.widgets.TbButton', array(
                            'type'=>'warning',        
                            //'buttonType'=>'button',
                            'size'=>'large',
                            'label'=>$tipo_pago==5?Yii::t('contentForm','Pay with credit card') :Yii::t('contentForm','Pay with PayPal'),
                            'url'=> $urlAztive, // action
                            'icon'=>'lock white',
                            'htmlOptions'=>array(
        //                        'onclick'=>'js:enviar_pago();'
                                'id' => 'btn-ComprarEsp',
//                                'data-toggle' => "modal",
//                                'data-target' => "#modalPrueba",
                                )
                        )); 
                        
                        echo "</div>";
                        
		  }
           ?>
        </div>
        <p><i class="icon-calendar"></i><?php echo Yii::t('contentForm','Date estimated delivery') ?>: <br/><?php echo date('d/m/Y', strtotime('+1 day'));?>  - <?php echo date('d/m/Y', strtotime('+1 week'));  ?> </p>
      </div>
      <p><a href="<?php echo Yii::app()->getBaseUrl(); ?>/site/politicas_de_devoluciones" title="Políticas de Envios y Devoluciones" target="_blank"> <?php echo Yii::t('contentForm', 'See Shipping and Returns Policies'); ?> </a></p>
      <p class="muted"><i class="icon-comment"></i> <?php echo Yii::t('contentForm', 'Contact an advisor for assistance Personaling: Monday to Friday 8:30 am to 5:00 pm'); ?></p>
      
      <!-- Resumen de Productos OFF --> 
      
    </section>
  </div>
</div>
<!-- /container -->

<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'myModal','htmlOptions'=>array('class'=>'modal_grande hide fade','tabindex'=>'-1','role'=>'dialog','aria-labelleby'=>'myModalLabel','aria-hidden'=>'true'))); ?>

<?php $this->endWidget(); ?>

<?php 

}// si esta logueado
else
{
	// redirecciona al login porque se murió la sesión
	//header('Location: /site/user/login');	
	$url = CController::createUrl("/user/login");
        header('Location: '.$url);	
}


?>
<script>


$(document).ready(function(){
    $("#btnPagarTDC").click(function(e){
	$(this).attr("disabled", true);
        $(this).html('<i class="icon-lock icon-white"></i> Procesando pago...');
        $("body").addClass("aplicacion-cargando");
        $("#form-Comprar").submit();
        
    });
});

function enviar_pago(){
    $(this).html("Procesando el Pago...");
    $(this).attr("disabled", true);                               
}

function enviar()
{
        $('#boton_completar').attr("disabled", true);
        var idDireccion = $("#idDireccion").attr("value");
        var tipoPago = $("#tipoPago").attr("value");
        var subtotal = $("#subtotal").attr("value");
        var descuento = $("#descuento").attr("value");
        var envio = $("#envio").attr("value");
        var iva = $("#iva").attr("value");
        var total = $("#total").attr("value");
        var usar_balance = $("#usar_balance").attr("value");
        var seguro = $("#seguro").attr("value");
        var tipo_guia = $("#tipo_guia").attr("value");
        var peso = $("#peso").attr("value");

        $.ajax({
        type: "post",
        dataType: 'json',
        url: "comprar", // action 
        data: { 'idDireccion':idDireccion, 'tipoPago':tipoPago, 'subtotal':subtotal, 'descuento':descuento, 'envio':envio, 'iva':iva, 'total':total, 'usar_balance':usar_balance,
                        'seguro':seguro, 'tipo_guia':tipo_guia, 'peso':peso }, 
        success: function (data) {
            //console.log('Total: '+data.total+' - Descuento: '+data.descuento);
            if(data.status=="ok")
            {

                    window.location=data.url;
            }else if(data.status=='error'){
                    //console.log(data.error);
            }
        }//success
       })

}
	
function registrarCompra(){

    $.ajax({
        type: "post",
        dataType: 'json',
        url: "comprarGC", // action 
        data: { 'idDireccion':idDireccion, 'tipoPago':tipoPago, 'subtotal':subtotal,
                        'descuento':descuento, 'envio':envio, 'iva':iva, 'total':total,
                        'usar_balance':usar_balance, 'idDetalle':data.idDetalle,'seguro':seguro,'tipo_guia':tipo_guia, 'peso':peso
                        }, 
        success: function (data) {
                        if(data.status=="ok")
                        {
                                window.location="pedido/"+data.orden+"";
                        }
        }//success
    })

}
        
        
function enviarTarjeta()
{
    $('#boton_pago_tarjeta').attr("disabled", true);

    var idDireccion = $("#idDireccion").attr("value");
    var tipoPago = $("#tipoPago").attr("value");
    var subtotal = $("#subtotal").attr("value");
    var descuento = $("#descuento").attr("value");
    var envio = $("#envio").attr("value");
    var iva = $("#iva").attr("value");
    var total = $("#total").attr("value");
    var seguro = $("#seguro").attr("value");
    var usar_balance = $("#usar_balance").attr("value");
    var tipo_guia = $("#tipo_guia").attr("value");
    var peso = $("#peso").attr("value");
    var tarjeta = $("#tarjeta").attr("value");
    //var total_cobrar = "<?php //echo $total; ?>";
    var total_cobrar = "<?php echo 0; ?>";
    /* lo de la tarjeta */
    /*
    var idCard = $("#idTarjeta").attr("value"); // por ahora siempre 0, luego deberia ser el id del escogido
    var nom = $("#nombre").attr("value");
    var num = $("#numero").attr("value");
    var cod = $("#codigo").attr("value");
    var mes = $("#mes").attr("value");
    var ano = $("#ano").attr("value");
    var dir = $("#direccion").attr("value");
    var ciud = $("#ciudad").attr("value");
    var est = $("#estado").attr("value");
    var zip = $("#zip").attr("value");
    */
    if(tarjeta!="0") // el id de la tarjeta de credito que esta temporal en la pagina anterior
    {
            /*if(nom=="" || num=="" || cod=="" || mes=="Mes" || ano=="Ano")
            {
                    alert("Por favor complete los datos.");
            }
            else
            {*/

            //alert("idCard: "+idCard+" nombre: "+nom+", numero"+num+", cod:"+cod+", mes y año "+mes+"-"+ano+", dir "+dir+", ciudad "+ciud+", estado "+est+", zip"+zip);

                    $.ajax({
            type: "post",
            dataType: 'json',
            url: "credito", // action 
           /* data: { 'tipoPago':tipoPago, 'total':total, 'idCard':idCard,'nom':nom,'num':num,'cod':cod,
                            'mes':mes,'ano':ano,'dir':dir,'ciud':ciud, 'est':est,'zip':zip
                            }, */
            data: { 'tipoPago':tipoPago, 'total':total_cobrar, 'tarjeta':tarjeta
                            }, 		
            success: function (data) {

                            if(data.status==201) // pago aprobado
                            {

                                    $.ajax({
                                    type: "post",
                                    dataType: 'json',
                                    url: "comprar", // action 
                                    data: { 'idDireccion':idDireccion, 'tipoPago':tipoPago, 'subtotal':subtotal,
                                                    'descuento':descuento, 'envio':envio, 'iva':iva, 'total':total,
                                                    'usar_balance':usar_balance, 'idDetalle':data.idDetalle,'seguro':seguro,'tipo_guia':tipo_guia, 'peso':peso
                                                    }, 
                                    success: function (data) {
                                                    if(data.status=="ok")
                                                    {
                                                            window.location="pedido/"+data.orden+"";
                                                    }
                                    }//success
                                  })
                            }
                            else
                            {
                                    // no pasó la tarjeta

                                    if(data.status==400)
                                    {
                                            if(data.mensaje=="Credit card has Already Expired"){
                                                    //alert('La tarjeta que intentó usar ya expiró.');
                                                    window.location="error/1";
                                            }

                                            if(data.mensaje=="The CardNumber field is not a valid credit card number."){
                                                    //alert('El número de tarjeta que introdujó no es un número válido.');
                                                    window.location="error/2";
                                            }
                                            if(data.mensaje=="CVC Number Invalid"){
                                                    //alert('El número de tarjeta que introdujó no es un número válido.');
                                                    window.location="error/6";
                                            }							
                                    }

                                    if(data.status==401)
                                    {
                                            //alert('error de autenticacion');
                                            window.location="error/3";
                                    }

                                    if(data.status==403)
                                    {
                                            //alert('No pudimos completar su operación: '+data.mensaje);
                                            window.location="error/5";
                                    }

                                    if(data.status==503)
                                    {
                                            //alert('error interno');
                                            window.location="error/4";
                                    }
                            }

            }//success
           })

            //}
    }
    else
    {

            $.ajax({
            type: "post",
            dataType: 'json',
            url: "credito", // action 
            data: { 'tipoPago':tipoPago, 'total':total, 'idCard':idCard }, 
            success: function (data) {

                            if(data.status==201) // pago aprobado
                            {

                                    $.ajax({
                                    type: "post",
                                    dataType: 'json',
                                    url: "comprar", // action 
                                    data: { 'idDireccion':idDireccion, 'tipoPago':tipoPago, 'subtotal':subtotal,
                                                    'descuento':descuento, 'envio':envio, 'iva':iva, 'total':total,
                                                    'usar_balance':usar_balance, 'idDetalle':data.idDetalle,'seguro':seguro,'tipo_guia':tipo_guia, 'peso':peso
                                                    }, 
                                    success: function (data) {
                                                    if(data.status=="ok")
                                                    {
                                                            window.location="pedido/"+data.orden+"";
                                                    }
                                    }//success
                                  })
                            }
                            else
                            {
                                    // no pasó la tarjeta			
                                    if(data.status==400){

                                            if(data.mensaje=="Credit card has Already Expired"){
                                                    //alert('La tarjeta que intentó usar ya expiró.');
                                                    window.location="error/1";
                                            }

                                            if(data.mensaje=="The CardNumber field is not a valid credit card number."){
                                                    //alert('El número de tarjeta que introdujó no es un número válido.');
                                                    window.location="error/2";
                                            }
                                    }

                                    if(data.status==401){
                                            //alert('error de autenticacion');
                                            window.location="error/3";
                                    }

                                    if(data.status==403){
                                            //alert('No pudimos completar su operación: '+data.mensaje);
                                            window.location="error/5";
                                    }

                                    if(data.status==503){
                                            //alert('error interno');
                                            window.location="error/4";
                                    }
                            }

            }//success
           })

    }

}
	
function enviar_mp(json)
{
        $('#boton_mp').attr("disabled", true);
        //alert("return");
        var idDireccion = $("#idDireccion").attr("value");
        var tipoPago = $("#tipoPago").attr("value");
        var subtotal = $("#subtotal").attr("value");
        var descuento = $("#descuento").attr("value");
        var envio = $("#envio").attr("value");
        var iva = $("#iva").attr("value");
        var total = $("#total").attr("value");
        var seguro = $("#seguro").attr("value");
        var tipo_guia = $("#tipo_guia").attr("value");
        var peso = $("#peso").attr("value");

         if (json.collection_status=='approved'){
            alert ('Pago acreditado');
        } else if(json.collection_status=='pending'){
            alert ('El usuario no completó el pago');
            $.ajax({
                type: "post",
                dataType: 'json',
                url: "comprar", // action 
                data: { 'idDireccion':idDireccion, 'tipoPago':tipoPago, 'subtotal':subtotal, 'descuento':descuento, 'envio':envio, 'iva':iva, 'total':total, 'id_transaccion':json.collection_id,'seguro':seguro,'tipo_guia':tipo_guia, 'peso':peso}, 
                success: function (data) {

                                if(data.status=="ok")
                                {
                                        window.location="pedido/"+data.orden+"";
                                }
                }//success
           })
        } else if(json.collection_status=='in_process'){    
            alert ('El pago está siendo revisado');    

        } else if(json.collection_status=='rejected'){
            alert ('El pago fué rechazado, el usuario puede intentar nuevamente el pago');
        } else if(json.collection_status==null){
            alert ('El usuario no completó el proceso de pago, no se ha generado ningún pago');
        }

}
	
</script> 
<script type="text/javascript">
	(function(){function $MPBR_load(){window.$MPBR_loaded !== true && (function(){var s = document.createElement("script");s.type = "text/javascript";s.async = true;
	s.src = ("https:"==document.location.protocol?"https://www.mercadopago.com/org-img/jsapi/mptools/buttons/":"http://mp-tools.mlstatic.com/buttons/")+"render.js";
	var x = document.getElementsByTagName('script')[0];x.parentNode.insertBefore(s, x);window.$MPBR_loaded = true;})();}
	window.$MPBR_loaded !== true ? (window.attachEvent ? window.attachEvent('onload', $MPBR_load) : window.addEventListener('load', $MPBR_load, false)) : null;})();
</script> 
