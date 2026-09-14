<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\MeetingDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingDocumentFactory extends Factory
{
    protected $model = MeetingDocument::class;

    public function definition(): array
    {
        return [
            'meeting_id' => Meeting::factory(),
            'document_type' => MeetingDocument::TYPE_SUPPORTING,
            'original_name' => 'meeting-document.pdf',
            'stored_path' => 'meetings/' . fake()->uuid() . '/meeting-document.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(1000, 500000),
            'uploaded_by' => null,
            'description' => null,
        ];
    }
}
