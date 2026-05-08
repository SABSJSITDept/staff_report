<?php

namespace App\Http\Controllers;

use App\Models\ITTicket;
use App\Models\ITTicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ITTicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        if ($user->canAccessIT()) {
            $tickets = ITTicket::with(['staff', 'itStaff'])->latest()->get();
        } else {
            $tickets = ITTicket::with(['itStaff'])->where('staff_id', $user->id)->latest()->get();
        }

        return view('ITTicket.Index', compact('tickets'));
    }

    public function create()
    {
        return view('ITTicket.Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category'          => 'required|in:Hardware,Software',
            'subject'           => 'required|string|max:255',
            'issue_description' => 'required|string',
            'photos'            => 'nullable|array|max:5',
            'photos.*'          => 'nullable|image|max:5120', // 5MB max each
        ]);

        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store('it_tickets', 'public');
            }
        }

        $ticket = ITTicket::create([
            'staff_id'          => Auth::id(),
            'category'          => $request->category,
            'subject'           => $request->subject,
            'issue_description' => $request->issue_description,
            'photos'            => $photoPaths,
            'status'            => 'Pending',
        ]);

        return redirect()->route('it-tickets.index')->with('success', 'Ticket successfully raise kar diya gaya hai!');
    }

    public function show(ITTicket $itTicket)
    {
        $user = Auth::user();

        // Authorization
        if (!$user->canAccessIT() && $itTicket->staff_id !== $user->id) {
            abort(403);
        }

        $itTicket->load(['staff', 'itStaff', 'replies.user']);

        return view('ITTicket.Show', compact('itTicket'));
    }

    public function reply(Request $request, ITTicket $itTicket)
    {
        $user = Auth::user();

        // Authorization
        if (!$user->canAccessIT() && $itTicket->staff_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'message'    => 'required|string',
            'attachment' => 'nullable|file|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('it_tickets/replies', 'public');
        }

        $itTicket->replies()->create([
            'user_id'    => $user->id,
            'message'    => $request->message,
            'attachment' => $attachmentPath,
        ]);

        return back()->with('success', 'Reply bhej di gayi hai!');
    }

    public function updateStatus(Request $request, ITTicket $itTicket)
    {
        if (!Auth::user()->canAccessIT()) {
            abort(403);
        }

        $request->validate([
            'status'  => 'required|in:Pending,In Progress,Completed,Paused',
            'remarks' => 'nullable|string',
        ]);

        $data = [
            'status'      => $request->status,
            'remarks'     => $request->remarks,
            'it_staff_id' => Auth::id(),
        ];

        $itTicket->update($data);

        return back()->with('success', 'Ticket status update kar diya gaya hai!');
    }

    public function assignTime(Request $request, ITTicket $itTicket)
    {
        if (!Auth::user()->canAccessIT()) {
            abort(403);
        }

        $request->validate([
            'expected_arrival_time' => 'required|date|after:now',
        ]);

        $itTicket->update([
            'expected_arrival_time' => $request->expected_arrival_time,
            'it_staff_id'           => Auth::id(),
        ]);

        return back()->with('success', 'Time assign kar diya gaya hai!');
    }
}
