<?php

namespace Pageon\Tests\ExpiringToken;

use DateInterval;
use DateTimeImmutable;
use Pageon\ExpiringToken\ExpiringToken;
use Pageon\ExpiringToken\InvalidToken;
use Pageon\ExpiringToken\TokenHasExpired;
use PHPUnit\Framework\TestCase;

class ExpiringTokenTest extends TestCase
{
    public function testDefaultExpirationDate()
    {
        $now = new DateTimeImmutable();
        $expiringToken = ExpiringToken::create();

        self::assertEquals(
            $now->add(new DateInterval(ExpiringToken::DEFAULT_DATE_INTERVAL)),
            $expiringToken->getExpiresOn()
        );
        self::assertFalse($expiringToken->hasExpired());
    }

    public function testCustomExpirationDate()
    {
        $now = new DateTimeImmutable();
        $fourDaysInterval = new DateInterval('P4D');
        $expiringToken = ExpiringToken::create($fourDaysInterval);

        self::assertEquals(
            $now->add($fourDaysInterval),
            $expiringToken->getExpiresOn()
        );
        self::assertFalse($expiringToken->hasExpired());
    }

    public function testExpiredToken()
    {
        $interval = DateInterval::createFromDateString('P0D');
        $expiredToken = ExpiringToken::create($interval);
        sleep(1);
        self::assertTrue($expiredToken->hasExpired());

        $this->expectException(TokenHasExpired::class);
        $this->expectExceptionMessage('The token has expired');

        $expiredToken->validateAgainst($expiredToken);
    }

    public function testEmptyToken()
    {
        $this->expectException(InvalidToken::class);
        $this->expectExceptionMessage('Unable to decode the token');

        ExpiringToken::fromString('');
    }

    public function testRandomStringAsToken()
    {
        $this->expectException(InvalidToken::class);
        $this->expectExceptionMessage('Unable to decode the token');

        ExpiringToken::fromString('bobTha');
    }

    public function testMissingExpirationDate()
    {
        $this->expectException(InvalidToken::class);
        $this->expectExceptionMessage('Unable to decode the token');

        ExpiringToken::fromString(base64_encode('bobTha'));
    }

    public function testInvalidExpirationDate()
    {
        $this->expectException(InvalidToken::class);
        $this->expectExceptionMessage('Unable to decode the token');

        ExpiringToken::fromString(base64_encode('bob_token:token'));
    }

    public function testTokenIsUrlSafe()
    {
        $stringToken = (string) ExpiringToken::create();

        self::assertSame($stringToken, urlencode($stringToken));
    }

    public function testSuccessfulEnAndDecodingOfToken()
    {
        $expiringToken = ExpiringToken::create();
        self::assertEquals($expiringToken, ExpiringToken::fromString((string) $expiringToken));
    }

    public function testValidToken()
    {
        $validToken = ExpiringToken::create();
        self::assertTrue($validToken->validateAgainst($validToken));
    }

    public function testInvalidToken()
    {
        $token1 = ExpiringToken::create();
        $token2 = ExpiringToken::create();

        $this->expectException(InvalidToken::class);
        $this->expectExceptionMessage('The token does not match the original token');

        $token1->validateAgainst($token2);
    }
}
