<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminEventRequest;
use App\Http\Requests\UpdateAdminEventRequest;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::where('status', 'upcoming')->latest()->get();

        return view('events.index', compact('events'));
    }

    public function register(Request $request, Event $event)
    {
        if ($event->status !== 'upcoming') {
            return back()->with('error', __('app.event_not_open'));
        }

        if ($event->seats !== null) {
            $count = $event->registrations()->count();
            if ($count >= $event->seats) {
                return back()->with('error', __('app.event_full'));
            }
        }

        EventRegistration::updateOrCreate(
            [
                'event_id' => $event->id,
                'graduate_id' => Auth::id(),
            ],
            ['status' => 'registered']
        );

        return back()->with('success', __('app.event_registered_success'));
    }

    public function adminIndex()
    {
        $events = Event::withCount('registrations')->latest()->get();

        return view('admin.events.index', compact('events'));
    }

    public function adminCreate()
    {
        return view('admin.events.create');
    }

    public function adminStore(StoreAdminEventRequest $request)
    {
        Event::create(array_merge($request->validated(), ['status' => 'upcoming']));

        return redirect()->route('admin.events.index')
            ->with('success', __('app.event_created'));
    }

    public function adminEdit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function adminUpdate(UpdateAdminEventRequest $request, Event $event)
    {
        $event->update($request->validated());

        return redirect()->route('admin.events.index')
            ->with('success', __('app.event_updated'));
    }

    public function adminRegistrations(Event $event)
    {
        $registrations = $event->registrations()
            ->with(['graduate.graduate.major'])
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.events.registrations', compact('event', 'registrations'));
    }

    public function adminCancel(Event $event)
    {
        if ($event->status === 'cancelled') {
            return back()->with('error', __('app.event_already_cancelled'));
        }

        $event->update(['status' => 'cancelled']);

        return back()->with('success', __('app.event_cancelled'));
    }

    public function adminDestroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', __('app.event_deleted'));
    }
}
