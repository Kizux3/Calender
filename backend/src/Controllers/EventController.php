<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Event;

class EventController extends BaseController
{
    public function index(Request $request, Response $response): Response
    {
        $userId = $request->getAttribute('user_id');
        $events = Event::where('user_id', $userId)->get();
        
        return $this->successResponse($response, $events);
    }

    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $userId = $request->getAttribute('user_id');

        // Validate required fields
        if (!isset($data['title']) || !isset($data['start_time']) || !isset($data['end_time'])) {
            return $this->errorResponse($response, 'Title, start time, and end time are required');
        }

        // Create event
        $event = new Event();
        $event->user_id = $userId;
        $event->title = $data['title'];
        $event->description = $data['description'] ?? '';
        $event->start_time = $data['start_time'];
        $event->end_time = $data['end_time'];
        $event->save();

        return $this->successResponse($response, $event, 'Event created successfully');
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $userId = $request->getAttribute('user_id');
        $event = Event::where('id', $args['id'])
                     ->where('user_id', $userId)
                     ->first();

        if (!$event) {
            return $this->errorResponse($response, 'Event not found', 404);
        }

        return $this->successResponse($response, $event);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $userId = $request->getAttribute('user_id');
        $event = Event::where('id', $args['id'])
                     ->where('user_id', $userId)
                     ->first();

        if (!$event) {
            return $this->errorResponse($response, 'Event not found', 404);
        }

        $data = $request->getParsedBody();

        // Update fields if provided
        if (isset($data['title'])) $event->title = $data['title'];
        if (isset($data['description'])) $event->description = $data['description'];
        if (isset($data['start_time'])) $event->start_time = $data['start_time'];
        if (isset($data['end_time'])) $event->end_time = $data['end_time'];

        $event->save();

        return $this->successResponse($response, $event, 'Event updated successfully');
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $userId = $request->getAttribute('user_id');
        $event = Event::where('id', $args['id'])
                     ->where('user_id', $userId)
                     ->first();

        if (!$event) {
            return $this->errorResponse($response, 'Event not found', 404);
        }

        $event->delete();

        return $this->successResponse($response, null, 'Event deleted successfully');
    }
} 