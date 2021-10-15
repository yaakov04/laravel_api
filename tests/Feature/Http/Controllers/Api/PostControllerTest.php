<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;
    public function test_store()
    {
        //$this->withoutExceptionHandling();
        $response = $this->json('POST', '/api/posts', [
            'title'=>'El post de prueba',

        ]);

        $response->assertJsonStructure(['id','title','created_at', 'updated_at'])
            ->assertJson(['title'=>'El post de prueba'])
            ->assertStatus(201);

        $this->assertDatabaseHas('posts', ['title'=>'El post de prueba']);
    }

    public function test_validate_title()
    {
        $response = $this->json('POST', '/api/posts', [
            'title'=>'',

        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }

    public function test_show()
    {
        $post = factory(Post::class)->create();
        $response = $this->json('GET', "/api/posts/{$post->id}");

        $response->assertJsonStructure(['id','title','created_at', 'updated_at'])
            ->assertJson(['title'=>$post->title])
            ->assertStatus(200);
    }
    public function test_404_show()
    {
        
        $response = $this->json('GET', "/api/posts/1000");

        $response->assertStatus(404);
    }

    public function test_update()
    {
        $post = factory(Post::class)->create();
        //$this->withoutExceptionHandling();
        $response = $this->json('PUT', "/api/posts/{$post->id}", [
            'title'=>'Nuevo',

        ]);

        $response->assertJsonStructure(['id','title','created_at', 'updated_at'])
            ->assertJson(['title'=>'Nuevo'])
            ->assertStatus(200);

        $this->assertDatabaseHas('posts', ['title'=>'Nuevo']);
    }

    public function test_delete()
    {
        $post = factory(Post::class)->create();
        //$this->withoutExceptionHandling();
        $response = $this->json('Delete', "/api/posts/{$post->id}");

        $response->assertSee(null)
            ->assertStatus(204);

        $this->assertDatabaseMissing('posts', ['id'=>$post->id]);
    }

    public function test_index()
    {
        factory(Post::class, 5)->create();
        $response = $this->json('GET','/api/posts');
        $response->assertJsonStructure([
            'data'=>[
                '*'=>['id','title','created_at','updated_at']
            ]
        ])->assertStatus(200);
    }
}
