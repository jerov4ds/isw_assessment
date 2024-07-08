<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testRegisterWithValidData()
    {
        // Arrange
        $data = [
            'name' => 'John Doe',
            'mobile' => '1234567890',
            'country' => 'CountryName',
            'state' => 'StateName',
            'email' => 'john.doe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $request = new Request($data);

        // Act
        $response = $this->postJson('/register', $data);

        // Assert
        $response->assertStatus(200);
        $response->assertJson([
            'code' => 200,
            'status' => 'Success',
        ]);
        $this->assertDatabaseHas('users', ['email' => 'john.doe@example.com']);
    }

    public function testRegisterWithInvalidData()
    {
        // Arrange
        $data = [
            'name' => '',
            'mobile' => '',
            'country' => '',
            'state' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'not-matching',
        ];

        // Act
        $response = $this->postJson('/register', $data);

        // Assert
        $response->assertStatus(403);
        $response->assertJson([
            'code' => '403',
            'status' => 'error',
        ]);
    }
}
