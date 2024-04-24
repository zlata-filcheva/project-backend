<?php

class Database
{
    protected $connection = null;

    public function __construct()
    {
        try {
            $this->connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME);

            if ( mysqli_connect_errno()) {
                throw new Exception("Could not connect to database.");
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    protected function select($query = "", $types = '', $params = [])
    {
        try {
            $stmt = $this->executeStatement($query, $types, $params);
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $result;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }
        return false;
    }

    protected function insert($query = "", $types, $params = [])
    {
        try {
            $stmt = $this->executeStatement($query, $types, $params);

            $stmt->close();
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }

        return false;
    }

    private function executeStatement($query = "" , $types = '', $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);

            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }

            if(!isset($params)) {
                foreach ($params as &$value) {
                    $value = $this->connection->real_escape_string($value);
                }

                unset($value);

                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();

            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }
    }
}