<?php

namespace App\Http\Services;

use Carbon\Carbon;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Validation\Constraint\RelatedTo;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Validator;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token\Plain;

class JwtService
{
    private Configuration $config;
    protected string $privateKeyPath;
    protected string $publicKeyPath;

    public function __construct()
    {
        // path to keys
        $this->privateKeyPath = storage_path('keys/private.key');
        $this->publicKeyPath = storage_path('keys/public.key');

        // Create keys if they don't exist
        if (!file_exists($this->privateKeyPath) || !file_exists($this->publicKeyPath)) {
            $this->generateKeys();
        }

        // Load private and public keys
        $privateKey = $this->getKey($this->privateKeyPath);
        $publicKey = $this->getKey($this->publicKeyPath);

        // Create the JWT configuration
        $this->config = Configuration::forAsymmetricSigner(
            new Sha256(),
            $privateKey,
            $publicKey
        );
    }

    protected function getKey(string $path): InMemory {
        if (empty($path) || !file_exists($path)) {
            throw new \InvalidArgumentException("Key path is invalid or does not exist: $path");
        }
        return InMemory::file($path);
    }

    protected function generateKeys(): void {
        // Ensure the keys directory exists
        $keysDir = dirname($this->privateKeyPath);
        if (!is_dir($keysDir)) {
            mkdir($keysDir, 0700, true);
        }

        // Generate the private key
        exec("openssl genpkey -algorithm RSA -out {$this->privateKeyPath} -pkeyopt rsa_keygen_bits:2048");

        // Generate the public key from the private key
        exec("openssl rsa -pubout -in {$this->privateKeyPath} -out {$this->publicKeyPath}");
    }

    /**
     * Create a JWT token.
     *
     * @param array<string, mixed> $claims
     * @return string
     */
    public function createToken(array $claims): string
    {
        $now = new \DateTimeImmutable();
        return $this->config
            ->builder()
            ->issuedBy(config('app.url'))
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('user', $claims)
            ->getToken($this->config->signer(), $this->config->signingKey())->toString();
    }

    /**
     * Parse a JWT token and return the claims.
     *
     * @param string $jwt
     * @return array<string, mixed>
     */
    public function parseToken(string $jwt): array {
        try {
            /** @var Plain $token */
            $token = $this->config->parser()->parse($jwt);
            return $token->claims()->all();
        } catch (\Exception $exception) {
            abort(401, "Invalid token");
        }
    }

    /**
     * Parse a JWT token and return the claims.
     *
     * @param string $jwt
     * @return array<string, mixed>
     */
    public function getUserFromToken(string $jwt): array {
        try {
            /** @var Plain $token */
            $token = $this->config->parser()->parse($jwt);

            return $token->claims()->get('user');
        } catch (\Exception $exception) {
            abort(401, "Invalid token");
        }
    }

    public function isTokenExpired(string $token): bool
    {
        try {
            /** @var Plain $parsedToken */
            $parsedToken = $this->config->parser()->parse($token);
            // Get the expiration timestamp from the parsed token
            $expTimestamp = $parsedToken->claims()->get('exp');

            $expTimestampMod = Carbon::instance($expTimestamp);

            // Check if the expiration time is in the past (i.e., token is expired)
            return Carbon::now()->greaterThan($expTimestampMod);

        } catch (\Throwable $e) {
            return true;
        }
    }

    /**
     * Validate a JWT token.
     *
     * @param string $jwt
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function validateToken(string $jwt): array {
        if ($this->isTokenExpired($jwt)) {
            throw new \Exception('Token has expired');
        }
        /** @var Plain $token */
        $token = $this->config->parser()->parse($jwt);
        $validator = new Validator();
        $constraints = [
            new SignedWith($this->config->signer(), $this->config->signingKey()),
        ];

        if (!$validator->validate($token, ...$constraints)) {
            throw new \Exception('Token is invalid.');
        }

        return $token->claims()->all();
    }
}
