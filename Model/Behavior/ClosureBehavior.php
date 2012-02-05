<?php
/**
 * Closure Model Behavior
 * 
 * An efficient alternative to the CakePHP Tree Behavior
 *
 * @package Closure
 * @author ProLoser (Dean Sofer)
 **/
class ClosureBehavior extends ModelBehavior {

	/**
	 * Contains configuration settings for use with individual model objects.
	 * Individual model settings should be stored as an associative array, 
	 * keyed off of the model name.
	 *
	 * @var array
	 * @access public
	 * @see Model::$alias
	 */
	var $settings = array();
	
	/**
	 * Defaul setting values
	 *
	 * @access private
	 * @var array
	 */
    private $defaults = array(
    	'limit' => false,
    	'auto' => true,
    	'ignore' => array(),
    	'useDbConfig' => null,
    	'model' => null
    );

	/**
	 * Allows the mapping of preg-compatible regular expressions to public or
	 * private methods in this class, where the array key is a /-delimited regular
	 * expression, and the value is a class method.  Similar to the functionality of
	 * the findBy* / findAllBy* magic methods.
	 *
	 * @var array
	 * @access public
	 */
	var $mapMethods = array();
	
	/**
	 * Shadow table prefix
	 * Only change this value if it causes table name crashes
	 *
	 * @access private
	 * @var string
	 */
	private $suffix = '_treepaths';

	/**
	 * Initiate Closure Behavior
	 *
	 * @param object $model
	 * @param array $config
	 * @return void
	 * @access public
	 */
	function setup(&$model, $config = array()) {
		if (is_array($config)) {
			$this->settings[$model->alias] = array_merge($this->defaults, $config);			
		} else {
			$this->settings[$model->alias] = $this->defaults;
		}		
		$this->createShadowModel($model);
	}

	/* -- All possible behavior callbacks have been stubbed out. Remove those you do not need. -- */

	/**
	 * Before find callback
	 *
	 * @param object $model Model using this behavior
	 * @param array $queryData Data used to execute this query, i.e. conditions, order, etc.
	 * @return boolean True if the operation should continue, false if it should abort
	 * @access public
	 */
	function beforeFind(&$model, $query) { 
		return true;
	}

	/**
	 * After find callback. Can be used to modify any results returned by find and findAll.
	 *
	 * @param object $model Model using this behavior
	 * @param mixed $results The results of the find operation
	 * @param boolean $primary Whether this model is being queried directly (vs. being queried as an association)
	 * @return mixed Result of the find operation
	 * @access public
	 */
	function afterFind(&$model, $results, $primary) { 
		return $results;
	}

	/**
	 * Before validate callback
	 *
	 * @param object $model Model using this behavior
	 * @return boolean True if validate operation should continue, false to abort
	 * @access public
	 */
	function beforeValidate(&$model) { 
		return true;
	}

	/**
	 * Before save callback
	 *
	 * @param object $model Model using this behavior
	 * @return boolean True if the operation should continue, false if it should abort
	 * @access public
	 */
	function beforeSave(&$model) { 
		return true;
	}

	/**
	 * After save callback
	 *
	 * @param object $model Model using this behavior
	 * @param boolean $created True if this save created a new record
	 * @access public
	 * @return boolean True if the operation succeeded, false otherwise
	 */
	function afterSave(&$model, $created) { 
		if ($created) {
			// INSERT INTO Comments ... <-- generates comment #8
			// INSERT INTO TreePaths (ancestor, descendant) VALUES (8,8)
			// INSERT INTO TreePaths (ancestor, descendant) SELECT ancestor, 8 FROM TreePaths WHERE descendant = 5
			$model->ShadowModel->create();
			return $model->ShadowModel->save(array('ancestor' => $model->id, 'descendant' => $model->id));
		}
		return true;
	}

	/**
	 * Before delete callback
	 *
	 * @param object $model Model using this behavior
	 * @param boolean $cascade If true records that depend on this record will also be deleted
	 * @return boolean True if the operation should continue, false if it should abort
	 * @access public
	 */
	function beforeDelete(&$model, $cascade = true) { 
		$this->deleteCascade = $cascade;
		$this->deleteId = $model->id;
		return true;
	}

	/**
	 * After delete callback
	 *
	 * @param object  Model using this behavior
	 * @access public
	 */
	function afterDelete(&$model) {
		if ($this->deleteCascade) {
			$this->_deleteSubtree($this->deleteId);
		}
	}

	/**
	 * DataSource error callback
	 *
	 * @param object $model Model using this behavior
	 * @param string $error Error generated in DataSource
	 * @access public
	 */
	function onError(&$model, $error) { 
	
	}
	
	/**
	 * Returns a generic model that maps to the current $model's shadow table.
	 *
	 * @param object $model
	 * @return boolean
	 */
	private function createShadowModel(&$model) {
		if (is_null($this->settings[$model->alias]['useDbConfig'])) {
			$dbConfig = $model->useDbConfig;
		} else {
			$dbConfig = $this->settings[$model->alias]['useDbConfig'];			
		}
		$db = & ConnectionManager::getDataSource($dbConfig);
		if ($model->useTable) {
			$shadow_table = $model->useTable;	
		} else {
			$shadow_table = Inflector::tableize($model->name);
		}
		$shadow_table = $shadow_table . $this->revision_suffix;
		$prefix = $model->tablePrefix ? $model->tablePrefix : $db->config['prefix'];
		$full_table_name = $prefix . $shadow_table;

		$existing_tables = $db->listSources();
		if (!in_array($full_table_name, $existing_tables)) {
			$model->ShadowModel = false;
			return false;
		}  
		$useShadowModel = $this->settings[$model->alias]['model'];
		if (is_string($useShadowModel) && App::import('model', $useShadowModel)) {
			$model->ShadowModel = new $useShadowModel(false, $shadow_table, $dbConfig);
		} else {
			$model->ShadowModel = new Model(false, $shadow_table, $dbConfig);
		}			
		if ($model->tablePrefix) {
			$model->ShadowModel->tablePrefix = $model->tablePrefix;
		}
		$model->ShadowModel->alias = $model->alias;
		return true;
	}
	
	public function _deleteSubtree($ancestor) {
		// DELETE FROM TreePaths WHERE descendant IN (SELECT descendant FROM TreePaths WHERE ancestor = 4)
		// DELETE p FROM TreePaths p JOIN TreePaths a USING (descendant) WHERE a.ancestor = 4
	}
	
	public function _deleteChildren($descendant) {
		// DELETE FROM TreePaths WHERE descendant = 7
		// ON DELETE CASCADE
	}

	public function _findDescendants($model, $state, $query, $results = array()) {
		// SELECT c.* FROM Comments c JOIN TreePaths t ON (c.comment_id = t.descendant) WHERE t.ancestor = 4
		if ($state === 'before') {
			$query['joins'][] = array(
				'alias' => $model->ShadowModel->alias,
				'table' => '',
				'type' => '',
				'conditions' => "`{$model->alias}`.`{$model->primaryKey}` = `{$model->ShadowModel->alias}`.`descendant`",
			);
			$query['conditions'][$model->ShadowModel->alias.'.ancestor'] = $query['id'];
			return $query;
		} elseif ($state === 'after') {
			return $results;
		}
	}
	
	public function _findAncestors($model, $state, $query, $results = array()) {
		// SELECT c.* FROM Comments c JOIN TreePaths t ON (c.comment_id = t.ancestor) WHERE t.descendant = 4
		if ($state === 'before') {
			$query['joins'][] = array(
				'alias' => $model->ShadowModel->alias,
				'table' => '',
				'type' => '',
				'conditions' => "`{$model->alias}`.`{$model->primaryKey}` = `{$model->ShadowModel->alias}`.`ancestor`",
			);
			$query['conditions'][$model->ShadowModel->alias.'.descendant'] = $query['id'];
			return $query;
		} elseif ($state === 'after') {
			return $results;
		}
	}
	
	public function _findParents($model, $state, $query, $results = array()) {
		// SELECT c.* FROM Comments c JOIN TreePaths t ON (c.comment_id = t.descendant) WHERE t.ancestor = 4 AND t.depth = 1
		if ($state === 'before') {
			$query['joins'][] = array(
				'alias' => $model->ShadowModel->alias,
				'table' => '',
				'type' => '',
				'conditions' => "`{$model->alias}`.`{$model->primaryKey}` = `{$model->ShadowModel->alias}`.`ancestor`",
			);
			$query['conditions'][$model->ShadowModel->alias.'.ancestor'] = $query['id'];
			$query['conditions'][$model->ShadowModel->alias.'.depth'] = $query['depth'];
			return $query;
		} elseif ($state === 'after') {
			return $results;
		}
	}
	
	public function _findChildren($model, $state, $query, $results = array()) {
		// SELECT c.* FROM Comments c JOIN TreePaths t ON (c.comment_id = t.ancestor) WHERE t.descendant = 4 AND t.depth = 1
		if ($state === 'before') {
			$query['joins'][] = array(
				'alias' => $model->ShadowModel->alias,
				'table' => '',
				'type' => '',
				'conditions' => "`{$model->alias}`.`{$model->primaryKey}` = `{$model->ShadowModel->alias}`.`descendant`",
			);
			$query['conditions'][$model->ShadowModel->alias.'.descendant'] = $query['id'];
			$query['conditions'][$model->ShadowModel->alias.'.depth'] = $query['depth'];
			return $query;
		} elseif ($state === 'after') {
			return $results;
		}
	}

}