<?php

namespace App\Http\Services;

use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Validation\Constraint\RelatedTo;
use Lcobucci\JWT\Validation\Validator;

class JwtService
{
    private $config;

    public function __construct()
    {
        $this->config = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
    }

    public function createToken(array $claims): string
    {
        $now = new \DateTimeImmutable();
        $signingKey   = InMemory::plainText(random_bytes(32));
        return $this->config
            ->issuedBy(config('app.url'))
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('user', $claims)
            ->getToken(new Sha256(), $signingKey)->toString();
    }

    public function parseToken(string $jwt): array {
        try {
            $parser = new Parser(new JoseEncoder());
            $token = $parser->parse($jwt);
            return $token->claims()->all();
        } catch (\Exception $exception) {
            abort("Invalid token", 401);
        }
    }

    public function getUserFromToken(string $jwt): array {
        try {
            $parser = new Parser(new JoseEncoder());
            $token = $parser->parse($jwt);
            return $token->claims()->get('user');
        } catch (\Exception $exception) {
            abort("Invalid token", 401);
        }
    }


    public function validateToken(string $jwt)
    {
        $parser = new Parser(new JoseEncoder());
        $token = $parser->parse($jwt);
        $validator = new Validator();

        if (!$validator->validate($token, new RelatedTo('1234567891'))) {
            throw new \Exception('Token is invalid.');
        }

        if (! $validator->validate($token, new RelatedTo('1234567890'))) {
            throw new \Exception('Token is invalid.');
        }

        return $token->claims()->all();
    }
}
