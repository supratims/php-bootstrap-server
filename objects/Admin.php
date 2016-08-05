<?php

class Admin extends BaseDao {

	public function users(){
		return $this->db->selectAll('users', []);
	}

}
