<?php

/**
 * This is the model class for table "{{defectuoso}}".
 *
 * The followings are the available columns in table '{{defectuoso}}':
 * @property integer $id
 * @property integer $cantidad
 * @property string $fecha
 * @property integer $user_id
 * @property integer $preciotallacolor_id
 */
class Defectuoso extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Defectuoso the static model class
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
		return '{{defectuoso}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cantidad, user_id, preciotallacolor_id', 'numerical', 'integerOnly'=>true),
			array('fecha', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, cantidad, fecha, user_id, preciotallacolor_id', 'safe', 'on'=>'search'),
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
			'cantidad' => 'Cantidad',
			'fecha' => 'Fecha',
			'user_id' => 'User',
			'preciotallacolor_id' => 'Preciotallacolor',
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
		$criteria->compare('cantidad',$this->cantidad);
		$criteria->compare('fecha',$this->fecha,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('preciotallacolor_id',$this->preciotallacolor_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}