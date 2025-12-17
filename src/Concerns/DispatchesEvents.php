<?php

namespace Stesa\CloudlinkerClient\Concerns;

trait DispatchesEvents
{
    /**
     * Dispatch an event if Laravel is available.
     */
    protected function dispatchEvent(object $event): void
    {
        if (function_exists('event') && function_exists('app')) {
            try {
                event($event);
            } catch (\Throwable) {
                // Laravel not fully booted, skip event dispatch
            }
        }
    }
}
