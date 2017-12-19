<?php

namespace Sumup\Api\Model\Merchant;

use Sumup\Api\Traits\HydratorTrait;

class Account
{
    use HydratorTrait;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $type;
}
