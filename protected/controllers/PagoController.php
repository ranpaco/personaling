<?php

class PagoController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/column2';


    var $_lastDate;
	var $_first;
	var $_last;
	var $filter=FALSE;
	
    public $_totallooksviews;
    
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
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('solicitar','index'),
				'expression'=>"UserModule::isPersonalShopper()",
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','view', 'detalle',
                                    'comisionAfiliacion','comisionClick','index','cambiarComisionClic'), 
				'expression'=>"UserModule::isAdmin()",
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
	 * Crear una nueva solicitud
	 */
	public function actionSolicitar()
	{
		$model = new Pago;
                $user = User::model()->findByPk(Yii::app()->user->id);
                $balance = $user->getSaldoPorComisiones();
                $forApproval = $user->getSaldoEnEspera();
                if($balance <= 0){
                    Yii::app()->user->setFlash("error", "No tienes suficiente balance
                        en comisiones para poder hacer una solicitud de cobro.");
                }
                
                
		if(isset($_POST['Pago']))
		{           
                    $model->attributes = $_POST['Pago'];
                    
                    $model->user_id = Yii::app()->user->id;
                    $model->fecha_solicitud = date("Y-m-d H:i:s");

                    //si metodo de pago es paypal
                    if($model->tipo == 0 && Yii::app()->params['pagoPS']['paypal']){                            
                        //poner el nombre del banco "PAYPAL" para no dejarlo vacío
                        $model->entidad = "PayPal";                            
                    }
                    //si el tipo de pago es Agregar al Balance
                    if($model->tipo == 2 && Yii::app()->params['pagoPS']['banco']){                            
                        //poner el nombre del banco "Personaling" para no dejarlo vacío
                        $model->entidad = "Personaling";                            
                        //poner como cuenta "Balance" ya que no se indicó en el formulario
                        $model->cuenta = "Balance"; 
                                                  
                    }
                    if(Yii::app()->language=='es_ve'&&$model->tipo == 1){
                            $model->recipient=$_POST['Pago']['recipient']; 
                            $model->identification=$_POST['Pago']['identification']; 
                            $model->accountType=Pago::model()->getTipoCuenta($_POST['Pago']['accountType']);
                    } 
                    
                    if($model->save()){
                        
                        //Bloquear saldo
                        $saldo = new Balance();
                        $saldo->total = - $model->monto;
                        $saldo->orden_id = $model->id;
                        $saldo->user_id = $model->user_id;
                        //ningun admin, el mismo usuario para no romper la integridad
                        $saldo->admin_id = $model->user_id; 
                        $saldo->tipo = 7; //por retiro de dinero PS                        
                        $saldo->fecha = date("Y-m-d H:i:s");
                        $saldo->save();
                        
                        Yii::app()->user->setFlash("success", "Se ha realizado tu solicitud con éxito,
                            en breve Personaling te dará respuesta.");
                        
                        /*Enviar correo OPERACIONES (operaciones@personaling.com
                         si no esta en develop                          
                         */  
                        if(Funciones::isDev()){

                            $this->enviarEmailOperaciones($model);  

                        }
                        
                        //Notificar por correo al Personal Shopper
                        $this->enviarNotificacionPersonalShopper($model);
                        
                        
                        $this->redirect(array('index'));

                    }else{
                        Yii::trace('Solicitando pago, Error:'.print_r($model->getErrors(), true), 'Pagos');                        
                        
                        if($model->tipo == 0){                            
                            $model->entidad = "";                                                    
                        }
                       
                    } 
                            
		}
                
		$this->render('solicitar',array(
			'model'=>$model,
			'balance'=>$balance,
			'forApproval'=>$forApproval,
			'user'=>$user
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionDetalle($id)
	{
            /* @var $model Pago */
            $model=$this->loadModel($id);
            $user = $model->user;

            // Uncomment the following line if AJAX validation is needed
            // $this->performAjaxValidation($model);

            if(isset($_POST['aceptar']))
            {
                //Si ingresaron algun id de transaccion o si es un pago para agregar
                //al balance, no necesitaria idTransaccion
                if((isset($_POST['idTransaccion']) && $_POST['idTransaccion'] != "") || $model->tipo == 2){

                    //Marcar el pago como aceptado, necesita un idTransaccion
                    //para identificar la operacion bancaria o tranferencia de paypal
                    $model->fecha_respuesta = date("Y-m-d H:i:s");
                    $model->id_transaccion = $model->tipo == 2 ? $model->id
                            : $_POST['idTransaccion'] ;
                    $model->admin_id = Yii::app()->user->id;
                    $model->estado = 1;

                    if($model->save()){
                                                
                        //Si el tipo de pago fue "agregar al balance", el idTransaccion
                        //será el mismo ID y se le carga saldo al usuario
                        if($model->tipo == 2){
                            
                            //Pasar para el saldo
                            $saldo = new Balance();
                            $saldo->total = $model->monto;
                            $saldo->orden_id = $model->id;
                            $saldo->user_id = $model->user_id;
                            $saldo->admin_id = Yii::app()->user->id;
                            $saldo->tipo = 9; //por pago al cobrar agregando al balance
                            $saldo->fecha = date("Y-m-d H:i:s");
                            if($saldo->save()){
                                //enviar email a PS                                
                                $this->enviarRespuestaPersonalShopper($model, 1);                        
                                Yii::app()->user->setFlash("success", "Se ha registrado el pago exitosamente.");                           
                                
                            }
                            else
                            {
                                Yii::trace('Aceptando pago, Error:'.print_r($saldo->getErrors(), true), 'Pagos');
                                Yii::app()->user->setFlash("error", "No se pudo registrar el pago.");                           

                                $model=$this->loadModel($id);                             
                            }
                        }
                        
                        //enviar email a PS
                        $this->enviarRespuestaPersonalShopper($model, 1);                        
                        Yii::app()->user->setFlash("success", "Se ha registrado el pago exitosamente.");

                    }else{
                        
                        $errores = "";
                        if($model->hasErrors("id_transaccion")){
                            
                            $errores = " Corrige los siguientes errores<br><ul>";
                            
                            $erroresArray = $model->getErrors("id_transaccion");
                            
                            foreach($erroresArray as $error){
                                $errores .= "<li> $error </li>";
                            }                            
                            
                            $errores .= "</ul>";
                        }

                        Yii::trace('Aceptando pago, Error:'.print_r($model->getErrors(), true), 'Pagos');
                        Yii::app()->user->setFlash("error", "No se pudo registrar el pago." . $errores);                           
                        
                        $model=$this->loadModel($id);
                    }                     

                }else{
                    Yii::app()->user->setFlash("error", "Debes ingresar un código de transacción.");
                }

                
            }else if(isset($_POST['rechazar'])){                    

                if($_POST['observacion'] != ""){
                   $model->observacion = $_POST['observacion'];
                }
                
                $model->fecha_respuesta = date("Y-m-d H:i:s");
                $model->admin_id = Yii::app()->user->id;
                $model->estado = 2; //Rechazado
                if($model->save()){

                    //Reintegrar saldo
                    $saldo = new Balance();
                    $saldo->total = $model->monto;
                    $saldo->orden_id = $model->id;
                    $saldo->user_id = $model->user_id;
                    $saldo->admin_id = Yii::app()->user->id;
                    $saldo->tipo = 8; //por reintegro de dinero PS       
                    $saldo->fecha = date("Y-m-d H:i:s");
                    $saldo->save();
                    
                    //enviar email a la PS
                    $this->enviarRespuestaPersonalShopper($model, 2);
                    Yii::app()->user->setFlash("success", "Se ha rechazado el pago exitosamente.");                           

                }else{
                    Yii::trace('Rechazando pago, Error:'.print_r($model->getErrors(), true), 'Pagos');
                    Yii::app()->user->setFlash("error", "No se pudo rechazar el pago.");  
                    $model=$this->loadModel($id);
//                    echo "<pre>";
//                    print_r($model->getErrors());
//                    echo "</pre><br>";
//                    Yii::app()->end();
                
                }  

            }            

            $this->render('detalle',array(
                    'model'=>$model,
                    'usuario'=>$user,
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
	 * Para el listado que ve el PS
	 */
	public function actionIndex()
	{
            $pago = new Pago();
            //Buscar mis solicitudes y pagos
            $pago->unsetAttributes();
            $pago->user_id = Yii::app()->user->id;
            $dataProvider = $pago->search();                
            
            $this->render('index',array(
                'dataProvider'=>$dataProvider,
                'user_id' => Yii::app()->user->id,
            ));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Pago('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Pago']))
			$model->attributes=$_GET['Pago'];
                
                $dataProvider = $model->search();

		$this->render('admin',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Pago the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Pago::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Pago $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='pago-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
        
        /*Enviar el correo para notificar a Operaciones*/
        function enviarEmailOperaciones($pago) {
            
            $message = new YiiMailMessage;
            //this points to the file test.php inside the view path
            $message->view = "mail_template";
            
            $subject = 'Solicitud de pago';
            $body = Yii::t('contentForm',
                    'Se ha generado una solicitud de pago por parte de un Personal
                     Shopper.
                     <br>
                     <b>Nombre:</b> '.$pago->user->profile->getNombre().'<br/>
                     <b>Email:</b> '.$pago->user->email.'<br/>
                     <br>
                     <br>
                     <a title="Ver solicitudes" 
                     href="http://www.personaling.es'.Yii::app()->baseUrl.
                    '/pago/admin" 
                        style="text-align:center;text-decoration:none;color:#ffffff;
                        word-wrap:break-word;background: #231f20; padding: 12px;" 
                        target="_blank">Ver solicitudes</a><br><br/><br/>'
                     ."Los datos de la solicitud generada son:<br/>
                     <b>Nro. de Solicitud:</b> {$pago->id}<br/>
                     <b>Fecha</b>: ".date("d/m/Y h:i:s a", $pago->getFechaSolicitud())."<br/>
                     <br/>
                         
                     <br/>");
                     
                     
            $destinatario = "operaciones@personaling.com";
            //si esta en test, enviarlo a cristal
            if(Funciones::isTest()){
                
                $destinatario = "cmontanez@upsidecorp.ch";               
            }     
                     
            $params = array('subject'=>$subject, 'body'=>$body);
            $message->subject = $subject;
            $message->setBody($params, 'text/html');
            $message->addTo($destinatario);
            $message->from = array('operaciones@personaling.com' => 'Tu Personal Shopper Online');            
            Yii::app()->mail->send($message);
        }
        
        /** 
         * Enviar el correo para notificar al PS sobre su pago
         * 
         * @param int $accion si fue aprobado o rechazado <br>
         * 1: aprobado
         * 2: rechazado
         */
        function enviarRespuestaPersonalShopper($pago, $accion) {
            
            $message = new YiiMailMessage;
            //Opciones de Mandrill
            $message->activarPlantillaMandrill();            
            $subject = 'Solicitud de pago';
            
            //Aprobado
            if($accion == 1){
                
                //Si el pago fue agregar al balance (2) u otro
                $tipoPago = $pago->tipo == 2 ? "Se ha hecho el pago cargándose 
                    a tu balance Personaling"
                        :"Se ha hecho el pago a tu cuenta. (Paypal o Banco) por";
                $body = Yii::t('contentForm',
                    'Tu solicitud de pago <b>Nro.'.$pago->id.'</b> ha sido aprobada.
                     '.$tipoPago.' un monto
                     de <b>'.$pago->getMonto().'</b>
                     <br>                    
                     <br>
                     <br>
                     <a title="Mis pagos" 
                     href="http://www.personaling.es'.Yii::app()->baseUrl.
                    '/pago/index" 
                        style="text-align:center;text-decoration:none;color:#ffffff;
                        word-wrap:break-word;background: #231f20; padding: 12px;" 
                        target="_blank">
                        
                        Mis pagos
                        
                      </a><br><br/><br/>'
                     ."<br/>");
            }else{
                
                $body = Yii::t('contentForm',
                    'Tu solicitud de pago <b>Nro.'.$pago->id.'</b> ha sido rechazada.
                     <br>                    
                     <br>
                     <br>
                     <a title="Mis pagos" 
                     href="http://www.personaling.es'.Yii::app()->baseUrl.
                    '/pago/index" 
                        style="text-align:center;text-decoration:none;color:#ffffff;
                        word-wrap:break-word;background: #231f20; padding: 12px;" 
                        target="_blank">
                        
                        Mis pagos
                        
                      </a><br><br/><br/>'
                     ."<br/>");
            }        
                     
            $destinatario = $pago->user->email;
            
            if(Funciones::isDev()){
                
                $destinatario = "lcasanova@upsidecorp.ch";               
            }     
                     
//            $params = array('subject'=>$subject, 'body'=>$body);
            $message->subject = $subject;
            $message->setBody($body, 'text/html');
            $message->addTo($destinatario);
//            $message->from = array('operaciones@personaling.com' => 'Tu Personal Shopper Online');            
            Yii::app()->mail->send($message);
        }
        
        function enviarNotificacionPersonalShopper($pago) {
            
            $message = new YiiMailMessage;
            //Opciones de Mandrill
            $message->activarPlantillaMandrill();
            
//            $message->view = "mail_template";
            
            $subject = 'Solicitud de pago';            
            
            $body = Yii::t('contentForm',
                'Se ha generado tu solicitud de pago <b>Nro.'.$pago->id.'</b> por un 
                 monto de <b>'.$pago->getMonto().'</b>.
                 En breve Personaling te dará respuesta.
                 <br>                    
                 <br>
                 <br>
                 <a title="Mis pagos" 
                 href="http://www.personaling.es'.Yii::app()->baseUrl.
                '/pago/index" 
                    style="text-align:center;text-decoration:none;color:#ffffff;
                    word-wrap:break-word;background: #231f20; padding: 12px;" 
                    target="_blank">

                    Mis pagos

                  </a><br><br/><br/>'
                 ."<br/>");
                     
            $destinatario = $pago->user->email;
            
            /*if(Funciones::isDev()){
                
                $destinatario = "nramirez@upsidecorp.ch";               
            }*/     
                     
//            $params = array('subject'=>$subject, 'body'=>$body);
            $message->subject = $subject;
            $message->setBody($body, 'text/html');
            $message->addTo($destinatario);
//            $message->from = array('operaciones@personaling.com' => 'Tu Personal Shopper Online');            
            Yii::app()->mail->send($message);
        }

        /**
         * Enviar notificacion de pago por comision de afiliados a PS
         */
        
        function enviarNotificacionPagoAfiliacionPS($user) {
            
            $message = new YiiMailMessage;
            //Opciones de Mandrill
            $message->activarPlantillaMandrill();            
            $subject = 'Pago por comisiones';
            
            $body = Yii::t('contentForm',
                '¡Enhorabuena FashionLover, has recibido un pago por comisión!
                 <br>                    
                 <br>
                 Entra en tu cuenta haciendo click 
                 <a title="Aquí" 
                 href="http://www.personaling.es'.Yii::app()->baseUrl.
                'inicio-personaling" target="_blank">
                    Aquí
                  </a><br>');                    
                     
            $destinatario = $user->email;                 

            $message->subject = $subject;
            $message->setBody($body, 'text/html');
            $message->addTo($destinatario);
            Yii::app()->mail->send($message);
        }
        
        /**
         * This action is used for paying the PersonalShoppers with the monthly
         * earnings, distributing the money based on their generated visits.
         */
        public function actionComisionAfiliacion() {
            
            // Get the last date of payment for computing the next period
            $lastPayment = AffiliatePayment::findLastPayment(1);            
            $this->_lastDate = $lastDate = $lastPayment ? $lastPayment->created_at : null;                
            // if doesn't exist any payment or if there is at least one of them
            $totalViews = $lastDate ? ShoppingMetric::getAllViewsPsByDate($lastDate, date("Y-m-d H:i:s")) :
                ShoppingMetric::getAllViewsPs();                   

            $anterior = $this->_lastDate;

            //Asign the totalViews attribute for optimize new queries
            $this->_totallooksviews = $totalViews;
            
            /*Si viene el campo con el monto a pagar*/
            if(isset($_POST["monthlyEarning"]) && $_POST["monthlyEarning"] > 0){                
               
                //Save the payment in the BD
                $paymentPs = new AffiliatePayment();
                $paymentPs->user_id = Yii::app()->user->id; //Admin who make the payment
                $paymentPs->created_at = date("Y-m-d H:i:s"); //Datetime which the payment was made
                $paymentPs->amount = $_POST["monthlyEarning"]; //Amount of the payment
                $paymentPs->total_views = $totalViews; //Amount of the payment
                $paymentPs->tipo = 1; // Por afiliacion

                if($paymentPs->save()){
                    
                    /*Recalculate the variables because the new payment*/
                    //Asign the payment so it can be shown on the page
                    $lastPayment = $paymentPs;
                    
                    $this->_lastDate = $lastDate = $lastPayment ? $lastPayment->created_at : null;                
                    // if doesn't exist any payment or if there is at least one of them
                    $totalViews = $lastDate ? ShoppingMetric::getAllViewsPsByDate($lastDate, date("Y-m-d H:i:s")) :
                        ShoppingMetric::getAllViewsPs();                   

                    //Asign the totalViews attribute for optimize new queries
                    $this->_totallooksviews = $totalViews;                    
                    
                    //find all Personal Shoppers so they can be paid
                    $allPs = User::model()->findAllByAttributes(array("personal_shopper" => 1));                
                    $primera = true;
                    foreach ($allPs as $userPs){

                        // if the percentage is computed from the beginning or from 
                      /*  // one specific date
                        $percent = $lastDate ? $userPs->getLookViewsPercentageByDate( 
                            $totalViews, date("Y-m-d"), false)
                            : $userPs->getLookViewsPercentage($totalViews, false);
*/
                        if($anterior==0 && $anterior!=$this->_lastDate){
                            $percent = $userPs->getLookViewsPercentage(ShoppingMetric::getAllViewsPs(), false); 
                            $total = $userPs->getLookReferredViews();
                        }
                        else{
                            $percent = $userPs->getLookViewsPercentageByDate($totalViews, date("Y-m-d H:i:s"), false);
                            $total = $userPs->getLookReferredViewsByDate($lastDate, date("Y-m-d H:i:s"));
                        }

                        $amountToPay = $_POST["monthlyEarning"] * $percent;

                        if($amountToPay == 0){
                            continue;
                        }
                        
                        //Register maonthly payment to PS
                        $payToPs = new PayPersonalShopper();
                        $payToPs->user_id = $userPs->id;
                        $payToPs->affiliatePay_id = $paymentPs->id;
                        //Total monthly views gathered by PS
                        $payToPs->total_views = $total;
                        $payToPs->percent = $percent;
                        $payToPs->amount = $amountToPay;
                        
                        if($payToPs->save()){
                            
                            //Pasar para el saldo
                            $saldo = new Balance();
                            $saldo->total = $amountToPay;
                            $saldo->orden_id = $paymentPs->id; //associated Payment
                            $saldo->user_id = $userPs->id; //PS to be paid
                            $saldo->admin_id = $paymentPs->user_id; //ADmin who made the payment
                            $saldo->tipo = 10; //Type of payment specified in Balance model
                            $saldo->fecha = date("Y-m-d H:i:s");
                            if($saldo->save()){
                                
                                //enviar email a PS if not in Test or dev. 
                                if(!Funciones::isDevTest()){
                
                                    $this->enviarNotificacionPagoAfiliacionPS($userPs);     
                                    
                                }
                                
                                Yii::app()->user->setFlash("success", "Se ha hecho el pago satisfactoriamente");

                            }
                            else
                            {
                                Yii::trace('Registrando el pago de la PS, Error:'.print_r($saldo->getErrors(), true), 'Pagos');
                                Yii::app()->user->setFlash("error", "No se pudo registrar el pago.");                                                      
                            }
                            
                        }else
                        {
                            Yii::trace('Registrando el pago de la PS, Error:'.print_r($saldo->getErrors(), true), 'Pagos');
                            Yii::app()->user->setFlash("error", "No se pudo registrar el pago.");                                                      
                        }


                    } //End for
                
                } //if saved affiliate Payment
                
            } //if $_POST
            
            if(isset($_GET['first']) && $_GET['first']!=""){
				// si trae algo del filtrado de fechas
				$this->filter = TRUE;
				$this->_first = $_GET['first'];
				$this->_last = $_GET['second'];
            }	

	            /*Enviar a la vista el listado de todos los PS*/
	            /*
                $criteria = new CDbCriteria;
	            $criteria->compare("personal_shopper", 1);
                //$criteria->order = "lookreferredviews DESC";
	            
	            $dataProvider = new CActiveDataProvider('User', array(
	                'criteria' => $criteria,
	                'sort' => array(
                       // 'attributes'=> array('lookreferredviews'),
					    'defaultOrder' => array(
							'lookreferredviews' => "DESC", 
						),
					),
	                'pagination' => array(
	                    'pageSize' => Yii::app()->getModule('user')->user_page_size,
	                ),
	            ));*/
            $rawData=User::model()->findAllByAttributes(array('personal_shopper'=>1));
            $dataProvider = new CArrayDataProvider($rawData, array(
               // 'criteria' => $criteria,
                'sort' => array(
                    //'attributes'=> array('lookreferredviews'),
                    'attributes'=> array('lookreferredviewslast'),
                    'defaultOrder' => array(
                        'lookreferredviewslast' => "DESC",
                    ),
                ),
                'pagination' => array(
                    'pageSize' => Yii::app()->getModule('user')->user_page_size,
                ),
            ));

			            
            $this->render("comision_afiliacion", array(
                "dataProvider" => $dataProvider,
                "lastPayment" => $lastPayment,
            ));
            
        }

	    public function actionCambiarComisionClic(){

            if(isset($_POST["ps"])){
                $user = User::model()->findByPk($_POST["ps"]);

                $error=FALSE;

                $perfil = $user->profile;
                $perfil->profile_type = 5;
                $perfil->pago_click = $_POST["totalClick"];
                   
                if(!$perfil->save()){
                    $error = true;
                }
                
                if($error){                        
                    $response["status"] = "error";
                    $response["message"] = "¡Hubo un error cambiando las comisiones!";
                }else{                        
                    $response["status"] = "success";
                    $response["message"] = "¡Se ha actualizado la comisión del Personal Shopper!";                        
                }

            }
 
            echo CJSON::encode($response); 
            Yii::app()->end();
        }


		 /**
         * This action is used for paying the PersonalShoppers with the monthly
         * earnings, distributed by monthly clicks and their value.
         */
        public function actionComisionClick() {
            
            // Get the last date of payment for computing the next period
            $lastPayment = AffiliatePayment::findLastPayment(2);            
            $this->_lastDate = $lastDate = $lastPayment ? $lastPayment->created_at : null;                
            // if doesn't exist any payment or if there is at least one of them
            $totalViews = $lastDate ? ShoppingMetric::getAllViewsPsByDate($lastDate, date("Y-m-d")) :
                ShoppingMetric::getAllViewsPs();                   
			
			$anterior = $this->_lastDate;
			
            //Asign the totalViews attribute for optimize new queries
            $this->_totallooksviews = $totalViews;
            
            /*Si viene el campo con el monto a pagar*/
            if(isset($_POST["pagar"]) && $_POST["pagar"]=="si"){                
               	$total = 0;
				
                //Save the payment in the BD
                $paymentPs = new AffiliatePayment();
                $paymentPs->user_id = Yii::app()->user->id; //Admin who make the payment
                $paymentPs->created_at = date("Y-m-d H:i:s"); //Datetime which the payment was made
                $paymentPs->amount = 0; //Amount of the payment
                $paymentPs->total_views = $totalViews; //Amount of the payment
                $paymentPs->tipo = 2; // Por clic

                if($paymentPs->save()){
                    
                    /*Recalculate the variables because the new payment*/
                    //Asign the payment so it can be shown on the page
                    $lastPayment = $paymentPs;
                    
                    $this->_lastDate = $lastDate = $lastPayment ? $lastPayment->created_at : null;                
                    // if doesn't exist any payment or if there is at least one of them
                    $totalViews = $lastDate ? ShoppingMetric::getAllViewsPsByDate($lastDate, date("Y-m-d")) :
                        ShoppingMetric::getAllViewsPs();                   

                    //Asign the totalViews attribute for optimize new queries
                    $this->_totallooksviews = $totalViews;                    
                    
                    //find all Personal Shoppers so they can be paid
                    $allPs = User::model()->findAllByAttributes(array("personal_shopper" => 1));                
                    $primera = true;
                    foreach ($allPs as $userPs){
						
						if($anterior==0 && $anterior!=$this->_lastDate)
							$amountToPay = $userPs->getPagoClick() * $userPs->getLookReferredViews();
						else
							$amountToPay = $userPs->getPagoClick() * ($userPs->getLookReferredViewsByDate($this->_lastDate, date("Y-m-d")));
						
						/*echo $amountToPay;
						Yii::app()->end();*/
						
                        if($amountToPay == 0){
                            continue;
                        }
                        
                        //Register monthly payment to PS
                        $payToPs = new PayPersonalShopper();
                        $payToPs->user_id = $userPs->id;
                        $payToPs->affiliatePay_id = $paymentPs->id;
                        //Total monthly views gathered by PS
                        if($anterior==0 && $anterior!=$this->_lastDate)
                        	$payToPs->total_views = $userPs->getLookReferredViews(); 
						else
							$payToPs->total_views = $lastDate ? $userPs->getLookReferredViewsByDate($lastDate, date("Y-m-d")) : $userPs->getLookReferredViews();
						
                        $payToPs->amount = $amountToPay;
                        $total += $amountToPay;
					
                        if($payToPs->save()){
                            
                            //Pasar para el saldo 
                            $saldo = new Balance();
                            $saldo->total = $amountToPay;
                            $saldo->orden_id = $paymentPs->id; //associated Payment
                            $saldo->user_id = $userPs->id; //PS to be paid
                            $saldo->admin_id = $paymentPs->user_id; //ADmin who made the payment
                            $saldo->tipo = 11; //Type of payment specified in Balance model
                            $saldo->fecha = date("Y-m-d H:i:s");
                            if($saldo->save()){
                                
								$paymentPs->saveAttributes(array('amount'=>$total)); // total payed by clicks 
								
                                //enviar email a PS if not in Test or dev. 
                                if(!Funciones::isDevTest()){                
                                    $this->enviarNotificacionPagoAfiliacionPS($userPs);
                                }
                                
                                Yii::app()->user->setFlash("success", "Se ha hecho el pago satisfactoriamente");

                            }
                            else
                            {
                                Yii::trace('Registrando el pago de la PS, Error:'.print_r($saldo->getErrors(), true), 'Pagos');
                                Yii::app()->user->setFlash("error", "No se pudo registrar el pago.");                                                      
                            }
                            
                        }else
                        {
                            Yii::trace('Registrando el pago de la PS, Error:'.print_r($payToPs->getErrors(), true), 'Pagos');
                            Yii::app()->user->setFlash("error", "No se pudo registrar el pago.");                                                      
                        }


                    } //End foreach
                
                } //if saved affiliate Payment
                
            } //if $_POST 

	            /*Enviar a la vista el listado de todos los PS*/
	            /*$criteria = new CDbCriteria;
	            $criteria->compare("personal_shopper", 1);
	            
	            $dataProvider = new CActiveDataProvider('User', array(
	                'criteria' => $criteria,
	                'pagination' => array(
	                    'pageSize' => Yii::app()->getModule('user')->user_page_size,
	                ),
	            ));*/
				
				
				 $rawData=User::model()->findAllByAttributes(array('personal_shopper'=>1));
            $dataProvider = new CArrayDataProvider($rawData, array(
               // 'criteria' => $criteria,
                'sort' => array(
                    'attributes'=> array('lookreferredviewslastClicks'),
                    'defaultOrder' => array(
                        'lookreferredviewslastClicks' => "DESC",
                    ),
                ),
                'pagination' => array(
                    'pageSize' => Yii::app()->getModule('user')->user_page_size,
                ),
            ));
			            
            $this->render("comision_click", array(
                "dataProvider" => $dataProvider,
                "lastPayment" => $lastPayment,
            ));
            
        }
		

}
