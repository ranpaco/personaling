<?php

/**
 * This is the model class for table "{{tienda}}".
 *
 * The followings are the available columns in table '{{tienda}}':
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $logo
 * @property integer $type
 * @property integer $status
 * @property string $url
 *
 * The followings are the available model relations:
 * @property Producto[] $productos
 * 
 * Type:
 * 0: Monomarca
 * 1: Multimarca
 * 
 */
class Tienda extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Tienda the static model class
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
		return '{{tienda}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, url', 'required', 'message'=>'No puedes dejar este campo en blanco'),
			array('type, status', 'numerical', 'integerOnly'=>true),
			array('url', 'url','defaultScheme'=>'http'),
			array('name, logo, url', 'length', 'max'=>100),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, description, logo, type, status, url', 'safe', 'on'=>'search'),
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
			'productos' => array(self::HAS_MANY, 'Producto', 'tienda_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Nombre',
			'description' => 'Descripción',
			'logo' => 'Logotipo',
			'type' => 'Monomarca',
			'status' => 'Status',
			'url' => 'Url',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('logo',$this->logo,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('status',$this->status);
		$criteria->compare('url',$this->url,true); 

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getUrlVista(){
		$url=strtoupper($this->url);	
		if(strpos($url,'HTTP://')!==false){
			$url=str_replace('HTTP://', '', $url);
		}
		if(strpos($url,'HTTPS://')!==false){
			$url=str_replace('HTTPS://', '', $url);
		}
		if(strpos($url,'WWW.')!==false){
			$url=str_replace('WWW.', '', $url);
		}
		if(strpos($url,'/')!==false){
			$url=explode('/',$url);
			$url=$url[0];
		}
		
		return $url;
	}
}