<?php

class BugController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */


	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array(''),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete', 'create', 'update', 'index', 'view'),
				'expression' => 'UserModule::isAdmin()',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Bug;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Bug']))
		{
			$model->attributes=$_POST['Bug'];
			$model->estado=0;
			$model->date=date('Y-m-d-h-i-s');
			
			if(!is_dir(Yii::getPathOfAlias('webroot').'/images/'.Yii::app()->language.'/bug/'))
			{
			  mkdir(Yii::getPathOfAlias('webroot').'/images/'.Yii::app()->language.'/bug/',0777,true);
			 }
			$images=CUploadedFile::getInstanceByName('image');
			
			
         	$contador = count(Bug::model()->findAll());
			$contador+= 1;
			#var_dump($contador);
			#Yii::app()->end();
			$nombre = Yii::getPathOfAlias('webroot').'/images/'.Yii::app()->language.'/bug/'.$contador;
			$extension = '.'.$images->extensionName;
			$model->image=$contador. $extension;
			#$images->saveAs($nombre . $extension);
			
			if ($images->saveAs($nombre . $extension)) {
		
		       		$image = Yii::app()->image->load($nombre.$extension);
					$image->resize(150, 150);
					$image->save($nombre.'_thumb'.$extension);
			}
			
					
			if($model->save())
			{
				$modelado=new BugReporte;
				$modelado->user_id=Yii::app()->user->id;
				$modelado->bug_id=$contador;
				$modelado->estado=0;	
				$modelado->descripcion=$_POST['Bug']['description'];
				$modelado->fecha=date('Y-m-d-h-i-s');
				$modelado->save();
				Yii::app()->user->setFlash('success',UserModule::t("Falla tecnica reportada exitosamente"));
                $this->redirect(array('admin'));
			}
			else 
			{
				Yii::app()->user->setFlash('error',UserModule::t("Falla tecnica no reportada, error inesperado"));
                $this->redirect(array('admin'));
			}
			
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Bug']))
		{
			$model->attributes=$_POST['Bug'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Bug');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Bug('search');

		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Bug']))
			$model->attributes=$_GET['Bug'];
		
			$criteria = new CDbCriteria;
			//$criteria->condition = 'data like "%look_id%"';
			$criteria->order = 'id DESC';
			$dataProvider = new CActiveDataProvider('Bug', array(
                    'criteria' => $criteria,
                    'pagination' => array(
                        #'pageSize' => Yii::app()->getModule('user')->user_page_size,
                    	),
                	));

		$this->render('admin',array(
			'model'=>$model,
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Bug the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Bug::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Bug $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='bug-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
