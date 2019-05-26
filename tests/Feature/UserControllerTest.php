<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;

class UserControllerTest extends TestCase
{
    // データベースの初期化にトランザクションを使う
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testIndex()
    {
        // `users` テーブルにデータを作成
        $user = factory(User::class)->create([
            'email' => 'user1@example.com',
        ]);

        // GET リクエスト
        $response = $this->get(route('users.index'));

        // 1件のレスポンスがある
        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'email' => 'user1@example.com',
            ]);

        // DBのデータチェック
        $this->assertDatabaseHas('users', [
            'email' => 'user1@example.com',
        ]);

        // レスポンスの内容を取得して JSON デコードする
        $json = json_decode($response->getContent(), 1);
        $this->assertArraySubset([[
                'email' => 'user1@example.com',
        ]], $json);

        $this->assertTrue(Arr::has($json, '0.email'));
        $this->assertEquals('user1@example.com', Arr::get($json, '0.email'));
    }

    public function testStore()
    {
        $data = [
            'user' => [
                'email' => 'user@example.com',
                'name' => 'Tanaka Taro',
                'password' => '123456',
            ],
        ];

        // POST リクエスト
        $response = $this->post(route('users.store'), $data);

        // 1件のレスポンスがある
        $response->assertOk()
            ->assertJsonFragment([
                'email' => 'user@example.com',
                'name' => 'Tanaka Taro',
            ]);

        // DBのデータチェック
        $this->assertDatabaseHas('users', [
            'email' => 'user@example.com',
        ]);
    }

    public function testDestroy()
    {
        // `users` テーブルにデータを作成
        $user = factory(User::class)->create([
            'email' => 'user1@example.com',
        ]);

        // GET リクエスト
        $response = $this->delete(route('users.destroy', [$user->id]));

        // ステータスコード 200
        $response->assertOk();

        // `users` テーブルは0件
        $this->assertEquals(0, User::count());

        $this->assertDatabaseMissing('users', [
            'email' => 'user1@example.com',
        ]);
    }
}
