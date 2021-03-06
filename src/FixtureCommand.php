<?php

namespace antonprogz\yii_fixture_command;


/**
 * Class FixtureCommand
 * @package antonprgz\yii_fixture_command
 */
class FixtureCommand extends \CConsoleCommand
{

    /**
     * @var string
     */
    public $fixture_id = 'fixture';

    /**
     * @var \CDbFixtureManager
     */
    private $fixture;


    /**
     * @throws \CException
     */
    public function init()
    {
        if (!YII_DEBUG) {
            throw new \CException("This command should be used in the debug mode only.");
        }

        $fixture = \Yii::app()->getComponent($this->fixture_id);
        if (!$fixture instanceof FixtureManager) {
            throw new \CException("Can't get a proper fixture manager component.");
        }

        $this->fixture = $fixture;
        parent::init();
    }

    public function actionLoad(array $args = [])
    {
        $fixtures = array_combine($args, $args);

        $not_existing = $this->findNotExisting($fixtures);

        if (!empty($not_existing)) {

            $this->echoNotExisting($not_existing);

            return 1;

        }

        foreach ($fixtures as $key => $table) {
            $fixtures[$key] = ':' . $table;
        }

        $this->load($fixtures);

        return 0;

    }

    private function load($fixtures)
    {

        $this->onBeforeLoad(new FixtureEvent($fixtures, $this));

        $this->fixture->load($fixtures);

        $this->onAfterLoad(new FixtureEvent($fixtures, $this));

    }


    public function onBeforeLoad($event)
    {
        $this->raiseEvent('onBeforeLoad', $event);
    }

    public function onAfterLoad($event)
    {
        $this->raiseEvent('onAfterLoad', $event);
    }

    private function echoNotExisting(array $not_existing)
    {
        echo "Fixtures for tables '" . implode(',', $not_existing) . "' do not exist.\n";
    }


    private function findNotExisting(array $fixtures): array
    {
        $all = array_keys($this->fixture->getFixtures());

        $existing = array_intersect($fixtures, $all);

        if (count($existing) != count($fixtures)) {

            return array_diff($fixtures, $existing);

        }

        return [];
    }


    public function actionLoadAll()
    {

        $all = array_keys($this->fixture->getFixtures());

        $fixtures = array_combine($all, $all);

        foreach ($fixtures as $key => $table) {
            $fixtures[$key] = ':' . $table;
        }

        $this->load($fixtures);

    }


    public function actionUnload(array $args = [])
    {

        $fixtures = array_combine($args, $args);

        $not_existing = $this->findNotExisting($fixtures);

        if (!empty($not_existing)) {

            $this->echoNotExisting($not_existing);

            return 1;

        }

        foreach ($fixtures as $fixture) {

            $this->fixture->resetTable($fixture);

        }

        return 0;

    }


    public function actionUnloadAll()
    {
        $tables = array_keys($this->fixture->getFixtures());

        foreach ($tables as $table) {
            $this->fixture->resetTable($table);
        }

        return 0;
    }


    /**
     * @throws \CDbException
     * @throws \CException
     */
    public function actionTruncateTables()
    {

        /** @var \CConsoleApplication $app */
        $app = \Yii::app();

        $cmd = $app->getCommandRunner()->createCommand('migrate');

        $migration_table = '';

        if ($cmd instanceof \MigrateCommand) {
            $migration_table = $cmd->migrationTable;
        }

        $this->fixture->checkIntegrity(false);

        foreach($app->db->getSchema()->getTableNames() as $table_name) {
            if ($table_name != $migration_table) {
                $this->fixture->truncateTable($table_name);
            }
        }

        $this->fixture->checkIntegrity(true);

    }

}