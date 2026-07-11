<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactUsEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_submits_saves_to_database_and_sends_email(): void
    {
        Mail::fake();

        // Configure the receiver email temporarily in config
        config(['mail.contact_receiver' => 'admin@sru.edu.ye']);

        $formData = [
            'name' => 'أحمد علي',
            'email' => 'ahmed@example.com',
            'subject' => 'استفسار حول موعد التخرج',
            'message' => 'السلام عليكم، أود الاستفسار عن موعد حفل التخرج القادم وشكراً لكم.',
        ];

        $response = $this->post(route('alumni.contact.store'), $formData);

        // Assert redirect back
        $response->assertRedirect(route('alumni.contact'));
        $response->assertSessionHas('success');

        // Assert database record exists
        $this->assertDatabaseHas('contact_messages', [
            'name' => 'أحمد علي',
            'email' => 'ahmed@example.com',
            'subject' => 'استفسار حول موعد التخرج',
            'message' => 'السلام عليكم، أود الاستفسار عن موعد حفل التخرج القادم وشكراً لكم.',
        ]);

        // Retrieve the created message to check mailable bindings
        $message = ContactMessage::first();
        $this->assertNotNull($message);

        // Assert email was sent to contact_receiver
        Mail::assertSent(ContactMessageMail::class, function (ContactMessageMail $mail) use ($message) {
            return $mail->hasTo('admin@sru.edu.ye') &&
                   $mail->contactMessage->id === $message->id;
        });
    }

    public function test_contact_form_validation(): void
    {
        Mail::fake();

        $formData = [
            'name' => '',
            'email' => 'invalid-email',
            'subject' => '',
            'message' => 'short',
        ];

        $response = $this->post(route('alumni.contact.store'), $formData);

        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
        
        // Assert no database record and no mail sent
        $this->assertDatabaseEmpty('contact_messages');
        Mail::assertNothingSent();
    }
}
