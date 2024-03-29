<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\User;

class ChecklistTest extends TestCase
{
    use DatabaseMigrations;
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
            "object_id" => 11,
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

    /**
     * check when is_completed is true, completed_at date is not null
     *
     * @return json
     */
    public function testPatchIsCompletedToTrueInsertDateToCompletedAtField()
    {
        factory(App\Checklist::class, 5)->create();
        $user = Factory(App\User::class)->create();
        $attributes = [
            "object_domain" => "contact",
            "object_id" => "1",
            "description" => "Need to verify this guy house.",
            "is_completed" => true,
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

        $response = json_decode($this->response->getContent());
        $this->assertNotNull($response->data->attributes->completed_at);
    }

    /**
     * check when is_completed is false, completed_at date null
     *
     * @return json
     */
    public function testPatchIsCompletedFalseCompletedAtNull()
    {
        factory(App\Checklist::class, 5)->create();
        $user = Factory(App\User::class)->create();
        $attributes = [
            "object_domain" => "contact",
            "object_id" => "1",
            "description" => "Need to verify this guy house.",
            "is_completed" => false,
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

        $response = json_decode($this->response->getContent());
        $this->assertNull($response->data->attributes->completed_at);
    }

    /**
     * test unauthenticated user acessing Delete route returns 401
     *
     * @return void
     */
    public function testAccessDeleteWithoutAuthReturns401()
    {
        factory(App\Checklist::class, 5)->create();

        $this->delete('/2')
            ->seeStatusCode(401);
    }

    /**
     * test delete a checklist with right credential
     *
     * @return void
     */
    public function testAuthenticatedAndValidDeleteUrlReturns204()
    {
        factory(App\Checklist::class, 5)->create();
        $user = Factory(App\User::class)->create();

        $this->actingAs($user)
            ->delete('/3')
            ->seeStatusCode(204)
            ->notSeeInDatabase('checklists',['id' => 3]);
    }

    /**
     * delete nonexistent resource returns 40
     *
     * @return void
     */
    public function testDeleteNonExistentRecordReturns404()
    {
        factory(App\Checklist::class, 5)->create();
        $user = Factory(App\User::class)->create();

        $this->actingAs($user)
            ->delete('/30')
            ->seeStatusCode(404)
            ->seeJson(['status' => 404, 'error' => 'Not Found']);
    }

    /**
     * test create new record with minimum fields, only required ones
     *
     * @return void
     */
    public function testCreateOnlyRequiredFieldsSuccess()
    {
        $user = Factory(App\User::class)->create();
        $attributes= [
            "object_domain" => "rarar",
            "object_id"=> "1",
            "description"=> "Need to verify this guy house."
        ];

        $this->actingAs($user)
             ->post('/', [
                "data"=> [
                    "attributes"=> $attributes
                ]
            ])
              ->seeInDatabase('checklists', $attributes)
            ->seeJsonStructure($this->checklistStructure)
                ->assertResponseStatus(200);
    }

    /**
     * test create new record with minimum fields, only required ones
     *
     * @return void
     */
    public function testCreateWithInvalidValueReturn422()
    {
        $user = Factory(App\User::class)->create();
        $attributes= [
            "object_domain" => 111,
            "object_id"=> 34,
            "description"=> 2344,
            "is_completed" => "ok"
        ];

        $this->actingAs($user)
             ->post('/', [
                "data"=> [
                    "attributes"=> $attributes
                ]
            ])
            ->assertResponseStatus(422);
    }

    /**
     * test create new record with minimum fields, only required ones
     *
     * @return void
     */
    public function testCreateWithoutReqyiredFieldReturn422()
    {
        $user = Factory(App\User::class)->create();
        $attributes= [
            "object_id"=> "1",
            "description"=> "Need to verify this guy house."
        ];

        $this->actingAs($user)
             ->post('/', [
                "data"=> [
                    "attributes"=> $attributes
                ]
            ])
                ->assertResponseStatus(422);
    }

}
