<?php

namespace Core\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Comparator;
use Core\Model\AbstractModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseSetupCommand extends Command
{

    /**
     * @var Connection $db ;
     */
    private $db = null;
    /**
     * @var AbstractSchemaManager sm
     */
    private $sm = null;

    /**
     * @var \CatalogApplication $app
     */
    private $app;

    private $keys;

    public function __construct(\CatalogApplication $app)
    {
        $this->db = $app['db'];
        $this->sm = $this->db->getSchemaManager();
        $this->keys = $app->keys();

        $this->app = $app;
        parent::__construct();

    }

    protected function configure()
    {
        $this
            ->setName('sale:database:setup')
            ->setDescription('setup DB scheme');
    }

    private function getCurrentTables()
    {
        $tables = array();

        foreach ($this->sm->listTables() as $table) {
            $tables[$table->getName()] = $table;
        }
        return $tables;
    }

    private function getSchemas()
    {
        $schemas = [];
        foreach ($this->keys as $key) {
            if (preg_match('/model\.(\S+)/', $key)) {
                if ($this->app[$key] instanceof AbstractModel) {
                    $tmpSchemas = $this->app[$key]->getTableSchema();
                    if (is_array($tmpSchemas)) {
                        foreach ($tmpSchemas as $s) {
                            //@TODO maybe array merge?
                            $schemas[] = $s;
                        }
                    } else {
                        $schemas[] = $tmpSchemas;
                    }
                }
            }
        }
        return $schemas;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currentTables = $this->getCurrentTables();
        $targetTables = $this->getSchemas();

        $comparator = new Comparator();

        foreach ($targetTables as $table) {
            /**
             * @var \Doctrine\DBAL\Schema\Table $table
             */
            $tname = $table->getName();
            if (!isset($currentTables[$tname])) {
                //Если нет таблицы
                $output->writeln("Таблица $tname не найдена");

                $this->sm->createTable($table);
                $output->writeln("Таблица $tname создана");
            } else {

                $diff = $comparator->diffTable($currentTables[$tname], $table);
                if ($diff) {
                    $output->writeln("Таблица $tname изменена");
                    /**
                     * @var \Doctrine\DBAL\Schema\Column $column
                     */
                    if ($addColumns = $diff->addedColumns) {
                        foreach ($addColumns as $column) {
                            $cname = $column->getName();
                            $output->writeln("Новое поле $cname");
                        }
                    }
                    if ($changeColums = $diff->changedColumns) {
                        /**
                         * @var \Doctrine\DBAL\Schema\ColumnDiff $columnDiff
                         */
                        foreach ($changeColums as $columnDiff) {
                            if (in_array('type', $columnDiff->changedProperties)) {
                                $newType = $columnDiff->column->getType()->getName();
                                $oldType = $currentTables[$tname]->getColumn($columnDiff->oldColumnName)->getType()->getName();
                                $cname = $columnDiff->oldColumnName;
                                $output->writeln("Поле $cname <comment>поменяло тип</comment> с $oldType на $newType");
                            }
                        }
                    }
                    if ($deleteColumns = $diff->removedColumns) {
                        foreach ($deleteColumns as $column) {
                            $cname = $column->getName();
                            $output->writeln("Поле $cname <error>удалено</error>");
                        }
                    }
                    if ($renameColumns = $diff->renamedColumns) {
                        foreach ($renameColumns as $oldName => $column) {
                            $newName = $column->getName();
                            $output->writeln("$oldName <info>переименованно</info> в $newName");
                        }
                    }

                    $this->sm->alterTable($diff);
                    $output->writeln("Таблица $tname обновлена");
                }
            }
        }

    }
} 