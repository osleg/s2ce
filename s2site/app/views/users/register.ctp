<h1>Register a new account</h2>

<?
	echo $form->create('User', array('action' => 'register'));
	echo $form->input('email', array('label' => 'E-Mail Address'));
	echo $form->input('username', array('label' => 'Nickname'));	
	echo $form->input('passwrd', array('type' => 'password', 'label' => 'Password'));
	echo $form->end('Register User');
?>