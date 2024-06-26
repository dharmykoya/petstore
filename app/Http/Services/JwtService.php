<?php

namespace App\Http\Services;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\Plain;

class JwtService
{
    private $config;

    public function __construct()
    {
        $this->config = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::plainText(env('JWT_PRIVATE_KEY')),
            InMemory::plainText(env('JWT_PUBLIC_KEY'))
        );
    }

    public function createToken(array $claims): Plain
    {
        $now = new \DateTimeImmutable();
        return $this->config->builder()
            ->issuedBy(config('app.url'))
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('uid', $claims['uid'])
            ->getToken($this->config->signer(), $this->config->signingKey());
    }

    public function parseToken(string $jwt): Plain
    {
        $token = $this->config->parser()->parse($jwt);
        $constraints = $this->config->validationConstraints();
        $this->config->validator()->assert($token, ...$constraints);

        return $token;
    }

    public function validateToken(string $jwt): bool
    {
        try {
            $token = $this->parseToken($jwt);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getTokenClaims(string $jwt): array
    {
        $token = $this->parseToken($jwt);
        return $token->claims()->all();
    }
}
