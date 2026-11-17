<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatus;
use App\Http\Requests\StoreReportRequest;
use App\Models\ComplaintType;
use App\Models\Office;
use App\Models\Report;
use App\Support\Audit;
use App\Support\ReportNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ReportController extends Controller
{
    public function create()
    {
        return view('reports.create', [
            // Regional Office first, then the others alphabetically
            'offices' => Office::routable()->orderByRaw("office_type = 'regional' desc")->orderBy('name')->get(),
            'types' => ComplaintType::where('is_active', true)->orderBy('id')->get(),
            'provinces' => Office::PROVINCES,
        ]);
    }

    public function store(StoreReportRequest $request)
    {
        $me = $request->user();
        $data = $request->validated();
        $anonymous = (bool) ($data['is_anonymous'] ?? false);
        $office = Office::findOrFail($data['target_office_id']);
        $now = now();
        $storedPath = null;   // remembers the saved file, in case we must remove it

        try {
            // Everything below is saved together, or not at all
            $report = DB::transaction(function () use ($request, $data, $anonymous, $office, $me, $now, &$storedPath) {

                $report = Report::create([
                    'report_number' => ReportNumber::next(),
                    'subject' => $data['subject'],
                    'complaint_type_id' => $data['complaint_type_id'],
                    'description' => $data['description'],

                    'filed_at' => $now,
                    'filed_by_user_id' => $me->id,

                    'is_anonymous' => $anonymous,
                    'complainant_name' => $anonymous ? null : $data['complainant_name'],
                    'complainant_phone' => ($data['complainant_phone'] ?? '') ?: null,
                    'complainant_email' => ($data['complainant_email'] ?? '') ?: null,

                    'barangay' => $data['barangay'],
                    'city' => $data['city'],
                    'province' => $data['province'],

                    'status' => ReportStatus::Pending,
                    'initial_office_id' => $office->id,
                    'current_office_id' => $office->id,
                ]);

                $report->histories()->create([
                    'occurred_at' => $now,
                    'status' => ReportStatus::Pending,
                    'office_id' => $office->id,
                    'remarks' => 'Initial complaint submitted',
                    'acted_by_user_id' => $me->id,
                    'acted_by_office_id' => $me->office_id,
                ]);

                $report->routings()->create([
                    'position' => 1,
                    'office_id' => $office->id,
                    'routed_by_user_id' => $me->id,
                    'routed_at' => $now,
                ]);

                // The attachment (optional): saved in the PRIVATE folder under a random name
                if ($file = $request->file('attachment')) {
                    $storedPath = $file->store('attachments', 'local');

                    $report->attachments()->create([
                        'original_name' => mb_substr($file->getClientOriginalName(), 0, 255),
                        'stored_path' => $storedPath,
                        'mime_type' => $file->getMimeType(),      // the REAL type, read from the content
                        'file_size' => $file->getSize(),
                        'uploaded_by_user_id' => $me->id,
                    ]);
                }

                return $report;
            });
        } catch (Throwable $e) {
            // The database was rolled back, so do not leave the file behind
            if ($storedPath) {
                Storage::disk('local')->delete($storedPath);
            }
            throw $e;
        }

        $who = $anonymous ? 'Anonymous' : $data['complainant_name'];
        Audit::record("Submitted report {$report->report_number} as {$who} -> {$office->name}", $me);

        return redirect()->route('reports.create')->with(
            'status',
            "Report created and submitted directly to {$office->name} with ID: {$report->report_number}"
        );
    }
}