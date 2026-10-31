<?php

namespace App\Notifications;

class AccountCreated extends WorxCarsNotification
{
    public function __construct(public string $welcomeMessage = 'Your WorxCars account has been created.')
    {
    }

    protected function title(): string
    {
        return 'Welcome to WorxCars';
    }

    protected function body(): string
    {
        return $this->welcomeMessage;
    }

    protected function url(): ?string
    {
        return '/dashboard';
    }

    protected function type(): string
    {
        return 'account';
    }
}