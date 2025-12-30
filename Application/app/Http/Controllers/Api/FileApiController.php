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
    public function getAllFiles(Request $request)
    {
        $search = $request->input('search');

        $query = FileEntry::notExpired()
            ->select('id', 'name', 'created_at'); // Added created_at for ordering

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $files = $query->orderBy('created_at', 'desc')
            ->paginate(50);

        $data = $files->map(function ($file) {
            return [
                'name' => $file->name,
                'direct_link' => route('secure.file', [hashid($file->id), $file->name]),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'pagination' => [
                'current_page' => $files->currentPage(),
                'last_page' => $files->lastPage(),
                'per_page' => $files->perPage(),
                'total' => $files->total(),
            ]
        ]);
    }
}
