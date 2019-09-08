<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\User;

class ChecklistTest extends TestCase
{
    use DatabaseTransactions;
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
        $user = Factory(App\User::class)->create();
        factory(App\Checklist::class, 5)->create();
        $response = $this->actingAs($user)
            ->get('/4')
                         ->seeStatusCode(200)
                     ->seeJsonStructure($this->checklistStructure);
    }

    /**
     * testing for no record returns 404
     *
     * @return void
     */
    public function testGetNonExistentResourceReturn404()
    {
        factory(App\Checklist::class, 5)->create();
        $user = Factory(App\User::class)->create();

        $response = $this->actingAs($user)
            ->get('/51')
            ->seeStatusCode(404)
            ->seeJsonEquals(['status' => 404, 'error' => 'Not Found']);
    }

    /**
     * make sure that patch will take validation first, 
     * then search for record
     *
     * @return void
     */
    public function testPatchNonExistentRecordWithInvalidDataReturns422()
    {
        $user = Factory(App\User::class)->create();
        factory(App\Checklist::class, 5)->create();
        $response = $this->actingAs($user)
            ->patch('/40')
            ->seeStatusCode(422);
    }

    /**
     * testing the validation is done first even with valid resource id
     *
     * @return void
     */
    public function testPatchExistingRecordWithInvalidDataReturns422()
    {
        factory(App\Checklist::class, 5)->create();
        $user = Factory(App\User::class)->create();

        $attributes = [
            "object_domain" => "contact",
            "object_id" => "1",
            "description" => "Need to verify this guy house.",
            "is_completed" => false,
            "completed_at" => null,
            'created_at' => "2018-01-25T07:50:14" 
        ];

        $this->actingAs($user)
             ->patch('/4', [
                "data" => [
                    'id' => 2,
                    'type' => 'checklists', 
                    'attributes' => $attributes,
                    'links' => [
                        "self" => 'some-links'
                    ]
                ]
             ])
             ->seeStatusCode(422);
    }

    /**
     * validated message will patch the resource
     *
     * @return json
     */
    public function testPatchWithMinimumRequiredFieldReturns200()
    {
        factory(App\Checklist::class, 5)->create();
        $user = Factory(App\User::class)->create();
        $attributes = [
            "object_domain" => "contact",
            "object_id" => "1",
            "description" => "Need to verify this guy house.",
            "is_completed" => false,
            "completed_at" => null,
        ];

        $dirtyAttribute = $attributes;
        $dirtyAttribute['created_at'] ="2018-01-25T07:50:14+00:00" ;

        $this->actingAs($user)
            ->patch('/2', [
            "data" => [
                'id' => 2,
                'type' => 'checklists', 
                'attributes' => $dirtyAttribute,
                'links' => [
                    "self" => 'some-links'
                ]
            ]
        ])->seeStatusCode(200)
        ->seeInDatabase('checklists', $attributes);
    }

    /**
     * testing
     *
     * @return json
     */
    public function testPatchWithAllFieldReturns200()
    {
        factory(App\Checklist::class, 5)->create();
        $user = Factory(App\User::class)->create();
        $attributes = [
            "object_domain"=> "contact",
            "object_id" => "1",
            "description" => "Need to verify this guy house.",
            "is_completed" => false,
            "due" => null,
            "urgency" => 0,
            "completed_at" => null,
        ];

        $dirtyAttribute = $attributes;
        $dirtyAttribute['created_at'] ="2018-01-25T07:50:14+00:00" ;
        $dirtyAttribute['updated_at'] ="2018-01-25T07:50:14+00:00" ;
        $dirtyAttribute['last_update_by'] ="someon" ;


        $this->actingAs($user)
            ->patch('/2', [
            "data" => [
                'id' => 2,
                'type' => 'checklists', 
                'attributes' => $dirtyAttribute,
                'links' => [
                    "self" => 'some-links'
                ]
            ]
        ])->seeStatusCode(200)
        ->seeInDatabase('checklists', $attributes);
    }

}
