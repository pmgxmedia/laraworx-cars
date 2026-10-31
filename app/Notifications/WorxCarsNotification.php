<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

abstract class WorxCarsNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    abstract protected function title(): string;

    abstract protected function body(): string;

    protected function url(): ?string
    {
        return null;
    }

    protected function type(): string
    {
        return class_basename($this);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->type(),
            'title' => $this->title(),
            'body' => $this->body(),
            'url' => $this->url(),
        ];
    }

    public function toMail(object $notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject($this->title())
            ->greeting('Hello '.($notifiable->name ?? 'there').',')
            ->line($this->body())
            ->when($this->url(), fn ($m) => $m->action('Open WorxCars', $this->url()))
            ->line('Thank you for using WorxCars.');
    }
}