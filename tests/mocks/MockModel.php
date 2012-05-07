<?php

namespace li3_taggable\tests\mocks;

class MockModel extends \li3_behaviors\extensions\Model {
	protected $_actsAs = array(
		'Taggable' => array(
			'model' => 'li3_taggable\tests\mocks\MockTags'
		)
	);

	public function save($entity, $data = null, array $options = array()) {
		$self = static::_object();
		$params = compact('entity', 'data', 'options');

		$filter = function($self, $params) {
			$entity = $params['entity'];
			$options = $params['options'];

			if ($params['data']) {
				$entity->set($params['data']);
			}
			return true;
		};

		return static::_filter(__FUNCTION__, $params, $filter);
	}
}

?>