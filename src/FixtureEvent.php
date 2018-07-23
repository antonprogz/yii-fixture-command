<?php


namespace antonprogz\yii_fixture_command;


class FixtureEvent extends \CEvent
{

    /**
     * @var array
     */
    private $fixtures;

    /**
     * FixtureEvent constructor.
     * @param array $fixtures
     * @param null $sender
     */
    public function __construct(array $fixtures, $sender = null)
    {
        $this->fixtures = $fixtures;
        parent::__construct($sender);
    }

    public function fixtures(): array
    {
        return $this->fixtures;
    }


}