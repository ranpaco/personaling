<?php

/**
 * This is the model class for table "{{bolsa}}".
 *
 * The followings are the available columns in table '{{bolsa}}':
 * @property integer $id
 * @property integer $user_id
 * @property string $created_on
 *
 * The followings are the available model relations:
 * @property Users $user
 * @property LookHasTblBolsa[] $lookHasTblBolsas
 * @property LookHasTblBolsa[] $lookHasTblBolsas1
 * @property Orden[] $ordens
 * @property Orden[] $ordens1
 */
class Bolsa extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Bolsa the static model class
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
		return '{{bolsa}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id', 'required'),
			array('user_id', 'numerical', 'integerOnly'=>true),
			array('created_on', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, created_on', 'safe', 'on'=>'search'),
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
			'lookHasTblBolsas' => array(self::HAS_MANY, 'LookHasTblBolsa', 'tbl_bolsa_id'),
			'lookHasTblBolsas1' => array(self::HAS_MANY, 'LookHasTblBolsa', 'tbl_bolsa_user_id'),
			'ordens' => array(self::HAS_MANY, 'Orden', 'tbl_bolsa_id'),
			'ordens1' => array(self::HAS_MANY, 'Orden', 'user_id'),
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
			'created_on' => 'Created On',
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
		$criteria->compare('created_on',$this->created_on,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}