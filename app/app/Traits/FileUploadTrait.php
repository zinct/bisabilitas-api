<?php

namespace App\Traits;

trait FileUploadTrait
{
    protected function uploadFileFromRequest($fileRequest, $filename, $foldername)
    {
        $filename = $filename . '.' . $fileRequest->getClientOriginalExtension();
        return $fileRequest->storeAs($foldername, $filename);
    }
}
