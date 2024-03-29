<?php
/*
 * Tipos de Pago 
 * 0:Paypal
 * 1:Cuenta
 * 2:Agregar al balance
 */

/**
 * This is the model class for table "{{pago}}".
 *
 * The followings are the available columns in table '{{pago}}':
 * @property integer $id
 * @property integer $estado
 * @property double $monto
 * @property string $fecha_solicitud
 * @property string $fecha_respuesta
 * @property integer $user_id
 * @property integer $admin_id
 * @property integer $tipo
 * @property integer $entidad
 * @property string $cuenta
 * @property integer $id_transaccion
 * @property integer $observacion
 * @property string $recipient
 * @property string $identification
 * @property string $accountType
 * @property string $email
 *
 * The followings are the available model relations:
 * @property Users $user
 * @property Users $admin
 */
class Pago extends CActiveRecord
{
    //Tipos de pago
    public static $tiposPago = array(
                        "PayPal",
                        "Cuenta Bancaria",        
                        "Agregar al balance",        
                        );
    public static $tiposCuenta = array(
                        "Ahorros",
                        "Corriente", 
                        );
    
    const MONTO_MIN = 1;
    const MONTO_MAX = 1000;
    const MONTO_MIN_PAYPAL = 50;
    const MONTO_MIN_BANCO = 50;
    const MONTO_MIN_BALANCE = 1;


                        /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Pago the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{pago}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        
        $rules= array(
            array('user_id', 'required'),
            array('tipo', 'required', 'message'=>'Debes seleccionar un método de pago'),
            array('entidad', 'required', 'message'=>'Debes indicar el nombre del banco'),
            array('cuenta', 'required', 'message'=>'Debes indicar tu cuenta'),
            array('monto', 'required', 'message'=>'Debes ingresar un monto'),
            array('estado, user_id, admin_id, tipo, id_transaccion', 'numerical',
                'integerOnly'=>true, "message" => "{attribute} debe ser un valor numérico"),
            array('monto', 'numerical', 'min' => self::MONTO_MIN, 'max' => self::MONTO_MAX,
                    'tooSmall' => 'El pago debe ser de al menos <b>'.
                        Yii::t('contentForm', 'currSym').' {min}</b>',
                    'tooBig' => 'El monto máximo que puedes solicitar es de <b>'.
                        Yii::t('contentForm', 'currSym').' {max}</b>'
                ),
            array('cuenta', 'length', 'max'=>140),
            array('fecha_solicitud, fecha_respuesta', 'safe'),
           // array('email', 'email', 'message'=>'Introduzca un correo electronico valido.'),
           
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, estado, monto, fecha_solicitud, fecha_respuesta, user_id,
                admin_id, tipo, entidad, cuenta, id_transaccion, observacion', 'safe', 'on'=>'search'),
        );
       return $rules;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
            'admin' => array(self::BELONGS_TO, 'User', 'admin_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'estado' => 'Estado',
            'monto' => 'Monto',
            'fecha_solicitud' => 'Fecha Solicitud',
            'fecha_respuesta' => 'Fecha Respuesta',
            'user_id' => 'User',
            'admin_id' => 'Admin',
            'tipo' => 'Tipo',
            'entidad' => 'Nombre del Banco',
            'cuenta' => 'Cuenta',
            'id_transaccion' => 'Id de Transacción',
            'accountType'=>'Tipo de cuenta',
            'recipient'=>'Beneficiario',
            'identification'=>'Cédula o RIF',
           // 'email'=>'E-mail del Beneficiario',
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
        $criteria->compare('estado',$this->estado);
        $criteria->compare('monto',$this->monto);
        $criteria->compare('fecha_solicitud',$this->fecha_solicitud,true);
        $criteria->compare('fecha_respuesta',$this->fecha_respuesta,true);
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('admin_id',$this->admin_id);
        $criteria->compare('tipo',$this->tipo);
        $criteria->compare('entidad',$this->entidad);
        $criteria->compare('cuenta',$this->cuenta,true);
        $criteria->compare('id_transaccion',$this->id_transaccion);
        
        $criteria->order = "fecha_solicitud DESC";

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    /**
     * Para usar la lista en los dropdowns
     * @return array la lista con los tipos de pago
     */
    public static function getTiposPago() {
        $pagosPS=self::$tiposPago;
          
        if(!Yii::app()->params['pagoPS']['paypal'])
            if(($key = array_search('PayPal', $pagosPS)) !== false)
             unset($pagosPS[$key]);
        if(!Yii::app()->params['pagoPS']['banco'])
            if(($key = array_search('Cuenta Bancaria', $pagosPS)) !== false)
             unset($pagosPS[$key]);
        if(!Yii::app()->params['pagoPS']['saldo'])
            if(($key = array_search('Agregar al balance', $pagosPS)) !== false)
             unset($pagosPS[$key]);
       
        return $pagosPS; 
    } 
    
    public static function getTiposCuenta() {
       
        return self::$tiposCuenta;
       
    }  
    public function getTipoCuenta($id) {
        if($id<count(self::$tiposPago)&&$id>=0)    
        return self::$tiposCuenta[$id];
        
        return "No Determinado"; 
    }   
    
    /**
     * Para conocer el tipo de pago de $this segun el array
     * @return type
     */
    public function getTipoPago() {
            
        return self::$tiposPago[$this->tipo]; 
    }    
    
    /*Retorna el estado*/
    public function getEstado() {
        $status = "ERROR";
        switch ($this->estado){
            case 0: $status = "En espera"; break;
            case 1: $status = "Pagado"; break;
            case 2: $status = "Rechazado"; break; 
        }
        return $status;
    }
    
    /*Retorna la fecha de carga como timestamp*/
    public function getFechaSolicitud() {
        return strtotime($this->fecha_solicitud);
    }
    /*Retorna la fecha de carga como timestamp*/
    public function getFechaRespuesta() {
        return strtotime($this->fecha_respuesta);
    }
    
    /*retorna el monto con formato o no*/
    public function getMonto($format = true) {
        $res = Yii::t('contentForm', 'currSym')." ";
        if ($format) {
            $res .= Yii::app()->numberFormatter->formatCurrency($this->monto, "");            
        } else {
            $res .= $this->monto;
        }
        return $res;

    }
    
    /**
     * This method is invoked before validation starts.
     * @return boolean whether validation should be executed. Defaults to true.
     */
    protected function beforeValidate() {
        
        $balance = $this->user->getSaldoPorComisiones(false);        
        $balance = round($balance, 2);        
        
        //Validar solamente cuando esta haciendo la solicitud
        if($this->isNewRecord){     
            
            if($this->monto > $balance){
                
                $this->addError("monto", "No tienes suficiente balance para
                    solicitar este pago");

                return false;
            }
            
            if($this->tipo == 0 && $this->monto < self::MONTO_MIN_PAYPAL){ //si es paypal
                
                $this->addError("monto", "Debes alcanzar un monto igual o superior
                    a ".Yii::t('contentForm', 'currSym')." ".self::MONTO_MIN_PAYPAL."
                        en tus comisiones para poder solicitar el pago a través de Paypal.");

                return false;
            }
            if($this->tipo == 1 && $this->monto < self::MONTO_MIN_BANCO){ //si es paypal
                
                $this->addError("monto", "Debes alcanzar un monto igual o superior
                    a ".Yii::t('contentForm', 'currSym')." ".self::MONTO_MIN_BANCO."
                        en tus comisiones para poder solicitar el pago a través de
                        una Cuenta Bancaria.");

                return false;
            }
            if($this->tipo == 2 && $this->monto < self::MONTO_MIN_BALANCE){ //si es paypal
                
                $this->addError("monto", "Debes alcanzar un monto igual o superior
                    a ".Yii::t('contentForm', 'currSym')." ".self::MONTO_MIN_BALANCE."
                        en tus comisiones para poder solicitar que el pago sea agregado
                        a tu balance.");

                return false;
            }

        }

        return parent::beforeValidate();
    }
    
}
