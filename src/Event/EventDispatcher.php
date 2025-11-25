<?php

namespace Tuezy\Event;

/**
 * EventDispatcher - Simple event system
 * Allows subscribing to and dispatching events
 */
class EventDispatcher
{
    private array $listeners = [];

    /**
     * Subscribe to event
     * 
     * @param string $event Event name
     * @param callable $listener Event listener
     */
    public function on(string $event, callable $listener): void
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }
        $this->listeners[$event][] = $listener;
    }

    /**
     * Dispatch event
     * 
     * @param string $event Event name
     * @param mixed $payload Event payload
     * @return mixed Result from listeners
     */
    public function dispatch(string $event, $payload = null)
    {
        if (!isset($this->listeners[$event])) {
            return $payload;
        }

        $result = $payload;
        foreach ($this->listeners[$event] as $listener) {
            $result = $listener($result, $event);
        }

        return $result;
    }

    /**
     * Remove all listeners for event
     * 
     * @param string $event Event name
     */
    public function off(string $event): void
    {
        unset($this->listeners[$event]);
    }

    /**
     * Check if event has listeners
     * 
     * @param string $event Event name
     * @return bool
     */
    public function hasListeners(string $event): bool
    {
        return isset($this->listeners[$event]) && !empty($this->listeners[$event]);
    }

    /**
     * Get all listeners for event
     * 
     * @param string $event Event name
     * @return array
     */
    public function getListeners(string $event): array
    {
        return $this->listeners[$event] ?? [];
    }
}

