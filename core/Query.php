<?php


namespace App\core;


class Query
{
    public static Database $database;

    /**
     *
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        self::$database = $database;
    }

    public static function create($table, $data)
    {
        $columns = [];
        $bindValues = [];
        $values = [];
        foreach ($data as $key => $value) {
            $columns[] = $key;
            $values[] = $value;
            $bindValues[] = preg_replace('/\w+/', ":$key", $key);
        }
        $columns_str = implode(',', $columns);
        $bindValues_str = implode(',', $bindValues);
        $statement = self::$database->pdo->prepare("INSERT INTO $table ($columns_str) values ($bindValues_str);");

        for ($i = 0; $i < sizeof($data); $i++) {
            $statement->bindParam($bindValues[$i], $values[$i]);
        }
        $row = $statement->execute();
        if ($row) {
            return self::findOne($table, $data,$columns_str);
        }
    }

    public static function findOne(string $table, array $param, string $columns = '')
    {
        $params_size = sizeof($param);
        $i = 0;
        $result = [];
        $fields = ['id', ...explode(',', $columns)];
        $columns = $columns === '' ? '*' : implode(',', $fields);
        foreach ($param as $key => $value) {
            $bindValue = self::bind($key);
            $statement = self::$database->pdo->prepare("SELECT $columns FROM $table WHERE $key=$bindValue LIMIT 1;");
            $statement->bindParam($bindValue, $value);
            $statement->execute();
            $result[] = $statement->fetch(\PDO::FETCH_NAMED);
        }
        return end($result);
    }

    public static function paginate(string $table, int $limit, int $page = 1, $filter=[], string $columns='*')
    {
        $filter =self::filterQuery($filter);
        $size = self::rowCount($table);
        $pages = $size > 0 ? ceil($size / $limit) : 1;
        $firstPage = 1;
        $lastPage = $pages;
        $nextPage = $page >= $pages ? null : $page + 1;
        $prevPage = $page <= 1 ? null : $page - 1;
        $offset = $page > 1 ? $limit * ($page - 1) : 0;
        $statement = self::$database->pdo->prepare("SELECT $columns FROM $table WHERE $filter LIMIT $limit OFFSET $offset");
        $statement->execute();
        $results = $statement->fetchAll(\PDO::FETCH_CLASS);
        return [
            'data' => $results,
            'currentPage' => $page,
            'lastPage' => $lastPage,
            'nextPage' => $nextPage,
            'prevPage' => $prevPage,
            'totalPages' => $pages
        ];

    }

    public static function find(string $table, array $filter = [], $columns ='*')
    {
        $filter =self::filterQuery($filter);
        $statement = self::$database->pdo->prepare("SELECT $columns FROM $table WHERE $filter");

        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_CLASS);
    }

    public static function rowCount(string $table)
    {
        $statement = self::$database->pdo->prepare("SELECT * FROM $table ");
        $statement->execute();
        return $statement->rowCount();
    }

    protected static function bind(string $key)
    {
        return preg_replace('/\w+/', ":$key", $key);
    }

    protected static function filterQuery($filter): string
    {
        $filter = count($filter) ? $filter :"''=''";
        $query = '';

        if (gettype($filter) === 'array' && count($filter)) {
            foreach ($filter as $key => $value) {
                $query .= " $key '$value' ";
                $query .= "AND";
            }
            $query_array = explode(' ', $query);
            array_splice($query_array, -1);
            return implode(' ', $query_array);
        }
        return $filter;
    }
    protected static function filterPaginateQuery($filter): string
    {
        $filter = count($filter) > 0 ? $filter : "''=''";
        $query = '';
        if (gettype($filter) === 'array') {
            foreach ($filter as $key => $value) {
                $query .= " $key='$value' ";
                $query .= "AND";
            }
        }
        $query_array = explode(' ', $query);
        array_splice($query_array, -1);
        return implode(' ', $query_array);

    }
}