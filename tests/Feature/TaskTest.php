<?php

namespace Tests\Feature;
use App\Task;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class TaskTest extends TestCase
{
    use WithoutMiddleware;
   /**
    * @test
    */
    public function testGetTasks()
    {
        $response = $this->get('/tasks');
        $response->assertStatus(200);
    }

    /**
    * @test
    */
    public function testBodySuccessCreateTask()
    {
        $task = factory(Task::class)->create([
            'name' => 'mejora diseño front',
            'description' => 'mejora de test con componente vue en vez jquery',
            'status' => 1
        ]);
        $response = $this->postJson('/tasks', [
            'name' => $task->name,
            'description' => $task->description,
            'status' => $task->status
        ])->assertStatus(200);
    }

    /**
    * @test
    */
    public function testBodyFailedCreateTask()
    {
        $task = factory(Task::class)->create([
            'name' => '',
            'description' => '',
            'status' => 1
        ]);

        $response = $this->postJson('/tasks', [
            "name" => $task->name,
            "description" => $task->description,
            "status" => $task->status
        ])
            ->assertStatus(400)
            ->assertJson([
                'status' => 0
            ]);
   
    }

    /**
    * @test
    */
    public function testBodySuccessUpdateTask()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $task = factory(Task::class)->create([
            'name' => 'mejora diseño frontsadasd',
            'description' => 'mejora de test',
            'status' => 1
        ]);
        $response = $this->putJson( 'tasks/'.$task->id, [
            "name" => $task->name,
            "description" => $task->description,
            "status" => $task->status
        ]); 

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 1
            ]);
    }

    /**
    * @test
    */
    public function testBodyFailedUpdateTask()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $task = factory(Task::class)->create([
            'name' => 'mejora diseño frontsadasd',
            'description' => '',
            'status' => 1
        ]);
        $response = $this->putJson( 'tasks/'.$task->id, [
            "name" => $task->name,
            "description" => $task->description,
            "status" => $task->status
        ]); 

        $response
            ->assertStatus(400)
            ->assertJson([
                'status' => 0
            ]);
    }

    /**
    * @test
    */
    public function testDeleteTask()
    {
        $task = factory(Task::class)->create([
            'name' => 'mejora diseño front',
            'description' => 'mejora de test',
            'status' => 0
        ]);

        $response = $this->deleteJson( 'tasks/'.$task->id, []);
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 1
            ]);
    }
}
