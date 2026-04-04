<?php

namespace App\Support;

class ToastManager
{
    protected string $sessionKey = 'toasts';

    public function add(
        string $type,
        ?string $title = null,
        ?string $message = null,
        array $args = [],
        int $timeout = 5000
    ): void
    {
        $toasts = session()->get($this->sessionKey, []);

        if (!empty($args)) {
            $escapedArgs = array_map('e', $args);
            $message = vsprintf($message, $escapedArgs);
        }

        $toasts[] = [
            'id' => uniqid(),
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'timeout' => $timeout,
        ];

        session()->flash($this->sessionKey, $toasts);
    }

    public function success(?string $title = null, ?string $message = null, array $args = [], int $timeout = 10000)
    {
        $this->add('success', $title, $message, $args, $timeout);
    }

    public function error(?string $title = null, ?string $message = null, array $args = [], int $timeout = 10000)
    {
        $this->add('error', $title, $message, $args, $timeout);
    }

    public function warning(?string $title = null, ?string $message = null, array $args = [], int $timeout = 10000)
    {
        $this->add('warning', $title, $message, $args, $timeout);
    }

    public function info(?string $title = null, ?string $message = null, array $args = [], int $timeout = 10000)
    {
        $this->add('info', $title, $message, $args, $timeout);
    }
}
