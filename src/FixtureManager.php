<?php


namespace antonprogz\yii_fixture_command;

\Yii::import('system.test.CDbFixtureManager');

class FixtureManager extends \CDbFixtureManager
{

    public function init()
    {
        \CApplicationComponent::init();
    }

}