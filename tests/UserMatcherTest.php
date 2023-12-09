<?php

declare(strict_types=1);

namespace Tests;

use AqBanking\Bank;
use AqBanking\BankCode;
use AqBanking\ExistingUser;
use AqBanking\HbciVersion;
use AqBanking\User;
use AqBanking\UserMatcher;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\UserMatcher
 * @uses \AqBanking\User
 * @uses \AqBanking\ExistingUser
 * @uses \AqBanking\Bank
 * @uses \AqBanking\BankCode
 * @uses \AqBanking\HbciVersion
 */
class UserMatcherTest extends TestCase
{
    private static string $matchingUserId = '12';

    private static string $matchingUserName = 'Max Mustermann';

    private static string $matchingBankCode = '50050010';

    /**
     * @dataProvider samples
     */
    public function testUserMatcher(string $userName, string $userId, string $bankCode, bool $pass): void
    {
        $user = new User(
            userId: $userId,
            userName: $userName,
            bank: new Bank(
                bankCode: new BankCode($bankCode),
                hbciUrl: 'https://fints.bank.de/fints',
                hbciVersion: new HbciVersion('1.2.3'),
            ),
        );

        $matcher = new UserMatcher($this->getDomDocument());
        if (! $pass) {
            $this->assertNull($matcher->getExistingUser($user));
        } else {
            $this->assertInstanceOf(ExistingUser::class, $matcher->getExistingUser($user));
        }
    }

    public function testUserMatcherNoDom(): void
    {
        $user = new User(
            userId: '123',
            userName: 'Name',
            bank: new Bank(
                bankCode: new BankCode('1235690'),
                hbciUrl: 'https://fints.bank.de/fints',
                hbciVersion: new HbciVersion('1.2.3'),
            ),
        );

        $matcher = new UserMatcher();
        $this->assertNull($matcher->getExistingUser($user));
    }

    public static function samples(): array
    {
        return [
            'match' => [self::$matchingUserName, self::$matchingUserId, self::$matchingBankCode, true],
            'no match' => ['User', 'ID', '123', false],
        ];
    }

    private function getDomDocument(): \DOMDocument
    {
        $domDocument = new \DOMDocument();
        $domDocument->loadXML('<?xml version="1.0"?>
        <users>
            <user>
                <userUniqueId>1</userUniqueId>
                <UserName><![CDATA[User]]></UserName>
                <UserId>23</UserId>
                <CustomerId>CutomerID</CustomerId>
                <BankCode>12345678</BankCode>
                <Country>de</Country>
                <LastSessionId>0</LastSessionId>
            </user>
            <user>
                <userUniqueId>3</userUniqueId>
                <UserName><![CDATA[' . self::$matchingUserName . ']]></UserName>
                <UserId>' . self::$matchingUserId . '</UserId>
                <CustomerId>123456</CustomerId>
                <BankCode>' . self::$matchingBankCode . '</BankCode>
                <Country>de</Country>
                <LastSessionId>0</LastSessionId>
            </user>
        </users>');

        return $domDocument;
    }
}
