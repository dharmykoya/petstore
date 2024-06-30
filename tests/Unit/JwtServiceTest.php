<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Http\Services\JwtService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class JwtServiceTest extends TestCase
{
    protected $jwtService;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure storage paths for keys exist
        Storage::fake('local');
        $keysPath = storage_path('keys');
        if (!is_dir($keysPath)) {
            mkdir($keysPath, 0700, true);
        }

        // Initialize JwtService
        $this->jwtService = new JwtService();
    }

    public function test_keys_generation()
    {
        $privateKeyPath = storage_path('keys/private.key');
        $publicKeyPath = storage_path('keys/public.key');

        $this->assertFileExists($privateKeyPath);
        $this->assertFileExists($publicKeyPath);
    }

    public function testCreateToken()
    {
        $claims = ['id' => 1, 'email' => 'user@example.com'];
        $token = $this->jwtService->createToken($claims);

        // Assert token is a non-empty string
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
    }

    public function testParseToken()
    {
        $claims = ['id' => 1, 'email' => 'user@example.com'];
        $token = $this->jwtService->createToken($claims);

        $parsedClaims = $this->jwtService->parseToken($token);

        // Assert the claims are correctly parsed
        $this->assertIsArray($parsedClaims);
        $this->assertArrayHasKey('user', $parsedClaims);
        $this->assertEquals($claims, $parsedClaims['user']);
    }

    public function testGetUserFromToken()
    {
        $claims = ['id' => 1, 'email' => 'user@example.com'];
        $token = $this->jwtService->createToken($claims);

        $userClaims = $this->jwtService->getUserFromToken($token);

        // Assert the user claims are correctly parsed
        $this->assertIsArray($userClaims);
        $this->assertEquals($claims, $userClaims);
    }

    public function testValidateToken()
    {
        $claims = ['uuid' => "some-uuid"];
        $token = $this->jwtService->createToken($claims);

        // Modify the validation to match the claims you generate
        Config::set('app.url', 'http://example.com');
        $parsedClaims = $this->jwtService->validateToken($token);

        // Assert the token is valid and claims are returned
        $this->assertIsArray($parsedClaims);
        $this->assertArrayHasKey('user', $parsedClaims);
        $this->assertEquals($claims, $parsedClaims['user']);
    }

    public function testIsTokenExpired()
    {
        $claims = ['uuid' => "some-uuid"];
        $token = $this->jwtService->createToken($claims);

        // Assert the token is not expired initially
        $this->assertFalse($this->jwtService->isTokenExpired($token));

        // Fast-forward time by 2 minutes and check expiration
        Carbon::setTestNow(Carbon::now()->addHours(2));
        $this->assertTrue($this->jwtService->isTokenExpired($token));

        // Reset Carbon
        Carbon::setTestNow();
    }

    public function testIsTokenExpiredForInvalidToken()
    {
        // Test with an invalid token
        $invalidToken = 'invalid.token.value';
        $this->assertTrue($this->jwtService->isTokenExpired($invalidToken));
    }
}
