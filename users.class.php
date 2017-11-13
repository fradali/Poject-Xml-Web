<?php
/**
	 * XML Project
	 * @version		1.0.0
	 * @authors 		Frad Ali and Jmal Chadly	 	 
	 */
	class users extends xmlMap {
		/**
		 * Load users
		 */		 		
		public function __construct() {
			$this->hasMany('user');
			$this->mapTag('user', 'user');
			parent::__construct('users.xml');
		}
		/**
		 * Search an user
		 */		 		
		public function findUser($login) {
			foreach($this->user as $u) {
				if ($u->login == $login) return $u;
			}
		}
		/**
		 * Try to login
		 */		 		
		public function connect($login, $password) {
			$user = $this->findUser($login);
			if ($user) {
				$ok = $user->login($password);
				if ($ok) $this->save(); // SAVE LOGIN DATE
				return $ok;
			} else return false;
		}
	}
	/**
	 * User class
	 */	 	
	class user extends xmlNode {
		/**
		 * Try to login
		 */		 		
		public function login($password) {
			if ($this->password == $password) {
				$this->lastLogin = time();
				return true;
			} else {
				return false;
			}
		}
		/**
		 * Get the last connection date
		 */		 		
		public function getLastLogin() {
			if ($this->lastLogin > 0) {
				return date('Y-m-d H:i:s', $this->lastLogin);
			} else return 'never';
		}
	}
?>