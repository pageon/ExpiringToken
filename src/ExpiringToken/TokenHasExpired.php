<?php

namespace Pageon\ExpiringToken;

use Exception;

final class TokenHasExpired extends Exception
{
    public function __construct()
    {
        parent::__construct('The token has expired');
    }
}
