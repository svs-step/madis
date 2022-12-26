<?php

namespace App\Tests\Domain\Notification\Symfony\EventSubscriber\Doctrine;

use App\Domain\Notification\Model\Notification;
use Prophecy\Argument\Token\TokenInterface;
use Prophecy\Comparator\Factory as ComparatorFactory;
use Prophecy\Util\StringUtil;

class NotificationToken implements TokenInterface
{
    private Notification $value;
    /**
     * Initializes token.
     *
     * @param Notification             $value
     */
    public function __construct(Notification $value)
    {
        $this->value = $value;
    }

    public function scoreArgument($argument)
    {
        if (!is_object($argument) || !get_class($argument) === Notification::class) {
            return false;
        }
        
        if ($this->value->getAction() == $argument->getAction()
            && $this->value->getModule()== $argument->getModule()
            && $this->value->getName()== $argument->getName()
            && $this->value->getCollectivity()== $argument->getCollectivity()
            && $this->value->getCreator()== $argument->getCreator()
            && json_encode($this->value->getObject()) == json_encode($argument->getObject())
        ) {
            return 11;
        }

        return 0;
    }

    public function isLast()
    {
        return false;
    }

    public function __toString()
    {
        return 'notification';
    }
}
