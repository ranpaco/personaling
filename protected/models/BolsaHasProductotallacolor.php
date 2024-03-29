<?php

/**
 * This is the model class for table "{{bolsa_has_productotallacolor}}".
 *
 * The followings are the available columns in table '{{bolsa_has_productotallacolor}}':
 * @property integer $bolsa_id
 * @property integer $preciotallacolor_id
 * @property integer $cantidad
 *
 * The followings are the available model relations:
 * @property PrecioTallaColor $preciotallacolor
 */
class BolsaHasProductotallacolor extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return BolsaHasProductotallacolor the static model class
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
		return '{{bolsa_has_productotallacolor}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('bolsa_id, preciotallacolor_id', 'required'),
			array('bolsa_id, preciotallacolor_id, cantidad', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('bolsa_id, preciotallacolor_id, cantidad', 'safe', 'on'=>'search'),
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
			'preciotallacolor' => array(self::BELONGS_TO, 'Preciotallacolor', 'preciotallacolor_id'),
			//'color' => array(self::BELONGS_TO, 'Color', array('color_id'=>'id'),'through'=>'preciotallacolor'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'bolsa_id' => 'Bolsa',
			'preciotallacolor_id' => 'Preciotallacolor',
			'cantidad' => 'Cantidad',
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

		$criteria->compare('bolsa_id',$this->bolsa_id);
		$criteria->compare('preciotallacolor_id',$this->preciotallacolor_id);
		$criteria->compare('cantidad',$this->cantidad);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}