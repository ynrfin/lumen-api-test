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
    

}
