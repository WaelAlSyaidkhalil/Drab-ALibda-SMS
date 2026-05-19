<?php

namespace App\Models\Traits;

use App\Models\Communication\Attachment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasAttachments
{
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable')->orderBy('order');
    }

    public function addAttachment(array $data): Attachment
    {
        return $this->attachments()->create($data);
    }

    public function images()
    {
        return $this->attachments()->where('type', 'image');
    }

    public function videos()
    {
        return $this->attachments()->where('type', 'video');
    }
}
