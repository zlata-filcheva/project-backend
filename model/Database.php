<?php

class Database
{
    protected $connection = null;

    public function __construct()
    {
        $hasDevelopmentMode = $_SERVER['SERVER_NAME'] === '127.0.0.1';
        $dbHostname = $hasDevelopmentMode
            ? DB_HOST_DEVELOPMENT
            : DB_HOST_PRODUCTION;

        $servername = "mariadb";  // Use the service name defined in docker-compose.yml
        $username = "root";
        $password = "";
        $dbname = "newReddit";

        try {
            //$this->connection = new mysqli($dbHostname, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME);

            $conn = new mysqli($servername, $username, $password, $dbname);

            if (mysqli_connect_errno()) {
                throw new Exception("Could not connect to database.");
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    protected function selectData($query, $types = '', $params = [])
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

    protected function modifyData($query, $types, $params = [])
    {
        try {
            $output = [];
            $stmt = $this->executeStatement($query, $types, $params);

            $output['insert_id'] = $stmt->insert_id;
            $output['affected_rows'] = $stmt->affected_rows;

            $stmt->close();

            return $output;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }
    }

    private function executeStatement($query, $types = '', $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);

            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }

            if(count($params) < 1) {

                $stmt->execute();

                return $stmt;
            }

            foreach ($params as &$value) {
                if (is_array($value)) {
                    $value = json_encode($value);

                    continue;
                }

                $value = $this->connection->real_escape_string($value);
            }

            unset($value);
            $stmt->bind_param($types, ...$params);

            $stmt->execute();

            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }
    }
}