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

        $this->fixture->load($fixtures);

        return 0;

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

        $this->fixture->load($fixtures);

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

}