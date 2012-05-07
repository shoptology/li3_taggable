<?php

namespace li3_taggable\models;

class Tags extends \lithium\data\Model {

	public $validates = array();
	protected $_schema = array(
		'_id' => array('type' => 'id'),
		'name' => array('type' => 'string')
	);

}

