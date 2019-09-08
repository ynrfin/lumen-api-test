<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ItemTest extends TestCase
{
    protected $baseUrl = '/api/v1/checklists';
    use DatabaseMigrations;

    protected $itemResponseStructure = [
        "data" => [
            'type',
            'id',
            'attributes' => [
                'description',
                'is_completed',
                'completed_at',
                'due',
                'urgency',
                'updated_by',
                'updated_at',
                'created_at',
            ],
            'links' => [
                'self'
            ]
        ]
    ];

    protected $itemShowOneResponseStructure = [
        "data" => [
            'type',
            'id',
            'attributes' => [
                'description',
                'is_completed',
                'completed_at',
                'due',
                'urgency',
                'updated_by',
                'created_by',
                'checklist_id',
                'assignee_id',
                'task_id' ,
                'deleted_at',
                'updated_at',
                'created_at',
            ],
            'links' => [
                'self'
            ]
        ]
    ];

    /**
     * test minimum payload
     *
     * @return void
     */
    public function testCreateWithOnlyRequiredParamsReturns200()
    {
        factory(App\Checklist::class, 5)->create();
        $user = Factory(App\User::class)->create();
        $attributes = [
            "description" => 'some descripton'
        ];

        $fullPayload = [
            "data" => [
                "attributes" => $attributes
            ]
        ];

        $resultAttribute = $attributes;
        $resultAttribute['is_completed'] = false;
        $resultAttribute['completed_at'] = null;
        $resultAttribute['due'] = null;
        $resultAttribute['urgency'] = 0;

        $resultLink = ['self' => route('checklists.showOne', ['id' => 3])];

        $this->actingAs($user)
            ->post('/3/items', $fullPayload)
            ->seeInDatabase('items', $attributes)
            ->seeJsonStructure($this->itemResponseStructure)
            ->seeJsonContains($resultAttribute)
            ->seeJsonContains($resultLink)
            ->seeStatusCode(200);
    }

    /**
     * test create without authorization header returns 401
     *
     * @return void
     */
    public function testCreateWithoutAuthReturns401()
    {
        $attributes = [
            "description" => 'some descripton'
        ];

        $fullPayload = [
            "data" => [
                "attributes" => $attributes
            ]
        ];

        $this->post('/3/items')->seeStatusCode(401);
    }
    
    /**
     * test full valid params
     *
     * @return void
     */
    public function testCreateFullParams()
    {
        factory(App\Checklist::class, 5)->create();
        $user = Factory(App\User::class)->create();
        $attributes = [
            "description" => 'some descripton',
            "due" => date("Y-m-d H:i:s"),
            "urgency" => 2,
            "assignee_id"  => 23,
            "task_id" => 3,
            "updated_by" => 3,
            "is_completed" => true,
        ];

        $fullPayload = [
            "data" => [
                "attributes" => $attributes
            ]
        ];

        $resultAttribute = $attributes;
        unset($resultAttribute['assignee_id']);
        unset($resultAttribute['task_id']);

        $resultLink = ['self' => route('checklists.showOne', ['id' => 3])];

        $this->actingAs($user)
            ->post('/3/items', $fullPayload)
            ->seeInDatabase('items', $attributes)
            ->seeJsonStructure($this->itemResponseStructure)
            ->seeJsonContains($resultAttribute)
            ->seeJsonContains($resultLink)
            ->seeStatusCode(200);
    }

    /**
     * test full valid params
     *
     * @return void
     */
    public function testWrongDueFormatReturns422()
    {
        factory(App\Checklist::class, 5)->create();
        $user = Factory(App\User::class)->create();
        $attributes = [
            "description" => 'some descripton',
            "due" => date("Ym-d H:i:s"),
            "urgency" => 2,
            "assignee_id"  => 23,
            "task_id" => 3,
            "updated_by" => 3,
            "is_completed" => true,
        ];

        $fullPayload = [
            "data" => [
                "attributes" => $attributes
            ]
        ];

        $resultAttribute = $attributes;
        unset($resultAttribute['assignee_id']);
        unset($resultAttribute['task_id']);

        $resultLink = ['self' => route('checklists.showOne', ['id' => 3])];

        $this->actingAs($user)
            ->post('/3/items', $fullPayload)
            ->seeStatusCode(422);
    }

    /**
     * test get exist item
     *
     * @return void
     */
    public function testGetOneExistItemReturn200AndTheItem()
    {
        factory(App\Checklist::class, 5)->create()->each(function($checklist){
            $checklist->items()->saveMany(factory(App\Item::Class, 12)->make());
        });
        $user = Factory(App\User::class)->create();

        $this->actingAs($user)
            ->get('/1/items/5')
            ->seeJsonStructure($this->itemShowOneResponseStructure)
            ->seeStatusCode(200);
    }

    /**
     * test get exist item
     *
     * @return void
     */
    public function testGetNonExistItemReturn404()
    {
        factory(App\Checklist::class, 2)->create()->each(function($checklist){
            $checklist->items()->saveMany(factory(App\Item::Class, 2)->make());
        });
        $user = Factory(App\User::class)->create();

        $this->actingAs($user)
            ->get('/1/items/5')
            ->seeStatusCode(404);
    }

    /**
     * delete record not exists
     *
     * @return void
     */
    public function testDeleteNonExistentRecordReturn404()
    {
        factory(App\Checklist::class, 2)->create()->each(function($checklist){
            $checklist->items()->saveMany(factory(App\Item::Class, 2)->make());
        });
        $user = Factory(App\User::class)->create();

        $this->actingAs($user)
            ->delete('/1/items/5')
            ->seeStatusCode(404);
    }

    /**
     * delete record not exists
     *
     * @return void
     */
    public function testDeleteExistRecordReturn204()
    {
        factory(App\Checklist::class, 2)->create()->each(function($checklist){
            $checklist->items()->saveMany(factory(App\Item::Class, 2)->make());
        });
        $user = Factory(App\User::class)->create();

        $this->actingAs($user)
            ->delete('/1/items/2')
            ->seeStatusCode(204);
    }

    /**
     * complete exist item
     *
     * @return void
     */
    public function testCompleteExistRecordReturn200()
    {
        factory(App\Checklist::class, 2)->create()->each(function($checklist){
            $checklist->items()->saveMany(factory(App\Item::Class, 2)->make());
        });
        $user = Factory(App\User::class)->create();

        $payload = [
            'data' =>[
                [
                    'item_id'=> 1,
                ],
                [
                    'item_id'=> 3,
                ],
                [
                    'item_id'=> 4,
                ],
            ]
        ];

        $this->actingAs($user)
            ->post('/complete', $payload)
            ->seeInDatabase('items', ['id' => 1, 'is_completed' => true])
            ->seeInDatabase('items', ['id' => 3, 'is_completed' => true])
            ->seeInDatabase('items', ['id' => 4, 'is_completed' => true])
            ->seeStatusCode(200);

        $response = json_decode($this->response->getContent(), true);

        $this->assertEquals(3, count($response['data']));
    }

    /**
     * complete partial exist item
     *
     * @return void
     */
    public function testCompletePartialExistRecordReturn200AndUpdatedCount()
    {
        factory(App\Checklist::class, 2)->create()->each(function($checklist){
            $checklist->items()->saveMany(factory(App\Item::Class, 2)->make());
        });
        $user = Factory(App\User::class)->create();

        $payload = [
            'data' =>[
                [
                    'item_id'=> 1,
                ],
                [
                    'item_id'=> 3,
                ],
                [
                    'item_id'=> 5,
                ],
            ]
        ];

        $this->actingAs($user)
            ->post('/complete', $payload)
            ->seeInDatabase('items', ['id' => 1, 'is_completed' => true])
            ->seeInDatabase('items', ['id' => 3, 'is_completed' => true])
            ->notSeeInDatabase('items', ['id' => 5, 'is_completed' => true])
            ->seeStatusCode(200);

        $response = json_decode($this->response->getContent(), true);

        $this->assertEquals(2, count($response['data']));
    }

    /**
     * complete partial exist item
     *
     * @return void
     */
    public function testCompletePartialExistRecordReturn0UpdatedCount()
    {
        factory(App\Checklist::class, 2)->create()->each(function($checklist){
            $checklist->items()->saveMany(factory(App\Item::Class, 2)->make());
        });
        $user = Factory(App\User::class)->create();

        $payload = [
            'data' =>[
                [
                    'item_id'=> 10,
                ],
                [
                    'item_id'=> 30,
                ],
                [
                    'item_id'=> 58,
                ],
            ]
        ];

        $this->actingAs($user)
            ->post('/complete', $payload)
            ->seeStatusCode(200);

        $response = json_decode($this->response->getContent(), true);

        $this->assertEquals(0, count($response['data']));

    }

    /**
     * incomplete exist item
     *
     * @return void
     */
    public function testIncompleteExistRecordReturnAllUpdatedCount()
    {
        factory(App\Checklist::class, 2)->create()->each(function($checklist){
            $checklist->items()->saveMany(factory(App\Item::Class, 2)->make());
        });
        $user = Factory(App\User::class)->create();

        $payload = [
            'data' =>[
                [
                    'item_id'=> 1,
                ],
                [
                    'item_id'=> 3,
                ],
                [
                    'item_id'=> 4,
                ],
            ]
        ];

        $this->actingAs($user)
            ->post('/incomplete', $payload)
            ->seeInDatabase('items', ['id' => 1, 'is_completed' => false, 'completed_at' => null])
            ->seeInDatabase('items', ['id' => 3, 'is_completed' => false, 'completed_at' => null])
            ->seeInDatabase('items', ['id' => 4, 'is_completed' => false, 'completed_at' => null])
            ->seeJson(['id' => 1, 'item_id' => 1, 'is_completed' => false, 'checklist_id' => 1])
            ->seeJson(['id' => 3, 'item_id' => 3, 'is_completed' => false, 'checklist_id' => 2])
            ->seeJson(['id' => 4, 'item_id' => 4, 'is_completed' => false, 'checklist_id' => 2])
            ->seeStatusCode(200);

        $response = json_decode($this->response->getContent(), true);

        $this->assertEquals(3, count($response['data']));

    }

    /**
     * incomplete partial exist item
     *
     * @return void
     */
    public function testIncompletePartialExistRecordReturnAllUpdatedCount()
    {
        factory(App\Checklist::class, 2)->create()->each(function($checklist){
            $checklist->items()->saveMany(factory(App\Item::Class, 2)->make());
        });
        $user = Factory(App\User::class)->create();

        $payload = [
            'data' =>[
                [
                    'item_id'=> 1,
                ],
                [
                    'item_id'=> 3,
                ],
                [
                    'item_id'=> 5,
                ],
            ]
        ];

        $this->actingAs($user)
            ->post('/incomplete', $payload)
            ->seeInDatabase('items', ['id' => 1, 'is_completed' => false, 'completed_at' => null])
            ->seeInDatabase('items', ['id' => 3, 'is_completed' => false, 'completed_at' => null])
            ->notSeeInDatabase('items', ['id' => 5, 'is_completed' => false, 'completed_at' => null])
            ->seeJson(['id' => 1, 'item_id' => 1, 'is_completed' => false, 'checklist_id' => 1])
            ->seeJson(['id' => 3, 'item_id' => 3, 'is_completed' => false, 'checklist_id' => 2])
            ->seeStatusCode(200);

        $response = json_decode($this->response->getContent(), true);

        $this->assertEquals(2, count($response['data']));

    }

    /**
     * incomplete nonexist item
     *
     * @return void
     */
    public function testIncompleteNonexistRecordReturnAllUpdatedCount()
    {
        factory(App\Checklist::class, 2)->create()->each(function($checklist){
            $checklist->items()->saveMany(factory(App\Item::Class, 2)->make());
        });
        $user = Factory(App\User::class)->create();

        $payload = [
            'data' =>[
                [
                    'item_id'=> 10,
                ],
                [
                    'item_id'=> 30,
                ],
                [
                    'item_id'=> 50,
                ],
            ]
        ];

        $this->actingAs($user)
            ->post('/incomplete', $payload)
            ->notSeeInDatabase('items', ['id' => 10, 'is_completed' => false, 'completed_at' => null])
            ->notSeeInDatabase('items', ['id' => 30, 'is_completed' => false, 'completed_at' => null])
            ->notSeeInDatabase('items', ['id' => 50, 'is_completed' => false, 'completed_at' => null])
            ->seeStatusCode(200);

        $response = json_decode($this->response->getContent(), true);

        $this->assertEquals(0, count($response['data']));

    }
}
