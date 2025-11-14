<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;

class AssetUploader extends Component
{
    use WithFilePond;

    public $attachments;

    public function render()
    {
        return view('livewire.asset-uploader');
    }
}
