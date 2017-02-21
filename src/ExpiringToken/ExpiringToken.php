<?php

namespace Pageon\ExpiringToken;

use DateInterval;
use DateTimeImmutable;
use Throwable;

/**
 * A random token of 128 characters with an expiration date.
 *
 * The token uses base64 but the length is calculated so that it is url safe, so no padding with the = character.
 */
final class ExpiringToken
{
    /** @var string */
    const DEFAULT_DATE_INTERVAL = 'P3D';

    /** @var DateTimeImmutable */
    private $expiresOn;

    /** @var string */
    private $token;

    /**
     * @param DateTimeImmutable $expiresOn
     * @param string $token
     */
    private function __construct(DateTimeImmutable $expiresOn, $token)
    {
        $this->expiresOn = $expiresOn;
        $this->token = $token;
    }

    /**
     * @param DateInterval|null $dateInterval If the interval isn't specified the token will expire in 3 days
     *
     * @return self
     */
    public static function create(DateInterval $dateInterval = null): self
    {
        return new self(
            (new DateTimeImmutable())->add(
                $dateInterval ?? new DateInterval(self::DEFAULT_DATE_INTERVAL)
            ),
            bin2hex(random_bytes(32))
        );
    }

    /**
     * @param string $string The string representation of a random token created via the __toString method.
     *
     * @throws InvalidToken
     *
     * @return ExpiringToken
     */
    public static function fromString(string $string): self
    {
        $decodedString = base64_decode($string);

        if (!$decodedString) {
            // couldn't decode the token
            throw InvalidToken::unableToDecode();
        }

        $tokenParts = explode('_token:', $decodedString, 2);

        if (count($tokenParts) !== 2) {
            throw InvalidToken::unableToDecode();
        }

        try {
            return new self(new DateTimeImmutable($tokenParts[0]), $tokenParts[1]);
        } catch (Throwable $throwable) {
            throw InvalidToken::unableToDecode();
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return base64_encode($this->expiresOn->format(DATE_ATOM) . '_token:' . $this->token);
    }

    /**
     * @param ExpiringToken $expiringToken
     *
     * @throws InvalidToken
     * @throws TokenHasExpired
     *
     * @return bool
     */
    public function validateAgainst(ExpiringToken $expiringToken): bool
    {
        if (!$this->equals($expiringToken)) {
            throw InvalidToken::tokensDoNotMatch();
        }

        if ($this->hasExpired()) {
            throw new TokenHasExpired();
        }

        return true;
    }

    /**
     * @return bool
     */
    public function hasExpired(): bool
    {
        return new DateTimeImmutable() > $this->expiresOn;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getExpiresOn(): DateTimeImmutable
    {
        return $this->expiresOn;
    }

    /**
     * @param ExpiringToken $expiringToken
     *
     * @return bool
     */
    private function equals(ExpiringToken $expiringToken): bool
    {
        return $this->token === $expiringToken->token && $this->expiresOn == $expiringToken->expiresOn;
    }
}
