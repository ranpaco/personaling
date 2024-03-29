<?php 
  $this->breadcrumbs=array(
  'Usuarios'=>array('admin'),
  'Editar',);
?>
<div class="container margin_top">
  <div class="page-header">
    <h1>Editar Usuario</small></h1>
  </div>
  <!-- SUBMENU ON -->
  <?php $this->renderPartial('_menu', array('model'=>$model, 'activo'=>6)); ?>
  <!-- SUBMENU OFF -->
  <div class="row margin_top">
    <div class="span12">
      <div class="bg_color3   margin_bottom_small padding_small box_1">
        <form method="post" action="/aiesec/user/registration?template=1" id="registration-form" class="form-stacked form-horizontal" enctype="multipart/form-data">
          <fieldset>
            <legend>Pedidos</legend>
            
            <?php
			$template = '{summary}
			  <table width="100%" cellspacing="0" cellpadding="0" border="0" class="table table-bordered table-hover table-striped">
			  <tbody>
			    <tr>
			      <th scope="col">Fecha</th>
                  <th scope="col">Monto</th>
                  <th scope="col">Estado</th>
                  <th scope="col">Método de pago</th>
                  <th scope="col">Acciones</th>
			    </tr>
			    {items}
				</tbody>
			    </table>
			    {pager}
				';
			
			$pagerParams=array(
            'header'=>'',
            'prevPageLabel' => Yii::t('contentForm','Previous'),
            'nextPageLabel' => Yii::t('contentForm','Next'),
            'firstPageLabel'=> Yii::t('contentForm','First'),
            'lastPageLabel'=> Yii::t('contentForm','Last'),
            
            'htmlOptions'=>array(
                'class'=>'pagination pagination-right'));
					
					$this->widget('zii.widgets.CListView', array(
				    'id'=>'list-pedidos',
				    'dataProvider'=>$dataProvider,
				    'itemView'=>'_view_pedido',
				      'summaryText' => 'Mostrando {start} - {end} de {count} Resultados', 
				    'template'=>$template,
				    'enableSorting'=>'true',
				    'afterAjaxUpdate'=>" function(id, data) {
										} ",
					'pager'=>$pagerParams,					
				));    
				?>
          </fieldset>
        </form>
      </div>
    </div>
    <?php /*?><div class="span3">
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
							top: "70px"
						});
					} else {
						if(st <= ot) {
							s.css({
								position: "relative",
								top: "0"
							});
						}
					}
				};
				$(window).scroll(move);
				move();
			}
		</script>
        <script type="text/javascript"> 
		// Script para dejar el sidebar fijo Parte 2
			$(function() {
				moveScroller();
			 });
		</script> 
        <!-- SIDEBAR OFF --> 
      
      
      
       
      </div>
    </div><?php */?>
  </div>
</div>
<!-- /container -->
