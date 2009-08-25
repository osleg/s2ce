<?php
class User extends AppModel {

	var $name = 'User';

	var $hasAndBelongsToMany = array(
		'buddy' => array(
			'className' => 'User',
			'joinTable' => 'buddies',
			'foreignKey' => 'source_id',
			'associationForeignKey' => 'target_id'
		)
	);

	var $validate = array(
		'email' => array(
			'email' => array(
				'rule' => 'email',
				'required' => true,
				'message' => 'Please enter a valid email address'
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'This email address is already in use.'
			)
		),
		'username' => array(
			'between' => array(
				'rule' => array('between', 3, 25),
				'required' => true,
				'message' => 'Your nickname should be between 3 and 25 characters.'
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'This nickname has already been taken.'
			)			
		),
		'passwrd' => array(
			'minLength' => array(
				'rule' => array('minLength', 5),
				'required' => true,
				'message' => 'Your password must be at least 5 characters long.'
			)
		)
	);

}
?>