<?php

class Login extends BaseDao {


	public function authenticate($login, $password){
		$user = $this->db->selectOne('users', ['email' => $login]);

		return $user['password']==$password;
	}

	public function register($login, $name, $email, $password){

	}

	public function isAdmin(){

	}

	public function signin(){

	}

	public function logout(){

	}
}
