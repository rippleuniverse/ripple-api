<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Resources\Event\EventResource;
use App\Models\Event;
use App\Traits\Files;
use App\Traits\Pagination;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    use Files, Pagination;

    public function viewAll()
    {
        $events = Event::latest()->paginate(12);
        $list = EventResource::collection($events);
        $data = $this->paginatedData($events, $list);

        return $this->success($data);
    }

    public function view(Event $event)
    {
        $data = new EventResource($event);
        return $this->success($data);
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'date' => ['required', 'date'],
            'featured_image' => ['required', 'mimes:jpg,jpeg,png', 'max:5120'],
            'access' => ['required', 'in:free,paid'],
            'type' => ['required', 'in:physical,online'],
        ]);

        $featuredImage = $this->uploadFile($request->file('featured_image'), 'events');

        $data['featured_image'] = $featuredImage;
        Event::create($data);
        return $this->success(null, 'Event created successfully.');
    }

    public function update(Event $event, Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'date' => ['required', 'date'],
            'featured_image' => ['nullable', 'mimes:jpg,jpeg,png', 'max:5120'],
            'access' => ['required', 'in:free,paid'],
            'type' => ['required', 'in:physical,online'],
        ]);
        $featuredImage = $request->file('featured_image') ?
            $this->uploadFile($request->file('featured_image'), 'events') :
            $event->featured_image;
        $data['featured_image'] = $featuredImage;

        $event->update($data);
        return $this->success(null, 'Event updated successfully.');

    }

    public function destroy(Event $event)
    {
        $event->delete();
        return $this->success(null, 'Event deleted successfully.');
    }
}
