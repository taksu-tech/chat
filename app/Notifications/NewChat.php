<?php

namespace Taksu\TaksuChat\Notifications;

use App\Models\ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class NewChat extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected ChatMessage $chatMessage)
    {
        //
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // return ['mail'];
        return [FcmChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->setData([
                'room_id' => $this->chatMessage->room->id,
                'message_id' => $this->chatMessage->id,
                'created_at' => $this->chatMessage->created_at->toDateTimeString(),
                'notification' => 'chat_message',
                // 'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                // 'clickAction' => 'FLUTTER_NOTIFICATION_CLICK',
            ])
            ->setNotification(
                \NotificationChannels\Fcm\Resources\Notification::create()
                    ->title($this->chatMessage->room->name)
                    ->body($this->chatMessage->message)
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
