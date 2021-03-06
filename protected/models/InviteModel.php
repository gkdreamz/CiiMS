<?php

class InviteModel extends CFormModel
{
	/**
	 * @var $id int		The User's ID that we are working with 
	 */
	public $id = NULL;
	
	/**
	 * @var $firstName string	The user's first name
	 */
	public $firstName = NULL;
	
	/**
	 * @var $lastName string	The user's last name
	 */
	public $lastName = NULL;

	/**
	 * @var $displayName string	The user's requested display name
	 */
	public $displayName = NULL;
	
	/**
	 * @var $email string
	 */
	public $email = NULL;

	/**
	 * @var $password string	The user's selected password
	 */
	public $password = NULL;

	/**
	 * Validation Rules
	 * @return array
	 */
	public function rules()
	{
		return array(
			array('id, firstName, lastName, displayName, password', 'required'),
			array('firstName, lastName, displayName, password', 'length', 'max' => 255)	
		);
	}

	/**
	 * Attribute labels
	 * @return array
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('ciims.models.InviteModel', 'ID'),
			'firstName' => Yii::t('ciims.models.InviteModel', 'First Name'),
			'lastName' => Yii::t('ciims.models.InviteModel', 'Last Name'),
			'displayName' => Yii::t('ciims.models.InviteModel', 'Display Name'),
			'password' => Yii::t('ciims.models.InviteModel', 'Password'),
		);
	}

	/**
	 * Actually creates the users
	 * @param  int $user_id    The id of the user that was created
	 * @return bool            If the user was created
	 */
	public function save($user_id)
	{
		$this->id = $user_id;
		if (!$this->validate())
			return false;


		if (!function_exists('password_hash'))
			require_once YiiBase::getPathOfAlias('ext.bcrypt.bcrypt').'.php';
		
		$user = Users::model()->findByPk($this->id);

		// Bcrypt the initial password instead of just using the basic hashing mechanism
		$hash = Users::model()->encryptHash($this->email, $this->password, Yii::app()->params['encryptionKey']);
		$cost = Cii::getBcryptCost();

		$this->password = password_hash($hash, PASSWORD_BCRYPT, array('cost' => $cost));

		$user->attributes = array(
			'email'			=> $this->email,
			'password'		=> $this->password,
			'firstName'		=> $this->firstName,
			'lastName'	 	=> $this->lastName,
			'displayName'	=> $this->displayName,
			'status'		=> Users::ACTIVE
		);

		return $user->save();
	}

}
