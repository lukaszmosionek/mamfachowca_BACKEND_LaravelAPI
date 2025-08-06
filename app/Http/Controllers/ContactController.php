<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendMailRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    use ApiResponse;

    public function send(SendMailRequest $request)
    {
        Mail::to( config('mail.admin_email') )->send( new \App\Mail\ContactMail($request->all()) );

        // Or store the message in the database or log
        // \App\Models\ContactMessage::create($validated);

        return $this->success('Message received successfully!');
    }
}
