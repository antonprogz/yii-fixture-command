<?php


namespace antonprogz\yii_fixture_command;


class FixtureManager extends \CDbFixtureManager
{

    public function init()
    {
        \CApplicationComponent::init();
    }

}