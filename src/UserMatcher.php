<?php

declare(strict_types=1);

namespace AqBanking;

use DOMDocument;
use DOMElement;

/**
 * Find user in existing user database
 */
class UserMatcher
{
    public function __construct(
        private readonly ?DOMDocument $domDocument = null
    ) {
    }

    public function getExistingUser(User $user): ?ExistingUser
    {
        if (null === $this->domDocument) {
            return null;
        }

        foreach ($this->domDocument->getElementsByTagName('user') as $node) {
            /** @var DOMElement $node */
            if (
                $user->getUserName() === $node->getElementsByTagName('UserName')->item(0)?->nodeValue &&
                $user->getUserId() === $node->getElementsByTagName('UserId')->item(0)?->nodeValue &&
                $user->getBank()->getBankCode()->getString() === $node->getElementsByTagName('BankCode')->item(0)?->nodeValue
            ) {
                return new ExistingUser($user, (int) $node->getElementsByTagName('userUniqueId')->item(0)->nodeValue);
            }
        }

        return null;
    }
}
