<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TemplateMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        $this->website_name = (isset($email->website_name)) ? $email->website_name : config('app.name');
        $this->website_url = (isset($email->website_url)) ? $email->website_url : config('app.url');
        $this->from_name = (isset($email->from_name)) ? $email->from_name : config('general.mail_name_from');
        $this->from_email = (isset($email->from_email)) ? $email->from_email : config('general.mail_address_from');
        $this->to_name = $email->to_name;
        $this->to_email = $email->to_email;
        $this->subject = $email->subject;
        $this->template = $email->template;
        $this->body_top = (isset($email->body_top)) ? $email->body_top : null;
        $this->cta_button = (isset($email->cta_button)) ? $email->cta_button : null;
        $this->cta_url = (isset($email->cta_url)) ? $email->cta_url : null;
        $this->cta_color = (isset($email->cta_color)) ? $email->cta_color : 'primary';
        $this->body_bottom = (isset($email->body_bottom)) ? $email->body_bottom : null;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.template')
            ->from($this->from_email, $this->from_name)
            ->to($this->to_email, $this->to_name)
            ->subject($this->subject)
            ->with([
                'website_name' => $this->website_name,
                'website_url' => $this->website_url,
                'template' => $this->template,
                'cta_button' => $this->cta_button,
                'cta_url' => $this->cta_url,
            ]);
    }
}
