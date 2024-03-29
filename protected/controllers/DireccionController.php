<?php

class DireccionController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/column2';

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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','municipiosZoom'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','editar','cargarCiudades','addDireccion','decode','cargarProvincias','cargarCodigos'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','distintas'),
				//'users'=>array('admin'),
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
	
	public function actionCargarCiudades(){
		if(isset($_POST['provincia_id'])){
			$ciudades = Ciudad::model()->findAllBySql("SELECT * FROM tbl_ciudad WHERE provincia_id =".$_POST['provincia_id']." AND cod_zoom IS NOT NULL order by nombre ASC");
			if(sizeof($ciudades) > 0){
				$return = '<option>'.Yii::t('contentForm','Select a city').'</option>';
				foreach ($ciudades as $ciudad) {
					$return .= '<option value="'.$ciudad->id.'">'.$ciudad->nombre.'</option>';
				}
				echo $return;
			}
		}
	}
	 
	public function actionCargarCodigos(){
		if(isset($_POST['ciudad_id'])){
			$codigos= CodigoPostal::model()->findAllBySql("SELECT * FROM tbl_codigo_postal WHERE ciudad_id =".$_POST['ciudad_id']);
			if(sizeof($codigos) > 0){
				$return = '<option>'.Yii::t('contentForm','Select a zip code').'</option>';
				foreach ($codigos as $codigo) {
					$return .= '<option value="'.$codigo->id.'">'.$codigo->codigo.'</option>';
				}
				echo $return;
			}
		}
	}
	
	public function actionCargarProvincias(){
		if(isset($_POST['pais_id'])){
			$provincias = Provincia::model()->findAllBySql("SELECT * FROM tbl_provincia WHERE pais_id =".$_POST['pais_id']." order by nombre ASC");
			if(sizeof($provincias) > 0){
				$return = '<option value>'.Yii::t('contentForm','Select a province').'</option>';
				foreach ($provincias as $provincia) {
					$return .= '<option value="'.$provincia->id.'">'.$provincia->nombre.'</option>';
				}
				echo $return;
			}
		}
	}
	
	
	
	

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate($id = null)
	{
		if(!$id){
			$model=new Direccion;
		}else{
			$model = Direccion::model()->findByPk($id);
		}
		
		if(isset($_POST['Direccion']))
		{
			$model->attributes=$_POST['Direccion'];
			$model->pais=Pais::model()->getOficial($model->pais);
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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

		if(isset($_POST['Direccion']))
		{
			$model->attributes=$_POST['Direccion'];
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
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Direccion');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Direccion('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Direccion']))
			$model->attributes=$_GET['Direccion'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	
	public function actionAddDireccion($user){
		$direccion=new Direccion;
		$direccion->attributes=$_POST;
        if(isset($_POST['codigo_postal_id']))
		  $direccion->codigo_postal_id=$_POST['codigo_postal_id'];
		else
            $direccion->codigo_postal_id=CodigoPostal::model()->getCode($_POST['ciudad_id'],'id');
        if(is_numeric($direccion->pais))
		  $direccion->pais=Pais::model()->getOficial($direccion->pais);
          
        
		if($direccion->save()){
			$direcciones = Direccion::model()->findAllByAttributes(array('user_id'=>$direccion->user_id));
			echo '<legend >'
				.Yii::t('contentForm','Addresses used above').': </legend>'
				.$this->renderPartial('/bolsa/_direcciones', array(
	       			'direcciones'=>$direcciones,'user'=>$user, 'iddireccionNueva' =>$direccion->id ),true)
	       		;
		}
        else {
            print_r($direccion->getErrors());
        }
		
	}
	
	public function actionProvinciasSeur(){
		$soapclient = new SoapClient('https://ws.seur.com/WSEcatalogoPublicos/servlet/XFireServlet/WSServiciosWebPublicos?wsdl');
		$params2 = array(
			'in0'=>'',
			'in1'=>'',
			'in2'=>'',
			'in3'=>'WSPERSONALING',
			'in4'=>'ORACLE',
			
			);
		$response = $soapclient->infoProvinciasStr($params2);
		$xml = simplexml_load_string($response->out);
		foreach ($xml as $reg){
				
				
			$provincia= Provincia::model()->findByAttributes(array('nombre'=>utf8_decode($reg->NOM_PROVINCIA)));
			if(is_null($provincia)){
				$provincia= new Provincia;
				$provincia->nombre=utf8_decode($reg->NOM_PROVINCIA);
				$provincia->pais_id;
				if($provincia->save())
					echo "OK <br/>";
				else
					echo "BAD <br/>";
			}
				else
					echo "PROVINCIA EXISTENTE  <br/>";
		}
		
	} 
	
	

	public function actionCompararSeur(){
		$soapclient = new SoapClient('https://ws.seur.com/WSEcatalogoPublicos/servlet/XFireServlet/WSServiciosWebPublicos?wsdl');
		
		/*
		$cont=0;
		for($i='a'; $i<='z'; $i++)
		{	 */
		  
			$allcities=Ciudad::model()->findAll(array(
		    'condition'=>" provincia_id < 26 "
		    ));
			foreach($allcities as $crit){
		
		
				
				$params4 = array(
				'in0'=>'',
				'in1'=>$crit->nombre,
				'in2'=>'',
				'in3'=>'',
				'in4'=>'',
				'in5'=>'WSPERSONALING',
				'in6'=>'ORACLE',
				
				);
				
				$response = $soapclient->infoPoblacionesCortoStr($params4);
				$xml = simplexml_load_string($response->out);
				foreach ($xml as $reg){
					print_r($reg);
					echo "-- PID: ".$crit->provincia_id." -- ID:".$crit->id."<br/>";
				}
				
				
				
			}
		 	
			
			
			/*$cont++;
			if($cont==26)
				break;
		}*/
		
	}




	public function actionDecode(){
	$soapclient = new SoapClient('https://ws.seur.com/WSEcatalogoPublicos/servlet/XFireServlet/WSServiciosWebPublicos?wsdl');
		$all=Ciudad::model()->findAll(array(
		    'condition'=>" ruta_id>4 AND id NOT IN (select distinct(ciudad_id) from tbl_codigo_postal )"
		    ));
			$i=1;
		foreach($all as $city){

			$params4 = array(
			'in0'=>'',
			'in1'=>'%%' , 
			'in2'=>'',
			'in3'=>'',
			'in4'=>'',
			'in5'=>'WSPERSONALING',
			'in6'=>'ORACLE',
			
			);
			$response = $soapclient->infoPoblacionesCortoStr($params4);
			$xml = simplexml_load_string($response->out);
			foreach ($xml as $reg){
				echo $i.' <b>'.$city->nombre."</b> ----> ".utf8_decode($reg->NOM_POBLACION)."<br/>";
			
			}
			
			
			
		}
		   
			
	}
	
	public function actionDistintas(){
			$soapclient = new SoapClient('https://ws.seur.com/WSEcatalogoPublicos/servlet/XFireServlet/WSServiciosWebPublicos?wsdl');
		
			$sql="select distinct(nombre) from tbl_ciudad where provincia_id>25";
			$ciudades=Yii::app()->db->createCommand($sql)->queryColumn();
			foreach ($ciudades as $ciudad){
				$res= Ciudad::model()->findAllByAttributes(array('nombre'=>$ciudad));
				if(count($res)>1)
				{	foreach ($res as $result){
						
						$params4 = array(
						'in0'=>'',
						'in1'=>$result->nombre,
						'in2'=>'',
						'in3'=>'',
						'in4'=>'',
						'in5'=>'WSPERSONALING',
						'in6'=>'ORACLE',
						
						);
						$response = $soapclient->infoPoblacionesCortoStr($params4);
						$xml = simplexml_load_string($response->out);
						$cad="(";
						foreach ($xml as $reg){
							$cad.="'".$reg->CODIGO_POSTAL."',";
						}
						$cad = trim($cad, ',');
						$cad.=')';
						echo $result->nombre." - ".$result->provincia->nombre."<br/>";
						$postales=CodigoPostal::model()->findAllBySql("select * from codigo_postal where codigo in ".$cad);
						/*foreach($postales as $postal){
							if($result->provincia->nombre)
						}*/
						echo "<br/>";
					}
				}
			}
			
	}
	
	
	
	
	public function actionPoblacionesSeur(){
		$soapclient = new SoapClient('https://ws.seur.com/WSEcatalogoPublicos/servlet/XFireServlet/WSServiciosWebPublicos?wsdl');
		
		/*
		$cont=0;
		for($i='a'; $i<='z'; $i++)
		{	 */
		  
		
		 	
			$params4 = array(
				'in0'=>'',
				'in1'=>'SANT CARLES DE PERALTA',
				'in2'=>'',
				'in3'=>'',
				'in4'=>'',
				'in5'=>'WSPERSONALING',
				'in6'=>'ORACLE',
				
				);
			$response = $soapclient->infoPoblacionesCortoStr($params4);
			$xml = simplexml_load_string($response->out);
			foreach ($xml as $reg){
				$pro= Provincia::model()->findByAttributes(array('nombre'=>utf8_decode($reg->NOM_PROVINCIA)));
				$test=Ciudad::model()->findByAttributes(array('nombre'=>utf8_decode($reg->NOM_POBLACION),'provincia_id'=>$pro->id));
				
				if(is_null($test))
				{
					
					$provincia= Provincia::model()->findByAttributes(array('nombre'=>utf8_decode($reg->NOM_PROVINCIA)));
					if(!is_null($provincia)){
						$ciudad= new Ciudad;
						$ciudad->nombre=utf8_decode($reg->NOM_POBLACION);
						$ciudad->provincia_id=$provincia->id;
						$ciudad->ruta_id=$provincia->pais_id;
						$ciudad->cod_zoom=0;
							  
						if($ciudad->save())
							echo "OK <br/>";
						else
							print_r ($ciudad->getErrors()); echo $reg->NOM_PROVINCIA."--".$reg->NOM_POBLACION." BAD <br/>";
					}
					else{
						echo "WORSE PROVINCIA ".$reg->NOM_PROVINCIA."--".$reg->NOM_POBLACION."<br/>";
					   
					}
				}else
					echo "YA ESTABA CIUDAD ".$reg->NOM_POBLACION."--".$reg->NOM_PROVINCIA."- ID: ".$test->id."-- PID:".$test->provincia_id."<br/>";	
				
			}
			/*$cont++;
			if($cont==26)
				break;
		}*/
		
	}
	
	
	
	
	
	public function actionCodigosPostalesSeur(){
		
		$soapclient = new SoapClient('https://ws.seur.com/WSEcatalogoPublicos/servlet/XFireServlet/WSServiciosWebPublicos?wsdl');
		//$all=Ciudad::model()->findAll(array('condition'=>'nombre="SANTA ANA"' ));
		$ciudades=Ciudad::model()->findAllByAttributes(array('provincia_id'=>39));	
		
		
		foreach($ciudades as $ciudad)
		{
			$city=$ciudad->nombre;
			print_r('<b>'.$city.'</b><br/>');
			$params4 = array(
			'in0'=>'',
			'in1'=>$city, 
			'in2'=>'',
			'in3'=>'', 
			'in4'=>'',
			'in5'=>'WSPERSONALING',
			'in6'=>'ORACLE',
			
			);
			$response = $soapclient->infoPoblacionesCortoStr($params4);
			$xml = simplexml_load_string($response->out);
			foreach ($xml as $reg){
				/*$plocal=Provincia::model()->findByAttributes(array('nombre'=>$reg->NOM_PROVINCIA));
				$clocal=Ciudad::model()->findByAttributes(array('nombre'=>$reg->NOM_POBLACION, 'provincia_id'=>$plocal->id));		
				$cplocal=CodigoPostal::model()->findByAttributes(array('codigo'=>$reg->CODIGO_POSTAL, 'ciudad_id'=>$clocal->id));
				if(is_null($cplocal)&&!is_null($clocal)){*/
					$codigo=new CodigoPostal;
					$codigo->codigo=$reg->CODIGO_POSTAL;
					$codigo->ciudad_id=$ciudad->id;
					if($codigo->save())
						echo "OK<br/>";
					else	
						echo "BAD<br/>";
			/*	}
				else {
					echo "YA ESTABA<br/>";
				}*/
			}
			echo "<br/><br/>";
			}
			
			
	}
	
	
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Direccion::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='direccion-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	} 
     
    public function actionMunicipiosZoom(){
        
        $cliente = new ZoomService;
        $ciudades=Ciudad::model()->findAllByAttributes(array(),array('condition'=>'cod_zoom IS NOT NULL'));
        foreach($ciudades as $key=>$ciudad){
            $array=array('codciudad'=>$ciudad->cod_zoom,'remitente'=>NULL);
            $municipios=$cliente->call('getMunicipios',$array);
            echo $ciudad->nombre.": ".count($municipios)." Municipios<br/><br/>";
            foreach($municipios as $municipio){
               echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; 
                
               $parroquias=$cliente->call('getParroquias',array('codciudad'=>$ciudad->cod_zoom,'codmunicipio'=>$municipio->codigo_municipio,'remitente'=>NULL));
               echo $municipio->nombre_municipio." - ".count($parroquias)." Parroquias <br/><br/>";
               foreach($parroquias as $parroquia){
                  echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                  echo $parroquia->nombre_parroquia." [".$parroquia->codigo_parroquia."] ".$parroquia->codigo_postal."<br/><br/>";  
               }
               
            }
            
            echo "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;OFICINAS:<br/>";
            $oficinas=$cliente->call('getOficinas',array('codigo_ciudad_destino'=>$ciudad->cod_zoom,'tipo_tarifa'=>1));
            foreach($oficinas as $oficina)
            {
                 echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";     
                print_r($oficina);
                echo "<br/>";
            }     
            echo "<br/><br/><br/>";
            if($key>6)
                break;
        }
        
        
    }
}
