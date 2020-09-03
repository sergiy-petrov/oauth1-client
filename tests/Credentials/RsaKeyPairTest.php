<?php

namespace Credentials;

use League\OAuth1\Client\Credentials\RsaKeyPair;
use PHPStan\Testing\TestCase;
use RuntimeException;

class RsaKeyPairTest extends TestCase
{
    /** @test */
    public function it_can_open_a_public_key(): void
    {
        $keyPair = new RsaKeyPair(
            __DIR__ . '/files/public.pem',
            __DIR__ . '/files/private.pem',
            'a-passphrase'
        );

        self::assertIsResource($keyPair->getPublicKey());
    }

    /** @test */
    public function it_throws_an_exception_when_it_cannot_open_a_public_key(): void
    {
        $keyPair = new RsaKeyPair(
            'invalid',
            'doesnt matter'
        );

        $this->expectException(RuntimeException::class);

        $keyPair->getPublicKey();
    }

    /** @test */
    public function it_can_open_a_private_key(): void
    {
        $keyPair = new RsaKeyPair(
            __DIR__ . '/files/public.pem',
            __DIR__ . '/files/private.pem',
            'a-passphrase'
        );

        self::assertIsResource($keyPair->getPrivateKey());
    }

    /** @test */
    public function it_throws_an_exception_when_it_cannot_open_a_private_key(): void
    {
        $keyPair = new RsaKeyPair(
            'doesnt matter',
            'invalid'
        );

        $this->expectException(RuntimeException::class);

        $keyPair->getPrivateKey();
    }

    /** @test */
    public function it_throws_an_exception_when_the_passphrase_is_incorrect(): void
    {
        $keyPair = new RsaKeyPair(
            __DIR__ . '/files/public.pem',
            __DIR__ . '/files/private.pem',
            'incorrect passphrase'
        );

        $this->expectException(RuntimeException::class);

        $keyPair->getPrivateKey();
    }
}
