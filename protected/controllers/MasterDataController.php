<?php

class MasterDataController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
//	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','detalle',
                                    'descargarExcel','descargarXml'),
				'expression' => 'UserModule::isAdmin()',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Muestra los productos contenidos en un Inbound
	 */
	public function actionDetalle($id)
	{
            $dataProvider = $this->loadModel($id)->buscarProductos();
            
            $this->render('adminDetalle',array(
                    'dataProvider'=>$dataProvider,
                    'id'=>$id,
            ));
	}

	/**
	 * Descargar el archivo excel correspondiente al MasterData cargado
	 */
	public function actionDescargarExcel()
	{
            //Revisar la extension
            $archivo = Yii::getPathOfAlias("webroot").MasterData::RUTA_ARCHIVOS.
                    $_GET["id"].".xlsx";
            $existe = file_exists($archivo);
            
            //si no existe con extension xlsx, poner xls
            if(!$existe){
                $archivo = substr($archivo, 0, -1);
            }
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=MasterData-'.basename($archivo));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($archivo));
            ob_clean();
            flush();
            readfile($archivo);
            
	}
        
	/**
	 * Descargar el archivo XML correspondiente al MasterData cargado
	 */
	public function actionDescargarXml()
	{
            //Revisar la extension
            $archivo = Yii::getPathOfAlias("webroot").MasterData::RUTA_ARCHIVOS.
                    $_GET["id"].".xml";
            $existe = file_exists($archivo);
            
            //si no existe con extension xlsx, poner xls
            if(!$existe){
                throw new CHttpException(404,'The requested page does not exist.');
            }
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=MasterData-'.basename($archivo));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($archivo));
            ob_clean();
            flush();
            readfile($archivo);
            
	}


	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new MasterData('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['MasterData']))
			$model->attributes=$_GET['MasterData'];

		$this->render('admin',array(
			'dataProvider'=>$model->search(),
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=MasterData::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='master-data-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
