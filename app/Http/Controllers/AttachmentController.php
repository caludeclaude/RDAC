<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function show(Request $request, Report $report)
    {
        $user = $request->user();

        // 1. May this person see this complaint at all?
        if (! $user->can('view', $report)) {
            Audit::record("DENIED: tried to open the attachment of {$report->report_number}", $user);
            abort(403, 'You do not have access to this complaint.');
        }

        // 2. Does it have a file, and is the file really there?
        $attachment = $report->attachments()->latest('id')->first();
        $disk = Storage::disk('local');

        if (! $attachment || ! $disk->exists($attachment->stored_path)) {
            abort(404, 'This complaint has no attachment.');
        }

        Audit::record("Opened the attachment of {$report->report_number}", $user);

        // 3. Send it. The name shown to the user is cleaned of odd characters.
        $safeName = preg_replace('/[^A-Za-z0-9._ -]/', '_', $attachment->original_name) ?: 'attachment';
        $headers = [
            'Content-Type' => $attachment->mime_type,
            'X-Content-Type-Options' => 'nosniff',      // the browser must not "guess" another type
        ];

        return $request->boolean('download')
            ? $disk->download($attachment->stored_path, $safeName, $headers)
            : $disk->response($attachment->stored_path, $safeName, $headers);
    }
}