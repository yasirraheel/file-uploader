<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FileEntry;
use Illuminate\Http\Request;

class FileApiController extends Controller
{
    /**
     * Get file details by shared ID.
     *
     * @param string $shared_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFileDetails($shared_id)
    {
        $fileEntry = FileEntry::where('shared_id', $shared_id)
            ->notExpired()
            ->first();

        if (!$fileEntry) {
            return response()->json([
                'status' => 'error',
                'message' => 'File not found or expired',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'name' => $fileEntry->name,
                'filename' => $fileEntry->filename,
                'mime_type' => $fileEntry->mime,
                'size' => $fileEntry->size,
                'extension' => $fileEntry->extension,
                'direct_link' => route('secure.file', [hashid($fileEntry->id), $fileEntry->name]),
                'download_link' => route('file.download', $fileEntry->shared_id),
                'created_at' => $fileEntry->created_at,
            ]
        ]);
    }
}
