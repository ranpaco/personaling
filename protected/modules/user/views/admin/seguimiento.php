<?php
$this->breadcrumbs = array(
    'Usuarios' => array('admin'),
    'Editar',);
?>
<div class="container margin_top">
    <div class="page-header">
        <h1>Editar Usuario</small></h1>
    </div>
    <!-- SUBMENU ON -->
    <?php $this->renderPartial('_menu', array('model' => $model, 'activo' => 11)); ?>
    <!-- SUBMENU OFF -->
    <div class="row margin_top">
        <div class="span12">
            <div class="bg_color3   margin_bottom_small padding_small box_1">
                <form method="post" action="" id="registration-form"   class="form-stacked form-horizontal" enctype="multipart/form-data">
                    <fieldset>
                        <legend>Historial de movimientos en el proceso de compra</legend>

                        <div class=" margin_top">
                            <div class="">

                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#tab1">Compra en Tienda</a></li>
                                    <li><a data-toggle="tab" href="#tab2">Compra de GiftCards</a></li>	   
                                </ul>    
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tab1">	            

                                        <?php
                                        $template = '{summary}
			  <table width="100%" cellspacing="0" cellpadding="0" border="0" class="table table-bordered ta table-hover table-striped">
			  <tbody>
			    <tr>
                            
                            <th scope="col">Paso del Proceso</th>
                            <th scope="col">Fecha</th>
             
			    </tr>
			    {items}
				</tbody>
			    </table>
			    {pager}
				';
                                            
                                        $this->widget('zii.widgets.CListView', array(
                                            'id' => 'email',
                                            'dataProvider' => $movimientos,
                                            'itemView' => '_shoppM',
                                            'template' => $template,
                                            'enableSorting' => 'true',
                                            'afterAjaxUpdate' => " function(id, data) {
										} ",
                                            'pager' => array(
                                                'header' => '',
                                                'htmlOptions' => array(
                                                    'class' => 'pagination pagination-right',
                                                )
                                            ),
                                        ));
                                        ?>
                                    </div>
                                    <div class="tab-pane" id="tab2">
                                        <?php
                                        $template = '{summary}
			  <table width="100%" cellspacing="0" cellpadding="0" border="0" class="table table-bordered ta table-hover table-striped">
			  <tbody>
			    <tr>
                            
                            <th scope="col">Paso del Proceso</th>
                            <th scope="col">Fecha</th>
             
			    </tr>
			    {items}
				</tbody>
			    </table>
			    {pager}
				';
                                            
                                        $this->widget('zii.widgets.CListView', array(
                                            'id' => 'email',
                                            'dataProvider' => $movimientosGC,
                                            'itemView' => '_shoppM',
                                            'template' => $template,
                                            'enableSorting' => 'true',
                                            'afterAjaxUpdate' => " function(id, data) {
										} ",
                                            'pager' => array(
                                                'header' => '',
                                                'htmlOptions' => array(
                                                    'class' => 'pagination pagination-right',
                                                )
                                            ),
                                        ));
                                        ?>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /container -->