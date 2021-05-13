<?php


namespace App\core;

class Olivine
{
    public static string $table;

    /**
     * Olivine constructor.
     * @param string $table
     */
    public function __construct(string $table)
    {
        self::$table = $table;
    }

    public static function create($data)
    {
        $table = self::$table;
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
        $statement = Database::$pdo->prepare("INSERT INTO $table ($columns_str) values ($bindValues_str);");

        for ($i = 0; $i < sizeof($data); $i++) {
            $statement->bindParam($bindValues[$i], $values[$i]);
        }
        $row = $statement->execute();
        if ($row) {
            return self::findOne($data, $columns_str);
        }
    }

    public static function findOne(array $where, string $columns = '')
    {

        $table = self::$table;
        $fields = ['id', ...explode(',', $columns)];
        $columns = $columns === '' ? '*' : implode(',', $fields);
        $attributes=array_keys($where);
        $conditions=implode(" AND ",array_map(fn($attr)=>"$attr=:$attr",$attributes));

        $statement = Database::$pdo->prepare("SELECT $columns FROM $table WHERE $conditions LIMIT 1");
        foreach ($where as $key => $value) {
            $statement->bindValue(":$key", $value);
        }
        $statement->execute();
        $result = $statement->fetchObject(static::class);
        return $result;
    }
    public static function findUser($user_id)
    {
        $statement = Database::$pdo->prepare("SELECT * FROM users WHERE id=:user_id 
                                                      ORDER BY password LIMIT 1");
        $statement->bindValue(":user_id", $user_id);
        $statement->execute();
        $user= $statement->fetchObject(static::class);
        unset($user->password);
        return $user;
    }
    public static function findByIdAndUpdate($id,array $attributes)
    {
        $table=self::$table;
        $attributesName=array_keys($attributes);
        if(count($attributes)>1){
            $columns=implode(",",array_map(fn($attr)=>"$attr=:$attr, ",$attributesName));
        }else{
            $columns=implode(",",array_map(fn($attr)=>"$attr=:$attr ",$attributesName));
        }
        $condition="id=$id";

        $statement = Database::$pdo->prepare("UPDATE $table SET $columns WHERE $condition");
        foreach ($attributes as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        return $statement->execute();

    }
    public static function paginate(int $limit, int $page = 1, $filter = [], string $columns = '*')
    {
        $table = self::$table;
        $filter = self::filterQuery($filter);
        $size = self::rowCount($table);
        $pages = $size > 0 ? ceil($size / $limit) : 1;
        $firstPage = 1;
        $lastPage = $pages;
        $nextPage = $page >= $pages ? null : $page + 1;
        $prevPage = $page <= 1 ? null : $page - 1;
        $offset = $page > 1 ? $limit * ($page - 1) : 0;
        $statement = Database::$pdo->prepare("SELECT $columns FROM $table WHERE $filter LIMIT $limit OFFSET $offset");
        $statement->execute();
        $results = $statement->fetchAll(\PDO::FETCH_NAMED);
        return [
            'data' => $results,
            'currentPage' => $page,
            'lastPage' => $lastPage,
            'nextPage' => $nextPage,
            'prevPage' => $prevPage,
            'totalPages' => $pages
        ];

    }

    public static function find(array $filter = [], $columns = '*')
    {
        $table = self::$table;
        $filter = self::filterQuery($filter);
        $statement = Database::$pdo->prepare("SELECT $columns FROM $table WHERE $filter");

        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_NAMED);
    }

    public static function rowCount()
    {
        $table = self::$table;
        $statement = Database::$pdo->prepare("SELECT * FROM $table ");
        $statement->execute();
        return $statement->rowCount();
    }

    protected static function bind(string $key)
    {
        return preg_replace('/\w+/', ":$key", $key);
    }

    protected static function filterQuery($filter): string
    {
        $filter = count($filter) ? $filter : "''=''";
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

    public static function hasMany(string $jointTable)
    {
//        $table = self::$table;
//        $id = substr($table, 0, -1) . '_id';
//        $results = [];
//        $statement = Database::$pdo->prepare("SELECT $table.*,$jointTable.id AS _id
//        FROM $table LEFT JOIN $jointTable
//        ON $table.id =$id"
//        );
//        $statement->execute();
//        $table1 = $statement->fetchAll(\PDO::FETCH_NAMED);
//
//        foreach ($table1 as $table) {
//            $id = $table['_id'];
//            if (!empty($id)) {
//                $statement = Database::$pdo->prepare("SELECT *
//                                                              FROM $jointTable
//                                                              WHERE $jointTable.id =$id"
//                );
//                $statement->execute();
//                $table2[] = $statement->fetchAll(\PDO::FETCH_NAMED);
//                for ($i = 0; $i < count($table1); $i++) {
//                    for ($j = 0; $j < count($table2); $j++) {
//                        if ($table1[$i]['id'] === $table2[$j][0]['user_id']) {
//                            //joinTable id = $table2[$j][0]['id']
//                            $jointTable_id=$table2[$j][0]['id'];
//                            $table1[$i][$jointTable][$jointTable_id] = $table2[$j][0];
//
//                        }
//                    }
//                    $results[$table1[$i]['id']]=$table1[$i];
//
//                }
//            }
//        }
//        $data=[];
//        foreach ($results as $key=>$value){
//            $data[]=$value;
//        }
//
//        return $data;
    }

    public static function leftJoin(array $jointTables)
    {
         $main_table=self::$table;
        $fk = substr(self::$table, 0, -1) . '_id';
        $data = [];

        foreach ($jointTables as $key=>$table){
            $data[]=self::getLeftTable($main_table,$fk,$table);
        }
        $x= [...$data[0],...$data[1]];

        return $x;
    }

    protected static function getLeftTable($table, $fk_id, string $joinTable)

    {
        $results = [];
        $statement = Database::$pdo->prepare("SELECT $table.*,$joinTable.id AS _id
                                                         FROM $table LEFT JOIN $joinTable
                                                         ON $table.id =$joinTable.$fk_id"
        );

        $statement->execute();
        $table1 = $statement->fetchAll(\PDO::FETCH_NAMED);
        foreach ($table1 as $table) {
            $id = $table['_id'];
            if (!empty($id)) {
                $statement = Database::$pdo->prepare("SELECT *
                                                              FROM $joinTable
                                                              WHERE $joinTable.id =$id"
                );
                $statement->execute();
                $table2[] = $statement->fetchAll(\PDO::FETCH_NAMED);
                $data = array_unique($table2, SORT_REGULAR);
                for ($i = 0; $i < count($table1); $i++) {
                    for ($j = 0; $j < count($data); $j++) {
                        if ($table1[$i]['id'] === $data[$j][0]['user_id']
                        ) {

                            $table1[$i][$joinTable][] =$data[$j][0];
                        }
                    }
                    $results[$table1[$i]['id']] = $table1[$i];

                }
            }
        }
        foreach ($results as $key => $value) {
            if (!empty($results[$key][$joinTable])) {
                $results[$key][$joinTable] = array_unique($results[$key][$joinTable], SORT_REGULAR);
            }
        }
        return array_merge($results);
    }
}



