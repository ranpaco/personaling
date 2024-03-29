<?php

/**
 * This is the model class for table "{{payPersonalShopper}}".
 *
 * The followings are the available columns in table '{{payPersonalShopper}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $affiliatePay_id
 * @property integer $total_views
 * @property double $percent
 * @property double $amount
 *
 * The followings are the available model relations:
 * @property Users $user
 * @property AffiliatePayment $affiliatePay
 */
class PayPersonalShopper extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PayPersonalShopper the static model class
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
		return '{{payPersonalShopper}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, affiliatePay_id, total_views, amount', 'required'),
			array('user_id, affiliatePay_id, total_views', 'numerical', 'integerOnly'=>true),
			array('percent, amount', 'numerical'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, affiliatePay_id, total_views, percent, amount', 'safe', 'on'=>'search'),
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
			'affiliatePay' => array(self::BELONGS_TO, 'AffiliatePayment', 'affiliatePay_id'),
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
			'affiliatePay_id' => 'Affiliate Pay',
			'total_views' => 'Total Views',
			'percent' => 'Percent',
			'amount' => 'Amount',
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
		$criteria->compare('affiliatePay_id',$this->affiliatePay_id);
		$criteria->compare('total_views',$this->total_views);
		$criteria->compare('percent',$this->percent);
		$criteria->compare('amount',$this->amount);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}