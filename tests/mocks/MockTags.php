<?php

namespace li3_taggable\tests\mocks;

use lithium\data\collection\DocumentArray;

class MockTags extends \lithium\data\Model {

	public static $saved = null;

	public function save($entity, $data = null, array $options = array()) {
		$params = compact('entity', 'data', 'options');
		$model = get_called_class();

		$filter = function($self, $params) use ($model) {
			$entity = $params['entity'];
			$options = $params['options'];
			if ($params['data']) {
				$entity->set($params['data']);
			}
			$model::$saved[] = $entity->data() + array('_id' => count($model::$saved) + 1);
			return true;
		};

		return static::_filter(__FUNCTION__, $params, $filter);
	}

	public static function find($type, array $options = array()) {
		if (!isset($options['conditions'])) {
			return new DocumentArray(array(
				'model' => get_called_class(),
				'data' => static::$saved
			));
		}
		$conditions = $options['conditions'];
		if (isset($conditions['name'])) {
			return static::_findByName($conditions['name']);
		}
		return null;
	}

	protected static function _findByName($name) {
		$found = null;
		if (empty(static::$saved)) {
			return null;
		}
		foreach (static::$saved as $entity) {
			if ($name === $entity['name']) {
				if (!$found) {
					$found = new DocumentArray(array('model' => get_called_class()));
				}
				$found[] = static::create($entity);
			}
		}
		return $found;
	}

}

?>