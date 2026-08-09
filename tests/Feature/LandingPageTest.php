<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_the_landing_page(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Learn with');
        $response->assertSee('Start learning');
    }

    public function test_guests_can_send_a_contact_message(): void
    {
        $response = $this->post(route('contact.store'), [
            'name' => 'Taylor Jordan',
            'email' => 'taylor@example.test',
            'phone' => '+20 100 000 0000',
            'message' => 'I would like to learn more about enrollment.',
        ]);

        $response->assertRedirect('/#contact');
        $response->assertSessionHas('contact_success');

        $this->assertDatabaseHas(ContactMessage::class, [
            'email' => 'taylor@example.test',
        ]);
    }
}
