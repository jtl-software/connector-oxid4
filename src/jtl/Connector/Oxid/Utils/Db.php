<?php
namespace jtl\Connector\Oxid\Utils;

class Db {
	private static $instance;
	private $db;

	public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct()
    {
    	$this->db = \oxDb::getInstance()->getDb(\oxDb::FETCH_MODE_ASSOC);
    }

    public function getAll($query)
    {
    	$this->db->setFetchMode(\oxDb::FETCH_MODE_ASSOC);
        return $this->db->getAll($query);
    }

    public function getOne($query)
    {
    	return $this->db->getOne($query);
    }

    public function execute($query)
    {
    	return $this->db->execute($query);
    }

    public function insert($obj, $table)
    {
        if (is_object($obj) && strlen($table) > 0) {
            $query = "INSERT INTO " . $table . " SET ";
    
            $sets = array();

            $members = array_keys(get_object_vars($obj));

            if (is_array($members) && count($members) > 0) {
                foreach ($members as $member) {                    
                    if (!is_array($obj->$member) && !is_object($obj->$member)) {
                        $value = $this->db->quote($obj->$member);
                        if ($obj->$member === null) {
                            $value = "NULL";
                        }
                        
                        $sets[] = "{$member} = {$value}";
                    }
                }
            }

            $query .= implode(', ', $sets);
   
            return $this->db->execute($query);
        } 
    }

    public function update($obj, $table, $key, $value, array $ignores = null)
    {
        if (is_object($obj) && strlen($table) > 0) {
            if ((is_array($key) && is_array($value)) || (strlen($key) > 0 && strlen($value) > 0)) {
                $query = "UPDATE " . $table . " SET ";

                $sets = array();

                $members = array_keys(get_object_vars($obj));
                if (is_array($members) && count($members) > 0) {
                    foreach ($members as $member) {
                        if ($ignores !== null && is_array($ignores) && count($ignores) > 0) {
                            if (in_array($member, $ignores)) {
                                continue;
                            }
                        }

                        if (!is_array($obj->$member) && !is_object($obj->$member)) {
                            $membervalue = $this->db->quote($obj->$member);
                            if ($obj->$member === null) {
                                $membervalue = "NULL";
                            }

                            $sets[] = "{$member} = {$membervalue}";
                        }
                    }

                    $query .= implode(', ', $sets);

                    if (is_array($key) && is_array($value)) {
                        if (count($key) == count($value)) {
                            foreach ($key as $i => $keyStr) {
                                if ($i > 0) {
                                    $query .= " AND {$keyStr} = " . $this->db->quote($value[$i]);
                                } else {
                                    $query .= " WHERE {$keyStr} = " . $this->db->quote($value[$i]);
                                }
                            }
                        }
                    } elseif (strlen($key) > 0 && strlen($value) > 0) {
                        $query .= " WHERE {$key} = " . $this->db->quote($value);
                    }

                    return $this->db->execute($query);
                }
            }
        }

        return false;
    }
}