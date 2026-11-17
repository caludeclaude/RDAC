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

        // Everything below is saved together, or not at all
        $report = DB::transaction(function () use ($data, $anonymous, $office, $me, $now) {

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

            // History line 1 (replaces the first line of the old Notes column)
            $report->histories()->create([
                'occurred_at' => $now,
                'status' => ReportStatus::Pending,
                'office_id' => $office->id,
                'remarks' => 'Initial complaint submitted',
                'acted_by_user_id' => $me->id,
                'acted_by_office_id' => $me->office_id,
            ]);

            // Routing row 1 (replaces the first line of the old ForwardedTo column)
            $report->routings()->create([
                'position' => 1,
                'office_id' => $office->id,
                'routed_by_user_id' => $me->id,
                'routed_at' => $now,
            ]);

            return $report;
        });

        $who = $anonymous ? 'Anonymous' : $data['complainant_name'];
        Audit::record("Submitted report {$report->report_number} as {$who} -> {$office->name}", $me);

        return redirect()->route('reports.create')->with(
            'status',
            "Report created and submitted directly to {$office->name} with ID: {$report->report_number}"
        );
    }
}