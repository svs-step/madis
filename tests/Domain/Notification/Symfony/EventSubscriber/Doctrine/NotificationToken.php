<?php

namespace App\Tests\Domain\Notification\Symfony\EventSubscriber\Doctrine;

use App\Domain\Notification\Model\Notification;
use Prophecy\Argument\Token\TokenInterface;

class NotificationToken implements TokenInterface
{
    private Notification $value;

    /**
     * Initializes token.
     */
    public function __construct(Notification $value)
    {
        $this->value = $value;
    }

    public function scoreArgument($argument): bool|int
    {
        if (!is_object($argument) || !(Notification::class === get_class($argument))) {
            return false;
        }

        if ($this->value->getAction() == $argument->getAction()
            && $this->value->getModule() == $argument->getModule()
            && $this->value->getName() == $argument->getName()
            && json_encode($this->value->getCollectivity()) == json_encode($argument->getCollectivity())
            && $this->value->getCreator() == $argument->getCreator()
            && json_encode($this->value->getObject()) == json_encode($argument->getObject())
        ) {
            return 11;
        }

        return 0;
    }

    public function isLast(): bool
    {
        return false;
    }

    public function __toString()
    {
        return 'notification';
    }
}
