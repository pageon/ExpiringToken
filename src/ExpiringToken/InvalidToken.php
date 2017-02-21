<?php

namespace Pageon\ExpiringToken;

use Exception;

final class InvalidToken extends Exception
{
    /**
     * @return InvalidToken
     */
    public static function unableToDecode(): self
    {
        return new self('Unable to decode the token');
    }

    /**
     * @return InvalidToken
     */
    public static function tokensDoNotMatch(): self
    {
        return new self('The token does not match the original token');
    }
}
