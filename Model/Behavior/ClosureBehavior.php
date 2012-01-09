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
	 * Initiate Closure Behavior
	 *
	 * @param object $model
	 * @param array $config
	 * @return void
	 * @access public
	 */
	function setup(&$model, $config = array()) {

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
		return true;
	}

	/**
	 * After delete callback
	 *
	 * @param object  Model using this behavior
	 * @access public
	 */
	function afterDelete(&$model) {

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

}