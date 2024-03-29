<?php
/* @var $this SiteController */
/* @var $model ContactForm */
/* @var $form CActiveForm */

$this->pageTitle=Yii::app()->name . ' - Términos de servicio';
$this->breadcrumbs=array(
	'Términos de servicio',
);
// Open Graph
  Yii::app()->clientScript->registerMetaTag('Personaling.com - Términos de servicio', null, null, array('property' => 'og:title'), null); 
  Yii::app()->clientScript->registerMetaTag('Portal de moda donde puedes comprar prendas y accesorios de marcas prestigiosas, personalizadas y combinadas a tu gusto, necesidades y características', null, null, array('property' => 'og:description'), null);
  Yii::app()->clientScript->registerMetaTag(Yii::app()->request->hostInfo.Yii::app()->request->url , null, null, array('property' => 'og:url'), null);
  Yii::app()->clientScript->registerMetaTag('Personaling.com', null, null, array('property' => 'og:site_name'), null); 
  Yii::app()->clientScript->registerMetaTag(Yii::app()->request->hostInfo.Yii::app()->baseUrl .'/images/icono_preview.jpg', null, null, array('property' => 'og:image'), null); 
?>

<div class="row">
  <div class="span8">
    <div class="box_1">
      <div class="page-header">
        <h1>Términos de servicio</h1>
      </div>
      <p align="justify">Los términos de servicio de nuestra página <a href="http://www.personaling.com" title="Personaling, Tu Personal Shopper Online">www.personaling.com</a> entran en vigencia desde el registro y al hacer una compra estás aceptando automáticamente cada uno de sus apartados, razón por la cual es muy importante te asegures de leerlos y comprenderlos con antelación. Cualquier cambio en la legislación vigente o de otra índole será notificado vía correo electrónico a cada uno de nuestros usuarios. </p>
      <ol>
        <li align="justify">Para estar registrado y comprar en personaling.com debes ser mayor de  dieciocho (18) años. </li>
        <li align="justify"><a href="http://www.personaling.com" title="Personaling, Tu Personal Shopper Online">Personaling.com</a> enviará a nuestros usuarios un número indeterminado de comunicaciones periódicas -que nuestro equipo considere necesarias- sobre productos, promociones e información general referente a nosotros o a empresas afiliadas a nuestra plataforma siempre con su consentimiento previo.</li>
        <li align="justify">La marca, contenido y logotipo son propiedad de Personaling.com, el registro en la página no otorga derecho ninguno sobre alguno de los elementos antes citados ni su distribución en ningún medio sin previa autorización de nuestro equipo. </li>
        <li align="justify"><a href="http://www.personaling.com" title="Personaling, Tu Personal Shopper Online">Personaling.com</a> podrá desincorporar cualquier perfil que considere viole o infrinja nuestros términos de servicio o considere fraudulento, sin previa notificación al usuario. 
        <li align="justify">El balance obtenido de promociones de parte de Personaling Enterprise Tiene un tiempo de uso Máximo de 30 Días o el que la promo lo indique.</li>  
</li>
      </ol>
    </div>
  </div>
  <!-- SIDEBAR ON -->
  <div class="span4"> <?php echo $this->renderPartial('_sidebar'); ?> </div>
  <!-- SIDEBAR ON --> 
</div>
