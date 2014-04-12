<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.dbtester
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * 
 * Joomla! dbtester Plugin
 * @author manish
 *
 */
class PlgSystemDbtester extends JPlugin
{
	
	/**
	 * 
	 * Supported testing array 
	 * @var Array
	 */
	static $available_db = Array('sqlite');
	
	
	public function onAfterRoute()
	{
		$app = JFactory::getApplication();
		
		// not available for frontend
		if ($app->isSite()) {
			return true;
		}
		
		// get input object and thier var 
		$input 		= $app->input;
		$option 	= $input->get('option','');
		$db_test	= $input->get('dbtest','');

		
		if ('plg_dbtesting' == $option  && in_array($db_test, self::$available_db)) {
			//@todo : implement for other db system	
			$this->testSqliteConnection();
			exit;
		}

		return true;
		
	}
		
	/**
	 * 
	 * Test with Sqlite 
	 */
	private function testSqliteConnection()
	{
		// stashing joomla current db driver
		$_stash = JFactory::$database;
		
		//db schema
		$sqlite_schema	= 
		"
				CREATE TABLE IF NOT EXISTS `jos_dbtesting_sqlite` (
				  `id` TEXT NOT NULL PRIMARY KEY,
				  `title` TEXT DEFAULT NULL,
				  `published` INTEGER DEFAULT 1
				) ; 
		
		";
		
		// test with SQLite memory database.
		$options = array(
			'driver' => 'sqlite',
			'database' => ':memory:',
			'prefix' => 'jos_'
		);
		
		############################################################
		############## Test:1 Connct with sqlite db ################
		############################################################ 
		try
		{
			// Attempt to instantiate the driver.
			$_driver = JDatabaseDriver::getInstance($options);

			// Create a new PDO instance for an SQLite memory database and load the test schema into it.
			$pdo = new PDO('sqlite::memory:'); 
			
			$pdo->exec($sqlite_schema);

			// Set the PDO instance to the driver using reflection whizbangery.
			self::setValue($_driver, 'connection', $pdo);
		}
		catch (Exception $e)
		{
			echo "# Database connection fail.<br />";
			return ;	
		}

		echo "<br /> # Database connection successfully created.<br />";
		
		// assign to sqlite driver object for further using
		JFactory::$database = $_driver;

		############################################################
		########### Test:2 get table listfrom sqlite db ############
		############################################################
		try{
			// get sqlite driver object
			$db = JFactory::getDbo();
			$tables = $db->getTableList();			
		}
		catch (Exception $e) {
			echo "# Table fetching from sqlite db is failed.<br />";
			var_dump($e->getMessage());
			exit;
		}

		if( empty($tables)){
			echo "# Query successfully run but Table fetching result is Zero.<br />";
		}
		
		echo "# Table list successfully fetched. <br /> ";
		var_dump($tables);
		
		// @TODO:: execute custom query
		
		JFactory::$database	= $_stash;
		return; 
	}
	
	
	/**
	 * Helper method that sets a protected or private property in a class by relfection.
	 *
	 * @param   object  $object        The object for which to set the property.
	 * @param   string  $propertyName  The name of the property to set.
	 * @param   mixed   $value         The value to set for the property.
	 *
	 * @return  void
	 *
	 */
	public static function setValue($object, $propertyName, $value)
	{
		$refl = new ReflectionClass($object);

		// First check if the property is easily accessible.
		if ($refl->hasProperty($propertyName))
		{
			$property = $refl->getProperty($propertyName);
			$property->setAccessible(true);

			$property->setValue($object, $value);
		}
		// Hrm, maybe dealing with a private property in the parent class.
		elseif (get_parent_class($object))
		{
			$property = new \ReflectionProperty(get_parent_class($object), $propertyName);
			$property->setAccessible(true);

			$property->setValue($object, $value);
		}
	}
}
