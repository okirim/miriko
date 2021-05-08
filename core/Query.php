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
            return self::findOne($table, $data, $columns_str);
        }
    }

    public static function findOne(string $table, array $param, string $columns = '')
    {
        $params_size = sizeof($param);
        $i = 0;
        $result = [];
        $fields=['id',...explode(',',$columns)];
        $columns = $columns === '' ? '*' : implode(',',$fields);
        foreach ($param as $key => $value) {
            $bindValue = self::bind($key);
            $statement = self::$database->pdo->prepare("SELECT $columns FROM $table WHERE $key=$bindValue;");
            $statement->bindParam($bindValue, $value);
            $statement->execute();
            $result[] = $statement->fetch();
        }
        return end($result);
    }
    public static function paginate(string $table,int $limit,int $currentPage=1)
    {
        $size=self::rowCount($table);
        $pages=$size>0 ?ceil($size/$limit): 1 ;
        $firstPage=1;
        $lastPage=$pages;
        $nextPage=$currentPage >=$pages? null : $currentPage+1;
        $prevPage=$currentPage <=0? 1 : $currentPage-1;
        $page=1;
        $offset=$currentPage > 1 ? $limit *($currentPage-1): 0;
       $statement=self::$database->pdo->prepare("SELECT * FROM $table LIMIT $limit OFFSET $offset");
       $statement->execute();
        $results=$statement->fetchAll();
         return  [
             'data'=>$results,
             'currentPage'=>$currentPage,
             'lastPage'=>$lastPage,
             'nextPage'=>$nextPage,
             'prevPage'=>$prevPage,
             'totalPages'=>$pages
         ];

    }
    public static function find(string $table,$columns=null)
    {
        $columns=$columns ===null ? '*' :$columns;
        $statement=self::$database->pdo->prepare("SELECT $columns FROM $table ");
        $statement->execute();
        $query=$statement->fetchAll();
         return json_encode($query);
    }
    public static function rowCount(string $table)
    {
        $statement=self::$database->pdo->prepare("SELECT * FROM $table ");
        $statement->execute();
        return $statement->rowCount();
    }
    protected static function bind($key)
    {
        return preg_replace('/\w+/', ":$key", $key);
    }
}