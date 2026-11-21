<?php
require_once 'ayar.php';

class DB implements ArrayAccess {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private $conn;
    private $error;
    private $lastQuery;
    private $lastParams = [];
    private $lastResult;
    private $inTransaction = false;
    private $logFile = 'db_log.txt';
    private $cacheEnabled = false;
    private $cacheDir = __DIR__.'/cache/';
    private $cacheTime = 3600;
    private $loggingEnabled = false;

    public function __construct(bool $enableLogging = true) {
        $this->loggingEnabled = $enableLogging;
        $this->connect();
        $this->setCharsetAndTimezone();
    }
    
    public function enableLogging(bool $enable = true): void {
        $this->loggingEnabled = $enable;
    }
    
    private function writeLog(string $message): void {
        if ($this->loggingEnabled) {
            $timestamp = date('Y-m-d H:i:s');
            $logEntry = "[$timestamp] $message\n";
            file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
        }
    }

    private function connect(): void {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($this->conn->connect_error) {
            $this->writeLog("Bağlantı hatası: " . $this->conn->connect_error);
            die("Bağlantı hatası: " . $this->conn->connect_error);
        }
    }

    private function setCharsetAndTimezone(): void {
        $this->conn->query("SET NAMES 'utf8'");
        $this->conn->query("SET time_zone = '+03:00'");
    }

    public function close(): void {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function beginTransaction(): bool {
        if ($this->inTransaction) {
            $this->error = "Zaten bir transaction açık.";
            $this->log($this->error);
            return false;
        }
        $result = $this->conn->begin_transaction();
        if ($result) {
            $this->inTransaction = true;
            $this->log("Transaction başlatıldı.");
        } else {
            $this->error = "Transaction başlatılamadı: " . $this->conn->error;
            $this->log($this->error);
        }
        return $result;
    }

    public function commit(): bool {
        if (!$this->inTransaction) {
            $this->error = "Açık bir transaction yok.";
            $this->log($this->error);
            return false;
        }
        $result = $this->conn->commit();
        if ($result) {
            $this->inTransaction = false;
            $this->log("Transaction onaylandı.");
        } else {
            $this->error = "Transaction onaylanamadı: " . $this->conn->error;
            $this->log($this->error);
        }
        return $result;
    }

    public function rollback(): bool {
        if (!$this->inTransaction) {
            $this->error = "Açık bir transaction yok.";
            $this->log($this->error);
            return false;
        }
        $result = $this->conn->rollback();
        if ($result) {
            $this->inTransaction = false;
            $this->log("Transaction geri alındı.");
        } else {
            $this->error = "Transaction geri alınamadı: " . $this->conn->error;
            $this->log($this->error);
        }
        return $result;
    }

    private function log(string $message): void {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] $message\n";
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    public function getError(): ?string {
        if ($this->error) {
            $output = "<div style='margin: 10px; padding: 10px; font-family: monospace; border: 1px solid red; background-color: #ffe6e6; color: red;'>";
            $simulatedQuery = $this->lastQuery;
            if (!empty($this->lastParams)) {
                foreach ($this->lastParams as $param) {
                    $param = is_string($param) ? "'" . addslashes($param) . "'" : $param;
                    $simulatedQuery = preg_replace('/\?/', $param, $simulatedQuery, 1);
                }
            }
            $output .= "<div style='width: 100%; font-weight: bold;'>Sorgu: " . htmlspecialchars($simulatedQuery) . "</div>\n";
            $output .= "<strong>Hata:</strong> <span style='font-weight: bold;'>" . htmlspecialchars($this->error) . "</span>\n";
            $output .= "</div>";
            $this->log("Hata: " . $this->error . " - Sorgu: " . $simulatedQuery);
            return $output;
        }
        return null;
    }
    
    public function batchInsert(string $table, array $rows): bool {
        if (empty($rows)) return false;
        
        $columns = implode('`, `', array_keys($rows[0]));
        $placeholders = implode(', ', array_fill(0, count($rows[0]), '?'));
        $values = [];
        $params = [];
        
        foreach ($rows as $row) {
            $values[] = "($placeholders)";
            $params = array_merge($params, array_values($row));
        }
        
        $valuesClause = implode(', ', $values);
        $query = "INSERT INTO `$table` (`$columns`) VALUES $valuesClause";
        
        return $this->query($query, $params);
    }

    public function batchUpdate(string $table, array $rows, string $keyColumn): bool {
        if (empty($rows)) return false;
        
        $this->beginTransaction();
        
        try {
            foreach ($rows as $row) {
                $setParts = [];
                $params = [];
                foreach ($row as $column => $value) {
                    if ($column != $keyColumn) {
                        $setParts[] = "`$column` = ?";
                        $params[] = $value;
                    }
                }
                $params[] = $row[$keyColumn];
                
                $setClause = implode(', ', $setParts);
                $query = "UPDATE `$table` SET $setClause WHERE `$keyColumn` = ?";
                
                if (!$this->query($query, $params)) {
                    throw new Exception("Batch update failed");
                }
            }
            
            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            return false;
        }
    }
        
   public function getJson(string $query, array $params = []) {
    $data = $this->getList($query, $params);
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
    public function insertJson(string $table, string $jsonData): bool {
        $data = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error = "Invalid JSON data";
            return false;
        }
        return $this->insert($table, $data);
    }
        
    public function getTableColumns(string $table): array {
        $query = "SHOW COLUMNS FROM `$table`";
        $columns = $this->getList($query);
        return array_map(function($col) {
            return $col->Field;
        }, $columns);
    }

    public function tableExists(string $table): bool {
        $query = "SHOW TABLES LIKE ?";
        return (bool)$this->getVar($query, [$table]);
    }
        
    public function getQueryLog(): string {
        if (file_exists($this->logFile)) {
            return file_get_contents($this->logFile);
        }
        return "No log file found";
    }

    public function clearQueryLog(): void {
        if (file_exists($this->logFile)) {
            file_put_contents($this->logFile, '');
        }
    }

    public function getLastQuery(): array {
        return [
            'query' => $this->lastQuery,
            'params' => $this->lastParams,
            'result' => $this->lastResult
        ];
    }
        
    public function insert(string $table, array $data): bool {
        $columns = implode('`, `', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $query = "INSERT INTO `$table` (`$columns`) VALUES ($placeholders)";
        return $this->query($query, array_values($data));
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): bool {
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "`$column` = ?";
        }
        $setClause = implode(', ', $setParts);
        $query = "UPDATE `$table` SET $setClause WHERE $where";
        $params = array_merge(array_values($data), $whereParams);
        return $this->query($query, $params);
    }

    public function delete(string $table, string $where, array $params = []): bool {
        $query = "DELETE FROM `$table` WHERE $where";
        return $this->query($query, $params);
    }
    
    public function paginate(string $query, array $params = [], int $perPage = 10, int $currentPage = 1): array {
        $countQuery = "SELECT COUNT(*) FROM ($query) AS total_count";
        $total = $this->getVar($countQuery, $params);
        
        $totalPages = ceil($total / $perPage);
        $offset = ($currentPage - 1) * $perPage;
        
        $dataQuery = "$query LIMIT $perPage OFFSET $offset";
        $data = $this->getList($dataQuery, $params);
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'last_page' => $totalPages,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }

    private $queryBuilder = [
        'select' => '*',
        'from' => '',
        'where' => [],
        'params' => [],
        'order' => '',
        'limit' => '',
        'offset' => ''
    ];

    public function select(string $columns = '*'): self {
        $this->queryBuilder['select'] = $columns;
        return $this;
    }

    public function from(string $table): self {
        $this->queryBuilder['from'] = $table;
        return $this;
    }

    public function where(string $condition, array $params = []): self {
        $this->queryBuilder['where'][] = $condition;
        $this->queryBuilder['params'] = array_merge($this->queryBuilder['params'], $params);
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self {
        $this->queryBuilder['order'] = "ORDER BY $column $direction";
        return $this;
    }

    public function limit(int $limit, int $offset = 0): self {
        $this->queryBuilder['limit'] = "LIMIT $limit";
        $this->queryBuilder['offset'] = $offset ? "OFFSET $offset" : '';
        return $this;
    }

    public function get(): array {
        $where = empty($this->queryBuilder['where']) ? '' : 'WHERE ' . implode(' AND ', $this->queryBuilder['where']);
        $query = sprintf(
            "SELECT %s FROM %s %s %s %s %s",
            $this->queryBuilder['select'],
            $this->queryBuilder['from'],
            $where,
            $this->queryBuilder['order'],
            $this->queryBuilder['limit'],
            $this->queryBuilder['offset']
        );
        
        $this->queryBuilder = [
            'select' => '*',
            'from' => '',
            'where' => [],
            'params' => [],
            'order' => '',
            'limit' => '',
            'offset' => ''
        ];
        
        return $this->getList($query, $this->queryBuilder['params']);
    }
    
    public function getVar(string $query, array $params = []): mixed {
        $this->lastQuery = $query;
        $this->lastParams = $params;
        $this->lastResult = null;
        $stmt = $this->prepareStatement($query, $params);
        if (!$stmt) return null;

        $this->executeStatement($stmt);
        $result = $stmt->get_result();
        $row = $result->fetch_array();
        $this->lastResult = $row ? [$row[0]] : null;
        $stmt->close();

        $this->log("getVar sorgusu çalıştırıldı: " . $query);
        return $row ? $row[0] : null;
    }

    public function getRow(string $query, array $params = []): ?object {
        $this->lastQuery = $query;
        $this->lastParams = $params;
        $this->lastResult = null;
        $stmt = $this->prepareStatement($query, $params);
        if (!$stmt) return null;

        $this->executeStatement($stmt);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $this->lastResult = $row ? [$this->createDualAccessObject($row)] : null;
        $stmt->close();

        $this->log("getRow sorgusu çalıştırıldı: " . $query);
        return $row ? $this->createDualAccessObject($row) : null;
    }

    public function getArray(string $query, array $params = []): array {
        $list = $this->getList($query, $params);
        return array_map(function($item) {
            return $item->toArray();
        }, $list);
    }
        
    public function getFlatArray(string $sql, array $params = []): array {
        $result = $this->getArray($sql, $params);
        if (empty($result)) {
            return [];
        }
        return array_column($result, 'kullanici_id');
    }
            
    public function getList(string $query, array $params = []): array {
        $this->lastQuery = $query;
        $this->lastParams = $params;
        $this->lastResult = null;
        $stmt = $this->prepareStatement($query, $params);
        if (!$stmt) return [];

        $this->executeStatement($stmt);
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $this->createDualAccessObject($row);
        }
        $this->lastResult = $rows;
        $stmt->close();

        $this->log("getList sorgusu çalıştırıldı: " . $query);
        return $rows;
    }

    public function query(string $query, array $params = []): bool {
        $this->lastQuery = $query;
        $this->lastParams = $params;
        $this->lastResult = null;
        $stmt = $this->prepareStatement($query, $params);
        if (!$stmt) return false;

        $result = $this->executeStatement($stmt);
        $stmt->close();
        $this->log("query çalıştırıldı: " . $query);
        return $result;
    }

    public function getLastInsertId(): int {
        $id = $this->conn->insert_id;
        $this->log("Son eklenen ID alındı: " . $id);
        return $id;
    }

    private function prepareStatement(string $query, array $params = []): ?mysqli_stmt {
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            $this->error = "Sorgu hazırlama hatası: " . $this->conn->error;
            $this->log($this->error);
            return null;
        }

        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        return $stmt;
    }

    private function executeStatement(mysqli_stmt $stmt): bool {
        if ($stmt->execute() === false) {
            $this->error = "Çalıştırma hatası: " . $stmt->error;
            $this->log($this->error);
            return false;
        }
        return true;
    }

    public function count(string $table, string $where = ''): int {
        $query = "SELECT COUNT(*) FROM `$table`";
        if (!empty($where)) $query .= " WHERE $where";
        $result = $this->getVar($query);
        $this->log("count sorgusu çalıştırıldı: " . $query);
        return (int)$result;
    }
    
    public function countr(array $data): int {
        $count = count($data);
        $this->log("countr: Verilen dizideki eleman sayısı: " . $count);
        return $count;
    }
    

    public function sum(string $column, string $table, string $where = ''): float {
        $query = "SELECT SUM(`$column`) FROM `$table`";
        if (!empty($where)) $query .= " WHERE $where";
        $result = $this->getVar($query);
        $this->log("sum sorgusu çalıştırıldı: " . $query);
        return (float)$result;
    }
    
    public function sumr(array $data, string $column): float {
        $total = 0.0;
        foreach ($data as $row) {
            if (isset($row->$column) && is_numeric($row->$column)) {
                $total += $row->$column;
            }
        }

        $this->log("sumr: Sütun '$column' için toplam: " . $total);
        return $total;
    }

    public function groupBySum(string $groupColumn, string $sumColumn, string $table, string $where = ''): array {
        $query = "SELECT `$groupColumn`, SUM(`$sumColumn`) as total FROM `$table`";
        if (!empty($where)) $query .= " WHERE $where";
        $query .= " GROUP BY `$groupColumn`";
        $result = $this->getList($query);
        $this->log("groupBySum sorgusu çalıştırıldı: " . $query);
        return $result;
    }

    private function createDualAccessObject(array $data): object {
        return new class($data) implements ArrayAccess {
            private array $data;

            public function __construct(array $data) {
                $this->data = $data;
            }

            public function __get(string $name): mixed {
                return $this->data[$name] ?? null;
            }

            public function offsetExists(mixed $offset): bool {
                return isset($this->data[$offset]);
            }

            public function offsetGet(mixed $offset): mixed {
                return $this->data[$offset] ?? null;
            }

            public function offsetSet(mixed $offset, mixed $value): void {
                $this->data[$offset] = $value;
            }

            public function offsetUnset(mixed $offset): void {
                unset($this->data[$offset]);
            }

            public function toArray(): array {
                return $this->data;
            }
        };
    }

    public function debug(): void {
        $output = "<div style='margin: 10px; padding: 10px; font-family: monospace; border: 1px solid ";
        $output .= $this->error ? "red; background-color: #ffe6e6; color: red;'>" : "green; background-color: #e6ffe6; color: green;'>";

        $simulatedQuery = $this->lastQuery ?: "Sorgu yok";
        if (!empty($this->lastParams)) {
            foreach ($this->lastParams as $param) {
                $param = is_string($param) ? "'" . addslashes($param) . "'" : $param;
                $simulatedQuery = preg_replace('/\?/', $param, $simulatedQuery, 1);
            }
        }
        $output .= "<div style='width: 100%; font-weight: bold;'>" . htmlspecialchars($simulatedQuery) . "</div>\n";
        if ($this->error) {
            $output .= "<strong>Hata:</strong> <span style='font-weight: bold;'>" . htmlspecialchars($this->error) . "</span>\n";
        }
        $output .= "</div>";
        echo $output;
        $this->log("Debug: " . $simulatedQuery . ($this->error ? " - Hata: " . $this->error : ""));
    }

    public function debugt(): void {
        $output = "<div style='margin: 2px; padding: 1px; font-family: monospace; border: 1px solid ";
        $output .= $this->error ? "red; background-color: #ffe6e6; color: red;'>" : "green; background-color: #e6ffe6; color: green;'>";

        $simulatedQuery = $this->lastQuery ?: "Sorgu yok";
        if (!empty($this->lastParams)) {
            foreach ($this->lastParams as $param) {
                $param = is_string($param) ? "'" . addslashes($param) . "'" : $param;
                $simulatedQuery = preg_replace('/\?/', $param, $simulatedQuery, 1);
            }
        }

        $output .= "<div style='width: 100%; font-weight: bold; margin-bottom: 2px;'>Sorgu: " . htmlspecialchars($simulatedQuery) . "</div>\n";

        if ($this->error) {
            $output .= "<strong>Hata:</strong> <span style='font-weight: bold;'>" . htmlspecialchars($this->error) . "</span>\n";
        } else {
            if (!empty($this->lastResult)) {
                $output .= "<table style='width: 100%; border-collapse: collapse; font-size: 11px;'>";

                $firstRow = is_array($this->lastResult) ? reset($this->lastResult) : $this->lastResult;
                if ($firstRow) {
                    $firstRowArray = $firstRow instanceof ArrayAccess ? $firstRow->toArray() : (array)$firstRow;
                    $output .= "<tr style='background-color: #ccffcc;'>";
                    foreach (array_keys($firstRowArray) as $key) {
                        $output .= "<th style='padding: 2px; border: 1px solid #ccc; text-align: left;'>" . htmlspecialchars($key) . "</th>";
                    }
                    $output .= "</tr>";

                    $results = is_array($this->lastResult) ? $this->lastResult : [$this->lastResult];
                    foreach ($results as $row) {
                        $rowArray = $row instanceof ArrayAccess ? $row->toArray() : (array)$row;
                        $output .= "<tr>";
                        foreach ($rowArray as $value) {
                            $output .= "<td style='padding: 8px; border: 1px solid #ccc;'>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                        }
                        $output .= "</tr>";
                    }
                }

                $output .= "</table>";
            } else {
                $output .= "<div style='color: #666;'>Sonuç yok.</div>";
            }
        }

        $output .= "</div>";
        echo $output;
        $this->log("Debugt: " . $simulatedQuery . ($this->error ? " - Hata: " . $this->error : ""));
    }

    public function offsetExists(mixed $offset): bool {
        return false;
    }

    public function offsetGet(mixed $offset): mixed {
        return null;
    }

    public function offsetSet(mixed $offset, mixed $value): void {
    }

    public function offsetUnset(mixed $offset): void {
    }

    public function __destruct() {
        $this->close();
    }
}