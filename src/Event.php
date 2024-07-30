<?php

declare(strict_types=1);

namespace PHP94\Event;

use Fig\EventDispatcher\AggregateProvider;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class Event extends AggregateProvider implements EventDispatcherInterface, ListenerProviderInterface
{
    public function dispatch(object $event)
    {
        if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
            return $event;
        }
        foreach ($this->getListenersForEvent($event) as $listener) {
            $listener($event);
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }
        }
        return $event;
    }
}
