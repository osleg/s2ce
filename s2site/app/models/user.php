<?php
class User extends AppModel {

	var $name = 'User';

	var $hasAndBelongsToMany = array(
		'User' => array(
			'className' => 'User',
			'joinTable' => 'buddies',
			'foreignKey' => 'source_id',
			'associationForeignKey' => 'target_id'
		)
	);

	var $validate = array(
		'email' => array('email'),
		'nickname' => array('maxlength', 30),
		'password' => array('minlength', 5)
	);

}
?>