<?php

namespace App\Models\Concerns;

use App\Models\Document;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasDocuments
{
    /**
     * Get all of the model's documents.
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable')->latest();
    }

    /**
     * Add a document to the model.
     */
    public function addDocument(array $data): Document
    {
        return $this->documents()->create($data);
    }

    /**
     * Delete all documents associated with the model.
     */
    public function deleteDocuments(): void
    {
        $this->documents->each(function ($document) {
            $document->deleteWithFile();
        });
    }
}
