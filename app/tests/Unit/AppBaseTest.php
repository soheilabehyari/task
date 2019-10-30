<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AppBaseTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAppBase()
    {
        $this->get('/');

        $this->assertEquals('Travian Exchange System',  $this->response->getContent());
    }
}
