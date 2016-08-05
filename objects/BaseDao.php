<?php

class BaseDao {
	/**
	 * @var \M1ke\Sql\ExtendedPdo
	 */
	public $db;

	public function __construct($db){
		$this->db = $db;
	}
}
