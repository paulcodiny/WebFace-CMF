<?php

class WfMysql {
	
	const HYDRATION_NODE = 4;
	
	private static $_instance = null;
	
	protected $_debug = false;
	protected $_lastQuery = null;
	protected $_connection = null;
	
	protected $_tablesInfo = array();
	protected $_tableAliases = array();
	
	protected $_tokens = array();
	protected $_querySelectedFields = array();
	
	protected $_recordNodeStructure  = null;
	protected $_recordNodeReferences = array();
	protected $_recordNode = array();
	protected $_recordNodeIndexes = array();
	
	public function __construct(array $params = array()) {
		$cfg = WfConfig::getInstance();
		$this->_connection = mysql_pconnect($cfg->db_host, $cfg->db_user, $cfg->db_pass);
		mysql_select_db($cfg->db_name);
		
		mysql_query("SET NAMES utf8 ;");
		mysql_query("SET GLOBAL time_zone = '+6:00' ;");
	}
	
	public function getInstance(array $params = array()) {
		if (null == self::$_instance) {
			self::$_instance = new self($params);
		}
		
		return self::$_instance;
	}
	
	public function query($q) {
		if ($this->_debug) {
			echo $q;
		}
		$this->_lastQuery = mysql_query($q) or exit(mysql_error());
		
		return $this;
	}
	
	public function fetch($hydration = MYSQL_NUM) {
		$fetchedData = array();
		if (null === $this->_lastQuery) {
			throw new Exception('Any query should be executed before fetch');
		}
		
		$nodeHydration = false;
		if ($hydration == self::HYDRATION_NODE) {
			$nodeHydration = true;
			$hydration = MYSQL_NUM;
		}
		
		while ($row = mysql_fetch_array($this->_lastQuery, $hydration)) {
			$fetchedData[] = $row;
		}
		
		// MANY 2 MANY
		// HAS MANY
		// HAS ONE
		
		// ->select()
		// ->from(wf_pages)
		// ->with(wf_widgets)
		
		if (!$nodeHydration) {
			return $fetchedData;
		}
		
		foreach ($fetchedData as $resultTuple) {
			$lastUsedIndexes = array();
			foreach ($this->_recordNodeReferences as $tableAlias => &$node) {
				
				$recordUnique = array();
				$primaries = $this->_getTablePrimaries($this->_getTableNameByAlias($tableAlias));
				foreach ($node['fields'] as $fieldName => $fieldIndex) {
					$column = substr($fieldName, strpos($fieldName, '.') + 1);
					if (in_array($column, $primaries)) {
						$recordUnique[$column] = $resultTuple[$fieldIndex];
					}
				}

				$zeroRecords = true;
				foreach ($recordUnique as $primaryValue) {
					if ($primaryValue) {
						$zeroRecords = false;
						break;
					}
				}
				
				if ($zeroRecords) {
					// perhaps this relation has not records
					$lastUsedIndexes[$tableAlias] = false;
					continue;
				}
				
				$id = serialize($recordUnique);
				if (!isset($node['data'][$id])) {
					$node['data'][$id] = array();
				}

				if (isset($this->_recordNodeIndexes[$tableAlias])) {
					if (!isset($this->_recordNodeIndexes[$tableAlias][$id])) {
						$this->_recordNodeIndexes[$tableAlias][$id] = count($this->_recordNodeIndexes[$tableAlias]);
					}
				} else {
					$this->_recordNodeIndexes[$tableAlias] = array($id => 0);
				}

				$lastUsedIndexes[$tableAlias] = $id;

				// fill fields with data
				foreach ($node['fields'] as $fieldName => $fieldIndex) {
					$column = substr($fieldName, strpos($fieldName, '.') + 1);
					$node['data'][$id][$column] = $resultTuple[$fieldIndex];
				}
			}
			
			// move relations data
			$reverse = array_reverse($this->_recordNodeReferences);
			foreach ($reverse as $tableAlias => &$node) {
				$lastUsedIndex = $lastUsedIndexes[$tableAlias];
				foreach ($node['linkFor'] as $alias => &$linkNode) {
					if (!isset($node['data'][$lastUsedIndex][$alias])) {
						$node['data'][$lastUsedIndex][$alias] = array();
					}
					
					$lastUserLinkIndex = $lastUsedIndexes[$alias];
					if ($lastUserLinkIndex) {
						$node['data'][$lastUsedIndex][$alias][$lastUserLinkIndex] = $linkNode['data'][$lastUserLinkIndex];
					} else {
						// zero records
						$node['data'][$lastUsedIndex][$alias] = array();
					}
					
				}
			}
		}
			
		// change indexes
		reset($this->_tableAliases);
		$firstAlias = current($this->_tableAliases);
		$fetchedData = $this->_prepareNodeData($firstAlias, $this->_recordNodeStructure['data']);
		
		return $fetchedData;
	}
	
	protected function _prepareNodeData($alias, $data) {
		foreach ($data as $index => $record) {
			foreach ($this->_recordNodeReferences as $tableAlias => $linkNode) {
				if (isset($record[$tableAlias])) {
					$record[$tableAlias] = $this->_prepareNodeData($tableAlias, $record[$tableAlias]);
				}
			}
			$data[$this->_recordNodeIndexes[$alias][$index]] = $record;
			unset($data[$index]);
		}
		
		return $data;
	}
	
	public function fetchRecord($hydration = MYSQL_NUM) {
		$data = $this->fetch($hydration);
		if (!isset($data[0])) {
			return null;
		}
		
		return $data[0];
	}
	
	public function select(array $fields = array()) {
		$this->_tokens['select'] = array(
			'fields' => $fields
		);
		
		return $this;
	}
		
	public function table($table, $alias = null) {
		$this->_setTableAlias($table, $alias);
		$this->prepareTable($table);
		
		$this->_tokens['table'] = array(
			'name' => $table
		);
		
		return $this;
	}
	
	public function leftJoin($table, $condition, $alias = null) {
		$this->_setTableAlias($table, $alias);
		$this->prepareTable($table);
		
		$this->_tokens['left_join'][] = array(
			'name' => $table,
			'condition' => $condition
		);
		
		return $this;
	}
	
	public function where($condition) {
		$this->_tokens['where'] = $condition;
		
		return $this;
	}
	
	public function orderBy($order) {
		$this->_tokens['order_by'] = $order;
		
		return $this;
	}

	public function execute() {
		$q = $this->_createQuery($this->_tokens);
		
		return $this->query($q);
	}

	protected function _setTableAlias($table, $alias = null) {
		if (isset($this->_tableAliases[$table])) {
			throw new Exception('Table ' . $table . ' already has alias ' . $this->_tableAliases[$table]);
		}
		
		if (null === $alias) {
			$alias = 't' . count($this->_tableAliases) + 1;
		}
		
		$this->_tableAliases[$table] = $alias;
		
		return $this;
	}
	
	protected function _getTableAlias($table) {
		if (!isset($this->_tableAliases[$table])) {
			throw new Exception('Table ' . $table . ' has not alias');
		}
		
		return $this->_tableAliases[$table];
	}
	
	protected function _getTableNameByAlias($alias) {
		foreach ($this->_tableAliases as $tableName => $tableAlias) {
			if ($alias == $tableAlias) {
				return $tableName;
			}
		}
		
		throw new Exception('Table for alias' . $alias . ' not exists.');
	}
	
	protected function _getTableColumns($table) {
		if (!isset($this->_tablesInfo[$table])) {
			throw new Exception('Table ' . $table . ' not prepared.');
		}
		
		return $this->_tablesInfo[$table]['columns'];
	}
	
	protected function _getTablePrimaries($table) {
		if (!isset($this->_tablesInfo[$table])) {
			throw new Exception('Table ' . $table . ' not prepared.');
		}
		
		return $this->_tablesInfo[$table]['primaries'];
	}
	
	public function prepareTable($table) {
		if (isset($this->_tablesInfo[$table])) {
			return $this;
		}
		
		$this->_tablesInfo[$table] = array(
			'columns' => array(),
			'primaries' => array()
		);
		$columns = $this->query('SHOW COLUMNS FROM ' . $table)->fetch(MYSQLI_ASSOC);
		foreach ($columns as $column) {
			$this->_tablesInfo[$table]['columns'][$column['Field']] = array(
				'type'    => $column['Type'],
				'is_null' => $column['Null'] == 'YES',
				'key'     => $column['Key'],
				'default' => $column['Default'],
			);
			if ($column['Key'] == 'PRI') {
				$this->_tablesInfo[$table]['primaries'][] = $column['Field'];
			}
		}
		
		return $this;
	}
	
	public function prepareRecordNode($table, $relations = array()) {
		// table => linkFor array
		// we know that FROM table has not links in the first this is empty array
		$primaryTableAlias = $this->_getTableAlias($table);
		$this->_recordNodeStructureTemplate = array('fields' => array(), 'data' => array(), 'linkFor' => array());
		
		$this->_recordNodeStructure = $this->_recordNodeStructureTemplate;
		$this->_recordNodeReferences[$primaryTableAlias] =& $this->_recordNodeStructure;
		
		if ($this->_singleTable) {
			return $this;
		}
		
		foreach ($this->_tableAliases as $currentAlias) {
			foreach ($relations as $joinTable) {
				$left = key($joinTable['condition']);
				$leftTableAlias = substr($left, 0, strpos($left, '.'));

				$right = current($joinTable['condition']);
				$rightTableAlias = substr($right, 0, strpos($right, '.'));
				
				if ($leftTableAlias == $currentAlias
						|| $rightTableAlias == $currentAlias) {
					// check that this table is not already in structure
					$alias = $this->_getTableAlias($joinTable['name']);
					$notInStructure = true;
					foreach (array_keys($this->_recordNodeReferences) as $table) {
						if ($table == $alias) {
							$notInStructure = false;
							break;
						}
					}
					if ($notInStructure) {
						$currentRecordNode =& $this->_recordNodeReferences[$currentAlias];
						$currentRecordNode['linkFor'][$alias] = $this->_recordNodeStructureTemplate;

						$this->_recordNodeReferences[$alias] =& $currentRecordNode['linkFor'][$alias];
					}
				}
			}
		}
		
		
		return $this;
	}
	
	protected function _buildSelectPart($tokens) {
		$query = 'SELECT ';
		
		if (!isset($tokens['table']['name'])) {
			throw new Exception('Set table for select() (->table method)');
		}

		if (empty($tokens['select']['fields'])) {
			foreach ($this->_tableAliases as $tableName => $alias) {
				$columns = array_keys($this->_getTableColumns($tableName));
				for ($i = 0, $n = count($columns); $i < $n; $i++) {
					$columns[$i] = $alias . '.' . $columns[$i];
				}
				$tokens['select']['fields'] = array_merge($tokens['select']['fields'], $columns);
			}
			$primariesAdded = true;
		}

		$selectFields = '';
		$alias = $this->_getTableAlias($tokens['table']['name']);
		if (is_array($tokens['select']['fields'])) {
			if ($this->_singleTable) {
				if (!$primariesAdded) {
					// add table PKs to table select field for fetch
					$primaryKeys = $this->_getTablePrimaries($tokens['table']['name']);
					$tokens['select']['fields'] = array_merge($tokens['select']['fields'], $primaryKeys);
				}
				
				$this->_recordNodeStructure['fields'] = $tokens['select']['fields'];

				// check that fields have not aliases
				if (strpos(current($tokens['select']['fields']), '.') === false) {
					$selectFields = "{$alias}." . implode(", {$alias}.", $tokens['select']['fields']);
				} else {
					$selectFields = implode(", ", $tokens['select']['fields']);
				}
				
			} else {
				if (!$primariesAdded) {
					foreach ($this->_tableAliases as $tableName => $tableAlias) {
						$primaryKeys = $this->_getTablePrimaries($tableName);
						$tableSelectFields = array();
						foreach ($tokens['select']['fields'] as $field) {
							if (strpos($field, $tableAlias . '.') !== false) {
								$tableSelectFields[] = $field;
							}
						}

						// check that all primary keys are exist in select fields
						foreach ($primaryKeys as $pk) {
							foreach ($tableSelectFields as $field) {
								// all fields selected, not necessary to add primaries
								if (strpos($field, '*') !== false) {
									break 2;
								}
								if ($tableAlias . '.' . $pk === $field) {
									continue 2;
								}
							}
							// primary key not found in SELECTed fields - add it
							$tokens['select']['fields'][] = $tableAlias . '.' . $pk;
						}
					}
				}
				
				foreach ($tokens['select']['fields'] as $fieldIndex => $fieldName) {
					$fieldTable = substr($fieldName, 0, strpos($fieldName, '.'));
					$this->_recordNodeReferences[$fieldTable]['fields'][$fieldName] = $fieldIndex;
				}

				$selectFields = implode(", ", $tokens['select']['fields']);
			}

			if ($this->_singleTable) {
				foreach ($tokens['select']['fields'] as $field) {
					$this->_querySelectedFields[] = array(
						'name' => $field,
						'table' => $this->_tableAliases[$this->_singleTable]
					);
				}
			} else {
				foreach ($tokens['select']['fields'] as $field) {
					$this->_querySelectedFields[] = array(
						'name' => $field,
						'table' => substr($field, 0, strpos($field, '.'))
					);
				}
			}
		} elseif (is_string($tokens['select']['fields'])) {
			throw new Exception('Not implemented yet.');
		}

		$query .= " $selectFields FROM `{$tokens['table']['name']}` `{$alias}`";
		
		return $query;
	}
	
	protected function _createQuery($tokens) {
		if (count($this->_tableAliases) == 1) {
			$tables = array_keys($this->_tableAliases);
			$this->_singleTable = $tables[0];
		} else {
			$this->_singleTable = false;
		}
		
		if (isset($tokens['left_join'])) {
			$this->prepareRecordNode($tokens['table']['name'], $tokens['left_join']);
		} else {
			$this->prepareRecordNode($tokens['table']['name']);
		}
		
		$query = '';
		if (isset($tokens['select'])) {
			$query .= $this->_buildSelectPart($tokens);
		}
		
		if (isset($tokens['left_join'])) {
			foreach ($tokens['left_join'] as $joinTable) {
				$alias = $this->_getTableAlias($joinTable['name']);
				$joinCondition = '';
				foreach ($joinTable['condition'] as $left => $right) {
					if ($joinCondition) {
						$joinCondition = ' AND ' . $joinCondition;
					}
					$joinCondition .= $left . ' = ' . $right;
				}
				 
				$query .= " LEFT JOIN `{$joinTable['name']}` `{$alias}` ON {$joinCondition}";
			}
		}
		
		if (isset($tokens['where'])) {
			$query .= " WHERE ";
			$index = 0;
			$alias = false;
			if ($this->_singleTable) {
				$alias = $this->_tableAliases[$this->_singleTable];
			}
			foreach ($tokens['where'] as $field => $value) {
				if ($index > 0) {
					$query .= ' AND ';
				}
				
				$fieldCondition = "{$field} = '" . mysql_real_escape_string($value) . "'";
				if ($alias && strpos($field, '.') === false) {
					$fieldCondition = "`{$alias}`." . $fieldCondition; 
				}
				$query .= $fieldCondition;
				$index++;
			}
		}
		
		if (isset($tokens['order_by'])) {
			$query .= " ORDER BY " . $tokens['order_by'];
		}
		
		return $query;
	}
	
	public function debug($debug = true) {
		$this->_debug = $debug;
		
		return $this;
	}
}