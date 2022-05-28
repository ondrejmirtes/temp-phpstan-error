<?php

/**
 * This class is for ajax trees
* @author matt
*/
abstract class DataStructure_Tree {

	const	DELIMITER				= '_',
			TREE_KEY__ROOT			= 'root',
			TREE_KEY__ORGANIZATION	= 'organization',
			TREE_KEY__DEFAULT		= 'default',
			TREE_KEY__DEPARTMENT	= 'department',
			TREE_KEY__EDUGRP		= 'edu-group',  // edugrp
			TREE_KEY__USER			= 'student'		// user
	;

	protected $name;

	protected $grayItems = array();

	/**
	 * prepare branch data for the js
	 *
	 * @return array
	*/
	public function Branchate(array $data) {
		$info = array();
		foreach ($data as $datum) {
			$key = $datum['type'];
			$id = self::getJoinedId($key, $datum['id']);
			$info[] = array(
					'attr' => array(
							'id' => $id,
							'class' => in_array($id, $this->grayItems) ? 'gray' : ''
					),
					'data' => $this->getDataInfo($datum['name'], $key),
					'metadata' => $datum['display'] ?: false,
					(!isset($datum['children']) || $datum['children'] > 0) ? 'state' : 'children' => (!isset($datum['children']) || $datum['children'] > 0) ? 'closed' : array()
			);
		}


		return $info;
	}

	/**
	 * get data for the js
	 *
	 * @param string $name
	 * @param string $key
	 * @return array
	 */
	protected function getDataInfo($name, $key) {
		return array('title' =>$name, 'icon' => $key);
	}

	/**
	 * parse several ids from the view
	 *
	 * @return array
	 */
	public function parseIds($ids) {
		$ret = array();
		foreach ($ids as $id) {
			$tmp = $this->parseId($id);
			$ret[] = array('type' => $tmp[0],
					'id' => $tmp[1]);
		}
		return $ret;
	}

	/**
	 * parse an id form the view
	 *
	 * @return array
	 */
	protected function parseId($id) {
		return explode(self::DELIMITER, $id);
	}

	/**
	 * get the id for the view
	 *
	 * @return string
	 */
	public static function getJoinedId($type, $id) {
		return $type . self::DELIMITER . $id;
	}

	/**
	 * get the id for the view
	 *
	 * @return string
	 */
	public static function parseOneId($id) {
		return explode(self::DELIMITER, $id);
	}


	/**
	 * get the data for the tree
	 *
	 * @return array
	 */
	protected abstract function getDataByParent($key, $id = null);

	/**
	 * get a branch of the tree
	 *
	 * @return array
	*/
	public function getBranch($rawId) {
		$ids = $this->parseId($rawId);
		$key = $ids[0];
		$id = isset($ids[1])?$ids[1]:null;
		return $this->getDataByParent($key, $id);
	}

	/**
	 * get the start of the tree
	 *
	 * @return array
	 */
	public function getRoot() {
		return $this->getDataByParent('root');
	}

	/**
	 * Not implemented
	 * get the whole tree
	 *
	 * @return array
	 */
	public function getTree() {
		return $this->Branchate($this->getDataByParent('root'), 'root');
	}

}