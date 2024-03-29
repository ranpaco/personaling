<?php

class BolsaController extends Controller
{
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			
			'accessControl', // perform access control for CRUD operations
			'https +compra, direcciones, confirmar', // Force https, but only on login page
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
                    'actions'=>array('vaciarGuest', 'koAzt', 'okAzt', 'notificacionAzt',),
                    'users'=>array('*'),
                ),                
                array('allow', // allow authenticated user to perform 'create' and 'update' actions
                    'actions'=>array('modal','credito','index','limpiar',
                    'eliminardireccion','editar','editardireccion','agregar','actualizar',
                    'pagos','eliminar','compra', 'direcciones','confirmar','comprar',
                    'pedido','cpago', 'pedidoGC','comprarGC','clickBotonConfirmar', 
                    'cambiarTipoPago','error','successMP', 'authGC', 'pagoGC', 'confirmarGC', 
                    'agregar2','eliminarLook',"errorGC"),
                    'users'=>array('@'),
                ),                
                array('deny',  // deny all users
                        'users'=>array('*'),
                ),
            );
	}
	
	public function actionIndex()
	{ 
	    if(!Yii::app()->user->isGuest){

                /*Si es compra de admin para usuario*/
                if(isset($_GET["user"]) && UserModule::isAdmin()){
                    Yii::app()->getSession()->add("bolsaUser", $_GET["user"]);
                }else{
                                        
                    Yii::app()->getSession()->remove("bolsaUser");                    
                    
                }

                $admin = Yii::app()->getSession()->contains("bolsaUser");                    
                
                /*ID del usuario propietario de la bolsa*/
                $usuario = $admin ? Yii::app()->getSession()->get("bolsaUser")
                                    : Yii::app()->user->id;

                $bolsa = Bolsa::model()->findByAttributes(array(
                            'user_id' => $usuario,
                            /* Si es la bolsa del admin para el usuario
                             * o la bolsa normal
                             */
                            'admin' => $admin, 
                            ));

                if (!is_null($bolsa)){
                    
                    $bolsa->actualizar();
                    if($bolsa->deleteInactivos()){
                        Yii::app()->user->updateSession();
                        Yii::app()->user->setFlash('info',
                                UserModule::t("Tu bolsa se ha actualizado porque algunos productos no se encuentran disponibles."));
                    }

                } else {
                    $bolsa = new Bolsa;
                    $bolsa->user_id = $usuario;
                    $bolsa->admin = $admin;
                    $bolsa->save();
                }

                if(!$admin){

                    ShoppingMetric::registro(ShoppingMetric::STEP_BOLSA,array("bolsa_id"=>$bolsa->id)); 

                }

                $this->render('bolsa', array('bolsa' => $bolsa)); 
            }
            else{
                Yii::app()->user->setFlash('error',Yii::t("contentForm", "¡La sesión ha expirado, intenta tu compra nuevamente!"));                              
                $this->redirect(array('/user/login'));
            }
	
	}

	// nueva funcion para agregar productos a la bolsa y devolver un json con los datos necesarios para analytics, mas el status que ya existía
	public function actionAgregar2(){
		//si no tiene una bolsa aun asociada se crea
			
			
		if(Yii::app()->user->isGuest==false) {
			
			$usuario = Yii::app()->user->id;
			$bolsa = Bolsa::model()->findByAttributes(array(
	                    'user_id'=>$usuario, 'admin' => 0));
			
			if(!isset($bolsa)) // si no tiene aun un carrito asociado se crea y se añade el producto
			{
				$bolsa = new Bolsa;
				$bolsa->user_id = $usuario;
				$bolsa->created_on = date("Y-m-d H:i:s");
				$bolsa->save();
			}
			if (isset($_POST['look_id'])){
				$todos = true;
				$productos_look = array();
				foreach($_POST['producto'] as $key => $value){ 
					list($producto_id,$color_id) = explode("_",$value);
					
					$response = $bolsa->addProducto($producto_id,$_POST['talla'.$value],$color_id,$_POST['look_id']);
					
					$producto = Producto::model()->findByPk($producto_id);
					$ptcolor = Preciotallacolor::model()->findByAttributes(array('producto_id'=>$producto_id,'talla_id'=>$_POST['talla'.$value],'color_id'=>$color_id));
					$category_product = CategoriaHasProducto::model()->findByAttributes(array('tbl_producto_id'=>$producto_id));
	                $category = Categoria::model()->findByPk($category_product->tbl_categoria_id);
	                $precio = Precio::model()->findByAttributes(array('tbl_producto_id'=>$producto_id));
					if($producto){
						$productos_look[] = array(
							'id' => $ptcolor->producto->id,
							'name' => $ptcolor->producto->nombre,
							'category' => $category->nombre,
							'brand' => $ptcolor->producto->mymarca->nombre,
							'variant' => $ptcolor->mycolor->valor." ".$ptcolor->mytalla->valor,
							'price' => $precio->precioImpuesto,
							'quantity' => 1
						);
					}
					if($response == 'fail'){
						$todos = false;
					}
				}
				if($todos){
					$look = Look::model()->findByPk($_POST['look_id']);
					echo json_encode(array(
						'status' => 'ok',
						'id' => $look->id,
						'name' => $look->title,
						'category' => 'Looks',
						'brand' => 'Personaling',
						'variant' => 'Look',
						'price' => $look->getPrecioDescuento(),
						'quantity' => 1,
						'productos' => $productos_look
					));
				}
			} else {
				if($_POST['productoIndividual']!="0") // si es 0 no trae look
				{
					$response = $bolsa->addProducto($_POST['producto'],$_POST['talla'],$_POST['color'], $_POST['productoIndividual']);
				}
				else
				{
					$response = $bolsa->addProducto($_POST['producto'],$_POST['talla'],$_POST['color']);
				}
				
				$ptcolor = Preciotallacolor::model()->findByAttributes(array('producto_id'=>$_POST['producto'],'talla_id'=>$_POST['talla'],'color_id'=>$_POST['color']));
				$category_product = CategoriaHasProducto::model()->findByAttributes(array('tbl_producto_id'=>$_POST['producto']));
                $category = Categoria::model()->findByPk($category_product->tbl_categoria_id);
                $precio = Precio::model()->findByAttributes(array('tbl_producto_id'=>$_POST['producto']));
				echo json_encode(array(
					'status' => $response,
					'id' => $ptcolor->producto->id,
					'name' => $ptcolor->producto->nombre,
					'category' => $category->nombre,
					'brand' => $ptcolor->producto->mymarca->nombre,
					'variant' => $ptcolor->mycolor->valor." ".$ptcolor->mytalla->valor,
					'price' => $precio->precioImpuesto,
					'quantity' => 1
				));
			}
			
		}else{
			echo json_encode(array('status'=>'no es usuario'));
		}

	}

	public function actionAgregar(){
		//si no tiene una bolsa aun asociada se crea
		
		
	if(Yii::app()->user->isGuest==false) 
	{
		
		$usuario = Yii::app()->user->id;
		$bolsa = Bolsa::model()->findByAttributes(array(
                    'user_id'=>$usuario, 'admin' => 0));
		
		if(!isset($bolsa)) // si no tiene aun un carrito asociado se crea y se añade el producto
		{
			$bolsa = new Bolsa;
			$bolsa->user_id = $usuario;
			$bolsa->created_on = date("Y-m-d H:i:s");
			$bolsa->save();
		}
		if (isset($_POST['look_id'])){
			foreach($_POST['producto'] as $key => $value){ 
				list($producto_id,$color_id) = explode("_",$value);
				echo $bolsa->addProducto($producto_id,$_POST['talla'.$value],$color_id,$_POST['look_id']);
			}
		} else 
		{
			if(isset($_POST['productoIndividual']))
			{
				if($_POST['productoIndividual']!="0") // si es 0 no trae look
				{
					
					echo $bolsa->addProducto($_POST['producto'],$_POST['talla'],$_POST['color'], $_POST['productoIndividual']);
				}
				else
				{
					echo $bolsa->addProducto($_POST['producto'],$_POST['talla'],$_POST['color']);
				}
			}
			else 
			{
				echo $bolsa->addProducto($_POST['producto'],$_POST['talla'],$_POST['color']);
			}		

		}
		 /*	 
		$usuario = Yii::app()->user->id;
		$bolsa = Bolsa::model()->findByAttributes(array('user_id'=>$usuario));
		
		if(!isset($bolsa)) // si no tiene aun un carrito asociado se crea y se añade el producto
		{
			$model = new Bolsa;
			$model->user_id = $usuario;
			$model->created_on = date("Y-m-d H:i:s");
			
			if($model->save()) // si guarda entonces se añade el nuevo producto
			{
				$carrito = Bolsa::model()->findByAttributes(array('user_id'=>$usuario));
				$ptcolor = PrecioTallaColor::model()->findByAttributes(array('producto_id'=>$_POST['producto'],'talla_id'=>$_POST['talla'],'color_id'=>$_POST['color']));
				
				$pn = new BolsaHasProductotallacolor;
				$pn->bolsa_id = $carrito->id;
				$pn->preciotallacolor_id = $ptcolor->id;
				$pn->cantidad = 1;
				if (isset($_POST['look']))
					$pn->look_id = $_POST['look'];
				if($pn->save())
				{// en bolsa tengo id de usuario e id de bolsa
					//$this->render('bolsa', array('preciotallacolor' => $ptcolor, 'bolsa'=>$carrito));
					echo "ok";
				}
			}
		}
		else // si ya tiene una bolsa
		{
			$carrito = Bolsa::model()->findByAttributes(array('user_id'=>$usuario));
			$ptcolor = PrecioTallaColor::model()->findByAttributes(array('producto_id'=>$_POST['producto'],'talla_id'=>$_POST['talla'],'color_id'=>$_POST['color']));
			
			//revisar si está o no en el carrito
			
			$nuevo = BolsaHasProductotallacolor::model()->findByAttributes(array('preciotallacolor_id'=>$ptcolor->id));
			
			if(isset($nuevo)) // existe
			{
				$cantidadnueva = $nuevo->cantidad + 1;
				BolsaHasProductotallacolor::model()->updateByPk($nuevo->preciotallacolor_id, array('cantidad'=>$cantidadnueva));
				echo "ok";
							
			}
			else{ // si el producto es nuevo en la bolsa
			
				$pn = new BolsaHasProductotallacolor;
				$pn->bolsa_id = $carrito->id;
				$pn->preciotallacolor_id = $ptcolor->id;
				$pn->cantidad = 1;
				if (isset($_POST['look']))
					$pn->look_id = $_POST['look'];	
				if($pn->save())
				{// en bolsa tengo id de usuario e id de bolsa
				
					echo "ok";
				
				//	$this->render('bolsa', array('preciotallacolor' => $ptcolor, 'bolsa'=>$carrito));
				}
					
			}
				
			
				
		}//else bolsa	
		 * */	
	}// isset usuario
		
	else
	{
	echo "no es usuario";	
	}

}


/*
 * action para actualizar las cantidades del producto en el carrito
 * 
 * */
	public function actionActualizar(){
			
			
		
		if (isset($_POST['cantidad'])){
			$bolsa_id = $_POST['bolsa_id'];
			$preciotallacolor_id = $_POST['prtc'];
			$look_id = $_POST['look_id'];
			if($_POST['cantidad']==0)
			{
				//$bolsa = BolsaHasProductotallacolor::model()->findByAttributes(array('preciotallacolor_id'=>$_POST['prtc']));
				$bolsa = BolsaHasProductotallacolor::model()->findByAttributes(array('bolsa_id'=>$bolsa_id,'preciotallacolor_id'=>$preciotallacolor_id,'look_id'=>$look_id));
				$bolsa->delete();
				
				echo "ok";
				
			} else if($_POST['cantidad']>0){
			
				//$bolsa = BolsaHasProductotallacolor::model()->findByAttributes(array('preciotallacolor_id'=>$_POST['prtc']));
				$bolsa = BolsaHasProductotallacolor::model()->findByAttributes(array('bolsa_id'=>$bolsa_id,'preciotallacolor_id'=>$preciotallacolor_id,'look_id'=>$look_id));
				$pr = Preciotallacolor::model()->findByPk($preciotallacolor_id);
				
				$mientras = $pr->cantidad;
				if(($mientras - $_POST['cantidad']) < 0){
					echo "NO";
				}
				else
				{
					$bolsa->cantidad = $_POST['cantidad'];
					
					if($bolsa->save())
					{
						echo "ok";
					}
				}
	
			}// mayor que 0
		}
		if (isset($_POST['cant'])){
                    
                    $res = "ok";
                    
			foreach($_POST['cant'] as $preciotallacolor_id => $cant){
				foreach($cant as $look_id => $cantidad){
					//echo "bolsa_id: ".$_POST['bolsa_id']." preciotallacolor_id: ".$preciotallacolor_id." look_id: ".$look_id;	
					$bolsa = BolsaHasProductotallacolor::model()->findByAttributes(array('bolsa_id'=>$_POST['bolsa_id'],'preciotallacolor_id'=>$preciotallacolor_id,'look_id'=>$look_id));
					$pr = Preciotallacolor::model()->findByPk($preciotallacolor_id);
					$mientras = $pr->cantidad;
					if(($mientras - $cantidad) < 0){
						//echo "NO";
                                                $res = "NO";
					}
					else
					{
						$bolsa->cantidad = $cantidad;
						
						if($bolsa->save())
						{
							//echo "ok";
						}
					}
				}
			}
                        echo $res;
				
		}
	} // actualizar
	
	/*
	 * 
	 * action para eliminar desde la bolsa
	 * 
	 * */
    public function actionEliminar() {
        if (Yii::app()->request->isPostRequest) {
			$model= BolsaHasProductotallacolor::model()->findByAttributes(array('preciotallacolor_id'=>$_POST['prtc']));
			           
            if ($model) {
            	$look_id = $model->look_id;
            	$bolsa_id = $model->bolsa_id;
            	$response = array();
                $model->delete();

                // check si es el último producto de un look dentro de la bolsa
                if($look_id != 0){
                	$ultimo= BolsaHasProductotallacolor::model()->findByAttributes(array('bolsa_id'=>$bolsa_id, 'look_id'=>$look_id));
                	if(!$ultimo){ // se acaba de eliminar el último producto del look, agrego datos para analytics
                		$look = Look::model()->findByPk($look_id);
                		$response['ultimo'] = 'true';
                		$response['look'] = array(
							'id' => $look->id,
							'name' => $look->title,
							'category' => 'Looks',
							'brand' => 'Personaling',
							'price' => $look->getPrecioDescuento(),
							'quantity' => 1,
                		);
                	}else{
                		$response['ultimo'] = 'false';
                	}
                }
            	
				$category_product = CategoriaHasProducto::model()->findByAttributes(array('tbl_producto_id'=>$model->preciotallacolor->producto->id));
                $category = Categoria::model()->findByPk($category_product->tbl_categoria_id);
                $precio = Precio::model()->findByAttributes(array('tbl_producto_id'=>$model->preciotallacolor->producto->id));
                $response['status'] = 'ok';
                $response['id'] = $model->preciotallacolor->producto->id;
                $response['name'] = $model->preciotallacolor->producto->nombre;
                $response['category'] = $category->nombre;
                $response['brand'] = $model->preciotallacolor->producto->mymarca->nombre;
                $response['variant'] = $model->preciotallacolor->mycolor->valor." ".$model->preciotallacolor->mytalla->valor;
                $response['price'] = $precio->precioImpuesto;
                $response['quantity'] = $model->cantidad;
                
				echo json_encode($response);
			}   
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }


    /*
	 * 
	 * action para eliminar un look completo de la bolsa
	 * 
	 * */
    public function actionEliminarLook() {
        if (Yii::app()->request->isPostRequest) {
        	$elementos_bolsa = BolsaHasProductotallacolor::model()->findAllByAttributes(array('look_id'=>$_POST['look_id'], 'bolsa_id'=>$_POST['bolsa_id']));
        	$look = Look::model()->findByPk($_POST['look_id']);
        	$productos_look = array();
        	foreach ($elementos_bolsa as $model) {
        		$model->delete();
        		
				$category_product = CategoriaHasProducto::model()->findByAttributes(array('tbl_producto_id'=>$model->preciotallacolor->producto->id));
                $category = Categoria::model()->findByPk($category_product->tbl_categoria_id);
                $precio = Precio::model()->findByAttributes(array('tbl_producto_id'=>$model->preciotallacolor->producto->id));
				
					$productos_look[] = array(
						'id' => $model->preciotallacolor->producto->id,
						'name' => $model->preciotallacolor->producto->nombre,
						'category' => $category->nombre,
						'brand' => $model->preciotallacolor->producto->mymarca->nombre,
						'variant' => $model->preciotallacolor->mycolor->valor." ".$model->preciotallacolor->mytalla->valor,
						'price' => $precio->precioImpuesto,
						'quantity' => 1
					);
				
        	}
			           
			echo json_encode(array(
				'status' => 'ok',
				'id' => $look->id,
				'name' => $look->title,
				'category' => 'Looks',
				'brand' => 'Personaling',
				'price' => $look->getPrecioDescuento(),
				'quantity' => 1,
				'productos' => $productos_look
			));
			
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

	/*
	 * 
	 * para validar los datos del usuario 
	 * 
	 */
	
        public function actionPagos()
        {   
            if(Bolsa::isEmpty(Yii::app()->getSession()->get("bolsaUser")))
               {
                	$this->redirect($this->createAbsoluteUrl('bolsa/index',array(),'http'));
               }

            if (Yii::app()->user->isGuest){
                //Redirigir a login si no esta logueado
                Yii::app()->user->setReturnUrl($this->createUrl('bolsa/compra'));
                Yii::app()->user->setFlash('error',Yii::t("contentForm", "¡La sesión ha expirado, intenta tu compra nuevamente!"));
                $this->redirect(array('/user/login'));                        
            }

            $admin = Yii::app()->getSession()->contains("bolsaUser");                    

            /*ID del usuario propietario de la bolsa*/
            $usuario = $admin ? Yii::app()->getSession()->get("bolsaUser")
                                : Yii::app()->user->id;

            $bolsa = Bolsa::model()->findByAttributes(array('user_id' => $usuario));
            // busco todos los productos en la bolsa del usuario para cargarlos en el script de google analytics
        	if($bolsa){
        		$bolsa_productos = BolsaHasProductotallacolor::model()->findAllByAttributes(array('bolsa_id'=>$bolsa->id));
        		$cont = 0;
        		foreach ($bolsa_productos as $bp) {
        			$category_product = CategoriaHasProducto::model()->findByAttributes(array('tbl_producto_id'=>$bp->preciotallacolor->producto->id));
					$category = Categoria::model()->findByPk($category_product->tbl_categoria_id);
					$ptcolor = Preciotallacolor::model()->findByAttributes(array('producto_id'=>$bp->preciotallacolor->producto_id,'talla_id'=>$bp->preciotallacolor->talla_id,'color_id'=>$bp->preciotallacolor->color_id));
	                $precio = Precio::model()->findByAttributes(array('tbl_producto_id'=>$bp->preciotallacolor->producto_id));

	                //echo $bp->preciotallacolor->producto->nombre.'</br>';
        			Yii::app()->clientScript->registerScript('metrica_analytics_'.$cont,"
						ga('ec:addProduct', {
						  'id': '".$bp->preciotallacolor->producto->id."',
						  'name': '".addslashes($bp->preciotallacolor->producto->nombre)."',
						  'category': '".addslashes($category->nombre)."',
						  'brand': '".addslashes($bp->preciotallacolor->producto->mymarca->nombre)."',
						  'variant': '".$ptcolor->mycolor->valor." ".$ptcolor->mytalla->valor."',
						  'price': '".$precio->precioImpuesto."',
						  'quantity': '".$bp->cantidad."',
						});
						
	  					ga('ec:setAction', 'detail');       // Detail action.
	 					ga('send', 'pageview');       // Send product details view with the initial pageview.
					");	
        			$cont++;
        		}

        		// envio datos de inicio del checkout a google analytics
            	Yii::app()->clientScript->registerScript('metrica_analytics_checkcout_step1',"
					ga('ec:setAction','checkout', {
					    'step': 3,            // A value of 1 indicates this action is first checkout step.
					    'option': 'Payment'      // Used to specify additional info about a checkout stage, e.g. payment method.
					});
					ga('send', 'pageview');   // Pageview for payment.html
				");	
        	}

            $tarjeta = new TarjetaCredito;  

            if(isset($_POST['tipo_pago']) && $_POST['tipo_pago']!=1){
                    if(isset($_POST['ajax']) && $_POST['ajax']==='tarjeta-form')
                    {
                            echo CActiveForm::validate($_POST['TarjetaCredito']);
                            Yii::app()->end();
                    }
            }

            if(isset($_POST['tipo_pago'])){

                    Yii::app()->getSession()->add('tipoPago',$_POST['tipo_pago']);
                    Yii::app()->getSession()->add('usarBalance', "0");
                    Yii::app()->getSession()->add('usarCupon', -1);
                    
                    //Errores en codigo de descuento
                    $errores = false;
                    
                    /*Saber que opcion selecciono, usar balance o usar cupon*/
                    if(isset($_POST['opcionSaldo'])){
                        
                        //Si selecciono usar balance
                        if($_POST['opcionSaldo'] == '1'){
                            Yii::app()->getSession()->add('usarBalance', "1");
                             
                        //Si selecciono usar cupon
                        }else if($_POST['opcionSaldo'] == '2'){
                            
                            //Revisar si el código es valido o no.
                           if(isset($_POST['textoCodigo']) && $_POST['textoCodigo'] != ""){
                               //Buscar el codigo en la BD
                               $codigo = CodigoDescuento::model()->findByAttributes(array("codigo"=>$_POST['textoCodigo']));
                               
                               //si es correcto
                               if($codigo){
                                   
                                   if($codigo->esValido()){

                                       //si el cliente ya usó ese cupon
                                       if(CuponHasOrden::clienteUsoCupon($codigo->id)){

                                           Yii::app()->user->setFlash('error',Yii::t("contentForm",
                                               "Ya has usado este cupón. Solo puedes usarlo una vez."));
                                           $errores = true;

                                       }else{

                                            //si la compra cumple con el minimo para usar el cupon                                                                             
                                           if($codigo->cumpleMinimo()){

                                               Yii::app()->getSession()->add('usarCupon', $codigo->id);                                   

                                           }else{

                                               Yii::app()->user->setFlash('error',Yii::t("contentForm",
                                                   "Para aplicar este cupón tu compra debe
                                                   tener un monto mínimo de <b>".$codigo->getMinimo()."</b>"));
                                               $errores = true;
                                           }
                                       }

                                   }else{

                                       Yii::app()->user->setFlash('error',Yii::t("contentForm",
                                               "Este cupón ha caducado, ya no puedes usarlo."));
                                       $errores = true;
                                   }                                  
                                  
                               }else{
                                   Yii::app()->user->setFlash('error',Yii::t("contentForm",
                                           "Has ingresado un código de descuento inválido."));
                                   $errores = true;
                               }
                               
                           }                            
                            
                        }
                        
                    }
                    
                    //Si no hay errores en el codigo de descuento
                    // preguntar por otros tipos de pago y redirigir 
                    //al siguiente paso
                    if(!$errores){
                        if($_POST['tipo_pago']==2){ // pago de tarjeta de credito

                                $idUsuario = Yii::app()->user->id; 

                                $tarjeta->nombre = $_POST['TarjetaCredito']['nombre'];
                                $tarjeta->numero = $_POST['TarjetaCredito']['numero'];
                                $tarjeta->codigo = $_POST['TarjetaCredito']['codigo'];

                                /*$tarjeta->month = $_POST['mes'];
                                $tarjeta->year = $_POST['ano'];*/

                                $tarjeta->month = $_POST['TarjetaCredito']['month'];
                                $tarjeta->year = $_POST['TarjetaCredito']['year'];
                                $tarjeta->ci = $_POST['TarjetaCredito']['ci'];
                                $tarjeta->direccion = $_POST['TarjetaCredito']['direccion'];
                                $tarjeta->ciudad = $_POST['TarjetaCredito']['ciudad'];
                                $tarjeta->zip = $_POST['TarjetaCredito']['zip'];
                                $tarjeta->estado = $_POST['TarjetaCredito']['estado'];
                                $tarjeta->user_id = $idUsuario;		

                                if($tarjeta->save()) 
                                {
                                        $tipoPago = $_POST['tipo_pago'];

                                        Yii::app()->getSession()->add('idTarjeta',$tarjeta->id);
                                        //$this->render('confirmar',array('idTarjeta'=>$tarjeta->id));
    //                                            $this->redirect(array('bolsa/confirmar'));
                                        $this->redirect($this->createUrl('bolsa/confirmar'));
                                }
                                else
                                echo CActiveForm::validate($tarjeta);

                        }
                        
                        $this->redirect($this->createUrl('bolsa/confirmar'));
                        
                    } //fin si no hay errores
                    
                    
                    //Si hay errores, quedarse en la pagina de pagos
            }
                //$tarjeta = new TarjetaCredito;
                /*Si es compra del usuario*/
                if(!$admin){
                    ShoppingMetric::registro(ShoppingMetric::STEP_PAGO); 
                }

                $aplicar = new AplicarGC;

                $this->render('pago',array(
                    'tarjeta'=>$tarjeta,
                    'model'=>$aplicar,
                    'admin'=>$admin,
                    'user'=>$usuario,
                    ));		
            

        }
		
		public function actionSuccessMP(){
			echo 'Tipo: '.Yii::app()->getSession()->get('tipoPago').'';
			$usuario = Yii::app()->user->id; 
			$bolsa = Bolsa::model()->findByAttributes(array('user_id'=>$usuario));
			
			if(Yii::app()->getSession()->get('tipoPago')==1 || Yii::app()->getSession()->get('tipoPago')==4){ // transferencia o MP
				$detalle = new Detalle;
			
				if($detalle->save())
				{
					$pago = new Pago;
					$pago->tipo = Yii::app()->getSession()->get('tipoPago'); // trans
					$pago->tbl_detalle_id = $detalle->id;
					
					if($pago->save()){
					
					// clonando la direccion
					$dir1 = Direccion::model()->findByAttributes(array('id'=>Yii::app()->getSession()->get('idDireccion'),'user_id'=>$usuario));
					$dirEnvio = new DireccionEnvio;
					
					$dirEnvio->nombre = $dir1->nombre;
					$dirEnvio->apellido = $dir1->apellido;
					$dirEnvio->cedula = $dir1->cedula;
					$dirEnvio->dirUno = $dir1->dirUno;
					$dirEnvio->dirDos = $dir1->dirDos;
					$dirEnvio->telefono = $dir1->telefono;
					$dirEnvio->ciudad_id = $dir1->ciudad_id;
					$dirEnvio->provincia_id = $dir1->provincia_id;
					$dirEnvio->pais = $dir1->pais;
					
					if(isset($_GET['collection_id']) && Yii::app()->getSession()->get('tipoPago') == 4){ // Pago con Mercadopago
						$detalle->nTransferencia = $_GET['collection_id'];
						$detalle->nombre = $dirEnvio->nombre.' '.$dirEnvio->apellido;
						$detalle->cedula = $dirEnvio->cedula;
						$detalle->monto = Yii::app()->getSession()->get('total');
						$detalle->fecha = date("Y-m-d H:i:s");
						$detalle->banco = 'Mercadopago';
						
						$detalle->estado = 0;
						
						$detalle->save();
					}

						if($dirEnvio->save()){
							// ya esta todo para realizar la orden
							
							$orden = new Orden;
							
							$orden->subtotal = Yii::app()->getSession()->get('subtotal');
							$orden->descuento = Yii::app()->getSession()->get('descuento');
							$rden->descuento_look=Yii::app()->getSession()->get('descuentoxLook');
							$orden->envio = Yii::app()->getSession()->get('envio');
							$orden->iva = Yii::app()->getSession()->get('iva');
							//$orden->descuentoRegalo = 0;
							if(Yii::app()->getSession()->get('descuentoRegalo')>0)
                            	$orden->descuentoRegalo = Yii::app()->getSession()->get('descuentoRegalo');
							else
                            	$orden->descuentoRegalo = 0;
							$orden->total = Yii::app()->getSession()->get('total');
							$orden->fecha = date("Y-m-d H:i:s"); // Datetime exacto del momento de la compra 
							$orden->estado = 1; // en espera de pago
							$orden->bolsa_id = $bolsa->id; 
							$orden->user_id = $usuario;
							$orden->pago_id = $pago->id;
							$orden->detalle_id = $detalle->id;
							$orden->direccionEnvio_id = $dirEnvio->id;
							$orden->tipo_guia = Yii::app()->getSession()->get('tipo_guia');
							
							if($orden->save()){
								$productosBolsa = BolsaHasProductotallacolor::model()->findAllByAttributes(array('bolsa_id'=>$bolsa->id));	
								$detalle->orden_id = $orden->id;
								$detalle->save();
								// añadiendo a orden producto
								foreach($productosBolsa as $prod)
								{
									$prorden = new OrdenHasProductotallacolor;
									$prorden->tbl_orden_id = $orden->id;
									$prorden->preciotallacolor_id = $prod->preciotallacolor_id;
									$prorden->cantidad = $prod->cantidad;
									$prorden->look_id = $prod->look_id;
									
									if($prorden->save()){
										//listo y que repita el proceso
									}
								}
								
								//descontando del inventario
								foreach($productosBolsa as $prod)
								{
									$uno = Preciotallacolor::model()->findByPk($prod->preciotallacolor_id);
									$cantidadNueva = $uno->cantidad - $prod->cantidad; // lo que hay menos lo que se compró
									
									Preciotallacolor::model()->updateByPk($prod->preciotallacolor_id, array('cantidad'=>$cantidadNueva));
									// descuenta y se repite									
								}
								
								
								// para borrar los productos de la bolsa								
								foreach($productosBolsa as $prod)
								{
									$prod->delete();															
								}
								
								// agregar cual fue el usuario que realizó la compra para tenerlo en la tabla estado
								$estado = new Estado;
									
								$estado->estado = 1;
								$estado->user_id = $usuario;
								$estado->fecha = date("Y-m-d");
								$estado->orden_id = $orden->id;
								
								if($estado->save())
									echo "";
								
								// Generar factura
								$factura = new Factura;
								$factura->fecha = date('Y-m-d');
								$factura->direccion_fiscal_id = Yii::app()->getSession()->get('idDireccion');  // esta direccion hay que cambiarla después, el usuario debe seleccionar esta dirección durante el proceso de compra
								$factura->direccion_envio_id = Yii::app()->getSession()->get('idDireccion');
								$factura->orden_id = $orden->id;
								$factura->save();
								
								// Enviar correo con resumen de la compra
								$user = User::model()->findByPk($usuario);
								$message            = new YiiMailMessage;
						           //this points to the file test.php inside the view path
						        $message->view = "mail_compra";
								$subject = 'Tu compra en Personaling';
						        $params              = array('subject'=>$subject, 'orden'=>$orden);
						        $message->subject    = $subject;
						        $message->setBody($params, 'text/html');
						        $message->addTo($user->email);
								$message->from = array('operaciones@personaling.com' => 'Tu Personal Shopper Online');
						        //$message->from = 'Tu Personal Shopper Online <operaciones@personaling.com>\r\n';   
						        Yii::app()->mail->send($message);
								
							// cuando finalice entonces envia id de la orden para redireccionar
							
							//$this->redirect(array('bolsa/pedido/'.$orden->id));
							//echo $this->createAbsoluteUrl('bolsa/pedido',array('id'=>$orden->id),'http');
							$this->redirect($this->createAbsoluteUrl('bolsa/pedido',array('id'=>$orden->id),'http'));
							
							
							}//orden
						}//direccion de envio
					} // pago
				}// detalle
			}// transferencia
			
			// detalle de pago (caso transferencia todo vacio)
			// tipo de pago y copiar direccion envio
			// realizar la orden
			// mover los productos
			// quitarlos de bolsa tiene producto
		}
		
		public function actionCambiarTipoPago()
		{
			
			if(isset($_POST['tipoPago'])) // escogiendo cual es la preferencia de pago
			{
				//Yii::app()->getSession()->remove('tipoPago'); 
				//Yii::app()->getSession()->add('tipoPago',1) = $_POST['tipoPago'];
			}
		}
		
		public function actionConfirmar()
		{
                    
               if(Bolsa::isEmpty(Yii::app()->getSession()->get("bolsaUser")))
               {
                	$this->redirect($this->createAbsoluteUrl('bolsa/index',array(),'http'));
               }
                    
                    if (Yii::app()->user->isGuest){
                        //Redirigir a login
                        Yii::app()->user->setReturnUrl($this->createUrl('bolsa/compra'));
                        Yii::app()->user->setFlash('error',Yii::t("contentForm", "¡La sesión ha expirado, intenta tu compra nuevamente!"));
                        $this->redirect(array('/user/login'));                        
                    }
                    
                    $admin = Yii::app()->getSession()->contains("bolsaUser");                    
                
                    /*ID del usuario propietario de la bolsa*/
                    $usuario = $admin ? Yii::app()->getSession()->get("bolsaUser")
                                        : Yii::app()->user->id;

                    $bolsa = Bolsa::model()->findByAttributes(array(
                            'user_id' => $usuario,
                            /* Si es la bolsa del admin para el usuario
                             * o la bolsa normal
                             */
                            'admin' => $admin, 
                            ));

		            // busco todos los productos en la bolsa del usuario para cargarlos en el script de google analytics
		        	if($bolsa){
		        		$bolsa_productos = BolsaHasProductotallacolor::model()->findAllByAttributes(array('bolsa_id'=>$bolsa->id));
		        		$cont = 0;
		        		foreach ($bolsa_productos as $bp) {
		        			$category_product = CategoriaHasProducto::model()->findByAttributes(array('tbl_producto_id'=>$bp->preciotallacolor->producto->id));
							$category = Categoria::model()->findByPk($category_product->tbl_categoria_id);
							$ptcolor = Preciotallacolor::model()->findByAttributes(array('producto_id'=>$bp->preciotallacolor->producto_id,'talla_id'=>$bp->preciotallacolor->talla_id,'color_id'=>$bp->preciotallacolor->color_id));
			                $precio = Precio::model()->findByAttributes(array('tbl_producto_id'=>$bp->preciotallacolor->producto_id));

			                //echo $bp->preciotallacolor->producto->nombre.'</br>';
		        			Yii::app()->clientScript->registerScript('metrica_analytics_'.$cont,"
								ga('ec:addProduct', {
								  'id': '".$bp->preciotallacolor->producto->id."',
								  'name': '".addslashes($bp->preciotallacolor->producto->nombre)."',
								  'category': '".addslashes($category->nombre)."',
								  'brand': '".addslashes($bp->preciotallacolor->producto->mymarca->nombre)."',
								  'variant': '".$ptcolor->mycolor->valor." ".$ptcolor->mytalla->valor."',
								  'price': '".$precio->precioImpuesto."',
								  'quantity': '".$bp->cantidad."',
								});
								
			  					ga('ec:setAction', 'detail');       // Detail action.
			 					ga('send', 'pageview');       // Send product details view with the initial pageview.
							");	
		        			$cont++;
		        		}

		        		// envio datos de inicio del checkout a google analytics
		            	Yii::app()->clientScript->registerScript('metrica_analytics_checkcout_step1',"
							ga('ec:setAction','checkout', {
							    'step': 4,            // A value of 1 indicates this action is first checkout step.
							    'option': 'Confirmation'      // Used to specify additional info about a checkout stage, e.g. payment method.
							});
							ga('send', 'pageview');   // Pageview for payment.html
						");	
		        	}

                    /*Si es compra normal del usuario*/
                    if(!$admin){
                        ShoppingMetric::registro(ShoppingMetric::STEP_CONFIRMAR,
                                array("bolsa_id"=>$bolsa->id));   
                    }                 
                    /*Revisar si actualizo la pagina para hacer la compra de nuevo
                     * en menos de un minuto
                     */
                    if(User::hasRecentOrder()){                       
                        Yii::app()->user->updateSession();
                        Yii::app()->user->setFlash("warning", "Al parecer estás intentando
                            hacer otra compra.<br>Revisa tu lista de pedidos, acabamos de registrar uno nuevo.");                

                        $this->redirect($this->createAbsoluteUrl('bolsa/index',array(),'http'));
                    }

                    if (!$bolsa->checkInventario())
                        $this->redirect($this->createAbsoluteUrl('bolsa/index',array(),'http'));
                    
                    
                    
                    /**********INICIO DEL CALCULO DEL MONTO DE LA ORDEN******/                    
                    $totalProductos = Yii::app()->getSession()->get('subtotal');
                    $totalDescuentos = Yii::app()->getSession()->get('descuento');
                    $iva = Yii::app()->getSession()->get('iva');
					$descuentoEachLook=Yii::app()->getSession()->get('descuentoxLook');
                    
                    //monto por productos, con sus descuentos y su iva
                    $subtotal = $totalProductos + $iva - $totalDescuentos- $descuentoEachLook;               
                    
                    /** Si esta usando un codigo de descuento, restarselo al subtotal**/
                    $cupon = array();                    
                    $idCupon = Yii::app()->getSession()->get('usarCupon');
                    if($idCupon != -1){
                        
                        $codigo = CodigoDescuento::model()->findByPk($idCupon);                        
                        //para mostrar
                        $cupon[0] = $codigo->getDescuento();
                        
                        //si es un monto fijo
                        if($codigo->tipo_descuento == 1){
                            $cupon[1] = $codigo->descuento;                                                    
                            
                        }else{       //si es porcentaje                     
                            
                            $cupon[1] = $subtotal * ($codigo->descuento / 100);
                            $cupon[1] = floor($cupon[1] * 100) / 100;                                                       
                        }                            
                        $subtotal = $subtotal - (($subtotal > $cupon[1])? $cupon[1] : $subtotal);                        
                    }
                                        
                    
                    /*El subtotal con descuentos, iva y el cupon restado*/
                    Yii::app()->getSession()->add('subTotal',$subtotal);
                    
                    /*Sumarle el Envio*/
                    $envio = Yii::app()->getSession()->get('envio');
                    $total = $subtotal + $envio;                                        
                                    
                    //Si usa balance
                    $descuentoRegalo = 0;
                    if(Yii::app()->getSession()->get('usarBalance') == '1'){

						if(UserModule::isAdmin())
							$balance = Profile::getSaldo(Yii::app()->getSession()->get("bolsaUser"), false);
						else 
							$balance = Profile::getSaldo(Yii::app()->user->id, false);					
                            
                            $balance = floor($balance *100)/100; 
                            if($balance > 0){
                                if($balance >= $total){
                                        $descuentoRegalo = $total;
//                                        $total = 0;
                                }else{
                                        $descuentoRegalo = $balance;
//                                        $total = $total - $balance;
                                }
                            }
                    }
                   Yii::app()->getSession()->add('descuentoRegalo',$descuentoRegalo);

                    //si pago toda la orden con balance
                    if($total == $descuentoRegalo){
                        Yii::app()->getSession()->add('tipoPago', 7); //pagar la orden totalmente con balance
                    }                   
                    
                    
                    /*el monto total de la orden*/
                    Yii::app()->getSession()->add('total', $total);
                    
                    /* El monto final a pagar con la tarjeta, que es el total menos
                     * lo que pago con balance
                     */
                    Yii::app()->getSession()->add('totalTarjeta', $total - $descuentoRegalo);
                    
                    /******FIN DEL CALCULO DEL MONTO DEL LA ORDEN****/
                    
                    

                    /*
                     * Para pago con tarjeta y paypal
                     */
                    $nombreProducto = "Looks: ".  $bolsa->getLooks().
                            " - Productos: ".$bolsa->getProductos();
                    
                    $tipo_pago = Yii::app()->getSession()->get('tipoPago');                   
                    
                    $idPagoAztive = $tipo_pago == 5? 8:5;                  
                    
                    //Si no esta en produccion
                    if(strpos(Yii::app()->baseUrl, "develop") !== false 
                        || strpos(Yii::app()->baseUrl, "test") !== false){

                        $idPagoAztive = $tipo_pago == 8? 999:$idPagoAztive; 
                    }
                    $monto = Yii::app()->getSession()->get('totalTarjeta');                    
                    
                    $optional = array(                        
                        'name'          => 'Personaling Enterprise S.L.',
                        'product_name'  => $nombreProducto,                             
                    );               
                    
					$usu=User::model()->findByPk( Yii::app()->user->id );
                    $cData = array(
                        "src" => 1, //origen de la compra, 1-Normal, 2-GiftCard
                        'idUsuario'=>Yii::app()->user->id,
                        'nombreUsuario'=>$usu->profile->first_name." ".$usu->profile->last_name, 
                        'cantProduc'=>$bolsa->getProductos()+$bolsa->getSumaCadaLook(),
                        'idsProductos'=> $bolsa->getEachProducto(),
   
                    );

                    $cData = CJSON::encode($cData);
                    $pago = new AzPay();

                    //Para cuando hay recurrencias
//                    $urlAztive = $pago->AztivePay($monto, $idPagoAztive, '',
//                            $idPagoAztive==8?"I":null, $optional, $cData);    
                    //Como no se tiene en este momento un ID de la orden,
                    //Poner un orderid mientras tanto en base al usuario
                    //y a la fecha del pago.
                    $orderID = "U" . Yii::app()->user->id."L".$bolsa->getLooks().
                            "P".$bolsa->getProductos();                    
                    
                    $urlAztive = $pago->AztivePay($monto, $idPagoAztive, $orderID,
                            $idPagoAztive==8?NULL:NULL, $optional, $cData);  
                    
                    
                    $this->render('confirmar',array(
                        'idTarjeta'=> Yii::app()->getSession()->get('idTarjeta'),
                        'bolsa' =>  $bolsa,
                        'admin'=> $admin,
                        'user'=> $usuario,
                        'urlAztive'=> $urlAztive,
                        'cupon'=> $cupon,
                            ));
                    
		}
		
		public function actionEliminardireccion()
		{
			// if(isset($_POST['idDir']))
			// {
			// 	$direccion = Direccion::model()->findByPk($_POST['idDir']);
			// 	$direccion->delete();
				
			// 	echo "ok";
			// }
			$id = $_POST['idDir'];
			$direccion = Direccion::model()->findByPk( $id  );
			$user = User::model()->findByPk( Yii::app()->user->id );
			if($direccion->delete()){
						echo "ok";
					}else{
						echo "wrong";
					}
			
			/*
			 * SE COMENTA LA VALIDACION PORQUE LAS DIRECCIONES GUARDADAS EN FACTURA SON LAS CLONADAS 
			 * EN LAS TABLAS DIRECCIONENVIO Y DIFRECCIONFACTURACION RESPECTIVAMENTE
			 * if($user){
				$facturas1 = Factura::model()->countByAttributes(array('direccion_fiscal_id'=>$id));
				$facturas2 = Factura::model()->countByAttributes(array('direccion_envio_id'=>$id));
				
				if($facturas1 == 0 && $facturas2 == 0){
					if($direccion->delete()){
						echo "ok";
					}else{
						echo "wrong";
					}
				}else{
					echo "bad";
				}
			}*/
		}
		
			/**
		 * editar una direccion.
		 */
		public function actionEditardireccion()
		{
			if(isset($_POST['idDireccion'])){
				$dirEdit = Direccion::model()->findByPk($_POST['idDireccion']);
				
				$dirEdit->nombre = $_POST['Direccion']['nombre'];
				$dirEdit->apellido = $_POST['Direccion']['apellido'];
				$dirEdit->cedula = $_POST['Direccion']['cedula'];
				$dirEdit->dirUno = $_POST['Direccion']['dirUno'];
				$dirEdit->dirDos = $_POST['Direccion']['dirDos'];
				$dirEdit->telefono = $_POST['Direccion']['telefono'];
				$dirEdit->ciudad_id = $_POST['Direccion']['ciudad_id'];
				$dirEdit->provincia_id = $_POST['Direccion']['provincia_id'];
				
				$dirEdit->pais=Pais::model()->getOficial($_POST['Direccion']['pais']);
				
				if($dirEdit->save()){
					$dir = new Direccion;
					$this->redirect(array('bolsa/direcciones')); // redir to action
					//$this->render('direcciones',array('dir'=>$dir));
					}
				
			}
			else if($_GET['id']){ // piden editarlo
				$direccion = Direccion::model()->findByAttributes(array('id'=>$_GET['id'],'user_id'=>Yii::app()->user->id));
				$this->render('editarDir',array('dir'=>$direccion));
			}
			
			
		}
		
		public function actionDirecciones()
		{
               if(Bolsa::isEmpty(Yii::app()->getSession()->get("bolsaUser")))
               {
                	$this->redirect($this->createAbsoluteUrl('bolsa/index',array(),'http'));
               }
		
	        if (Yii::app()->user->isGuest){
	            //Redirigir a login
	            Yii::app()->user->setReturnUrl($this->createUrl('bolsa/compra'));
	            Yii::app()->user->setFlash('error',Yii::t("contentForm", "¡La sesión ha expirado, intenta tu compra nuevamente!"));                              
	            $this->redirect(array('/user/login'));                        
	        }
	        if(!isset(Yii::app()->session['login'])&&!UserModule::isAdmin())
				 $this->redirect(array('/bolsa/compra'));
			
	        $admin = Yii::app()->getSession()->contains("bolsaUser");                    
	    
	        /*ID del usuario propietario de la bolsa*/
	        $usuario = $admin ? Yii::app()->getSession()->get("bolsaUser")
	                            : Yii::app()->user->id;
	        
	        $dir = new Direccion;

	        $bolsa = Bolsa::model()->findByAttributes(array('user_id' => $usuario));
            // busco todos los productos en la bolsa del usuario para cargarlos en el script de google analytics
        	if($bolsa){
        		$bolsa_productos = BolsaHasProductotallacolor::model()->findAllByAttributes(array('bolsa_id'=>$bolsa->id));
        		$cont = 0;
        		foreach ($bolsa_productos as $bp) {
        			$category_product = CategoriaHasProducto::model()->findByAttributes(array('tbl_producto_id'=>$bp->preciotallacolor->producto->id));
					$category = Categoria::model()->findByPk($category_product->tbl_categoria_id);
					$ptcolor = Preciotallacolor::model()->findByAttributes(array('producto_id'=>$bp->preciotallacolor->producto_id,'talla_id'=>$bp->preciotallacolor->talla_id,'color_id'=>$bp->preciotallacolor->color_id));
	                $precio = Precio::model()->findByAttributes(array('tbl_producto_id'=>$bp->preciotallacolor->producto_id));

	                //echo $bp->preciotallacolor->producto->nombre.'</br>';
        		
                    Yii::app()->clientScript->registerScript('metrica_analytics_'.$cont,'
                        ga("ec:addProduct", {
                          "id": "'.$bp->preciotallacolor->producto->id.'",
                          "name": "'.addslashes($bp->preciotallacolor->producto->nombre).'",
                          "category": "'.addslashes($category->nombre).'",
                          "brand": "'.addslashes($bp->preciotallacolor->producto->mymarca->nombre).'",
                          "variant": "'.$ptcolor->mycolor->valor.' '.$ptcolor->mytalla->valor.'",
                          "price": "'.$precio->precioImpuesto.'",
                          "quantity": "'.$bp->cantidad.'",
                        });
                        
                        ga("ec:setAction", "detail");       // Detail action.
                        ga("send", "pageview");       // Send product details view with the initial pageview.
                    '); 
                    
                    
        			$cont++;
        		}

        		// envio datos de inicio del checkout a google analytics
            	Yii::app()->clientScript->registerScript('metrica_analytics_checkcout_step1',"
					ga('ec:setAction','checkout', {
					    'step': 2,            // A value of 1 indicates this action is first checkout step.
					    'option': 'Addresses'      // Used to specify additional info about a checkout stage, e.g. payment method.
					});
					ga('send', 'pageview');   // Pageview for payment.html
				");	
        	}
			
			if(isset($_POST['tipo']) && $_POST['tipo']=='direccionVieja')
			{
                            //echo "Id:".$_POST['Direccion']['id'];
                            $dirEnvio = $_POST['Direccion']['id'];


                            Yii::app()->getSession()->add('idDireccion',$dirEnvio);
                            Yii::app()->getSession()->add('idFacturacion',$_POST['billAdd']);
							

//				$this->redirect(array('bolsa/pagos'));
                            if(isset(Yii::app()->session['login']))
                                    unset(Yii::app()->session['login']);
                            $this->redirect($this->createUrl('bolsa/pagos'));
			}
			else if(isset($_POST['Direccion'])) // nuevo registro
			{
                            //if($_POST['Direccion']['nombre']!="")
                    //	{

                            // guardar en el modelo direccion
                            $dir->attributes=$_POST['Direccion'];

                          /*  if($dir->pais=="1")
                                    $dir->pais = "Venezuela";

                            if($dir->pais=="2")
                                    $dir->pais = "Colombia";

                            if($dir->pais=="3")
                                    $dir->pais = "Estados Unidos"; */
                            $dir->pais=Pais::model()->getOficial($dir->pais);
							
//				$dir->user_id = Yii::app()->user->id;
                            $dir->user_id = $_POST["user"];

                            if($dir->save())
                            {

                                //$tarjeta = new TarjetaCredito;

                                Yii::app()->getSession()->add('idDireccion',$dir->id);
//						$this->redirect(array('bolsa/pagos'));		
								if(isset(Yii::app()->session['login']))
									unset(Yii::app()->session['login']);
                                $this->redirect($this->createUrl('bolsa/pagos'));
                                //$this->render('pago',array('idDireccion'=>$dir->id,'tarjeta'=>$tarjeta));

                                //$this->redirect(array('bolsa/pagos','id'=>$dir->id)); // redir to action Pagos
                            }
 
                            //} // nombre
                    //	else {
                                    //$this->render('direcciones',array('dir'=>$dir)); // regresa
                            //}
				
			}
			else // si está viniendo de la pagina anterior que muestre todo 
			{
				
                            if(!$admin){
								ShoppingMetric::registro(ShoppingMetric::STEP_DIRECCIONES);   
                            }
                            
                            $this->render('direcciones',array(
                                'dir'=>$dir,
                                'user'=> $usuario,
                                    ));
                            
			}
			

		}
	
		/**
	 * Displays the login page
	 */
	public function actionCompra()
	{
             if(isset($_SESSION['idFacturacion']))
				unset($_SESSION['idFacturacion']);	
			
            if(Bolsa::isEmpty(Yii::app()->getSession()->get("bolsaUser"))){
                $this->redirect($this->createAbsoluteUrl('bolsa/index',array(),'http'));
            }

            if (!Yii::app()->user->isGuest) { // que esté logueado para llegar a esta acción

                /* Si es compra de admin para usuario */
                $admin = Yii::app()->getSession()->contains("bolsaUser");
				

                if ($admin) {
                    $this->redirect($this->createUrl('bolsa/direcciones'));
                }

                $model = new UserLogin;
                $user = User::model()->notsafe()->findByPk(Yii::app()->user->id);

                if (isset($_POST['UserLogin'])) {
                    $model->attributes = $_POST['UserLogin'];
                    // validate user input and redirect to previous page if valid

                    if ($model->validate()) {
                        //echo 'Status: ' . $user->status;
                        if ($user->status == 1) {
                            Yii::app()->session['login'] = 1;
                            $this->redirect(array('bolsa/direcciones'));
                        } else {
                            Yii::app()->user->setFlash('error', Yii::t('contentForm', 'You must validate your account before continuing. We\'ve sent a new validation link')." <strong>" . $user->email . "</strong>");
                            $activation_url = $this->createAbsoluteUrl('/user/activation/activation', array("activkey" => $user->activkey, "email" => $user->email));

                            $message = new YiiMailMessage;
                            $message->view = "mail_template";
                            $subject = 'Activa tu cuenta en Personaling';
                            $body = Yii::t('contentForm', 'You are receiving this email because you have requested a new link to validate your account. You can continue by clicking on the link below:<br/>') . $activation_url;
                            $params = array('subject' => $subject, 'body' => $body);
                            $message->subject = $subject;
                            $message->setBody($params, 'text/html');
                            $message->addTo($user->email);
                            $message->from = array('info@personaling.com' => 'Tu Personal Shopper Online');
                            Yii::app()->mail->send($message);
                            $this->refresh();
                        }
                    } else {
                        $this->render('login', array('model' => $model));
                        Yii::app()->user->setFlash('error', UserModule::t("La contraseña es incorrecta."));
                    }
                } else {
                    $bolsa = Bolsa::model()->findByAttributes(array('user_id' => $user->id));
                    // busco todos los productos en la bolsa del usuario para cargarlos en el script de google analytics
                	//$bolsa = Bolsa::model()->findByAttributes(array('user_id' => $user->id));
                	if($bolsa){
                		$bolsa_productos = BolsaHasProductotallacolor::model()->findAllByAttributes(array('bolsa_id'=>$bolsa->id));
                		$cont = 0;
                		foreach ($bolsa_productos as $bp) {
                			$category_product = CategoriaHasProducto::model()->findByAttributes(array('tbl_producto_id'=>$bp->preciotallacolor->producto->id));
        					$category = Categoria::model()->findByPk($category_product->tbl_categoria_id);
        					$ptcolor = Preciotallacolor::model()->findByAttributes(array('producto_id'=>$bp->preciotallacolor->producto_id,'talla_id'=>$bp->preciotallacolor->talla_id,'color_id'=>$bp->preciotallacolor->color_id));
			                $precio = Precio::model()->findByAttributes(array('tbl_producto_id'=>$bp->preciotallacolor->producto_id));

			                //echo $bp->preciotallacolor->producto->nombre.'</br>';
                			Yii::app()->clientScript->registerScript('metrica_analytics_'.$cont,"
								ga('ec:addProduct', {
								  'id': '".$bp->preciotallacolor->producto->id."',
								  'name': '".addslashes($bp->preciotallacolor->producto->nombre)."',
								  'category': '".addslashes($category->nombre)."',
								  'brand': '".addslashes($bp->preciotallacolor->producto->mymarca->nombre)."',
								  'variant': '".$ptcolor->mycolor->valor." ".$ptcolor->mytalla->valor."',
								  'price': '".$precio->precioImpuesto."',
								  'quantity': '".$bp->cantidad."',
								});
								
			  					ga('ec:setAction', 'detail');       // Detail action.
			 					ga('send', 'pageview');       // Send product details view with the initial pageview.
							");	
                			$cont++;
                		}

                		// envio datos de inicio del checkout a google analytics
                    	Yii::app()->clientScript->registerScript('metrica_analytics_checkcout_step1',"
							ga('ec:setAction','checkout', {
							    'step': 1,            // A value of 1 indicates this action is first checkout step.
							    'option': 'Authentication'      // Used to specify additional info about a checkout stage, e.g. payment method.
							});
							ga('send', 'pageview');   // Pageview for payment.html
						");	
                	}
                    if ($bolsa->deleteInactivos()) {
                        Yii::app()->session['inactivos'] = 1;
                        $this->redirect(array('bolsa/index'));
                    } else {
						ShoppingMetric::registro(ShoppingMetric::STEP_LOGIN,array("bolsa_id"=>$bolsa->id));
                        // si no viene del formulario. O bien viene de la pagina anterior
                        $this->render('login', array('model' => $model));
                    }
                }
            } else {
                // no va a llegar nadie que no esté logueado 
                // (CLARO QUE SI, SI ESCRIBEN LA URL O SI SE MUERE 
                // LA SESION EN EL PROCESO DE COMPRA)
                
                //Redirigir a login
                Yii::app()->user->setReturnUrl($this->createUrl('bolsa/compra'));
                Yii::app()->user->setFlash('error',Yii::t("contentForm", "¡La sesión ha expirado, intenta tu compra nuevamente!"));                              
                
                $this->redirect(array('/user/login'));
            }
	}//fin
	/*
	 * Realizar cobro a la tarjeta
	 * /
	 * *
	 * **
	 * //
	 * 
	*/
	public function cobrarTarjeta($tarjeta_id,$usuario,$monto){
		$monto = round($monto,2);
		Yii::trace('Entro cobrar tarjeta user:'.$usuario, 'registro');
		//$usuario = Yii::app()->user->id; 
		$tarjeta = TarjetaCredito::model()->findByPk($tarjeta_id);
		$data_array = array(
			"Amount"=>$monto, // MONTO DE LA COMPRA
			"Description"=>"Tarjeta de Credito", // DESCRIPCION 
			"CardHolder"=>$tarjeta->nombre, // NOMBRE EN TARJETA 
			"CardHolderID"=>$tarjeta->ci, // CEDULA
			"CardNumber"=>$tarjeta->numero, // NUMERO DE TARJETA
			"CVC"=>"".$tarjeta->codigo, //CODIGO DE SEGURIDAD
			"ExpirationDate"=>$tarjeta->vencimiento, // FECHA DE VENCIMIENTO
			"StatusId"=>"2", // 1 = RETENER 2 = COMPRAR 
			"IP"=>$_SERVER['REMOTE_ADDR'],
			"Address"=>$tarjeta->direccion, // DIRECCION
			"City"=>$tarjeta->ciudad, // CIUDAD
			"ZipCode"=>$tarjeta->zip, // CODIGO POSTAL
			"State"=>$tarjeta->estado, //ESTADO 
		);
		
		$output = Yii::app()->curl->putPago($data_array); // se ejecuto
		Yii::trace('realizo cobro, return:'.print_r($output, true), 'registro');
		//Yii::app()->end();
		
		if($output->code == 201){ // PAGO AUTORIZADO
			$rest = substr($tarjeta->numero, -4);
			 // se guardan solo los ultimos 4 numeros y se limpian los datos
			$tarjeta->numero = $rest;
			$tarjeta->codigo = " ";
			$tarjeta->vencimiento = " ";
			$tarjeta->user_id = $usuario;		
			if (!$tarjeta->save())
				Yii::trace('UserID:'.$usuario.' Error al eliminar tarjeta:'.print_r($tarjeta->getErrors(),true), 'registro');
			// cuando finalice entonces envia id de la orden para redireccionar
			return array(
			'codigo'=>$output->code, 
				'status'=> true, // paso o no
				'mensaje' => $output->message,
				'idOutput' => $output->id,
				'referencia' => $output->reference,
				'voucher' => $output->voucher,
				//'idDetalle' => $detalle->id,
				
			);
		}else{ // 201
			$tarjeta->delete();
			// cuando finalice entonces envia id de la orden para redireccionar
			return array(
				'status'=> false,
				'codigo'=> $output->code, // paso o no
				'mensaje' => $output->message									
			);
		}
		Yii::trace('Entro cobrar tarjeta user:'.$usuario, 'registro');
	}
	/*
	 * Pago con tarjeta de credito
	 * */
	public function actionCredito(){
			//Yii::trace('delete a look, Error:'.print_r($this->getErrors(), true), 'registro');
			Yii::trace('Entro credito user:'.Yii::app()->user->id, 'registro');
			if(isset($_POST['tipoPago']) && $_POST['tipoPago'] == 2){ // Pago con TDC
						
					if($_POST['tarjeta'] != 0) // grabo temporal la tarjeta
					{
						$usuario = Yii::app()->user->id; 
						
						$tarjeta = TarjetaCredito::model()->findByPk($_POST['tarjeta']);
							
						//$exp = $_POST['mes']."/".$_POST['ano'];
							/*
							$data_array = array(
								"Amount"=>$_POST['total'], // MONTO DE LA COMPRA
								"Description"=>"Tarjeta de Credito", // DESCRIPCION 
								"CardHolder"=>$_POST['nom'], // NOMBRE EN TARJETA
								"CardNumber"=>$_POST['num'], // NUMERO DE TARJETA
								"CVC"=>$_POST['cod'], //CODIGO DE SEGURIDAD
								"ExpirationDate"=>$exp, // FECHA DE VENCIMIENTO
								"StatusId"=>"2", // 1 = RETENER 2 = COMPRAR
								"Address"=>$_POST['dir'], // DIRECCION
								"City"=>$_POST['ciud'], // CIUDAD
								"ZipCode"=>$_POST['zip'], // CODIGO POSTAL
								"State"=>$_POST['est'], //ESTADO
							);
							*/
							
							$data_array = array(
								"Amount"=>$_POST['total'], // MONTO DE LA COMPRA
								"Description"=>"Tarjeta de Credito", // DESCRIPCION 
								"CardHolder"=>$tarjeta->nombre, // NOMBRE EN TARJETA
								"CardHolderID"=>$tarjeta->ci, // CEDULA
								"CardNumber"=>$tarjeta->numero, // NUMERO DE TARJETA
								"CVC"=>"".$tarjeta->codigo, //CODIGO DE SEGURIDAD
								"ExpirationDate"=>$tarjeta->vencimiento, // FECHA DE VENCIMIENTO
								"StatusId"=>"2", // 1 = RETENER 2 = COMPRAR
								"IP"=>$_SERVER['REMOTE_ADDR'],
								"Address"=>$tarjeta->direccion, // DIRECCION
								"City"=>$tarjeta->ciudad, // CIUDAD
								"ZipCode"=>$tarjeta->zip, // CODIGO POSTAL
								"State"=>$tarjeta->estado, //ESTADO
							);
							
						$output = Yii::app()->curl->putPago($data_array); // se ejecuto
						Yii::trace('realizo cobro, return:'.print_r($output, true), 'registro');	
							if($output->code == 201){ // PAGO AUTORIZADO
							
								$rest = substr($tarjeta->numero, -4);
								
								$detalle = new Detalle;
								
								$detalle->nTarjeta = $rest;
								$detalle->nTransferencia = $output->id;
								$detalle->nombre = $tarjeta->nombre;
								$detalle->cedula = $tarjeta->ci;
								$detalle->monto = $_POST['total'];
								$detalle->fecha = date("Y-m-d H:i:s");
								$detalle->banco = 'TDC';
								$detalle->estado = 1; // aceptado
								
								if($detalle->save()){
								
									// se guardan solo los ultimos 4 numeros y se limpian los datos
									$tarjeta->numero = $rest;
									$tarjeta->codigo = " ";
									$tarjeta->vencimiento = " ";
									$tarjeta->user_id = $usuario;		
										
									$tarjeta->save();
									
									// cuando finalice entonces envia id de la orden para redireccionar
									echo CJSON::encode(array(
										'status'=> $output->code, // paso o no
										'mensaje' => $output->message,
										'idDetalle' => $detalle->id
										
									));
									
								}//detalle
								
							}// 201
							else
							{
								$tarjeta->delete();
									
								// cuando finalice entonces envia id de la orden para redireccionar
								echo CJSON::encode(array(
									'status'=> $output->code, // paso o no
									'mensaje' => $output->message									
								));
									
							}
							Yii::trace('salio credito user:'.Yii::app()->user->id, 'registro');
							//$respCard = $respCard."Success: ".$output->success."<br>"; // 0 = FALLO 1 = EXITO
						//	$respCard = $respCard."Message:".$output->success."<br>"; // MENSAJE EN EL CASO DE FALLO
						//	$respCard = $respCard."Id: ".$output->id."<br>"; // EL ID DE LA TRANSACCION
						//	$respCard = $respCard."Code: ".$output->code."<br>"; // 201 = AUTORIZADO 400 = ERROR DATOS 401 = ERROR AUTENTIFICACION 403 = RECHAZADO 503 = ERROR INTERNO

						}/*
						else // escogio una tarjeta
						{
							
							$card = TarjetaCredito::model()->findByPk($_POST['idCard']);
							$usuario = Yii::app()->user->id; 
							
							$data_array = array(
								"Amount"=>$_POST['total'], // MONTO DE LA COMPRA
								"Description"=>"Tarjeta de Credito", // DESCRIPCION 
								"CardHolder"=>$card->nombre, // NOMBRE EN TARJETA
								"CardNumber"=>$card->numero, // NUMERO DE TARJETA
								"CVC"=>$card->codigo, //CODIGO DE SEGURIDAD
								"ExpirationDate"=>$card->vencimiento, // FECHA DE VENCIMIENTO
								"StatusId"=>"2", // 1 = RETENER 2 = COMPRAR
								"Address"=>$card->direccion, // DIRECCION
								"City"=>$card->ciudad, // CIUDAD
								"ZipCode"=>$card->zip, // CODIGO POSTAL
								"State"=>$card->estado, //ESTADO
							);
							
						$output = Yii::app()->curl->putPago($data_array); // se ejecuto
							
							if($output->code == 201){ // PAGO AUTORIZADO
							
								$detalle = new Detalle;
							
								$detalle->nTarjeta = $card->numero;
								$detalle->nTransferencia = $output->id;
								$detalle->nombre = $card->nombre;
								$detalle->codigo = $card->codigo;
								$detalle->vencimiento = $card->vencimiento;
								$detalle->monto = $_POST['total'];
								$detalle->fecha = date("Y-m-d H:i:s");
								$detalle->banco = 'TDC';
								$detalle->estado = 1; // aceptado
								
								if($detalle->save()){
									// cuando finalice entonces envia id de la orden para redireccionar
									echo CJSON::encode(array(
										'status'=> $output->code, // paso o no
										'mensaje' => $output->message,
										'idDetalle' => $detalle->id										
									));
								}
					
							}
						}*/


					}

	
	}
	
	public function actionComprar()
	{
		if (Yii::app()->request->isPostRequest){ // asegurar que viene en post
			$codigo_randon = Yii::app()->getSession()->get('codigo_randon');
			if ($codigo_randon == $_POST['codigo_randon'])
				Yii::app()->end();
			Yii::app()->getSession()->add('codigo_randon',$codigo_randon);	
                        
                         $admin = Yii::app()->getSession()->contains("bolsaUser");                    
                
                        /*ID del usuario propietario de la bolsa*/
                        $usuario = $admin ? Yii::app()->getSession()->get("bolsaUser")
                                            : Yii::app()->user->id;
                        
			$user = User::model()->findByPk($usuario);
			$bolsa = Bolsa::model()->findByAttributes(array(
                            'user_id' => $usuario,
                            /* Si es la bolsa del admin para el usuario
                             * o la bolsa normal
                             */
                            'admin' => $admin, 
                            ));
			if (!$bolsa->checkInventario())
				$this->redirect($this->createAbsoluteUrl('bolsa/index',array('mensaje'=>"Hola"),'http'));
                        
                        /*Revisar si actualizo la pagina para hacer la compra de nuevo
                         * en menos de un minuto
                         */
                        if(User::hasRecentOrder()){                       
                            Yii::app()->user->updateSession();
                            Yii::app()->user->setFlash("warning", "Al parecer estás intentando
                                hacer otra compra.<br>Revisa tu lista de pedidos, acabamos de registrar uno nuevo.");                

                            $this->redirect($this->createAbsoluteUrl('bolsa/index',array(),'http'));
                        }
                        
			$tipoPago = Yii::app()->getSession()->get('tipoPago');	
                        
			switch ($tipoPago) {
			    case 1: // TRANSFERENCIA
			       	$dirEnvio = $this->clonarDireccion(Direccion::model()->findByAttributes(array('id'=>Yii::app()->getSession()->get('idDireccion'),'user_id'=>$usuario)));
                                $dirFacturacion = $this->clonarDireccion(Direccion::model()->findByAttributes(array('id'=>Yii::app()->getSession()->get('idFacturacion'),'user_id'=>$usuario)),true);
                                
                                
                                $orden = new Orden;
                                $orden->subtotal = Yii::app()->getSession()->get('subtotal');
                                $orden->descuento = 0;
                                $orden->descuento_look=Yii::app()->getSession()->get('descuentoxLook'); //new
								if(Yii::app()->getSession()->get('envio')>0)
                                	$orden->envio = Yii::app()->getSession()->get('envio');
								else
                                	$orden->envio = 0;
                                $orden->iva = Yii::app()->getSession()->get('iva');
                                if(Yii::app()->getSession()->get('descuentoRegalo')>0)
                                	$orden->descuentoRegalo = Yii::app()->getSession()->get('descuentoRegalo');
								else
                                	$orden->descuentoRegalo = 0;
                                //$orden->descuentoRegalo = 0;
                                $orden->total = Yii::app()->getSession()->get('total');
                                $orden->seguro = Yii::app()->getSession()->get('seguro');
                                $orden->fecha = date("Y-m-d H:i:s"); // Datetime exacto del momento de la compra 
                                $orden->estado = Orden::ESTADO_ESPERA; // en espera de pago
                                $orden->bolsa_id = $bolsa->id; 
                                $orden->user_id = $usuario;
                                $orden->direccionEnvio_id = $dirEnvio->id;
                                $orden->direccionFacturacion_id = $dirFacturacion->id;
                                $orden->tipo_guia = Yii::app()->getSession()->get('tipo_guia');
                                $orden->peso = Yii::app()->getSession()->get('peso');
                                $total_orden = round(Yii::app()->getSession()->get('total'), 2);
                                $orden->total = $total_orden;

                                /*Si es compra del admin para el usuario*/
                                if($admin){
                                    $orden->admin_id = Yii::app()->user->id;
                                }

                                if (!($orden->save())){

                                    echo CJSON::encode(array(
                                        'status' => 'error',
                                        'error' => $orden->getErrors(),
                                    ));
                                    Yii::trace('UserID:' . $usuario . ' Error al guardar la orden:' . print_r($orden->getErrors(), true), 'registro');
                                    Yii::app()->end();

                                }	
                                $userBalance = 	Yii::app()->getSession()->get('usarBalance');			
                                if($userBalance == '1'){                                    
                                    $balance_usuario = $user->saldo;
                                    $balance_usuario = floor($balance_usuario * 100) / 100;
                                    if ($balance_usuario > 0) {
                                        $balance = new Balance;
                                        $detalle_balance = new Detalle;
                                        if ($balance_usuario >= $total_orden) {
                                            $orden->cambiarEstado(Orden::ESTADO_CONFIRMADO);

                                            $balance->total = $total_orden * (-1);
                                            $detalle_balance->monto = $total_orden;
                                        } else {

                                            $orden->cambiarEstado(Orden::ESTADO_INSUFICIENTE);
                                            $balance->total = $balance_usuario * (-1);
                                            $detalle_balance->monto = $balance_usuario;
                                        }

                                        $detalle_balance->comentario = "Uso de Saldo";
                                        $detalle_balance->estado = 1;
                                        $detalle_balance->orden_id = $orden->id;
                                        $detalle_balance->tipo_pago = 3;
                                        if ($detalle_balance->save()) {
                                            $balance->orden_id = $orden->id;
                                            $balance->user_id = $usuario;
                                            $balance->tipo = 1;
                                            //$balance->total=round($balance->total,2);
                                            $balance->save();
                                        }
                                    }
                                }

                                $this->hacerCompra($bolsa->id,$orden->id);
                                
                                // Agregar el usuario que creo el estado
                                // estado En espera de PAGO

                                $estado = new Estado;
                                $estado->estado = 1;
                                $estado->user_id = $usuario;
                                $estado->fecha = date("Y-m-d");
                                $estado->orden_id = $orden->id;
                                $estado->save();
					
			        break;
			    case 2: // TARJETA DE CREDITO
			        $resultado = $this->cobrarTarjeta(Yii::app()->getSession()->get('idTarjeta'), $usuario, Yii::app()->getSession()->get('totalTarjeta'));
					$global = $resultado;
					
					            if ($resultado['status'] == "ok") {
                                    $tarjeta = TarjetaCredito::model()->findByPk(Yii::app()->getSession()->get('idTarjeta'));
                                    $detalle = new Detalle;
                                    $detalle->nTarjeta = $tarjeta->numero;
                                    $detalle->nTransferencia = $resultado["idOutput"];
                                    $detalle->nombre = $tarjeta->nombre;
                                    $detalle->cedula = $tarjeta->ci;
                                    $detalle->monto = Yii::app()->getSession()->get('totalTarjeta'); 
                                    $detalle->fecha = date("Y-m-d H:i:s");
                                    $detalle->banco = 'TDC';
                                    $detalle->estado = 1; // aceptado
                                    if (!$detalle->save()) {
                                        Yii::trace('UserID:' . $usuario . ' Error al guardar detalle:' . print_r($detalle->getErrors(), true), 'registro');
                                    }
                                    $dirEnvio = $this->clonarDireccion(Direccion::model()->findByAttributes(array('id' => Yii::app()->getSession()->get('idDireccion'), 'user_id' => $usuario)));
                                    $dirFacturacion = $this->clonarDireccion(Direccion::model()->findByAttributes(array('id' => Yii::app()->getSession()->get('idFacturacion'), 'user_id' => $usuario)), true);
                                    $orden = new Orden;
                                    $orden->subtotal = Yii::app()->getSession()->get('subtotal');
                                    /*if(isset(Yii::app()->getSession()->get('descuento'))){
                                    	$orden->descuento = Yii::app()->getSession()->get('descuento');
                                    }else{
                                    	$orden->descuento = 0;
                                    }*/
                                    $orden->descuento = Yii::app()->getSession()->get('descuento');
									$orden->descuento_look=Yii::app()->getSession()->get('descuentoxLook');
                                    
                                    $orden->envio = Yii::app()->getSession()->get('envio');
                                    $orden->iva = Yii::app()->getSession()->get('iva');
                                    //$orden->descuentoRegalo = 0;
                                    if(Yii::app()->getSession()->get('descuentoRegalo')>0)
	                                	$orden->descuentoRegalo = Yii::app()->getSession()->get('descuentoRegalo');
									else
	                                	$orden->descuentoRegalo = 0;
                                    $orden->total = Yii::app()->getSession()->get('total');
                                    $orden->seguro = Yii::app()->getSession()->get('seguro');
                                    $orden->fecha = date("Y-m-d H:i:s"); // Datetime exacto del momento de la compra 
                                    $orden->estado = Orden::ESTADO_CONFIRMADO; // en espera de pago
                                    $orden->bolsa_id = $bolsa->id;
                                    $orden->user_id = $usuario;
                                    $orden->direccionEnvio_id = $dirEnvio->id;
                                    $orden->direccionFacturacion_id = $dirFacturacion->id;
                                    $orden->tipo_guia = Yii::app()->getSession()->get('tipo_guia');
                                    $orden->peso = Yii::app()->getSession()->get('peso');
                                    $total_orden = round(Yii::app()->getSession()->get('total'), 2);
                                    $orden->total = $total_orden;
                                    if (!($orden->save())) {

                                        echo CJSON::encode(array(
                                            'status' => 'error',
                                            'error' => $orden->getErrors(),
                                        ));
                                        Yii::trace('UserID:' . $usuario . ' Error al guardar la orden:' . print_r($orden->getErrors(), true), 'registro');
                                        Yii::app()->end();
                                    }
                                    $userBalance = Yii::app()->getSession()->get('usarBalance');

                                    if ($userBalance == '1') {
                                        //$balance_usuario=$balance_usuario=str_replace(',','.',Profile::model()->getSaldo(Yii::app()->user->id));	
                                        $balance_usuario = $user->saldo;
                                        $balance_usuario = floor($balance_usuario * 100) / 100;
                                        if ($balance_usuario > 0) {
                                            $balance = new Balance;
                                            $detalle_balance = new Detalle;
                                            if ($balance_usuario >= $total_orden) {
                                                //$orden->cambiarEstado(Orden::ESTADO_CONFIRMADO);

                                                $balance->total = $total_orden * (-1);
                                                $detalle_balance->monto = $total_orden;
                                            } else {

                                                //$orden->cambiarEstado(Orden::ESTADO_CONFIRMADO);
                                                $balance->total = $balance_usuario * (-1);
                                                $detalle_balance->monto = $balance_usuario;
                                            }

                                            $detalle_balance->comentario = "Uso de Saldo";
                                            $detalle_balance->estado = 1;
                                            $detalle_balance->orden_id = $orden->id;
                                            $detalle_balance->tipo_pago = 3;
                                            if ($detalle_balance->save()) {
                                                $balance->orden_id = $orden->id;
                                                $balance->user_id = $usuario;
                                                $balance->tipo = 1;
                                                //$balance->total=round($balance->total,2);
                                                $balance->save();
                                            }
                                        }
                                    }
                                    $this->hacerCompra($bolsa->id, $orden->id);
                                    $estado = new Estado;
                                    $estado->estado = 1;
                                    $estado->user_id = $usuario;
                                    $estado->fecha = date("Y-m-d");
                                    $estado->orden_id = $orden->id;
                                    if ($estado->save()) {
                                        // pasar a estado confirmado de una vez por que ya se pagó el dinero 
                                        $estado = new Estado;
                                        $estado->estado = 3;
                                        $estado->user_id = $usuario;
                                        $estado->fecha = date("Y-m-d");
                                        $estado->orden_id = $orden->id;
                                        if ($estado->save()) {
                                            $detalle->orden_id = $orden->id;
                                            $detalle->tipo_pago = 2;
                                            $detalle->save();
                                        }
                                    }// estado                                                                                                                                                
                                } 
                                else {
                                    $this->redirect($this->createAbsoluteUrl('bolsa/error', array('codigo' => $resultado['codigo'], 'mensaje' => $resultado['mensaje']), 'http'));
                                }			
			        break; 
                            case 7: //SI LA ORDEN SALIO EN CERO, PAGANDO CON BALANCE O CUPON
                                
                                $dirEnvio = $this->clonarDireccion(Direccion::model()->findByAttributes(array('id'=>Yii::app()->getSession()->get('idDireccion'),'user_id'=>$usuario)));
                                $dirFacturacion = $this->clonarDireccion(Direccion::model()->findByAttributes(array('id'=>Yii::app()->getSession()->get('idFacturacion'),'user_id'=>$usuario)),true);
                                
                                $orden = new Orden;
                                $orden->subtotal = Yii::app()->getSession()->get('subtotal'); //suma de los productos sin iva ni descuentos                                
                                $orden->descuento = Yii::app()->getSession()->get('descuento');
								$orden->descuento_look=Yii::app()->getSession()->get('descuentoxLook');
                                $orden->envio = Yii::app()->getSession()->get('envio');
                                $orden->iva = Yii::app()->getSession()->get('iva');                                
                                $orden->descuentoRegalo = Yii::app()->getSession()->get('descuentoRegalo'); //por balance usado
                                $orden->total = Yii::app()->getSession()->get('total');
                                $orden->seguro = Yii::app()->getSession()->get('seguro');
                                
                                $orden->fecha = date("Y-m-d H:i:s"); // Datetime exacto del momento de la compra 
                                $orden->estado = Orden::ESTADO_CONFIRMADO; // en espera de pago
                                $orden->bolsa_id = $bolsa->id; 
                                $orden->user_id = $usuario;
                                $orden->direccionEnvio_id = $dirEnvio->id;
                                $orden->direccionFacturacion_id = $dirFacturacion->id;
                                $orden->tipo_guia = Yii::app()->getSession()->get('tipo_guia');
                                $orden->peso = Yii::app()->getSession()->get('peso');
                                $total_orden = round(Yii::app()->getSession()->get('total'), 2);
                                $orden->total = $total_orden;

                                /*Si es compra del admin para el usuario*/
                                if($admin){
                                    $orden->admin_id = Yii::app()->user->id;
                                }

                                if (!($orden->save())){

                                    echo CJSON::encode(array(
                                        'status' => 'error',
                                        'error' => $orden->getErrors(),
                                    ));
                                    Yii::trace('UserID:' . $usuario . ' Error al guardar la orden:' . print_r($orden->getErrors(), true), 'registro');
                                    Yii::app()->end();

                                }	
                                
                                //Poner la orden en estado "pagado" (3)
                                $estado = new Estado;
                                $estado->estado = Orden::ESTADO_CONFIRMADO;
                                $estado->user_id = $usuario;
                                $estado->fecha = date("Y-m-d");
                                $estado->orden_id = $orden->id;
                                $estado->save();
                                
                                //Si fue usando balance
                               $descuentoRegalo = $orden->descuentoRegalo; //Pagado con balance            
                               if($descuentoRegalo > 0){ 
                                    
                                    $balance = new Balance;
                                    $balance->total = $descuentoRegalo * (-1); //Descontar al usuario

                                    $detalleBalance = new Detalle;
                                    $detalleBalance->monto = $descuentoRegalo;

                                    $detalleBalance->comentario = "Uso de Saldo";
                                    $detalleBalance->estado = 1;//Aprobado
                                    $detalleBalance->fecha = date("Y-m-d H:i:s");
                                    $detalleBalance->orden_id = $orden->id;
                                    $detalleBalance->tipo_pago = Detalle::USO_BALANCE;

                                    if ($detalleBalance->save()) {
                                        $balance->orden_id = $orden->id;
                                        $balance->user_id = $usuario;
                                        $balance->tipo = 1;                        
                                        $balance->save();
                                    }                                                                       
                                    
                                }
                                
                                //si uso cupon en vez de balance
                                $idCupon = Yii::app()->getSession()->get('usarCupon');
                                if($idCupon != -1){ 
                                    
                                    $detallePago = new Detalle;
                                    $detallePago->monto = $orden->total;

                                    $detallePago->comentario = "Pago con cupon";
                                    $detallePago->estado = 1;//Aprobado
                                    $detallePago->fecha = date("Y-m-d H:i:s");
                                    $detallePago->orden_id = $orden->id;
                                    $detallePago->tipo_pago = Detalle::CUPON_DESCUENTO;

                                    $detallePago->save();
                                    
                                }
                                
                                $this->agregarCupon($orden);
                                
                                $this->hacerCompra($bolsa->id,$orden->id);
                                					
                               break;
			} //FIN SWITCH
			
			/*===========================================*/
								
								if(Yii::app()->params['zohoActive'] == TRUE){ // Zoho Activo    
									$zoho = new ZohoSales;
									
									//transformando Lead a posible cliente.
									if($user->tipo_zoho == 0){

										if($user->zoho_id == ""){
					            			$zoho->getLostId($user->email);
					            		}

										$conv = $zoho->convertirLead($user->zoho_id, $user->email);
										$datos = simplexml_load_string($conv);
										
										/*
										var_dump($datos);
										Yii::app()->end();
										*/

										$id = $datos->Contact;
										$user->zoho_id = $id;
										$user->tipo_zoho = 1;
										
										if(!$user->save())
											Yii::trace('ZOHO:'.$user.' Error al guardar:'.print_r($user->getErrors(),true),'Compra');

									}
									
									if($user->tipo_zoho == 1) // es ahora un contact
									{
										if($user->zoho_id == ""){ 
					            			$zoho->getLostId($user->email);
					            		}

										$respuesta = $zoho->save_potential($orden);
									
										$datos = simplexml_load_string($respuesta);
										
										if(isset($datos->result[0]->recorddetail->FL[0])) // si hay un error, el cliente no deberia verlo
										{
											$id = $datos->result[0]->recorddetail->FL[0];
											if(isset(Yii::app()->session['zoho_error'])) // si hay un error, pero no en el producto, si no en el usuario
												$orden->zoho_error=2;
											$orden->zoho_id = $id;
											$orden->save();
										}
										else 
										{
											if(isset(Yii::app()->session['zoho_error']))	//hay error de usuario y producto
												$orden->zoho_error=3;
											else
												$orden->zoho_error=1; // solo hay error de producto
											$orden->save();
										}
 
									}
								} 
			
			
				// Generar factura
			$factura = new Factura;
			$factura->fecha = date('Y-m-d');
			$factura->direccion_fiscal_id = $dirFacturacion->id; // esta direccion hay que cambiarla después, el usuario debe seleccionar esta dirección durante el proceso de compra
			$factura->direccion_envio_id = $dirEnvio->id;
			$factura->orden_id = $orden->id;
			if (!$factura->save())
				Yii::trace('user id:'.Yii::app()->user->id.' Factura error:'.print_r($factura->getErrors(),true), 'registro');
			
                        // Enviar correo con resumen de la compra
                        $this->enviarEmail($orden, $user);
                        
                        /*Enviar correo OPERACIONES (operaciones@personaling.com*/                        
                        if(strpos(Yii::app()->baseUrl, "develop") === false){

                            $this->enviarEmailOperaciones($orden);  

                        }

                        /*Generar el Outbound para Logishfashion*/
                        $this->generarOutbound($orden);
                        
						
						/* Si llega aca, se asignan las variables globales de voucher y referencia */
						if($tipoPago == 2){
							Yii::app()->session['voucher'] = $global['voucher'];
							Yii::app()->session['referencia'] = $global['referencia'];
						} 
						
                        $this->redirect($this->createAbsoluteUrl('bolsa/pedido',array(
                            'id'=>$orden->id,
                            'admin' => $admin,
                            'user' => $usuario,
                                ),'http'));	 
		}
		 
	}
        
	public function clonarDireccion($direccion, $facturacion = false){
		if($facturacion)
			$dirEnvio=new DireccionFacturacion;	
		else
			$dirEnvio = new DireccionEnvio;
					
		$dirEnvio->nombre = $direccion->nombre;
		$dirEnvio->apellido = $direccion->apellido;
		$dirEnvio->cedula = $direccion->cedula;
		$dirEnvio->dirUno = $direccion->dirUno;
		$dirEnvio->dirDos = $direccion->dirDos;
		$dirEnvio->telefono = $direccion->telefono;
		$dirEnvio->ciudad_id = $direccion->ciudad_id;
		$dirEnvio->provincia_id = $direccion->provincia_id;
		$dirEnvio->codigo_postal_id = $direccion->codigo_postal_id;
		$dirEnvio->pais = $direccion->pais;	
		$dirEnvio->save();
		return $dirEnvio;
	}
	public function hacerCompra($bolsa_id, $order_id){
            $productosBolsa = BolsaHasProductotallacolor::model()->findAllByAttributes(array('bolsa_id'=>$bolsa_id));	
            $hoy = new DateTime();
            
            // añadiendo a orden producto
            foreach($productosBolsa as $producto){
                $prorden = new OrdenHasProductotallacolor;
                $prorden->tbl_orden_id = $order_id;
                $prorden->preciotallacolor_id = $producto->preciotallacolor_id;
                $prorden->cantidad = $producto->cantidad;
                $prorden->look_id = $producto->look_id;
                $prtc = Preciotallacolor::model()->findByPk($producto->preciotallacolor_id); // tengo preciotallacolor
                $precio = Precio::model()->findByAttributes(array('tbl_producto_id'=>$prtc->producto_id));
                $prorden->precio = $precio->precioDescuento;

                /* Revisar si cumple con el tiempo de validez del PS y
                 *  agregar los datos referentes a la comision en la orden
                 */
                
                //Solo los productos que esten en un look                
                if($producto->look_id > 0){ 
                    
                    $agregado = new DateTime($producto->added_on);
                    $diferencia = $hoy->diff($agregado)
                                      ->days; //Dias transcurridos desde que se agrego

                    $lookActual = Look::model()->findByPk($producto->look_id);
                    $personalShopper = $lookActual->user->profile;
                    
                    //si cumple, marcarlo para pagar comision
                    if($diferencia <= $personalShopper->tiempo_validez){                            
                            $prorden->comision = $personalShopper->comision;
                            $prorden->tipo_comision = $personalShopper->tipo_comision;
                            $prorden->status_comision = OrdenHasProductotallacolor::STATUS_PENDIENTE;                            
                        }
                }

                $prorden->save();
                            //listo y que repita el proceso
            }
            //descontando del inventario
            foreach($productosBolsa as $producto){
                    $uno = Preciotallacolor::model()->findByPk($producto->preciotallacolor_id);
                    $cantidadNueva = $uno->cantidad - $producto->cantidad; // lo que hay menos lo que se compró
                    Preciotallacolor::model()->updateByPk($producto->preciotallacolor_id, array('cantidad'=>$cantidadNueva));
                    // descuenta y se repite									
            }
            // para borrar los productos de la bolsa								
            foreach($productosBolsa as $producto){
                    $producto->delete();															
            }

	}
	/*
	 * 
	 * */
	public function actionComprar2()
	{
			if (Yii::app()->request->isPostRequest) // asegurar que viene en post
		 {
		 	$respCard = "";
		 	$usuario = Yii::app()->user->id; 
			$bolsa = Bolsa::model()->findByAttributes(array('user_id'=>$usuario));
			
			if($_POST['tipoPago']==1 || $_POST['tipoPago']==4 || $_POST['tipoPago']==2){ // transferencia o MP
				
				if($_POST['tipoPago']==2)
				{
					$detalle = Detalle::model()->findByPk($_POST['idDetalle']); // si viene de tarjeta de credito trae ya el detalle listo
					
				}
				else
				{
					$detalle = new Detalle;
				}
			
				if($detalle->save()){
					
					$pago = new Pago;
					$pago->tipo = $_POST['tipoPago']; // trans
					$pago->tbl_detalle_id = $detalle->id;
					
					if($pago->save()){
					
					// clonando la direccion
					$dir1 = Direccion::model()->findByAttributes(array('id'=>$_POST['idDireccion'],'user_id'=>$usuario));
					$dirEnvio = new DireccionEnvio;
					
					$dirEnvio->nombre = $dir1->nombre;
					$dirEnvio->apellido = $dir1->apellido;
					$dirEnvio->cedula = $dir1->cedula;
					$dirEnvio->dirUno = $dir1->dirUno;
					$dirEnvio->dirDos = $dir1->dirDos;
					$dirEnvio->telefono = $dir1->telefono;
					$dirEnvio->ciudad_id = $dir1->ciudad_id;
					$dirEnvio->provincia_id = $dir1->provincia_id;
					$dirEnvio->pais = $dir1->pais;
					
					if(isset($_POST['id_transaccion']) && $_POST['tipoPago'] == 4){ // Pago con Mercadopago
						$detalle->nTransferencia = $_POST['id_transaccion'];
						$detalle->nombre = $dirEnvio->nombre.' '.$dirEnvio->apellido;
						$detalle->cedula = $dirEnvio->cedula;
						$detalle->monto = $_POST['total'];
						$detalle->fecha = date("Y-m-d H:i:s");
						$detalle->banco = 'Mecadopago';
						
						$detalle->estado = 0;
						
						$detalle->save();
					}
					
					
						if($dirEnvio->save()){
							// ya esta todo para realizar la orden
							
							$orden = new Orden;
							
							$orden->subtotal = $_POST['subtotal'];
							//$orden->descuento = $_POST['descuento'];
							$orden->descuento = 0;
							$orden->envio = $_POST['envio'];
							$orden->iva = $_POST['iva'];
							//$orden->descuentoRegalo = 0;
							if(isset($_POST['descuentoRegalo'])){
								if($_POST['descuentoRegalo']>0)
	                            	$orden->descuentoRegalo = $_POST['descuentoRegalo'];
								else
	                            	$orden->descuentoRegalo = 0;
                            }
							$orden->total = $_POST['total'];
							$orden->seguro = $_POST['seguro'];
							$orden->fecha = date("Y-m-d H:i:s"); // Datetime exacto del momento de la compra 
							$orden->estado = 1; // en espera de pago
							$orden->bolsa_id = $bolsa->id; 
							$orden->user_id = $usuario;
							$orden->pago_id = $pago->id;
							$orden->detalle_id = $detalle->id;
							$orden->direccionEnvio_id = $dirEnvio->id;
							$orden->tipo_guia = $_POST['tipo_guia'];
							$orden->peso = $_POST['peso'];
							
							if($detalle->nTarjeta!="") // Pagó con TDC
							{
								$orden->estado = 3; // Estado: Pago Confirmado
							}
							
							$okk = round($_POST['total'], 2);
							$orden->total = $okk;
							
							if($orden->save()){
								$detalle->orden_id=$orden->id;
								$detalle->save();
								if(isset($_POST['usar_balance']) && $_POST['usar_balance'] == '1'){
								$balance_usuario=str_replace(',','.',Profile::model()->getSaldo(Yii::app()->user->id));	
									//$balance_usuario = Yii::app()->db->createCommand(" SELECT SUM(total) as total FROM tbl_balance WHERE user_id=".Yii::app()->user->id." GROUP BY user_id ")->queryScalar();
									if($balance_usuario > 0){
										$balance = new Balance;
										if($balance_usuario >= $_POST['total']){
											/*$orden->descuento = $_POST['total'];
											$orden->total = 0;
											$orden->estado = 2; // en espera de confirmación*/
											$orden->estado = 3; 
											$balance->total = $_POST['total']*(-1);
											$detalle->monto=$_POST['total'];
										}else{
											//$orden->descuento = $balance_usuario;
											//$orden->total = $_POST['total'] - $balance_usuario;
											
											$orden->estado = 7; 
											$balance->total = $balance_usuario*(-1);
											$detalle->monto=$balance_usuario;
											
										}
										
										if($orden->save()){
											
											$detalle->comentario="Prueba de Saldo";
											$detalle->estado=1;
											$detalle->orden_id=$orden->id;
											if($detalle->save()){
												$balance->orden_id = $orden->id;
												$balance->user_id = $usuario;
												$balance->tipo = 1;
												$balance->total=round($balance->total,2);
												
												$balance->save();
												
												$pago->tipo = 3; // trans
												
												$pago->save();
												
												
											}
										}
										
										//$balance->total = $orden->descuento*(-1);
										
											
									}
								}
								
								$productosBolsa = BolsaHasProductotallacolor::model()->findAllByAttributes(array('bolsa_id'=>$bolsa->id));	
								
								// añadiendo a orden producto
								foreach($productosBolsa as $prod)
								{
									$prorden = new OrdenHasProductotallacolor;
									$prorden->tbl_orden_id = $orden->id;
									$prorden->preciotallacolor_id = $prod->preciotallacolor_id;
									$prorden->cantidad = $prod->cantidad;
									$prorden->look_id = $prod->look_id;
									
									$prtc = Preciotallacolor::model()->findByPk($prod->preciotallacolor_id); // tengo preciotallacolor
									$precio = Precio::model()->findByAttributes(array('tbl_producto_id'=>$prtc->producto_id));
									
									if($prod->look_id == 0){ // no es look
										$prorden->precio = $precio->precioDescuento;
									}
									else{
										$look = Look::model()->findByPk($prod->look_id);
										
										if(isset($look))
											$prorden->precio = $look->getPrecio(false);										
										
									}
									
									if($prorden->save()){
										//listo y que repita el proceso
									}
								}
								
								//descontando del inventario
								foreach($productosBolsa as $prod)
								{
									$uno = Preciotallacolor::model()->findByPk($prod->preciotallacolor_id);
									$cantidadNueva = $uno->cantidad - $prod->cantidad; // lo que hay menos lo que se compró
									
									Preciotallacolor::model()->updateByPk($prod->preciotallacolor_id, array('cantidad'=>$cantidadNueva));
									// descuenta y se repite									
								}
								
								
								// para borrar los productos de la bolsa								
								foreach($productosBolsa as $prod)
								{
									$prod->delete();															
								}
								
								// agregar cual fue el usuario que realizó la compra para tenerlo en la tabla estado
								// se agrega este estado en el caso de que no se haya pagado por TDC
								if($detalle->nTarjeta=="")
								{
									$estado = new Estado;
									
									$estado->estado = 1;
									$estado->user_id = $usuario;
									$estado->fecha = date("Y-m-d");
									$estado->orden_id = $orden->id;
									
									if($estado->save())
										echo "";
								}
								else // si pago con tarjeta
								{
									
									$estado = new Estado;
									
										$estado->estado = 1;
										$estado->user_id = $usuario;
										$estado->fecha = date("Y-m-d");
										$estado->orden_id = $orden->id;
										
										if($estado->save())
											{
												// otro estado de una vez ya que ya se pagó el dinero 
												$estado = new Estado;
									
												$estado->estado = 3;
												$estado->user_id = $usuario;
												$estado->fecha = date("Y-m-d");
												$estado->orden_id = $orden->id;
												
												if($estado->save())
												{
													$detalle->orden_id = $orden->id;
													$detalle->save();
												}
													
												
											}// estado
									
								}
								
								// Generar factura
								$factura = new Factura;
								$factura->fecha = date('Y-m-d');
								$factura->direccion_fiscal_id = $_POST['idDireccion'];  // esta direccion hay que cambiarla después, el usuario debe seleccionar esta dirección durante el proceso de compra
								$factura->direccion_envio_id = $_POST['idDireccion'];
								$factura->orden_id = $orden->id;
								$factura->save();
								
								// Enviar correo con resumen de la compra
								$user = User::model()->findByPk($usuario);
								$message            = new YiiMailMessage;
						           //this points to the file test.php inside the view path
						        $message->view = "mail_compra";
								$subject = 'Tu compra en Personaling';
						        $params              = array('subject'=>$subject, 'orden'=>$orden);
						        $message->subject    = $subject;
						        $message->setBody($params, 'text/html');
						        $message->addTo($user->email);
								$message->from = array('operaciones@personaling.com' => 'Tu Personal Shopper Online');
						        //$message->from = 'Tu Personal Shopper Online <operaciones@personaling.com>\r\n';   
						        Yii::app()->mail->send($message);
								
							// cuando finalice entonces envia id de la orden para redireccionar
							echo CJSON::encode(array(
								'status'=> 'ok',
								'orden'=> $orden->id,
								'total'=> $orden->total,
								'respCard' => $respCard,
								'descuento'=>$orden->descuento,
								'url'=> $this->createAbsoluteUrl('bolsa/pedido',array('id'=>$orden->id),'http'),
							));
							
							
							}else{ //orden
								echo CJSON::encode(array(
								'status'=> 'error',
								'error'=> $orden->getErrors(),
							));
							}
						}//direccion de envio
					} // pago
				}// detalle
			}// transferencia
			
			// detalle de pago (caso transferencia todo vacio)
			// tipo de pago y copiar direccion envio
			// realizar la orden
			// mover los productos
			// quitarlos de bolsa tiene producto
			
		 }

		 
	}

	/*
	 * 
	 * */
	public function actionPedido($id)
	{  
            $admin = Yii::app()->getSession()->contains("bolsaUser");                    
                
            /*ID del usuario propietario de la bolsa*/
            $usuario = $admin ? Yii::app()->getSession()->get("bolsaUser")
                                : Yii::app()->user->id;
            
            $orden = Orden::model()->findByPk($id);
			$orden->setActualizadas();
            //$pago = Pago::model()->findByPk($orden->pago_id);
            if(!$admin){
                ShoppingMetric::registro(ShoppingMetric::STEP_PEDIDO,array("orden_id"=>$orden->id));
                $addItem = "";
                foreach ($orden->ohptc as $producto){

                        $category_product = CategoriaHasProducto::model()->findByAttributes(array('tbl_producto_id'=>$producto->preciotallacolor->producto->id));
                        $category = Categoria::model()->findByPk($category_product->tbl_categoria_id);
                        $precio = Precio::model()->findByAttributes(array('tbl_producto_id'=>$producto->preciotallacolor->producto_id));

                        $addItem .= "
                                ga('ec:addProduct', {               // Provide product details in an productFieldObject.
                                  'id': '".$producto->preciotallacolor->sku."',                   // Product ID (string).
                                  'name': '".addslashes($producto->preciotallacolor->producto->nombre)."', // Product name (string).
                                  'category': '".addslashes($category->nombre)."',            // Product category (string).
                                  'brand': '".addslashes($producto->preciotallacolor->producto->mymarca->nombre)."',                // Product brand (string).
                                  'variant': '".$producto->preciotallacolor->mycolor->valor."',               // Product variant (string).
                                  'price': '".$producto->precio."',                 // Product price (currency).
                                  'quantity': ".$producto->cantidad."                     // Product quantity (number).
                                });
                        ";

                }
                Yii::app()->clientScript->registerScript('metrica_analytics',$addItem."

                ga('ec:setAction', 'purchase', {
                  'id': '".$orden->id."',
                  'affiliation': 'Personaling',
                  'revenue': '".$orden->total."',
                  'tax': '".$orden->iva."',
                  'shipping': '".$orden->envio."',
                 // 'coupon': 'SUMMER2013'    // User added a coupon at checkout.
                });

                ga('send', 'pageview'); 

                ");	
                // var _gaq = _gaq || [];
                //_gaq.push(['_setAccount', 'UA-1015357-44']);
                //_gaq.push(['_trackPageview']);
                //(function() {
                //    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                //    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                //    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
                //  })(); 	
            }
            $this->render('pedido',array(
                 'orden'=>$orden,
                 'admin'=>$admin,
                 'user'=>$usuario,
                 'voucher'=>Yii::app()->session['voucher'],
                 'referencia'=>Yii::app()->session['referencia'],
                ));
	}

	/*
	 *	Error 
	 * */
	/*
	public function actionError($id)
	{
		if($id==1)
		{	
			$msj = 'La tarjeta que intentó usar ya expiró.';
			$this->render('error',array('mensaje'=>$msj));
		}
		else if($id==2)
		{	
			$msj = 'El número de tarjeta que introdujó no es un número válido.';
			$this->render('error',array('mensaje'=>$msj));
		}	
		else if($id==3)
		{
			$msj = 'Error de autenticación.';
			$this->render('error',array('mensaje'=>$msj));
		}
		else if($id==4)
		{
			$msj = 'Error interno.';
			$this->render('error',array('mensaje'=>$msj));
		}	
		else if($id==5)
		{
			$msj = 'La tarjeta ha sido rechazada por el banco';
			$this->render('error',array('mensaje'=>$msj));
		}	
		
		//$orden = Orden::model()->findByPk($id);
		//$pago = Pago::model()->findByPk($orden->pago_id);
		//$this->render('pedido',array('orden'=>$orden));
	}
*/
        
        /*
         * CODIGOS DE ERROR:
         * 001 - Error con datos errados enviados desde Aztive
         * otros - Error pagando con Banking Card o Paypal de Aztive
         */
	public function actionError(){
		
            $codigo = 	isset($_GET['codigo']) ? $_GET['codigo'] : "000";
            $mensaje = 	$_GET['mensaje'];
            
            if($codigo != "000"){                
                
                
            }else{
                
                if ($mensaje=="The CardNumber field is not a valid credit card number.")
                    $mensaje = "El número de tarjeta que introdujó no es un número válido.";
            }                
                
            $this->render('error',array(
                'mensaje'=>$mensaje,
                'codigo'=>$codigo,
                    ));
	}
	/*
	 * 
	 * */
	public function actionCpago()
	{
		if (Yii::app()->request->isPostRequest) // asegurar que viene en post
		{
			$usuario = Yii::app()->user->id; 
			
			
			$detPago = new Detalle;
			$pago=new Pago;	
			$detPago->nombre = $_POST['nombre'];
			$detPago->nTransferencia = $_POST['numeroTrans'];
			$detPago->comentario = $_POST['comentario'];
			$detPago->banco = $_POST['banco'];
			$nf = new NumberFormatter("es_VE", NumberFormatter::DECIMAL);
			$detPago->monto = $nf->parse($_POST['monto']);
			$detPago->cedula = $_POST['cedula'];
			$detPago->estado = 0; // defecto
			$detPago->orden_id = $_POST['idOrden'];
			$detPago->tipo_pago =  1;				
			$detPago->fecha = $_POST['ano']."-".$_POST['mes']."-".$_POST['dia']." ".date("H:i:s");
			
			if($detPago->save())
			{
				
					//$pago->tipo = 1; // trans
					//$pago->tbl_detalle_id = $detPago->id;
					//$pago->save();
				
					$orden = Orden::model()->findByAttributes(array('id'=>$_POST['idOrden']));
			
					
				$orden->estado = 2;	// se recibió los datos de pago por transferencia
				
				if($orden->save())
				{
					// agregar cual fue el usuario que realizó la compra para tenerlo en la tabla estado
					$estado = new Estado;
									
					$estado->estado = 2;
					$estado->user_id = $usuario;
					$estado->fecha = date("Y-m-d");
					$estado->orden_id = $orden->id;
					
					if($estado->save())
					{	
						$zoho = new ZohoSales;
						$zoho->updateStatus($orden->id); 

						Yii::app()->user->setFlash('success', 'Hemos recibido tu pago y está en espera de confirmación');
						echo "ok";	
					}

				 $user = User::model()->findByPk($usuario);
				 
				 $message = new YiiMailMessage;
	            //Opciones de Mandrill
	            $message->activarPlantillaMandrill();
	            
	            $subject = 'Tu Pago en Personaling';
	            $message->subject    = $subject;
	            $body = $this->renderPartial("//mail/verificar_pago", array(
	                "orden" => $orden), true);
	            
	            $message->setBody($body, 'text/html');                
	            $message->addTo($user->email);
	            
	            Yii::app()->mail->send($message);	
					
				}				
			}
			else {
				print_r($detPago->getErrors());
			}
			
		}
		
		
	}

	/*
	 * 
	 * */
	public function actionLimpiar()
	{
		
		if(isset($_POST['idBolsa'])){	
		
			$bolsahas = BolsaHasProductotallacolor::model()->findAllByAttributes(array('bolsa_id'=>$_POST['idBolsa']));
			$productos = array();
			$looks = array();
			
			foreach($bolsahas as $uno){
				$look_id = $uno->look_id;
				$uno->delete();

				// check si es el último producto de un look dentro de la bolsa
                if($look_id != 0){
                	$ultimo= BolsaHasProductotallacolor::model()->findByAttributes(array('bolsa_id'=>$_POST['idBolsa'], 'look_id'=>$look_id));
                	if(!$ultimo){ // se acaba de eliminar el último producto del look, agrego datos para analytics
                		$look = Look::model()->findByPk($look_id);
                		
                		$looks[] = array(
							'id' => $look->id,
							'name' => $look->title,
							'category' => 'Looks',
							'brand' => 'Personaling',
							'price' => $look->getPrecioDescuento(),
							'quantity' => 1,
                		);
                	}else{
                		$response['ultimo'] = 'false';
                	}
                }

				$category_product = CategoriaHasProducto::model()->findByAttributes(array('tbl_producto_id'=>$uno->preciotallacolor->producto->id));
                $category = Categoria::model()->findByPk($category_product->tbl_categoria_id);
                $precio = Precio::model()->findByAttributes(array('tbl_producto_id'=>$uno->preciotallacolor->producto->id));
				
				$productos[] = array(
					'id' => $uno->preciotallacolor->producto->id,
					'name' => $uno->preciotallacolor->producto->nombre,
					'category' => $category->nombre,
					'brand' => $uno->preciotallacolor->producto->mymarca->nombre,
					'variant' => $uno->preciotallacolor->mycolor->valor." ".$uno->preciotallacolor->mytalla->valor,
					'price' => $precio->precioImpuesto,
					'quantity' => 1
				);
			}
			
			echo json_encode(array(
				'status'=>'ok',
				'productos'=>$productos,
				'looks'=>$looks
			));
		
		}
	}


	/*
	 * modal
	 * */
	public function actionModal()
	{
		$tarjeta = new TarjetaCredito;
		
		$datos="";
		
		$datos=$datos."<div class='modal-header'>";
		$datos=$datos."Agregar datos de tarjeta de crédito";
    	$datos=$datos."</div>";
		
		$datos=$datos."<div class='modal-body'>";
		
		$datos=$datos.'<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-condensed">';
  		$datos=$datos.'<tr>';			
		$datos=$datos.'<th scope="col" colspan="3">&nbsp;</th>';
		$datos=$datos.'<th scope="col">Número</th>';		
		$datos=$datos.'<th scope="col">Nombre en la Tarjeta</th>';
		$datos=$datos.'<th scope="col">Fecha de Vencimiento</th>';
		$datos=$datos.'</tr>';	
		
		$tarjetas = TarjetaCredito::model()->findAllByAttributes(array('user_id'=>Yii::app()->user->id));
		
		if(isset($tarjetas))
		{
			foreach($tarjetas as $cada){
				
				$datos=$datos.'<tr>';
				$datos=$datos.'<td><input class="radioss" type="radio" name="optionsRadios" id="tarjeta" value="'.$cada->id.'" ></td>';
				$datos=$datos.'<td><i class="icon-picture"></i></td>';
				$datos=$datos.'<td>Mastercard</td>';
				
				$rest = substr($cada->numero, -4);
				
				$datos=$datos.'<td>XXXX XXXX XXXX '.$rest.'</td>';
				$datos=$datos.'<td>'.$cada->nombre.'</td>';
				$datos=$datos.'<td>'.$cada->vencimiento.'</td>';
				$datos=$datos.'</tr>';
			}	
			$datos=$datos.'</table>';
		}
		else
			{
				$datos=$datos.'<tr>';
				$datos=$datos.'<td>No tienes tarjetas de credito asociadas.</td>';
				$datos=$datos.'</tr>';
				$datos=$datos.'</table>';
			}	
			
		
		$datos=$datos.'<button type="button" id="nueva" class="btn btn-info btn-small" data-toggle="collapse" data-target="#collapseOne"> Agregar una nueva tarjeta </button>';
    	
		$datos=$datos.'<div class="collapse" id="collapseOne">';
		$datos=$datos.'<form class="">';
        $datos=$datos.'<h5 class="braker_bottom">Nueva tarjeta de crédito</h5>';
		
		$datos=$datos.'<div class="control-group">';
        $datos=$datos.'<div class="controls">';     
		$datos=$datos. CHtml::activeTextField($tarjeta,'nombre',array('id'=>'nombre','class'=>'span5','placeholder'=>'Nombre impreso en la tarjeta'));
        $datos=$datos.'<div style="display:none" class="help-inline"></div>';  
		$datos=$datos.'</div></div>';
    	
  		$datos=$datos.'<div class="control-group">';
        $datos=$datos.'<div class="controls">';     
		$datos=$datos. CHtml::activeTextField($tarjeta,'numero',array('id'=>'numero','class'=>'span5','placeholder'=>'Número de la tarjeta'));
        $datos=$datos.'<div style="display:none" class="help-inline"></div>';  
		$datos=$datos.'</div></div>';
  
  		$datos=$datos.'<div class="control-group">';
        $datos=$datos.'<div class="controls">';     
		$datos=$datos. CHtml::activeTextField($tarjeta,'codigo',array('id'=>'codigo','class'=>'span2','placeholder'=>'Código de seguridad'));
        $datos=$datos.'<div style="display:none" class="help-inline"></div>';  
		$datos=$datos.'</div></div>';
  
  		$datos=$datos.'<div class="control-group">';
		$datos=$datos.'<label class="control-label required">Fecha de Vencimiento</label>';
        $datos=$datos.'<div class="controls">';     
	  	$datos=$datos. CHtml::dropDownList('mes','',array('Mes'=>'Mes','01'=>'01','02'=>'02','03'=>'03','04'=>'04','05'=>'05','06'=>'06','07'=>'07','08'=>'08','09'=>'09','10'=>'10','11'=>'11','12'=>'12'),array('id'=>'mes','class'=>'span1','placeholder'=>'Mes'));
        $datos=$datos. CHtml::dropDownList('ano','',array('Ano'=>'Año','2013'=>'2013','2014'=>'2014','2015'=>'2015','2016'=>'2016','2017'=>'2017','2018'=>'2018','2019'=>'2019'),array('id'=>'ano','class'=>'span1','placeholder'=>'Año'));
        $datos=$datos.'<div style="display:none" class="help-inline"></div>';  
		$datos=$datos.'</div></div>';
		
		$datos=$datos."<div class='control-group'>";
		$datos=$datos."<div class='controls'>";
		$datos=$datos. CHtml::activeTextField($tarjeta,'direccion',array('id'=>'direccion','class'=>'span5','placeholder'=>'Dirección')) ;
		$datos=$datos."<div style='display:none' class='help-inline'></div>";
		$datos=$datos."</div>";
		$datos=$datos."</div>";
		
		$datos=$datos."<div class='control-group'>";
		$datos=$datos."<div class='controls'>";
		$datos=$datos. CHtml::activeTextField($tarjeta,'ciudad',array('id'=>'ciudad','class'=>'span5','placeholder'=>'Ciudad'));
        $datos=$datos."<div style='display:none' id='RegistrationForm_email_em_' class='help-inline'></div>";
		$datos=$datos."</div>";
		$datos=$datos."</div>";
		
		$datos=$datos."<div class='control-group'>";
		$datos=$datos."<div class='controls'>";
		$datos=$datos. CHtml::activeTextField($tarjeta,'estado',array('id'=>'estado','class'=>'span5','placeholder'=>'Estado'));
        $datos=$datos."<div style='display:none' id='RegistrationForm_email_em_' class='help-inline'></div>";
		$datos=$datos."</div>";
		$datos=$datos."</div>";
		
		$datos=$datos."<div class='control-group'>";
		$datos=$datos."<div class='controls'>";
		$datos=$datos. CHtml::activeTextField($tarjeta,'zip',array('id'=>'zip','class'=>'span2','placeholder'=>'Código Postal'));
        $datos=$datos."<div style='display:none' id='RegistrationForm_email_em_' class='help-inline'></div>";
		$datos=$datos."</div>";
		$datos=$datos."</div>";
		
		$datos=$datos."</div>"; // modal body
		
		$datos=$datos."<div class='modal-footer'>";
		
		$datos=$datos."<div class=''><a id='boton_pago_tarjeta' onclick='enviarTarjeta()' class='pull-left btn-large btn btn-danger'> Pagar </a></div>";
    	$datos=$datos."</form>";
		$datos=$datos."</div>";
		
		$datos=$datos."<input type='hidden' id='idTarjeta' value='0' />"; // despues aqui se mandaria el id si la persona escoge una tarjeta que ya utilizó
		
		$datos=$datos."</div>"; // footer
		
		$datos=$datos."<script>";
		$datos=$datos."$(document).ready(function() {";
		
			$datos=$datos.'$("#nueva").click(function() { ';
				$datos=$datos."$('.table').find('input:radio:checked').prop('checked',false);";
				$datos=$datos.'$("#tarjeta").prop("checked", false);';
				$datos=$datos.'$("#idTarjeta").val(0);'; // lo regreso a 0 para que sea tarjeta nueva
			$datos=$datos.'});';
		
			$datos=$datos.'$(".radioss").click(function() { ';
				$datos=$datos."var numero = $(this).attr('value');";
				//$datos=$datos." alert(numero); ";
        		$datos=$datos.'$("#idTarjeta").val(numero);';
        	$datos=$datos."});";
		
		$datos=$datos."});"; 
		$datos=$datos."</script>"; 
		
		
		echo $datos;
		
		
	}
        
        /**
         * 1. Paso
	 * Paso de autenticacion para la compra de giftcard
	 */
	public function actionAuthGC()
	{
            // que esté logueado para llegar a esta acción
            
		if (!Yii::app()->user->isGuest) { 
                    //y que tenga giftcards en la bolsa
                    $giftcard = BolsaGC::model()->findByAttributes(array("user_id" => Yii::app()->user->id));
                    if(!$giftcard){
                        $this->redirect(array("giftcard/comprar"));
                    }
			
			$model=new UserLogin;
			$user = User::model()->notsafe()->findByPk(Yii::app()->user->id);
			
			if(isset($_POST['UserLogin']))
			{
				$model->attributes=$_POST['UserLogin'];
				// validate user input and redirect to previous page if valid
				
				if($model->validate()) {
                                    
					//Si esta activo - ir al siguiente paso
					if($user->status == 1){
						$this->redirect(array('bolsa/pagoGC'));
					}else{
						Yii::app()->user->setFlash('error',Yii::t('contentForm', 'You must validate your account before continuing. We\'ve sent a new validation link')."<strong>".$user->email."</strong>"); 
						$activation_url = $this->createAbsoluteUrl('/user/activation/activation',array("activkey" => $user->activkey, "email" => $user->email));
		
						$message            = new YiiMailMessage;
						$message->view = "mail_template";
						$subject = 'Activa tu cuenta en Personaling';
						#$body = Yii::t('contentForm','You are receiving this email because you have requested a new link to validate your account. You can continue by clicking on the link below:<br/>').$activation_url;
						$body = Yii::t('contentForm','You are receiving this email because you have requested a new link to validate your account. You can continue by clicking on the link below:<br/><br/>{{link}}<br/>', array('{{link}}'=>$activation_url));
						$params              = array('subject'=>$subject, 'body'=>$body);
						$message->subject    = $subject;
						$message->setBody($params, 'text/html');
						$message->addTo($user->email);
						$message->from = array('info@personaling.com' => 'Tu Personal Shopper Online');
						Yii::app()->mail->send($message);
						$this->refresh();
					}
				}else{
					$this->render('authGC',array('model'=>$model));
					//Yii::app()->user->setFlash('error',UserModule::t("La contraseña es incorrecta")); 
				}	
			}else{
                            ShoppingMetric::registro(ShoppingMetric::STEP_LOGIN,array("tipo_compra"=>ShoppingMetric::TIPO_GIFTCARD));
                            // si no viene del formulario. O bien viene de la pagina anterior
                            $this->render('authGC',array('model'=>$model));
			}
		} else{
			// no va a llegar nadie que no esté logueado
                    
                        Yii::app()->user->setReturnUrl($this->createUrl('bolsa/authGC'));
                        Yii::app()->user->setFlash('error',Yii::t("contentForm", "¡La sesión ha expirado, intenta tu compra nuevamente!"));
                        //Redirigir a login
                        $this->redirect(array('/user/login'));                        
                    
		}
	}//fin
        
        
        /**
	 * 2. Paso
	 * Paso para escoger el metodo de pago en la compra de giftcard
         * (solo tarjeta actualmente 04/12/2013)
	 */	
        public function actionPagoGC()
        {

            if (Yii::app()->user->isGuest){
                //Redirigir a login
                Yii::app()->user->setReturnUrl($this->createUrl('bolsa/authGC'));
                Yii::app()->user->setFlash('error',Yii::t("contentForm", "¡La sesión ha expirado, intenta tu compra nuevamente!"));                              
                $this->redirect(array('/user/login'));                        
            }
            
            $tarjeta = new TarjetaCredito; 

            if(isset($_POST['tipo_pago'])){
                
                if($_POST['tipo_pago'] == 2 && isset($_POST['ajax']) && $_POST['ajax']==='tarjeta-form')
                {
                        echo CActiveForm::validate($_POST['TarjetaCredito']);
                        Yii::app()->end();
                }
                
                Yii::app()->getSession()->add('tipoPago',$_POST['tipo_pago']);
                 

                if($_POST['tipo_pago'] == 2){ // pago de tarjeta de credito
                    

                    
//                    foreach(Yii::app()->getSession() as $name=>$value){
//                        echo "<br>".$name." - ".$value;
//                    }
//                    Yii::app()->end();
                    //$this->redirect(array('bolsa/confirmarGC'));

                        
                    $usuario = Yii::app()->user->id; 

                    $tarjeta->nombre = $_POST['TarjetaCredito']['nombre'];
                    $tarjeta->numero = $_POST['TarjetaCredito']['numero'];
                    $tarjeta->codigo = $_POST['TarjetaCredito']['codigo'];

                    /*$tarjeta->month = $_POST['mes'];
                    $tarjeta->year = $_POST['ano'];*/

                    $tarjeta->month = $_POST['TarjetaCredito']['month'];
                    $tarjeta->year = $_POST['TarjetaCredito']['year'];
                    $tarjeta->ci = $_POST['TarjetaCredito']['ci'];
                    $tarjeta->direccion = $_POST['TarjetaCredito']['direccion'];
                    $tarjeta->ciudad = $_POST['TarjetaCredito']['ciudad'];
                    $tarjeta->zip = $_POST['TarjetaCredito']['zip'];
                    $tarjeta->estado = $_POST['TarjetaCredito']['estado'];
                    $tarjeta->user_id = $usuario;		

                    if($tarjeta->save())
                    {
                            //$tipoPago = $_POST['tipo_pago'];

                            Yii::app()->getSession()->add('idTarjeta',$tarjeta->id);
                            //$this->render('confirmar',array('idTarjeta'=>$tarjeta->id));
                            $this->redirect(array('bolsa/confirmarGC'));
                    }
                    else
                            //var_dump($tarjeta->getErrors());
                    echo CActiveForm::validate($tarjeta);

                }
                else {
                    //$this->render('confirmar');
                    $this->redirect(array('bolsa/confirmarGC'));
                }

            }
            else{
                //$tarjeta = new TarjetaCredito;
                $metric = new ShoppingMetric();
                $metric->user_id = Yii::app()->user->id;
                $metric->step = ShoppingMetric::STEP_PAGO;
                $metric->tipo_compra = ShoppingMetric::TIPO_GIFTCARD;
                $metric->save();

                //Buscar todas las giftcards de la bolsa del usuario y totalizar
//                $giftcards = BolsaGC::model()->findAllByAttributes(array("user_id" => Yii::app()->user->id));
//
//                $total = 0;
//                foreach($giftcards as $gift){
//                    $total += $gift->monto;
//                }
//                
                
                //Comprobar que hay giftcards en la bolsa - si no, redirigir a la primera página
                $giftcard = BolsaGC::model()->findByAttributes(array("user_id" => Yii::app()->user->id));
                
                if(!$giftcard){
                    $this->redirect(array("giftcard/comprar"));
                }
                
                
                $total = $giftcard->monto;
                Yii::app()->getSession()->add('total',$total); 

                $this->render('pagoGC',array(
                    'tarjeta'=>$tarjeta,                       
                    'total' => $total,
                        ));		
            }

        }        
        
        /**
	 * 3. Paso
	 * Paso para ver el resumen de la compra y hacer el pago
         * 
	 */
        public function actionConfirmarGC()
        {                
            
            if (Yii::app()->user->isGuest){
                //Redirigir a login
                Yii::app()->user->setReturnUrl($this->createUrl('bolsa/authGC'));
                Yii::app()->user->setFlash('error',Yii::t("contentForm", "¡La sesión ha expirado, intenta tu compra nuevamente!"));                              
                $this->redirect(array('/user/login'));                        
            }
            
            $metric = new ShoppingMetric();
            $metric->user_id = Yii::app()->user->id;
            $metric->step = ShoppingMetric::STEP_CONFIRMAR;
            $metric->tipo_compra = ShoppingMetric::TIPO_GIFTCARD;
            $metric->save();
                
            //por los momentos solo la primera giftcard que encuentre
            $giftcard = BolsaGC::model()->findByAttributes(array("user_id" => Yii::app()->user->id));

            if(!$giftcard){
                $this->redirect(array("giftcard/comprar"));
            }


            $monto = Yii::app()->getSession()->get('total');

             /*
             * Para pago con tarjeta y paypal
             */
            $nombreProducto = "GiftCard Personaling";

            $tipo_pago = Yii::app()->getSession()->get('tipoPago');
            //Tipos de pago aceptados por Aztive
            $idPagoAztive = $tipo_pago == 5? 8:5; 

            $optional = array(                        
                'name'          => 'Personaling Enterprise S.L.',
                'product_name'  => $nombreProducto,                             
            );   
			$usu=User::model()->findByPk( Yii::app()->user->id );                                 
            $cData = array(
                "src" => 2, //origen de la compra, 1-Normal, 2-GC
                'nombreUsuario'=>$usu->profile->first_name." ".$usu->profile->last_name, 
            );

            $cData = CJSON::encode($cData);
            $pago = new AzPay();
			
			 $orderID = "G" . Yii::app()->user->id."F";   
			
            $urlAztive = $pago->AztivePay($monto, $idPagoAztive, $orderID,
                    $idPagoAztive==8?null:null, $optional, $cData);   



            $this->render('confirmarGC',array(
                'idTarjeta'=> Yii::app()->getSession()->get('idTarjeta'),
                'monto'=> $monto,
                'giftcard' => $giftcard,
                'urlAztive' => $urlAztive,
                ));
        }
        
        /**
         * Para pasar la tarjeta y cobrar
         */
        public function actionComprarGC()
		{
			
			$global;
			
            if (Yii::app()->request->isPostRequest){ // asegurar que viene en post
                
                $codigo_randon = Yii::app()->getSession()->get('codigo_randon');
                if ($codigo_randon == $_POST['codigo_randon'])
                        Yii::app()->end();
                
                Yii::app()->getSession()->add('codigo_randon',$codigo_randon);
                
                $userId = Yii::app()->user->id;                 
                $tipoPago = Yii::app()->getSession()->get('tipoPago');	
                $total = Yii::app()->getSession()->get('total');
                
                switch ($tipoPago) {
                    case 1:
                        break;
                    case 2: // TARJETA DE CREDITO
                        $tarjetaId = Yii::app()->getSession()->get('idTarjeta');
                        $resultado = $this->cobrarTarjeta($tarjetaId, $userId, $total);
						$global = $resultado;
						
//                        if (true)
                        if ($resultado['status'] == "ok")
                        {
                            $tarjeta = TarjetaCredito::model()->findByPk($tarjetaId);
                            $detalle = new DetallePago();
                            $detalle->nTarjeta = $tarjeta->numero;
                            $detalle->nTransferencia = $resultado["idOutput"];
                            $detalle->nombre = $tarjeta->nombre;
                            $detalle->cedula = $tarjeta->ci;
                            $detalle->monto = $total;
                            $detalle->fecha = date("Y-m-d H:i:s");
                            $detalle->banco = 'TDC';
                            $detalle->estado = 1; // aceptado
                            
                            if(!$detalle->save()){
                                    Yii::trace('UserID: '.$userId.' Error al guardar detalle:'.print_r($detalle->getErrors(),true), 'registro');
                            }
                            
                            $orden = new OrdenGC;                            
                            $orden->estado = Orden::ESTADO_CONFIRMADO;
                            $orden->fecha = date("Y-m-d H:i:s"); // Datetime exacto del momento de la compra 
                            $orden->total = $total;
                            $orden->user_id = $userId;
                           
                            if (!($orden->save())){
                                    echo CJSON::encode(array(
                                                    'status'=> 'error',
                                                    'error'=> $orden->getErrors(),
                                            ));
                                    Yii::trace('UserID: '.$userId.' Error al guardar la orden:'.print_r($orden->getErrors(),true), 'registro');	
                                    Yii::app()->end();

                            }	
                            //Pasar de la bolsa a las giftcards
                            $this->crearGC($userId, $orden->id);
                            
                            //Generar el detalle de pago
                            $detalle->orden_id = $orden->id;
                            $detalle->tipo_pago = 2;
                            $detalle->save();
                            
                        } else { 
                            $this->redirect($this->createAbsoluteUrl('bolsa/errorGC',array('codigo'=>$resultado['codigo'],'mensaje'=>$resultado['mensaje']),'http'));
                        }			
                        break;
                    case 3:			        
                        break;
                }

                // Enviar correo con resumen de la compra
//                $user = User::model()->findByPk($userId);
//                $message            = new YiiMailMessage;
//	        $message->view = "mail_compra";
//			$subject = 'Tu compra en Personaling';
//	        $params              = array('subject'=>$subject, 'orden'=>$orden);
//	        $message->subject    = $subject;
//	        $message->setBody($params, 'text/html');
//	        $message->addTo($user->email);
//			$message->from = array('operaciones@personaling.com' => 'Tu Personal Shopper Online');
//	        //$message->from = 'Tu Personal Shopper Online <operaciones@personaling.com>\r\n';   
//	        Yii::app()->mail->send($message);		
                
                //Ver resumen del pedido
                if($tipoPago == 2){ // tarjeta
            		Yii::app()->session['voucher'] = $global['voucher'];
					Yii::app()->session['referencia'] = $global['referencia'];
				}
				
                $this->redirect($this->createAbsoluteUrl('bolsa/pedidoGC',array('id'=>$orden->id),'http'));	
            }
		 
	}
        
        /*Pasar de la bolsa a generar las giftcards*/
        public function crearGC($userId, $ordenId){
            
            $giftcards = BolsaGC::model()->findAllByAttributes(array("user_id" => $userId));		
            $resumen="";
            foreach($giftcards as $gift){
                
                $model = new Giftcard;
                $model->monto = $gift->monto;
                $model->plantilla_url = $gift->plantilla_url;
                
                $model->estado = 2; //Activa
                $model->inicio_vigencia = date('Y-m-d');
                $now = date('Y-m-d', strtotime('now'));
                $model->fin_vigencia = date("Y-m-d", strtotime($now." + 1 year"));
                $model->comprador = $userId; 
                
                do{  

                    $model->codigo = Giftcard::generarCodigo();
                    $existe = Giftcard::model()->countByAttributes(array('codigo' => $model->codigo));                        

                }while($existe);
                
                $model->orden_id = $ordenId;
                
                $model->save();
                $gift->delete();
              
				
				
                //Enviar la giftcard por correo solo si se selecciono email al comprar
                if(Yii::app()->getSession()->get('entrega') == 2){
                	                	
                    $envio = new EnvioGiftcard();
                    $campos = Yii::app()->getSession()->get('envio');                    
                    
                    $envio->nombre = $campos["nombre"];
                    $envio->mensaje = $campos["mensaje"];
                    $envio->email = $campos["email"];
                                        
                    $saludo = "<strong>{$model->UserComprador->profile->first_name}</strong> te ha enviado una Gift Card como obsequio.";               

                    $personalMes = ""; 
                    
                    if($envio->mensaje != ""){
                        $personalMes = "<br/><br/><i>" . $envio->mensaje . "</i><br/>";
                    }
                                      
                    $message = new YiiMailMessage;
                    //Opciones de Mandrill
                    $message->activarPlantillaMandrill("plantilla-correos-no-footer");
                    $subject = 'Gift Card de Personaling';
                    
                    if(Yii::app()->language == "es_ve"){ 
                                        $body = "¡Hola <strong>{$envio->nombre}</strong>!<br><br> {$saludo} 
                        	                    <br/>".Yii::t('contentForm','Start enjoying your Gift Card in <a href="https://www.personaling.com.ve" title="Personaling">Personaling.com.ve</a> using it.')."
                        	                    <br/>
                                                (Para ver la Gift Card permite mostrar las imagenes de este correo) <br/><br/>";
                                        }
                                        else{
                                        $body = "¡Hola <strong>{$envio->nombre}</strong>!<br><br> {$saludo} 
                        	                    <br/>".Yii::t('contentForm','Start enjoying your Gift Card in <a href="https://www.personaling.es" title="Personaling">Personaling.es</a> using it.')."
                        	                    <br/>
                                                (Para ver la Gift Card permite mostrar las imagenes de este correo) <br/><br/>";	
                                        }

                    $body = $this->renderPartial("//mail/_giftcard",
                            array('body' => $body,'envio' => $envio,
                                'model'=> $model), true);
                    
                    $message->subject = $subject;
                    $message->setBody($body, 'text/html');
                    $message->addTo($envio->email);
                    Yii::app()->mail->send($message); 
                            
                    
//                    $message->view = "mail_giftcard";
//                    $params = array('subject' => $subject, 'body' => $body,'envio' => $envio, 'model'=> $model);
//                    $message->from = array('info@personaling.com' => 'Tu Personal Shopper Online');


                    $resumen.="<tr><td>Email</td><td>{$envio->email}</td><td>{$model->monto}</td><tr>";
                    
                }
                else{
                        $resumen.="<tr><td colspan='2' align='center' style='text-align:center'>Impresa</td><td>".Yii::t('contentForm','currSym')." {$model->monto}</td><tr>";
                }		
                
            }
            
            $this->actionSendSummary($resumen,$ordenId,$userId);
				                     

	}
	
	
	public function actionSendSummary($resumen,$ordenId,$userId){
                			
            $comprador=User::model()->findByPk($userId);
            $user=$comprador->profile;
            
            $message = new YiiMailMessage;                
            //Opciones de Mandrill
            $message->activarPlantillaMandrill();
            $subject = 'Tu compra de Gift Card de Personaling';
            $body = "¡Hola <strong>{$user->first_name}</strong>!<br/><br/>
                    Hemos procesado satisfactoriamente tu compra de Gift Card.";

            $body = $this->renderPartial("//mail/_giftcard_summary",
                            array( 'body' => $body,
                                'resumen' => $resumen, 'orden'=> OrdenGC::model()->findByPk($ordenId)),
                            true
                    );
            
            $message->subject = $subject;
            $message->setBody($body, 'text/html');
            
            $message->addTo($comprador->email);
            return Yii::app()->mail->send($message);


//            $message->view = "mail_giftcard_summary";
//            $message->from = array('info@personaling.com' => 'Tu Personal Shopper Online');
//            $params = array('subject' => $subject, 'body' => $body,'resumen' => $resumen, 'orden'=> OrdenGC::model()->findByPk($ordenId));


            
	}
        
        /**
         * Muestra el detalle de una compra de giftcard,
         * se puede imprimir la tarjeta
         */
        public function actionPedidoGC($id)
	{
		$orden = OrdenGC::model()->findByPk($id);
				
		$this->render('pedidoGC',array('orden'=>$orden,'voucher'=>Yii::app()->session['voucher'],'referencia'=>Yii::app()->session['referencia'],
										'tipoPago'=>Yii::app()->getSession()->get('tipoPago'))); 
	}
        
        /**
         * Para mostrar el Error con el pago de TDC en la compra de una GC
         */
        public function actionErrorGC(){ 
		
            $codigo = 	isset($_GET['codigo']) ? $_GET['codigo'] : "000";
            $mensaje = 	$_GET['mensaje'];
			
            if ($mensaje=="The CardNumber field is not a valid credit card number."){
                $mensaje = "El número de tarjeta que introdujo no es un número válido.";
            }
            if ($mensaje=="Credit card has Already Expired"){
                $mensaje = "La tarjeta que introdujo ha expirado.";
            }
            if ($codigo == 403){
               $mensaje = "El pago ha sido rechazado por el banco.";
            }
            
            $this->render('errorGC',array('mensaje'=>$mensaje));
		} 
        
        /**
         * Urls para recibir las notificaciones del proceso de compra
         * con la API de Aztive
         */
        public function actionNotificacionAzt(){
            
            error_log("Notification Develop: " . print_r("Se registro una notificacion desde Aztive", true));
            

            $sCustomerID      = isset($_GET['onepay_customer_code']) ? $_GET['onepay_customer_code'] : "-1";
            $sCustomerTerminal = isset($_GET['onepay_customer_terminal']) ? $_GET['onepay_customer_terminal'] : '';
            $sOrderID           = isset($_GET['onepay_customer_order'])? $_GET['onepay_customer_order']    : '';
            $sSignature         = isset($_GET['onepay_signature'])? $_GET['onepay_signature']         : '';
            $lang               = isset($_GET['lang'])? $_GET['lang'] : 'es';
            // datos de Transaccion
            $opResponse = isset($_GET['onepay_response'])? $_GET['onepay_response'] : '';
            $opAuthCode = isset($_GET['onepay_authorization_code']) ? $_GET['onepay_authorization_code']: '';
            $opOrder    = isset($_GET['onepay_customer_order'])? $_GET['onepay_customer_order']    : '';
            
            
            $op = new AzPay ();
            
//            if (isset($_GET['action']) && $_GET['action'] == "async") {
            $ack = true;
            if ($op->validateResponseData ($_GET)) {
                echo "ACK=true";   
                
                //Error en la compra - KO
                if ( $opResponse != "0000" ) {
                
                } else if ($opResponse == "0000") {
                //Compra exitosa en la compra - KO
                    

                    
                }
                
            } else {
                echo "ACK=false";
                $ack = false;
            }                
            $_GET["ACK"] = $ack;
            ShoppingMetric::registro(ShoppingMetric::STEP_PAGO_RESPONSE,$_GET);
            exit;
//            }
            
            
	}
        /**
         * Urls para recibir las notificaciones del proceso de compra
         * con la API de Aztive
         */
        public function actionOkAzt(){
                       
            $opResponse = isset($_GET['onepay_response'])? $_GET['onepay_response'] : '';           
            $op = new AzPay();            

            if ($op->validateResponseData($_GET)) {                                                       
                ShoppingMetric::registro(ShoppingMetric::STEP_PAGO_OK,$_GET);
                $cData = isset($_GET['onepay_cData']) ? $_GET['onepay_cData'] : '';
                
                $cData = CJSON::decode($cData);
                
                /*Ver de cual compra viene*/
                if($cData["src"] == 2) //si es de compra de GC
                {
                    $this->comprarGC($_GET['onepay_authorization_code']);
                    
                }else if($cData["src"] == 1) //si es de compra normal
                {
                    $this->compraAztive($_GET['onepay_authorization_code']);                
                }
                  

            } 
            else {
                
                $opResponse = "001";               
                $mensaje = "Hubo un error con la plataforma de pago Aztive, intenta de nuevo";      
                ShoppingMetric::registro(ShoppingMetric::STEP_PAGO_FAIL_RESPONSE,$_GET); 
                $url = $this->createAbsoluteUrl('bolsa/error',
                        array(
                            'codigo'=>$opResponse,
                            'mensaje'=>$mensaje,
                        ),
                        'http');
                //Mostrar info en el modal de pago y redirigir de una vez
                //a la pagina de error
                $this->renderPartial("_redirectAztive", array("url" => $url));
                
            }  
            
	}
        
        /**
         * Urls para recibir las notificaciones del proceso de compra
         * con la API de Aztive
         */
        public function actionKoAzt(){
                       
            $opResponse = isset($_GET['onepay_response'])? $_GET['onepay_response'] : '';           
            
            $op = new AzPay();
			
            if ($op->validateResponseData($_GET)) {
            	ShoppingMetric::registro(ShoppingMetric::STEP_PAGO_FAIL,$_GET);    
                $mensaje = "Hubo un error realizando el pago, intenta de nuevo.";  
                
                $cData = isset($_GET['onepay_cData']) ? $_GET['onepay_cData'] : '';
                
                $cData = CJSON::decode($cData);
                
                /*Ver de cual compra viene*/
                if($cData["src"] == 2) //si es de compra de GC
                {
                    
                    $url = $this->createAbsoluteUrl('bolsa/errorGC',
                        array(
                            'codigo'=>$opResponse,
                            'mensaje'=>$mensaje,
                        ),
                        'http');

                    $this->renderPartial("_redirectAztive", array("url" => $url));
                    
            
                    
                }else if($cData["src"] == 1) //si es de compra normal
                {
                    
                    
                    $url = $this->createAbsoluteUrl('bolsa/error',
                        array(
                            'codigo'=>$opResponse,
                            'mensaje'=>$mensaje,
                        ),
                        'http');
//                    echo "<script>
//                        window.top.location.href = '".$url."';
//                        </script>
//                        ";
                    $this->renderPartial("_redirectAztive", array("url" => $url));

                }
                
                              

            } else {
                ShoppingMetric::registro(ShoppingMetric::STEP_PAGO_FAIL_RESPONSE,$_GET); 
                $opResponse = "001";               
                $mensaje = "Hubo un error con la plataforma de pago Aztive, intenta de nuevo";                
                
            }  
            
                       
	}
        
        
        /* Crear la orden, los pagos y registrar el pedido
         * cuando fue hecho con algún método de Aztive
         */
        public function compraAztive($codigoTransaccion){            
           
            $admin = Yii::app()->getSession()->contains("bolsaUser");
             
            /*ID del usuario propietario de la bolsa*/
            $userId = $admin ? Yii::app()->getSession()->get("bolsaUser")
                                : Yii::app()->user->id;

             
            $usuario = User::model()->findByPk($userId);
            $bolsa = Bolsa::model()->findByAttributes(array(
                            'user_id' => $userId,
                            'admin' => $admin, //Revisar para compras desde admin
                            ));
            
            
            if(User::hasRecentOrder()){
                Yii::app()->user->setFlash("warning", "Al parecer estás intentando
                    hacer otra compra, revisa tu lista de pedidos, acabamos de registrar uno nuevo.");                
                
                $this->redirect($this->createAbsoluteUrl('bolsa/index',array(),'http'));
            }          
                        
            if (!$bolsa->checkInventario())
                    $this->redirect($this->createAbsoluteUrl('bolsa/index',array(),'http'));
            
            /*Crear la orden*/
            $orden = $this->crearOrden($bolsa, $userId);
            
            /*Crear el detalle de pago*/
            $this->crearDetallePago($orden, $usuario, $codigoTransaccion);
            
            /*Revisar si uso balance en la compra*/
            $this->usarBalance($orden, $usuario);
            
            /*Revisar si uso cupones de descuento*/
            $this->agregarCupon($orden);
            
            /*Vaciar bolsa, enviar a la orden*/
            $this->hacerCompra($bolsa->id, $orden->id);
            
            /*Registrar estados de la orden*/
            $this->cambiarEstadoOrden($orden, $userId);
            
            
            $dirEnvio = $this->clonarDireccion(Direccion::model()->findByAttributes(
                    array('id' => Yii::app()->getSession()->get('idDireccion'),
                        'user_id' => $userId)));
            
            $dirFacturacion = $this->clonarDireccion(Direccion::model()->findByAttributes(
                    array('id' => Yii::app()->getSession()->get('idFacturacion'),
                        'user_id' => $userId)), true);
            
            /*Crear Factura*/
            $factura = new Factura;
            $factura->fecha = date('Y-m-d');
            // esta direccion hay que cambiarla después, el usuario debe 
            // seleccionar esta dirección durante el proceso de compra
            $factura->direccion_fiscal_id = $dirFacturacion->id; 
            $factura->direccion_envio_id = $dirEnvio->id;
            $factura->orden_id = $orden->id;
            if (!$factura->save())
                Yii::trace('user id:'.Yii::app()->user->id.' Factura error:'.print_r($factura->getErrors(),true), 'registro');
            
            /*Enviar correo con el resumen de la compra*/
            $this->enviarEmail($orden, $usuario);  
            
            
            /*===========================================*/
	    	if(Yii::app()->params['zohoActive'] == TRUE){ // Si Zoho Activo    
	            $zoho = new ZohoSales;

	            //transformando Lead a posible cliente.
	            if($usuario->tipo_zoho == 0){
	                    
	            		if($usuario->zoho_id == ""){ 
	            			$zoho->getLostId($usuario->email);
	            		} 

	                    $conv = $zoho->convertirLead($usuario->zoho_id, $usuario->email);
	                    $datos = simplexml_load_string($conv);
	                    
	                    /*
	                    var_dump($datos);
						Yii::app()->end();		
						*/ 
 
	                    $id = $datos->Contact; 
	                    $usuario->zoho_id = $id;
	                    $usuario->tipo_zoho = 1; 

	                    if(!$usuario->save()) 
	                    	Yii::trace('ZOHO:'.$usuario.' Error al guardar:'.print_r($usuario->getErrors(),true),'Compra'); 
	            }

	            if($usuario->tipo_zoho == 1) // es ahora un contacto
	            {

	            	if($usuario->zoho_id == ""){
            			$zoho->getLostId($usuario->email);  
            		} 
 
	                    $respuesta = $zoho->save_potential($orden);

	                    $datos = simplexml_load_string($respuesta);

	                    //var_dump($datos);
	                    //Yii::app()->end();
						if(isset($datos->result[0]->recorddetail->FL[0])) // si hay un error, el cliente no deberia verlo
						{
							$id = $datos->result[0]->recorddetail->FL[0];
							if(isset(Yii::app()->session['zoho_error'])) // si hay un error, pero no en el producto, si no en el usuario
								$orden->zoho_error=2;
							$orden->zoho_id = $id;
							$orden->save();
						}
						else 
						{
							if(isset(Yii::app()->session['zoho_error']))	//hay error de usuario y producto
								$orden->zoho_error=3;
							else
								$orden->zoho_error=1; // solo hay error de producto
							$orden->save();
						}
	            }
	            /*===========================================*/
            }
			
            /*Enviar correo OPERACIONES (operaciones@personaling.com*/
            /*Solo enviar correos cuando no este en develop*/
            if(strpos(Yii::app()->baseUrl, "develop") === false){
                
                $this->enviarEmailOperaciones($orden);  

            }
            
            /*Generar el Outbound para Logishfashion*/
            $this->generarOutbound($orden);
            
            $url = $this->createAbsoluteUrl('bolsa/pedido',array(
                        'id'=>$orden->id,
                            ),'http');
            
           //Mostrar info en el modal de pago y redirigir de una vez
            //a la pagina de error
            $this->renderPartial("_redirectAztive", array("url" => $url));
            
        }
        
        /**
         * Crear la orden nueva
         * @return Orden
         */
        function crearOrden($bolsa, $userId) {
            
            $dirEnvio = $this->clonarDireccion(Direccion::model()->findByAttributes(
                    array('id' => Yii::app()->getSession()->get('idDireccion'),
                        'user_id' => $userId)));
            
            $dirFacturacion = $this->clonarDireccion(Direccion::model()->findByAttributes(
                    array('id' => Yii::app()->getSession()->get('idFacturacion'),
                        'user_id' => $userId)), true);
            

            $orden = new Orden;
            $orden->subtotal = Yii::app()->getSession()->get('subtotal');
            //$orden->descuento = Yii::app()->getSession()->get('descuento');
            if(Yii::app()->getSession()->get('descuento')>0){
            	$orden->descuento = Yii::app()->getSession()->get('descuento');
            }else{
            	$orden->descuento = 0;
            }
            if(Yii::app()->getSession()->get('descuentoRegalo')>0){
            	$orden->descuentoRegalo = Yii::app()->getSession()->get('descuentoRegalo');
            }else{
            	$orden->descuentoRegalo = 0;
            }
			$orden->descuento_look=Yii::app()->getSession()->get('descuentoxLook');
            $orden->envio = Yii::app()->getSession()->get('envio');
            $orden->iva = Yii::app()->getSession()->get('iva');
            $orden->seguro = Yii::app()->getSession()->get('seguro');
            $orden->fecha = date("Y-m-d H:i:s"); // Datetime exacto del momento de la compra 
            $orden->estado = Orden::ESTADO_CONFIRMADO;
            $orden->bolsa_id = $bolsa->id; //Borrar si es innecesario
            $orden->user_id = $userId;
            $orden->direccionEnvio_id = $dirEnvio->id;
            $orden->direccionFacturacion_id = $dirFacturacion->id;
            $orden->tipo_guia = Yii::app()->getSession()->get('tipo_guia');
            $orden->peso = Yii::app()->getSession()->get('peso');
            
            $totalOrden = round(Yii::app()->getSession()->get('total'), 2);
            $orden->total = $totalOrden;
            if (!($orden->save())) {
                echo CJSON::encode(array(
                    'status' => 'error',
                    'error' => $orden->getErrors(),
                ));
                Yii::trace('UserID:' . $userId . ' Error al guardar la orden:' . print_r($orden->getErrors(), true), 'registro');
                Yii::app()->end();
            }            
            
            return $orden;
        }
        
        /* Crear detalle de pago según el método seleccionado
         * El tipo de pago está en sesión
         */
        function crearDetallePago($orden, $usuario, $codigoTransaccion) {
            
            //5 BkCard - 6 Paypal
            $metodoPago = Yii::app()->getSession()->get('tipoPago');
            
            $metodoPago--; //llevarlo a los metodos de pago usados para las órdenes
            
            $detalle = new Detalle;            
            $detalle->nTransferencia = $codigoTransaccion;
            $detalle->nombre = $usuario->profile->first_name." ".$usuario->profile->last_name;            
            //lo que queda por pagar despues de usar el saldo
            $detalle->monto = Yii::app()->getSession()->get('totalTarjeta');
            $detalle->fecha = date("Y-m-d H:i:s");
            $detalle->banco = $metodoPago == Detalle::TDC_AZTIVE ? 'Sabadell' : 'PayPal'; //TDC o PayPal
            $detalle->estado = 1; // aceptado
            $detalle->orden_id = $orden->id;
            $detalle->tipo_pago = $metodoPago;
            $detalle->save();
        }
        
        /*Determinar si se uso el balance, registrar pago respectivo*/
        function usarBalance($orden, $usuario) {
            
            $descuentoRegalo = $orden->descuentoRegalo; //Pagado con balance
            
            if($descuentoRegalo > 0){
                
                $balance = new Balance;
                $balance->total = $descuentoRegalo * (-1); //Descontar al usuario

                $detalleBalance = new Detalle;
                $detalleBalance->monto = $descuentoRegalo;

                $detalleBalance->comentario = "Uso de Saldo";
                $detalleBalance->estado = 1;//Aprobado
                $detalleBalance->fecha = date("Y-m-d H:i:s");
                $detalleBalance->orden_id = $orden->id;
                $detalleBalance->tipo_pago = Detalle::USO_BALANCE;

                if ($detalleBalance->save()) {
                    $balance->orden_id = $orden->id;
                    $balance->user_id = $usuario->id;
                    $balance->tipo = 1;                        
                    $balance->save();
                }
            }
            
            
//            $usarBalance = Yii::app()->getSession()->get('usarBalance');
//            $totalOrden = $orden->total;
//            if ($usarBalance == '1') {                                
//                $balanceUsuario = floor($usuario->saldo * 100) / 100;
//                if ($balanceUsuario > 0) {
//                    $balance = new Balance;
//                    $detalleBalance = new Detalle;
//                    if ($balanceUsuario >= $totalOrden) {
//                        //Descontar del saldo el monto total de la orden
//                        $balance->total = $totalOrden * (-1);
//                        $detalleBalance->monto = $totalOrden;
//                        
//                    } else {
//                        //Descontar todo el saldo del usuario
//                        $balance->total = $balanceUsuario * (-1);
//                        $detalleBalance->monto = $balanceUsuario;
//                    }
//
//                    $detalleBalance->comentario = "Uso de Saldo";
//                    $detalleBalance->estado = 1;//Aprobado
//                    $detalleBalance->fecha = date("Y-m-d H:i:s");
//                    $detalleBalance->orden_id = $orden->id;
//                    $detalleBalance->tipo_pago = Detalle::USO_BALANCE;
//                    
//                    if ($detalleBalance->save()) {
//                        $balance->orden_id = $orden->id;
//                        $balance->user_id = $usuario->id;
//                        $balance->tipo = 1;                        
//                        $balance->save();
//                    }
//                }
//            }

        }
        /*Determinar si se uso un cupon de descuento*/
        function agregarCupon($orden) {
            
            // Si uso cupon, registrarlo
            $idCupon = Yii::app()->getSession()->get('usarCupon');
            if($idCupon != -1){
                
                $cuponHasOrden = new CuponHasOrden();
                $cuponHasOrden->cupon_id = $idCupon;
                $cuponHasOrden->orden_id = $orden->id;                
                        
                $codigo = CodigoDescuento::model()->findByPk($idCupon);
                
                //si es un monto fijo
                if($codigo->tipo_descuento == 1){
                    $cuponHasOrden->descuento = $codigo->descuento;                            
                }else{
                    //modificar - Calcular bien
                    $descuento = $orden->total * ($codigo->descuento / 100);
                    $cuponHasOrden->descuento = floor($descuento * 100) / 100;

                }
                
                $cuponHasOrden->save();
            }

        }
        
        /*Cambiar estado de la orden a Pago Confirmado*/
        function cambiarEstadoOrden($orden, $userId) {
             // pasar a estado confirmado de una vez por que ya se pagó el dinero 
                $estado = new Estado;
                $estado->estado = Orden::ESTADO_CONFIRMADO;
                $estado->user_id = $userId;
                $estado->fecha = date("Y-m-d");
                $estado->orden_id = $orden->id;
                $estado->save();
        }
        
        /*Enviar el correo con el resumen de la orden al usuario*/
        function enviarEmail($orden, $usuario) {
        
            $message = new YiiMailMessage;
            //Opciones de Mandrill
            $message->activarPlantillaMandrill();
            
            $subject = 'Tu compra en Personaling';
            $message->subject    = $subject;
            $body = $this->renderPartial("//mail/_pedido", array(
                "orden" => $orden), true);
            
            $message->setBody($body, 'text/html');                
            $message->addTo($usuario->email);
            
            Yii::app()->mail->send($message);
            
            
//            $message = new YiiMailMessage;
//            $message->view = "mail_compra";
//            $subject = 'Tu compra en Personaling';
//            $params = array('subject'=>$subject, 'orden'=>$orden);
//            $message->subject = $subject;
//            $message->setBody($params, 'text/html');
//            $message->addTo($usuario->email);
//            $message->from = array('operaciones@personaling.com' => 'Tu Personal Shopper Online');            
//            Yii::app()->mail->send($message);
        }
        
        
        
        /*Enviar el correo para notificar a Operaciones*/
        function enviarEmailOperaciones($orden) {
            
            $message = new YiiMailMessage;
            //this points to the file test.php inside the view path
            $message->view = "mail_template";
            
            $subject = 'Compra en Personaling';
            $body = $body = Yii::t('contentForm',
                    'Alguien ha comprado en personaling, debes generar el archivo Excel
                     correspondiente al Outbound para enviar a LogisFashion
                     <br>
                     <br>
                     <br>
                     <a title="Ver órdenes" 
                     href="http://www.personaling.es'.Yii::app()->baseUrl.
                    '/orden/admin" 
                        style="text-align:center;text-decoration:none;color:#ffffff;
                        word-wrap:break-word;background: #231f20; padding: 12px;" 
                        target="_blank">Ver órdenes</a><br><br/><br/><br/><br/>'
                     ."Los datos de la orden generada son:<br/>
                     Codigo: {$orden->id}<br/>
                     Fecha: {$orden->fecha}<br/>
                     <br/>
                         
                     <br/>");
                     
                     
            $destinatario = "operaciones@personaling.com";
            //si esta en test, enviarlo a cristal
            if(strpos(Yii::app()->baseUrl, "test") !== false){
                
                $destinatario = "cmontanez@upsidecorp.ch";
            }
                    
                     
            $params = array('subject'=>$subject, 'body'=>$body);
            $message->subject = $subject;
            $message->setBody($params, 'text/html');
            $message->addTo($destinatario);
            $message->from = array('operaciones@personaling.com' => 'Tu Personal Shopper Online');            
            Yii::app()->mail->send($message);
        }
        

        /*Para realizar la compra de una giftcard*/
        function comprarGC($codigoTransaccion){
            
            $userId = Yii::app()->user->id;    
            $usuario = User::model()->findByPk($userId);
            
            //5 BkCard - 6 Paypal
            $metodoPago = Yii::app()->getSession()->get('tipoPago');
            $metodoPago--; //llevarlo a los metodos de pago usados para las órdenes	
            
            $total = Yii::app()->getSession()->get('total');
            
            $orden = new OrdenGC;                            
            $orden->estado = Orden::ESTADO_CONFIRMADO;
            $orden->fecha = date("Y-m-d H:i:s"); // Datetime exacto del momento de la compra 
            $orden->total = $total;
            $orden->user_id = $userId;
            
            if (!($orden->save())){
                    echo CJSON::encode(array(
                                    'status'=> 'error',
                                    'error'=> $orden->getErrors(),
                            ));
                    Yii::trace('UserID: '.$userId.' Error al guardar la orden:'.print_r($orden->getErrors(),true), 'registro');	
                    Yii::app()->end();

            }	
            //Pasar de la bolsa a las giftcards
            $this->crearGC($userId, $orden->id);
            
            $detalle = new DetallePago();            
            $detalle->nTransferencia = $codigoTransaccion;
            $detalle->nombre = $usuario->profile->first_name." ".$usuario->profile->last_name;            
            $detalle->monto = $total;
            $detalle->fecha = date("Y-m-d H:i:s");
            $detalle->banco = $metodoPago == Detalle::TDC_AZTIVE ? 'Sabadell' : 'PayPal'; //TDC o PayPal
            $detalle->estado = 1; // aceptado
            $detalle->orden_id = $orden->id;
            $detalle->tipo_pago = $metodoPago;
            $detalle->save();
            
            $url = $this->createAbsoluteUrl('bolsa/pedidoGC',array('id'=>$orden->id),'http');
            echo "<script>
                window.top.location.href = '".$url."';
                </script>
                ";
            //$this->redirect($this->createAbsoluteUrl('bolsa/pedidoGC',array('id'=>$orden->id),'http'));	
            
        }
        
        /**
         * Para generar el archivo XML correspondiente a un outbound
         * LogisFashion
         * @param Orden $orden La orden de donde se extrae la informacion para el Outbound
         */
        function generarOutbound($orden){
            
            $outbound = new SimpleXMLElement('<Outbound/>');
            
            //Codigo de Albaran
            $codigo = $orden->id;
            $outbound->addChild('Albaran', $codigo);
            
            //Fecha de Albaran
            $fecha = date("Y-m-d", strtotime($orden->fecha));
            $outbound->addChild("FechaAlbaran", "{$fecha}");
            
            //Cliente - Usuario
            $usuario = $orden->user;
            $cliente = $outbound->addChild("Cliente");
            $cliente->addChild("Codigo", "{$usuario->id}");
            $cliente->addChild("Nombre", "{$usuario->profile->getNombre()}");
            
            //Direccion
            $direccionEnvio = DireccionEnvio::model()->findByPk($orden->direccionEnvio_id);
            $dirString = $direccionEnvio->dirUno.", ".$direccionEnvio->dirDos;
            
            $ciudadEnvio = Ciudad::model()->findByPk($direccionEnvio->ciudad_id);
            
            $codigoPostal = CodigoPostal::model()->findByPk($direccionEnvio->codigo_postal_id);
            //ZIP
            if($codigoPostal){
                $codigoPostal = $codigoPostal->codigo;                
            }else{
               $codigoPostal = "No existe";                
            }
            
            $cliente->addChild("Direccion", "{$dirString}");
            $cliente->addChild("CP", "{$codigoPostal}");
            $cliente->addChild("Poblacion", "{$ciudadEnvio->nombre}");
            $cliente->addChild("Pais", "{$direccionEnvio->pais}");
            
            $cliente->addChild("Email", "{$usuario->email}");

            //Listado de items vendidos   
            $productos = $orden->ohptc;
            foreach ($productos as $producto) {
                
                $item = $outbound->addChild("Item");                
                //Agregar el SKU
                $item->addChild("EAN", "{$producto->preciotallacolor->sku}");
                //Agregar la cantidad vendida.                
                $item->addChild("Cantidad", "{$producto->cantidad}");                
                
            }            

            //Guardar Outbound en la BD
            $outboundBD = new Outbound();
            $outboundBD->orden_id = $orden->id;
            //discrep, estado, cantBultos por defecto en 0
            $outboundBD->save();
            
            
            //Enviar Outbound a LF y guardarlo en local para respaldo
            $subido = MasterData::subirArchivoFtp($outbound, 3, $orden->id);
            
            
        }
        
        public function actionClickBotonConfirmar() {
            
            if(isset($_POST["tipoPago"])){
                $data = array("Tipo de Pago" => "");
                if($_POST["tipoPago"] == 5){
                    $data["Tipo de Pago"] = "TPV Sabadell";
                    
                }elseif($_POST["tipoPago"] == 6){
                    $data["Tipo de Pago"] = "PayPal";
                    
                }elseif($_POST["tipoPago"] == 7){
                    $data["Tipo de Pago"] = "Con saldo o Cupon de descuento";                    
                    
                }elseif($_POST["tipoPago"] == 8){
                    $data["Tipo de Pago"] = "Pago para pruebas";
                    
                }else{
                    $data["Tipo de Pago"] = "Tipo de pago desconocido ({$_POST['tipoPago']})";
                    
                }                
                

                ShoppingMetric::registro(ShoppingMetric::STEP_CONFIRMAR_BOTON, $data);
            
            }
            
            
        }
        
        /*Para vaciar la bolsa del usuario GUEST*/
        public function actionVaciarGuest() {
            //si existe la bolsa de invitado
            $response = array();
            $response["status"] = "error";
            
            if(Yii::app()->getSession()->contains("Bolsa")){
                Yii::app()->getSession()->remove("Bolsa");
                $response["status"] = "success";
            }
            
            echo CJSON::encode($response);
            
        }
        
                
}
