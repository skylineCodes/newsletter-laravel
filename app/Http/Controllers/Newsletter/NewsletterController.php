<?php

namespace App\Http\Controllers\Newsletter;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmail;
use App\Mail\SendNewsLetter;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class NewsletterController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function send_emails()
    {
        try {
            // Get emails from database
            $user = $this->user->where('email', 'onakoyak@gmail.com')->first();
            
            // Dispatch the queue
            SendEmail::dispatch($user->email, $user->name);

            $response = response()->json([
                'status' => 200,
                'message' => 'Email sent to subscribers'
            ], 200);
        } catch (Exception $e) {
            Log::emergency("File: " . $e->getFile() . PHP_EOL .
                "Line: " . $e->getLine() . PHP_EOL .
                "Message: " . $e->getMessage());

            $response = response()->json(['error' => $e->getMessage(), 'status' => 400], 400);
        }

        return $response;
    }

    public function subscribe(Request $request)
    {
        try {
             // Validate Input
            $validator = validator($request->all(), [
                'name' => 'required',
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {
                return $this->respondWithErrorMessage($validator);
            }

            $subscriber = $this->user->create([
                'name' => $request->name,
                'email' => $request->email,
                'is_subscribed' => true
            ]);

            $response = response()->json([
                'status' => 201,
                'data' => $subscriber
            ], 201);
        } catch (Exception $e) {
            Log::emergency("File: " . $e->getFile() . PHP_EOL .
                "Line: " . $e->getLine() . PHP_EOL .
                "Message: " . $e->getMessage());

            $response = response()->json(['error' => $e->getMessage(), 'status' => 400], 400);
        }

        return $response;
    }

    public function unsubscribe(Request $request)
    {
        try {
             // Validate Input
            $validator = validator($request->all(), [
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {
                return $this->respondWithErrorMessage($validator);
            }

            $subscriber = $this->user->where('email', $request->email)->update(['is_subscribed' => false]);

            $response = response()->json([
                'status' => 200,
                'message' => 'Unsubscribed successfully!'
            ], 200);
        } catch (Exception $e) {
            Log::emergency("File: " . $e->getFile() . PHP_EOL .
                "Line: " . $e->getLine() . PHP_EOL .
                "Message: " . $e->getMessage());

            $response = response()->json(['error' => $e->getMessage(), 'status' => 400], 400);
        }

        return $response;
    }
}
