<?php

namespace li3_taggable\extensions\data\behavior;

/**
 * A behavior that makes your models taggable.
 *
 * To enable, add `Taggable` to the `$_actsAs` array in your model that extends
 * from `li3_behaviors\extensions\Model`:
 *
 * {{{
 * protected $_actsAs = array(
 *     'Taggable' => array(
 *         'field' => 'tags',
 *         'model' => 'li3_taggable\models\Tags'
 *     )
 * );
 * }}}
 *
 * The above settings for `field` and `model` are the defaults and may be
 * omitted or substitured with your own field and tag model names.
 *
 * The `Tags` model is used for keeping a collection of all tags in
 * your application.  If you don't want this, pass `false` to the `'model'`
 * behavior config option.
 *
 *
 *
 * When bound to a model, `Taggable` adds an instance method called `tags()`
 * which takes the following arguments:
 * 
 *     - $tags _string|array_: (optional) A single tag or array of tags.  Use an empty array 
 *         to unset the tag list or null (the default) to just return the current tags.
 *     - $remove _boolean_: (optional) If true, will remove the specified tags rather than add them
 *
 * It returns the current tag list as a string separated by commas. Ex:
 * {{{
 *    $tags = $entity->tags(array('hello', 'there'));
 *    // returns "hello, there", but $entity->tags
 *    // is now set to array('hello', 'there')
 *
 *    $tags = $entity->tags('hello', true);
 *    // returns "there" and $entity->tags
 *    // is now set to array('there')
 * }}}
 *
 * For handling the user experience of adding tags to your documents, we
 * recommend the [jquery-tags-input](https://github.com/xoxco/jQuery-Tags-Input)
 * project and will likely be adding a view helper to integrate with it as
 * examples on adding autocomplete of tags.
 */
class Taggable extends \lithium\core\StaticObject {

	/**
	 * An array of configurations indexed by model class name, for each model
	 * to which this class is bound.
	 *
	 * @var array
	 */
	protected static $_configurations = array();

	/**
	 * The default configuration options
	 */
	protected static $_defaults = array(
		'field' => 'tags',
		'model' => 'li3_taggable\models\Tags'
	);

	/**
	 * Binds a `tags` instance method to a model.  Also adds save filter that
	 * to the model which converts comma-separated tag strings to arrays
	 * and after a successful save, ensures that each tag has been added to the
	 * collection for the configured `Tags` model. 
	 *
	 * @param string $model Fully-namespaced model class name this is being bound to
	 * @param array $options Valid options include:
	 *     - `'field'`: Name of the field in your entity that will
	 *         hold the tags
	 *     - `'model'`: Fully-namespaced model to use for storing a list of
	 *         all tags in your application.  Pass `false` to disable this
	 *         feature.
	 * @todo keep track of tags when item is deleted
	 */
	public static function bind($model, array $config = array()) {
		$config += static::$_defaults;
		static::$_configurations[$model] = $config;

		$tags = function($entity, $tags = null, $remove = false) use ($config) {
			$field = $config['field'];
			if ($tags !== null) {
				if (!$entity->$field) {
					$entity->$field = array();
				}
				if (is_string($tags)) {
					$tags = array($tags);
				}
				$current = $entity->$field;
				if (is_object($current)) {
					$current = $current->data();
				}
				if ($remove) {
					$entity->$field = array_values(array_diff($current, $tags));
				} else {
					$entity->$field = array_merge($current, array_diff($tags, $current));
				}
			}
			if ($entity->$field) {
				return implode(', ', $entity->$field->data());
			} else {
				return '';
			}
		};
		$model::instanceMethods(compact('tags'));

		$model::applyFilter('save', function($self, $params, $chain) use ($config) {
			$tagClass = $config['model'];
			$field = $config['field'];

			if (isset($params['data'][$field])) {
				if (!empty($params['data'][$field])) {
					$tags = $params['data'][$field];
					if (is_string($tags)) {
						$params['data'][$field] = array_map('trim', explode(',', $tags));
					}
				} else {
					$params['data'][$field] = null;
				}
			}
			if (is_string($params['entity']->$field)) {
				if (!empty($params['entity']->$field)) {
					$tags = $params['entity']->$field;
					$params['entity']->$field = array_map('trim', explode(',', $tags));
				} else {
					$params['entity']->$field = null;
				}
			}

			$result = $chain->next($self, $params, $chain);

			if ($result && $tagClass) {
				$entity = $params['entity'];
				if ($entity->$field && !is_string($entity->$field)) {
					foreach ($entity->$field as $tag) {
						if (!$tagClass::findByName($tag)) {
							$newTag = $tagClass::create(array('name' => $tag));
							$newTag->save();
						}
					}
					// workaround li3 issue with multiple foreach loops
					$entity->$field->sync();
				}
			}
			return $result;
		});

		return true;
	}

}

?>