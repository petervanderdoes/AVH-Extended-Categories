<?php
if (! defined('AVH_FRAMEWORK'))
	die('You are not allowed to call this page directly.');
if (! class_exists('AVH_DB')) {

	final class AVH_DB
	{
		private $_data_cache;

		/**
		 * Fetch MySQL Field Names
		 *
		 * @access	public
		 * @param	string	the table name
		 * @return	array
		 */
		public function list_fields ($table = '')
		{
			global $wpdb;
			
			$sql = $this->_getQueryShowColumns($table);
			
			$query = $wpdb->get_results($sql, ARRAY_A);
			
			$retval = array ();
			foreach ($query->result_array() as $row) {
				if (isset($row['COLUMN_NAME'])) {
					$retval[] = $row['COLUMN_NAME'];
				} else {
					$retval[] = current($row);
				}
			}
			
			$this->data_cache['field_names'][$table] = $retval;
			return $this->data_cache['field_names'][$table];
		}

		/**
		 * Determine if a particular field exists
		 * @access	public
		 * @param	string
		 * @param	string
		 * @return	boolean
		 */
		public function field_exists ($field_name, $table_name)
		{
			return (array_key_exists($field_name, $this->list_fields($table_name)));
		}

		/**
		 * Show column query
		 *
		 * Generates a platform-specific query string so that the column names can be fetched
		 *
		 * @access	public
		 * @param	string	the table name
		 * @return	string
		 */
		private function _getQueryShowColumns ($table = '')
		{
			global $wpdb;
			return $wpdb->prepare('SHOW COLUMNS FROM ' . $table);
		
		}
	}
}

