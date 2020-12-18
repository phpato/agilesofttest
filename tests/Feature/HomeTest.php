<?php

namespace Tests\Feature;
use App\Task;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class HomeTest extends TestCase
{
    use WithoutMiddleware;
    /**
    * @test
    */
    public function testInitialHomeView()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
