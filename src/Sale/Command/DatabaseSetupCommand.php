<?php

namespace Sale\Command;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseSetupCommand extends Command{

    private $db = null;
    /**
     * @var AbstractSchemaManager sm
     */
    private $sm = null;

    public function __construct(Connection $db){
        $this->db = $db;
        $this->sm = $db->getSchemaManager();

        parent::__construct();

    }

    protected function configure()
    {
        $this
            ->setName('sale:database:setup')
            ->setDescription('setup DB scheme')
        ;
    }
    private function getCurrentTables()
    {
        $tables = array();

        foreach ($this->sm->listTables() as $table) {
                $tables[ $table->getName() ] = $table;
        }
        return $tables;
    }

    private function getClearTables(){

        $schema = new Schema();
        $tables = [];

        $houseTable = $schema->createTable('house');
        $houseTable->addColumn('id', 'integer', array('autoincrement' => true));
        $houseTable->setPrimaryKey(array('id'));
        $houseTable->addColumn('name', 'string', array('length' => 32));
        $houseTable->addColumn('address', 'string', array('length' => 32));
        $houseTable->addColumn('material', 'integer', array('length' => 1));
        $houseTable->addColumn('floor', 'integer', array('length' => 2));
        $houseTable->addColumn('deliverydate', 'string', array('length' => 32));

        $tables[] = $houseTable;


        $apartmentTable = $schema->createTable('apartment');
        $apartmentTable->addColumn('id', 'integer', array('autoincrement' => true));
        $apartmentTable->setPrimaryKey(array('id'));
        $apartmentTable->addColumn('house_id', 'integer');
        $apartmentTable->addForeignKeyConstraint($houseTable, ['house_id'], ['id'], ['onDelete'=>'CASCADE']);
        $apartmentTable->addColumn('cnt_room', 'integer');
        $apartmentTable->addColumn('square', 'integer');

        $tables[] = $apartmentTable;


        return $tables;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currentTables = $this->getCurrentTables();
        $targetTables = $this->getClearTables();

        $comparator = new Comparator();

        foreach($targetTables as $table){
            /**
             * @var \Doctrine\DBAL\Schema\Table $table
             */
            $tname = $table->getName();
            if(!isset($currentTables[$tname])){
                //Если нет таблицы
                $output->writeln("Таблица $tname не найдена");

                $this->sm->createTable($table);
                $output->writeln("Таблица $tname создана");
            }
            else {

                $diff = $comparator->diffTable( $currentTables[$tname], $table );
                if ($diff) {
                    $output->writeln("Таблица $tname изменена");
                    /**
                     * @var \Doctrine\DBAL\Schema\Column $column
                     */
                    if($addColumns = $diff->addedColumns){
                        foreach($addColumns as $column){
                            $cname = $column->getName();
                            $output->writeln("Новое поле $cname");
                        }
                    }
                    if($changeColums = $diff->changedColumns){
                        /**
                         * @var \Doctrine\DBAL\Schema\ColumnDiff $columnDiff
                         */
                        foreach($changeColums as $columnDiff){
                            if(in_array('type', $columnDiff->changedProperties)){
                                $newType = $columnDiff->column->getType()->getName();
                                $oldType = $currentTables[$tname]->getColumn($columnDiff->oldColumnName)->getType()->getName();
                                $cname = $columnDiff->oldColumnName;
                                $output->writeln("Поле $cname <comment>поменяло тип</comment> с $oldType на $newType");
                            }
                        }
                    }
                    if($deleteColumns = $diff->removedColumns){
                        foreach($deleteColumns as $column){
                            $cname = $column->getName();
                            $output->writeln("Поле $cname <error>удалено</error>");
                        }
                    }
                    if($renameColumns = $diff->renamedColumns){
                        foreach($renameColumns as $oldName => $column){
                            $newName = $column->getName();
                            $output->writeln("$oldName <info>переименованно</info> в $newName");
                        }
                    }

                    $this->sm->alterTable( $diff );
                    $output->writeln("Таблица $tname обновлена");
                }
            }
        }

    }
} 