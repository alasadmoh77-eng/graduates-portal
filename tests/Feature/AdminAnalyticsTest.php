<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Services\RequestStatusService;
use App\Notifications\RequestStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_is_sent_on_status_change()
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $graduate = User::factory()->create(['role' => 'graduate']);
        $type = DocumentType::create(['name_ar' => 'Test', 'name_en' => 'Test', 'code' => 'T1', 'fee_mock' => 10, 'eta_days' => 1]);
        
        $request = DocumentRequest::create([
            'user_id' => $graduate->id,
            'document_type_id' => $type->id,
            'tracking_code' => 'TEST-123',
            'status' => 'SUBMITTED',
            'language' => 'AR',
            'purpose' => 'Test',
            'delivery_type' => 'PICKUP'
        ]);

        $service = app(RequestStatusService::class);
        $service->transition($request, 'UNDER_REVIEW', 'Reviewing', $admin->id);

        Notification::assertSentTo($graduate, RequestStatusChanged::class);
    }

    public function test_export_requests_csv_returns_stream()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)
            ->get(route('admin.reports.requests.export'));

        $response->assertStatus(200);
        $this->assertEquals('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));
    }
}
