<?php /*<style>
.cajadevideo{

   max-width 710px;
   margin:auto;

}
.video{

   height:0px;
   width:100%;
   max-width:710px;    
   padding-top:56.25%;
   position:relative;

}

#acerca{

   position:absolute;
   height:80%;
   width:80%;
   top:0px;
   left:0px;

}
	
	
</style>

<?php
//$this->pageTitle=Yii::app()->name . ' - Página de inicio';
if(isset($seo)){
  $this->pageTitle = $seo->title;
  Yii::app()->clientScript->registerMetaTag($seo->title, 'title', null, null, null);
  Yii::app()->clientScript->registerMetaTag($seo->description, 'description', null, null, null);
  Yii::app()->clientScript->registerMetaTag($seo->keywords, 'keywords', null, null, null);
}
?>
<?php
/* @var $this SiteController */
/* @var $model ContactForm */
/* @var $form CActiveForm */

//$this->pageTitle=Yii::app()->name . ' - Acerca de Personaling';
$this->breadcrumbs=array(
	'Acerca de Personaling',
);
// Open Graph
  Yii::app()->clientScript->registerMetaTag('Personaling.com - Acerca de Personaling', null, null, array('property' => 'og:title'), null); 
  Yii::app()->clientScript->registerMetaTag('Personaling.com es el canal online de prestigiosas y conocidas marcas de moda internacional, donde no solo podrás comprar prendas y accesorios de tus tiendas favoritas, también tendrás a disposición de forma gratuita el servicio exclusivo de asesoria e inspiración de especialistas, conocedores en moda (personal shoppers) y hasta celebridades; quienes crearán atuendos adecuándolos a tu perfil, gusto, necesidades y ocasiones personales, permitiendo adquirir en un solo clic los productos y recibirlos en la comodidad de tu hogar u oficina.', null, null, array('property' => 'og:description'), null);
  Yii::app()->clientScript->registerMetaTag(Yii::app()->request->hostInfo.Yii::app()->request->url , null, null, array('property' => 'og:url'), null);
  Yii::app()->clientScript->registerMetaTag('Personaling.com', null, null, array('property' => 'og:site_name'), null); 
  Yii::app()->clientScript->registerMetaTag(Yii::app()->request->hostInfo.Yii::app()->baseUrl .'/images/icono_preview.jpg', null, null, array('property' => 'og:image'), null); 
?>

<div class="row">
  <div class="span12">
    <div class="box_1">
      <div class="padding_medium">
        <div class="page-header">
          <h1>Acerca Personaling</h1>
        </div>
        <div class="row">
          
            <?php echo Yii::t('contentForm','About us text'); ?>
          
        	<div class="cajadevideo">
        		<div class="video">
            		<iframe id="acerca" src="http://www.youtube.com/embed/wRP91tMMcH4"></iframe>
        		</div>
    		</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- form --> 

