<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Twilio\Rest\Client;

Route::post('/test-twilio-whatsapp', function () {
    try {

        $twilio = new Client(
            env('TWILIO_SID'),
            env('TWILIO_AUTH_TOKEN')
        );

        $message = $twilio->messages->create(
            "whatsapp:+212635775634",
            [
                "from" => "whatsapp:" . env('TWILIO_WHATSAPP_NUMBER'),
                "body" => "This is a test WhatsApp message from Laravel using Twilio!"
            ]
        );
        
        return response()->json([
            'message' => 'WhatsApp message sent successfully!',
            'message_sid' => $message->sid
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'hint' => 'Make sure your Twilio credentials are correct and the recipient has joined your WhatsApp sandbox'
        ], 500);
    }
});