<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPostPublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Post $post;

    /**
     * Create a new notification instance.
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $project = $this->post->project;
        $company = $project->company;

        return (new MailMessage)
            ->subject('Nueva publicación en ' . $company->name)
            ->view('emails.new_post', [
                'post'       => $this->post,
                'project'    => $project,
                'company'    => $company,
                'notifiable' => $notifiable,
            ]);
    }
}
