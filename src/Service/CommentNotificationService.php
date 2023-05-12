<?php

namespace App\Service;

use App\Entity\Postcomment;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class CommentNotificationService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmailIfNotApproved(Postcomment $comment): void
    {   
        if (!$comment->isApproved()) {
            $email = (new Email())
                ->from('troudik033@gmail.com')
                ->to('khalilovic0073@gmail.com')
                ->subject('New Comment Requires Approval')
                ->text(sprintf('New comment with ID #%d requires approval.', $comment->getId()));
            $this->mailer->send($email);
        }
    }
}