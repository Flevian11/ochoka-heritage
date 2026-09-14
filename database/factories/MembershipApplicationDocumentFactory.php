<?php

namespace Database\Factories;

use App\Models\MembershipApplication;
use App\Models\MembershipApplicationDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MembershipApplicationDocument> */
class MembershipApplicationDocumentFactory extends Factory
{
    protected $model = MembershipApplicationDocument::class;

    public function definition(): array
    {
        $application = MembershipApplication::factory()->create();
        return [
            'membership_application_id' => $application->id,
            'organization_id' => $application->organization_id,
            'uploaded_by' => null,
            'document_type' => 'identity',
            'original_name' => 'identity.pdf',
            'storage_disk' => 'local',
            'storage_path' => 'membership-applications/example/identity.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 1024,
            'metadata' => null,
        ];
    }
}
