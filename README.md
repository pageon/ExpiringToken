# Expiring Token

[![Build Status](https://travis-ci.org/Pageon/ExpiringToken.svg?branch=master)](https://travis-ci.org/Pageon/ExpiringToken)
[![Latest Stable Version](https://poser.pugx.org/pageon/expiring-token/v/stable.svg)](https://packagist.org/packages/Pageon/ExpiringToken)
[![License](https://poser.pugx.org/pageon/expiring-token/license.svg)](https://packagist.org/packages/Pageon/ExpiringToken)
[![Code Coverage](https://scrutinizer-ci.com/g/Pageon/ExpiringToken/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Pageon/ExpiringToken/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Pageon/ExpiringToken/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Pageon/ExpiringToken/?branch=master)

The expiring token generates a random token of 128 characters with an expiration date.

The token uses base64 but the length is calculated so that it is url safe, so no padding with the = character.

The actual token is generated with `random_bytes` with a length of `32`

## Public api

### ExpiringToken::create

You can create a new token this way. It accepts a `DateInterval` as optional parameter to set a different expiration date.
 
The default expiration date is 3 days.

### ExpiringToken::fromString

Used to create an instance of the token from the string representation

### ExpiringToken::__toString

This turns the class instance into the string version of the token when the instance is used as or cast to a string.

### ExpiringToken::validateAgainst

This can be used to validate the current token against an other token
 
* When the tokens maths this method will return `true`
* An `InvalidToken` exception will be thrown if the tokens don't match
* An `TokenHasExpired` exception will be thrown if the token has expired

### ExpiringToken::hasExpired

Returns a bool indicating if the current token has expired

## ExpiringToken::getExpiresOn

Can be used to get the expiration date of the token
