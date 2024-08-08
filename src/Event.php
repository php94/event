<?php

declare(strict_types=1);

namespace PHP94\Event;

use Fig\EventDispatcher\AggregateProvider;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class Event extends AggregateProvider implements EventDispatcherInterface, ListenerProviderInterface
{
    private $listeners = [];

    public function dispatch(object $event)
    {
        foreach ($this->listeners as $vo) {
            if (is_a($event, $vo['event'])) {
                if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                    return $event;
                }
                call_user_func($vo['callback'], $event);
            }
        }
        foreach ($this->getListenersForEvent($event) as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                return $event;
            }
            call_user_func($listener, $event);
        }
        return $event;
    }

    public function listen(string $event, callable $callback): self
    {
        $this->listeners[] = [
            'event' => $event,
            'callback' => $callback
        ];
        return $this;
    }
}
