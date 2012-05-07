<?php

namespace li3_taggable\tests\cases;

use li3_taggable\tests\mocks\MockModel;
use li3_taggable\tests\mocks\MockTags;
use li3_taggable\tests\mocks\Wtf;

class TaggableTest extends \lithium\test\Unit {

	public function setUp() {}

	public function testInstanceMethods() {
		$entity = MockModel::create();

		$tags = $entity->tags('hi');
		$this->assertEqual('hi', $tags);

		$tags = $entity->tags('there');
		$this->assertEqual('hi, there', $tags);
		$this->assertEqual('hi, there', $entity->tags());
		$this->assertEqual(array('hi', 'there'), $entity->tags->data());

		$tags = $entity->tags(array('foo', 'bar'));
		$this->assertEqual('hi, there, foo, bar', $tags);
		$this->assertEqual(array('hi', 'there', 'foo', 'bar'), $entity->tags->data());

		$tags = $entity->tags('hi', true);
		$this->assertEqual('there, foo, bar', $tags);
		$this->assertEqual(array('there', 'foo', 'bar'), $entity->tags->data());

		$tags = $entity->tags(array('there', 'bar'), true);
		$this->assertEqual('foo', $tags);
		$this->assertEqual(array('foo'), $entity->tags->data());

		$tags = $entity->tags(array('howdy'));
		$this->assertEqual('foo, howdy', $entity->tags());
	}

	public function testSaveFilter() {
		$expected = array('one', 'two', 'three');
		$saved = array(
			array('_id' => 1, 'name' => 'one'),
			array('_id' => 2, 'name' => 'two'),
			array('_id' => 3, 'name' => 'three')
		);

		$entity = MockModel::create();
		$entity->save(array('tags' => ''));
		$this->assertIdentical(null, $entity->tags);
		$this->assertEqual(MockTags::$saved, null);

		$entity = MockModel::create();
		$entity->save(array('tags' => 'one,  two,three'));
		$this->assertEqual(array('one', 'two', 'three'), $entity->tags->data());
		$this->assertEqual(MockTags::$saved, $saved);

		$entity = MockModel::create(array('tags' => 'one,  two,three'));
		$entity->save();
		$this->assertEqual(array('one', 'two', 'three'), $entity->tags->data());
		$this->assertEqual(MockTags::$saved, $saved);

		$entity->tags('four');
		$entity->tags('two', true);
		$entity->save();
		$this->assertEqual(array('one', 'three', 'four'), $entity->tags->data());
		$saved[] = array('_id' => 4, 'name' => 'four');
		$this->assertEqual(MockTags::$saved, $saved);
	}

	public function tearDown() {}

}

?>