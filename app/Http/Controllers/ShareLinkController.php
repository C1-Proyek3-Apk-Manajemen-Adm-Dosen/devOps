<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Dokumen;

class ShareLinkController extends Controller
{
    public function generate($id)
    {
        $dokumen = Dokumen::find($id);

        if (!$dokumen) {
            return response()->json(['error' => 'Document not found'], 404);
        }

        $expiresAt = now()->addDays(7);

        $hash = Str::random(40);

        Cache::put("share:$hash", [
            'dokumen_id' => $dokumen->dokumen_id,
            'expires_at' => $expiresAt
        ], $expiresAt);

        return response()->json([
            'url' => url("/share/$hash"),
            'expires_at' => $expiresAt
        ]);
    }

    public function open($hash)
    {
        $data = Cache::get("share:$hash");

        if (!$data) {
            abort(404);
        }

        if (now()->greaterThan($data['expires_at'])) {
            abort(410);
        }

        $dokumen = Dokumen::find($data['dokumen_id']);

        if (!$dokumen) {
            abort(404);
        }

        $tempUrl = Storage::disk('minio')->temporaryUrl(
            $dokumen->file_path,
            now()->addDays(7)
        );

        return redirect()->away($tempUrl);
    }
}
