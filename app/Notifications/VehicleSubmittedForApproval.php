<?php

namespace App\Notifications;

use App\Models\Vehicle;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleSubmittedForApproval extends Notification
{
    use Queueable;

    public function __construct(public Vehicle $vehicle)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New vehicle awaiting approval')
            ->line('A new vehicle has been submitted for approval:')
            ->line('**'.$this->vehicle->displayName().'** — '.money($this->vehicle->price))
            ->line('Reference: '.$this->vehicle->vehicle_reference)
            ->action('Review vehicle', url(route('admin.vehicles.show', $this->vehicle)));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New vehicle awaiting approval',
            'message' => $this->vehicle->displayName().' ('.$this->vehicle->vehicle_reference.') has been submitted for approval.',
            'url' => route('admin.vehicles.show', $this->vehicle),
        ];
    }
}