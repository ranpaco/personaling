<?php

/**
 * This is the model class for table "{{categoria}}".
 *
 * The followings are the available columns in table '{{categoria}}':
 * @property integer $id
 * @property integer $padreId
 * @property string $nombre
 * @property string $urlImagen
 * @property string $mTitulo
 * @property string $mDescripcion
 */
class Categoria extends CActiveRecord
{
	
	public $items;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Categoria the static model class
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
		return '{{categoria}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('padreId', 'numerical', 'integerOnly'=>true),
			array('nombre', 'length', 'max'=>100),
			array('urlImagen', 'length', 'max'=>150),
			array('mTitulo', 'length', 'max'=>80),
			array('mDescripcion', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, padreId, nombre, urlImagen, mTitulo, mDescripcion', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'padreId' => 'Categoría Padre',
			'nombre' => 'Nombre',
			'urlImagen' => 'Url Imagen',
			'mTitulo' => 'Meta Titulo',
			'mDescripcion' => 'Meta Descripcion',
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
		$criteria->compare('padreId',$this->padreId);
		$criteria->compare('nombre',$this->nombre,true);
		$criteria->compare('urlImagen',$this->urlImagen,true);
		$criteria->compare('mTitulo',$this->mTitulo,true);
		$criteria->compare('mDescripcion',$this->mDescripcion,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	
	
	public function beforeSave()
	{

	$valor->attributes=$_POST['Categoria'];

		if($this->padreId=='')
		{
			$this->padreId = 0;
		}
		
		echo($this->padreId);
		

	return parent::beforeSave();	
	}
	
	public function hasChildren(){
		if (Categoria::model()->findByAttributes(array('padreId'=>$this->id))){
			return true; 
		}	else {
			return false;
		}
	}
	
	public function getChildren(){
		return Categoria::model()->findAllByAttributes(array('padreId'=>$this->id));
	}
	
	
}