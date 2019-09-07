<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class GeneralTest extends TestCase
{
    //protected $baseUrl = '/api/v1/checklists';
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetNonexistentPage()
    {
        $response = $this->get('/some-nonexistent-page')
             ->seeStatusCode(404)
             ->seeJson(['status' => 404, 'error' => 'Not Found']);
    }
}
