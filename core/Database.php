<?php


namespace App\core;


class Database
{
    public \PDO $pdo;

    /**
     * Database constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $dsn = $config['dsn'];
        $user = $config['user'];
        $password = $config['password'];

        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    }

    public function migrate()
    {
        $this->createTableMigrations();
        $migrations = $this->selectAppliedMigrations();
        $migrationsFiles = $this->getFilesWithoutExtents(scandir(Application::$ROOT_DIR . '/migrations'));
        $migrationsNotApplied = array_diff($migrationsFiles, $migrations);
        $newMigration = $this->appliedNewMigration($migrationsNotApplied);
        $this->saveAppliedMigration($newMigration);
    }

    protected function createTableMigrations()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations ( 
                         id INT AUTO_INCREMENT PRIMARY KEY,
                         migration VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
                         ) ENGINE=INNODB;"
        );
    }

    protected function selectAppliedMigrations()
    {
        //get applied migrations from migrations table
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    protected function appliedNewMigration(array $migrationsNotApplied)
    {
        $newMigration=[];
        foreach ($migrationsNotApplied as $migration) {
            $file_name = pathinfo($migration)["filename"];
            call_user_func(["\App\migrations\\$file_name", 'up']);
            $newMigration[] = $file_name;
        }
        return $newMigration;
    }

    protected function saveAppliedMigration(array $migrations)
    {
        foreach ($migrations as $migration) {
            $statement = $this->pdo->prepare("INSERT INTO migrations (migration) values ('$migration') ");
            $statement->execute();
        }
    }

    protected function getFilesWithoutExtents(array $files)
    {
        $filesWithoutExt = [];
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $filesWithoutExt[] = pathinfo($file)["filename"];
        }
        return $filesWithoutExt;
    }
}