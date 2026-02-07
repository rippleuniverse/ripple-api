<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Resources\Event\CategoryResource;
use App\Http\Resources\Event\EventResource;
use App\Http\Resources\Event\TicketResource;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventTicket;
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
        $data = [
            'id' => $event->id,
            'featured_image' => $this->getFilePath($event->featured_image),
            'images' => $this->getFilePaths(json_decode($event->images, true)) ?? [],
            'title' => $event->title,
            'description' => $event->description,
            'date' => $event->date->format('Y-m-d'),
            'access' => $event->access,
            'type' => $event->type,
            'category' => [
                'id' => (string)$event->category->id,
                'name' => $event->category->name,
            ],
            'what_to_expect' => json_decode($event->what_to_expect),
            'who_to_expect' => json_decode($event->who_to_expect),
            'agendas' => json_decode($event->agendas),
            'facilitators' => json_decode($event->facilitators),
            'tickets' => TicketResource::collection($event->tickets),
            'created_at' => $event->created_at->format('Y-m-d'),
        ];
        return $this->success($data);
    }

    public function viewCategories()
    {
        $categories = EventCategory::all();
        $data = CategoryResource::collection($categories);

        return $this->success($data);
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'date' => ['required', 'date'],
            'category_id' => ['required', 'exists:event_categories,id'],
            'featured_image' => ['required', 'mimes:jpg,jpeg,png', 'max:5120'],
            'images' => ['array', 'max:4'],
            'images.*' => ['required', 'mimes:jpg,jpeg,png', 'max:5120'],
            'access' => ['required', 'in:free,paid'],
            'type' => ['required', 'in:physical,online'],
            'what_to_expect' => ['array'],
            'what_to_expect.*' => ['required', 'string', 'max:191'],
            'who_to_expect' => ['array'],
            'who_to_expect.*' => ['required', 'string', 'max:191'],
            'agendas' => ['array'],
            'agendas.*' => ['required', 'string', 'max:191'],
            'facilitators' => ['array'],
            'facilitators.*.name' => ['required', 'string', 'max:80'],
            'facilitators.*.role' => ['required', 'string', 'max:80'],
            'facilitators.*.company' => ['required', 'string', 'max:80'],
            'facilitators.*.description' => ['required', 'string', 'max:80'],
            'tickets' => ['required', 'array', 'min:1'],
            'tickets.*.name' => ['required', 'string', 'max:191'],
            'tickets.*.price' => ['array', 'min:2', 'max:2'],
            'tickets.*.price.0.currency' => ['required', 'string', 'in:NGN'],
            'tickets.*.price.0.amount' => ['required', 'numeric:', 'min:0'],
            'tickets.*.price.1.currency' => ['required', 'string', 'in:USD'],
            'tickets.*.price.1.amount' => ['required', 'numeric:', 'min:0'],
            'tickets.*.features' => ['array'],
            'tickets.*.features.*' => ['required', 'string', 'max:191'],
        ]);

        $featuredImage = $this->uploadFile($request->file('featured_image'), 'events');
        $images = $request->file('images') ?
            $this->uploadFiles($request->file('images'), 'events') : [];
        $whatToExpect = json_encode($data['what_to_expect'] ?? []);
        $whoToExpect = json_encode($data['who_to_expect'] ?? []);
        $agendas = json_encode($data['agendas'] ?? []);
        $facilitators = json_encode($data['facilitators'] ?? []);

        $data['featured_image'] = $featuredImage;
        $event = Event::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'date' => $data['date'],
            'images' => json_encode($images),
            'featured_image' => $data['featured_image'],
            'access' => $data['access'],
            'type' => $data['type'],
            'event_category_id' => $data['category_id'],
            'what_to_expect' => $whatToExpect,
            'who_to_expect' => $whoToExpect,
            'agendas' => $agendas,
            'facilitators' => $facilitators,
        ]);
        $tickets = array_map(function ($ticket) use ($event) {
            return [
                'name' => $ticket['name'],
                'price' => json_encode($ticket['price']),
                'features' => json_encode($ticket['features'] ?? []),
            ];
        }, $data['tickets'] ?? []);

        count($tickets) > 0 && $event->tickets()->createMany($tickets);

        return $this->success(null, 'Event created successfully.');
    }

    public function update(Event $event, Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'date' => ['required', 'date'],
            'featured_image' => ['nullable', 'mimes:jpg,jpeg,png', 'max:5120'],
            'images' => ['array', 'max:4'],
            'images.*' => ['required', 'mimes:jpg,jpeg,png', 'max:5120'],
            'access' => ['required', 'in:free,paid'],
            'type' => ['required', 'in:physical,online'],
            'category_id' => ['required', 'exists:event_categories,id'],
            'what_to_expect' => ['array'],
            'what_to_expect.*' => ['required', 'string', 'max:191'],
            'who_to_expect' => ['array'],
            'who_to_expect.*' => ['required', 'string', 'max:191'],
            'agendas' => ['array'],
            'agendas.*' => ['required', 'string', 'max:191'],
            'facilitators' => ['array'],
            'facilitators.*.name' => ['required', 'string', 'max:80'],
            'facilitators.*.role' => ['required', 'string', 'max:80'],
            'facilitators.*.company' => ['required', 'string', 'max:80'],
            'facilitators.*.description' => ['required', 'string', 'max:80'],
            'tickets' => ['required', 'array', 'min:1'],
            'tickets.*.id' => ['string', 'max:191'],
            'tickets.*.name' => ['required', 'string', 'max:191'],
            'tickets.*.price' => ['array', 'min:2', 'max:2'],
            'tickets.*.price.0.currency' => ['required', 'string', 'in:NGN'],
            'tickets.*.price.0.amount' => ['required', 'numeric:', 'min:0'],
            'tickets.*.price.1.currency' => ['required', 'string', 'in:USD'],
            'tickets.*.price.1.amount' => ['required', 'numeric:', 'min:0'],
            'tickets.*.features' => ['array'],
            'tickets.*.features.*' => ['required', 'string', 'max:191'],
        ]);

        $whatToExpect = json_encode($data['what_to_expect'] ?? []);
        $whoToExpect = json_encode($data['who_to_expect'] ?? []);
        $agendas = json_encode($data['agendas'] ?? []);
        $facilitators = json_encode($data['facilitators'] ?? []);
        $data['what_to_expect'] = $whatToExpect;
        $data['who_to_expect'] = $whoToExpect;
        $data['agendas'] = $agendas;
        $data['facilitators'] = $facilitators;

        $featuredImage = $request->file('featured_image') ?
            $this->uploadFile($request->file('featured_image'), 'events') :
            $event->featured_image;
        $data['featured_image'] = $featuredImage;

        $images = $request->file('images') ?
            $this->uploadFiles($request->file('images'), 'events') :
            [];
        $newImages = array_merge(json_decode($event->images), $images);
        $data['images'] = $newImages;

        $tickets = array_map(function ($ticket) use ($event) {
            return [
                'id' => $ticket['id'] ?? null,
                'name' => $ticket['name'],
                'price' => json_encode($ticket['price']),
                'features' => json_encode($ticket['features'] ?? []),
            ];
        }, $data['tickets'] ?? []);

        foreach ($tickets as $ticket) {
            $event->tickets()->updateOrCreate(['id' => $ticket['id']], [
                'name' => $ticket['name'],
                'price' => $ticket['price'],
                'features' => $ticket['features']
            ]);
        }

        $event->update($data);
        return $this->success(null, 'Event updated successfully.');

    }

    public function destroyTicket(EventTicket $ticket)
    {
        $ticket->delete();
        return $this->success(null, 'Ticket deleted successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return $this->success(null, 'Event deleted successfully.');
    }

    public function deleteImage(Event $event, Request $request)
    {
        $data = $request->validate([
            'index' => ['required', 'integer'],
        ]);

        $index = $data['index'];
        $images = json_decode($event->images, true);
        unset($images[$index]);
        $images = array_values($images);
        $event->images = json_encode($images);
        $event->save();
        return $this->success(null, 'Image deleted successfully.');
    }
}
