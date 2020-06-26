<?php
namespace GlaivePro\Ajaxable\Tests\Feature;

class CreateTest extends \GlaivePro\Ajaxable\Tests\TestCase
{
    private $uri;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->uri = route('ajaxable.create');
	}

    /**
     * Test model creation
     *
     * @return void
     */
    public function testCreate(): void
    {
        $response = $this->json(
            'POST',
            $this->uri,
            [
                'model' => $this->model,
            ]
		);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'data'
            ])
            ->assertJson([
                'status' => 'success',
			]);

    }

    /**
     * Test data on newly created model
     *
     * @return void
     */
    public function testDataOnCreated(): void
    {
        $response = $this->json(
            'POST',
            $this->uri,
            [
				'model' => $this->model,
				'attributes' => [
					'col1' => 'content1'
				],
            ]
		);

        $response
            ->assertJson([
                'data.object.col1' => 'content1',
                'data.object.col2' => 'content',
			]);
    }
}
