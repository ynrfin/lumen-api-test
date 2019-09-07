<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ChecklistTest extends TestCase
{
    protected $baseUrl = '/api/v1/checklists';

    protected $checklistStructure = [
      "data"=> [
        "type",
        "id",
        "attributes" => [
          "object_domain",
          "object_id",
          "description",
          "is_completed",
          "due",
          "urgency",
          "completed_at",
          "last_update_by",
          "created_at",
          "updated_at"
        ],
        "links"=> [
          "self",
        ]
        ]
    ];
    /**
     * Get Existing resource returns 200
     *
     * @return void
     */
    public function testGetExistingRecordsReturns200AndCorrectStructure()
    {
        factory(App\Checklist::class, 5)->create();
        $response = $this->get('/4')
                         ->seeStatusCode(200)
                     ->seeJsonStructure($this->checklistStructure);
    }
}
