<?php

/**
 * This is the model class for table "{{shopping_metric}}".
 *
 * The followings are the available columns in table '{{shopping_metric}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $step
 * @property string $created_on
 * @property integer $tipo_compra
 *
 * The followings are the available model relations:
 * @property Users $user
 */
class ShoppingMetric extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ShoppingMetric the static model class
	 */
	const STEP_BOLSA = 0;
	const STEP_LOGIN = 1; 
	const STEP_DIRECCIONES = 2; 
	const STEP_PAGO = 3;
	const STEP_CONFIRMAR = 4; 
	const STEP_CONFIRMAR_BOTON = 41; 
	
        
        const STEP_PEDIDO = 5;
	const STEP_PAGO_OK = 6;
	const STEP_PAGO_FAIL = 7; 
	const STEP_PAGO_FAIL_RESPONSE = 8;
	const STEP_PAGO_RESPONSE = 9;
	const STEP_BOTON_PAGO = 10; 
	
        /*MOVIMIENTOS DEL USUARIO*/
        const USER_INICIO = 100;
	const USER_TIENDA = 101;
	const USER_LOOK = 102;
    const USER_VIEW_LOOK = 103;
        
        /*TIPOS DE COMPRA*/
	const TIPO_TIENDA = 0; 
	const TIPO_GIFTCARD = 1; 
        
        
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{shopping_metric}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		/*array('created_on','default',
              'value'=>new CDbExpression('NOW()'),
              'setOnEmpty'=>false,'on'=>'insert'), */
			array('user_id, step, created_on, tipo_compra, HTTP_USER_AGENT, REMOTE_ADDR, HTTP_X_FORWARDED_FOR,HTTP_REFERER,data', 'required'),
			array('user_id, step, tipo_compra', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, step, created_on, tipo_compra', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'step' => 'Step',
			'created_on' => 'Created On',
			'tipo_compra' => 'Tipo de compra',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('step',$this->step);
		$criteria->compare('tipo_compra',$this->step);
		$criteria->compare('created_on',$this->created_on,true);
		$criteria->order = 'id DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public function beforeValidate()
	{
		$ua =
		$this->created_on=date("Y-m-d H:i:s"); 	
		$this->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
		//$this->platform;
		//$this->browser;
		//$this->version; 
		$this->REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
		$this->HTTP_X_FORWARDED_FOR = (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
		$this->HTTP_REFERER = (!empty($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:'AJAX';
		return parent::beforeValidate();
	}
	public static function registro($step,$data=array(), $checkViews=0){
			$clickValido=1;	
			if($checkViews==1)
			{
				$clickValido=ShoppingMetric::model()->checkViews($data); 
			}
				
			$metric = new ShoppingMetric();
            $metric->user_id = Yii::app()->user->id;
			$metric->click_validez=$clickValido;
			
			if (empty($data['tipo_compra']))
				$metric->tipo_compra = ShoppingMetric::TIPO_TIENDA;
			else
				$metric->tipo_compra = $data['tipo_compra'];
			$metric->data = json_encode($data);
            $metric->step = $step;
            $metric->save();
	}
	
	public function getShow($field){
		
		switch ($field){
            case 'REMOTE_ADDR':
                return $this->REMOTE_ADDR; 
                break; 
			case 'HTTP_X_FOWARDED_FOR':
                return $this->HTTP_X_FOWARDED_FOR; 
                break;
			case 'HTTP_REFERER':
                $http=trim($this->$field);
                if(strpos($http, 'http://')!==false)
                	$http=str_replace('http://', '',$http);
				if(strpos($http, 'https://')!==false)
                	$http=str_replace('https://', '',$http);
				if(strpos($http, 'www.')!==false)
                	$http=str_replace('www.', '',$http);
				if(strpos($http, 'http://')!==false)
                	$http=str_replace('http://', '',$http);
				if(strpos($http, 'personaling.es')!==false)
                	$http=str_replace('personaling.es', '..',$http);
				if(strpos($http, '?')!==false){
					$http=explode('?',$http);
					$http=$http[0];
				}
				   
				return $http; 
	            break;
			case 'HTTP_USER_AGENT':
				$browser = 'Desconocido';
				if(strstr($this->$field, 'MSIE'))
					$browser = 'Internet explorer';
				elseif(strstr($this->$field, 'Trident'))
					$browser = 'Internet explorer';
				elseif(strstr($this->$field, 'Firefox'))
					$browser = 'Mozilla Firefox';
				elseif(strstr($this->$field, 'Chrome'))
					$browser = 'Google Chrome';
				elseif(strstr($this->$field, 'Opera'))
					$browser = "Opera";
				elseif(strstr($this->$field, 'Safari'))
					$browser = "Safari";
				elseif(strstr($this->$field, 'Firefox')){
					$browser = "Mozilla Firefox";
				}
                //$http=explode(' ',$this->$field);
                                
                //return $http[0]; 
                return $browser; 
                break;
          
            default: //5
                return "No disponible";
				break; 
        }
	}
        
        public function getDescripcion(){
            
            switch ($this->step){
                case 0:
                    echo "[Compra] - Viendo el carrito"; 
                    break;
                case 1:
                    echo "[Compra] - Autenticación";  
                    break;
                case 2:
                    echo "[Compra] - Escogiendo dirección"; 
                    break;
                case 3:
                    echo "[Compra] - Escogiendo método de pago"; 
                    break;
                case 4: 
                    echo "[Compra] - Confirmando la compra"; 
                    break;
                case self::STEP_CONFIRMAR_BOTON: 
                    echo "[Compra] - Presionó botón de Pagar"; 
                    break;
                case 5: 
                    echo "[Compra] - Viendo resumen del pedido"; 
                    break;
                case 6: 
                    echo "[Compra] - Respuesta OK de Aztive"; 
                    break;
                case 7: 
                    echo "[Compra] - Respuesta K-O de Aztive (Hubo error)"; 
                    break;
                case 8: 
                    echo "[Compra] - Respuesta Errada de Aztive"; 
                    break;
                case 9: 
                    echo "[Compra] - Respuesta Notificación de Aztive"; 
                    break;
                case 100: 
                    echo "Inició sesión"; 
                    break;
                case 101: 
                    echo "Tienda de productos"; 
                    break;
                case 102: 
                    echo "Tienda de Looks"; 
                    break;
                case 103:
                    echo "Vista de Look";
                    break;
                default: //5 
                    echo "Acción no definida"; 
            }
            
        }
        
        public function getData(){
            
            if(strlen($this->data) > 30){
               return chunk_split($this->data, 30, "<br>"); 
            }
            
            return $this->data;
        }
        public function getReferred(){
            $ref = $this->getShow('HTTP_REFERER');
            if(strlen($ref) > 30){
               return chunk_split($ref, 30, "<br>"); 
            }
            
            return $ref;
        }
    public static function getAllViewsPs(){
        $match = addcslashes('ps_id":"', '%_');
        return ShoppingMetric::model()->count(
            'data LIKE :match',
            array(':match' => "%$match%")
        );
    }
    public static function getAllViewsPsByDate($from,$to){
        $match = addcslashes('ps_id":"', '%_');
       return ShoppingMetric::model()->count(
            'data LIKE :match and created_on between :from and :to',
            array(
                ':match' => "%$match%",
                ':from' => $from,
                ':to' => $to
            )
        );
    }
	
	public function getBrowser($navegador)
	{
				$browser = 'Desconocido';
				if(strstr($navegador, 'MSIE'))
					$browser = 'Internet explorer';
				elseif(strstr($navegador, 'Trident'))
					$browser = 'Internet explorer';
				elseif(strstr($navegador, 'Firefox'))
					$browser = 'Mozilla Firefox';
				elseif(strstr($navegador, 'Chrome'))
					$browser = 'Google Chrome';
				elseif(strstr($navegador, 'Opera'))
					$browser = "Opera";
				elseif(strstr($navegador, 'Safari'))
					$browser = "Safari";
				elseif(strstr($navegador, 'Firefox')){
					$browser = "Mozilla Firefox";
				}
                //$http=explode(' ',$this->$field);
                                
                //return $http[0]; 
                return $browser; 
	}
	
	public function buscarFiltro($keyword)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		
		#$word='%"ps_id":'.$keyword.'}%';
		$word='%{"look_id":"'.$keyword.'"%';
		$criteria->condition = "data like '".$word."'";
		$criteria->order = 'id DESC';

		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public function buscarPorFiltros($filters) 
	{
			
		#$condicion_final=' AND data like "%look_id%"';
		$criteria = new CDbCriteria;
            
            $criteria->with = array();

            for ($i = 0; $i < count($filters['fields']); $i++) 
            {
                
                $column = $filters['fields'][$i];
                $value = $filters['vals'][$i];
                $comparator = $filters['ops'][$i];
                
                if($i == 0){
                   $logicOp = 'AND'; 
                }else{                
                    $logicOp = $filters['rels'][$i-1];                
                }                  
			
			
			
			 if($column == 'id')
              {
                 
                 $word='{"look_id":"'.$value.'"'; // $word='%{"look_id":"'.$value.'"%';
				 /*if($comparator=="=")
			     	$criteria->condition = "data like '".$word."'".$condicion_final;*/
				if($comparator=="=")
				{
					$criteria->compare("data", $word,
                        true, $logicOp);
				}		 
				 continue;
               }
			  
			  
			  if($column == 'nombre_look')
              {
                 if($modelado=Look::model()->find('title LIKE :match',array(':match' => "%$value%")))	
                 {
                 	$modelado=Look::model()->find('title LIKE :match',array(':match' => "%$value%"));
					#$word='%{"look_id":"'.$modelado->id.'"%';
					$word='{"look_id":"'.$modelado->id.'"';		
                 }
				else
				{
					$fa=NULL;	
					#$word='%{"look_id":"'.$fa.'"%';
					$word='{"look_id":"'.$fa.'"';
					
				}
				 
				 if($comparator=="=")
				 {
				 	#$criteria->condition = "data like '".$word."'".$condicion_final;
				 	$criteria->compare("data", $word,true, $logicOp);
				 }
			     			
                 continue;
     
               }
			  
			  
			  if($column == 'ps_id')
              {
				if(strlen($value)<5)
				{
					$ceros=5-strlen($value);
					for($i=0; $i<$ceros;$i++)
					{
						$value="0".$value;
					}

				}
				#$word='%"ps_id":"'.$value.'"}%';
				$word='"ps_id":"'.$value.'"}';
				if($comparator=="=")
				{
					#$criteria->condition = "data like '".$word."'".$condicion_final;
					$criteria->compare("data", $word,true, $logicOp);
				}
			     			
     			 continue;
               }
			  
  
			  
			  if($column == 'ps_nombre')
              {
				if(strpos($value, " ")==TRUE)
				{
					$partir=explode(" ", $value);	
					$nombre=$partir[0];
					$apellido=$partir[1];
					$modelado=Profile::model()->find('(first_name LIKE :match AND last_name LIKE :match2) OR 
													  (first_name LIKE :match2 AND last_name LIKE :match)  
													  ',array(':match' => "%$nombre%", ':match2' => "%$apellido%"));
					$busqueda=$modelado->user_id;
						if(strlen($busqueda)<5)
						{
							$ceros=5-strlen($busqueda);
							for($i=0; $i<$ceros;$i++)
							{
								$busqueda="0".$busqueda;
							}
						
						}
					
					#$word='%"ps_id":"'.$busqueda.'"}%';
					$word='"ps_id":"'.$busqueda.'"}';					
				}
				else
				{
					if(Profile::model()->find('first_name LIKE :match OR last_name LIKE :match',array(':match' => "%$value%")))
					{
						
						$modelado=Profile::model()->find('first_name LIKE :match OR last_name LIKE :match',
														array(':match' => "%$value%"));
						$busqueda=$modelado->user_id;
						if(strlen($busqueda)<5)
						{
							$ceros=5-strlen($busqueda);
							for($i=0; $i<$ceros;$i++)
							{
								$busqueda="0".$busqueda;
							}
						
						}
					
					#$word='%"ps_id":"'.$busqueda.'"}%';
					$word='"ps_id":"'.$busqueda.'"}';
					}
					else 
					{
						$fa=NULL;	
						#$word='%{"look_id":"'.$fa.'"%';
						$word='{"look_id":"'.$fa.'"';
					}
				}	

				if($comparator=="=")
				{
					$criteria->compare("data", $word,true, $logicOp);	
					#$criteria->condition = "data like '".$word."'".$condicion_final;
				}
			     			
     			continue;
               }


			  if($column == 'fuente')
              {
              	if($value=="Provino desde Pagina no localizada")
				{
					$value="AJAX";
				}	
              	
					
              	#$criteria->condition = "HTTP_REFERER like '".$value."'".$condicion_final;
				$criteria->compare("HTTP_REFERER", $value,true, $logicOp);	
				continue;	
              }
			  
			  if($column == 'navegador')
              {
				#$value="%".$value."%";
				#$criteria->condition = "HTTP_USER_AGENT like '".$value."'".$condicion_final;
				$criteria->compare("HTTP_USER_AGENT", $value,true, $logicOp);	
				continue;		
              }
					
			 if($column == 'ip')
              {
				#$criteria->condition = "REMOTE_ADDR = '".$value."'".$condicion_final;
				$criteria->compare("REMOTE_ADDR", $value,true, $logicOp);
				continue;		
              }
			  
			  if($column == 'vis_id')
              {
				#$criteria->condition = "user_id = '".$value."'".$condicion_final;
				$criteria->compare("user_id", $value,true, $logicOp);
				continue;			
              }
			  
			  if($column == 'vis_nombre')
              {
				if(strpos($value, " ")==TRUE)
				{
					$partir=explode(" ", $value);	
					$nombre=$partir[0];
					$apellido=$partir[1];
					$modelado=Profile::model()->find('(first_name LIKE :match AND last_name LIKE :match2) OR 
													  (first_name LIKE :match2 AND last_name LIKE :match)  
													  ',array(':match' => "%$nombre%", ':match2' => "%$apellido%"));
					
					$word=$modelado->user_id;					
				}
				else
				{
					if(Profile::model()->find('first_name LIKE :match OR last_name LIKE :match',array(':match' => "%$value%")))
					{
						
						$modelado=Profile::model()->find('first_name LIKE :match OR last_name LIKE :match',
														array(':match' => "%$value%"));
						$word=$modelado->user_id;
					}
					else 
					{
						$word=NULL;
					}
				}	

				if($comparator=="=")
				{
					$criteria->compare("user_id", $word,true, $logicOp);		
					#$criteria->condition = "user_id like '".$word."'".$condicion_final;
				}
			     continue;			
              }

			  if($column == 'fecha') // FALTA LA FECHA
              {
				$date=explode('-',$value);
				$value=$date[2]."-".$date[1]."-".$date[0];	
				$sql = "created_on ".$comparator."'".$value."'";
				$criteria->addCondition($sql, $logicOp);
				
				#$criteria->compare("created_on", $comparator." ".$value,true, $logicOp);		
              }

			}	    
	
			
			
			#$criteria->together = true;
			 //$criteria->condition = 'data like "%look_id%"';
			 $criteria->compare("data", 'look_id',true);
			 $criteria->order = 'id DESC';  
			return new CActiveDataProvider($this, array(
                'criteria' => $criteria,
            ));
        
	}
	
	public function checkViews($data=array()) // contarle solo los clicks cada cierto tiempo en mismos looks
	{
				$clickValido=1;				
				$ip=$_SERVER['REMOTE_ADDR'];
				$comp='%"look_id":"'.$data['look_id'].'%';
				$sql=ShoppingMetric::model()->findAll("data like '".$comp."' and REMOTE_ADDR=:ip order by id DESC", 
				array(':ip'=>$ip));
				foreach($sql as $modelado)
				{
					 if(strtotime(date("Y-m-d H:i:s"))-strtotime($modelado->created_on)>5*60) 
					 {
					 	break; 
					 }
					 else 
					 {
						$clickValido=0;	
						break;
					 }
				}
				return $clickValido;
	}

}
	
