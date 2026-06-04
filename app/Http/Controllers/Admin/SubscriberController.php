<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index()
    {
        $subscribers = \Illuminate\Support\Facades\DB::table('subscribers')
            ->select('id', 'type', 'contact_value', 'ip_address', 'created_at', \Illuminate\Support\Facades\DB::raw("'subscriber' as source"));

        $users = \Illuminate\Support\Facades\DB::table('users')
            ->select('id', \Illuminate\Support\Facades\DB::raw("'email' as type"), 'email as contact_value', \Illuminate\Support\Facades\DB::raw("NULL as ip_address"), 'created_at', \Illuminate\Support\Facades\DB::raw("'user' as source"));

        $usersWithPhone = \Illuminate\Support\Facades\DB::table('users')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->select('id', \Illuminate\Support\Facades\DB::raw("'whatsapp' as type"), 'phone as contact_value', \Illuminate\Support\Facades\DB::raw("NULL as ip_address"), 'created_at', \Illuminate\Support\Facades\DB::raw("'user' as source"));

        $contacts = $subscribers->union($users)->union($usersWithPhone)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.subscribers.index', compact('contacts'));
    }

    public function bulkMessage(Request $request)
    {
        $request->validate([
            'contacts' => 'required|array',
            'message_body' => 'required|string',
        ]);

        $subject = $request->input('subject', 'Zintoop Update');
        $body = $request->input('message_body');
        
        $emailQueue = 0;
        $waQueue = [];

        foreach ($request->contacts as $contactString) {
            // Expected format: source:type:contact_value
            $parts = explode(':', $contactString, 3);
            if (count($parts) === 3) {
                $type = $parts[1];
                $value = $parts[2];
                
                if ($type === 'email') {
                    // Filter out invalid emails just in case
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        \Illuminate\Support\Facades\Mail::to($value)->send(new \App\Mail\BulkSubscriberEmail($subject, $body));
                        $emailQueue++;
                    }
                } elseif ($type === 'whatsapp') {
                    // Convert markdown to WhatsApp friendly text
                    $waText = $body;
                    // Remove markdown images entirely or convert to links. Let's just remove image tags since they don't work in WA.
                    $waText = preg_replace('/!\[.*?\]\(.*?\)/', '', $waText);
                    // Convert Headers to WhatsApp Bold
                    $waText = preg_replace('/###\s+(.*)/', '*$1*', $waText);
                    $waText = preg_replace('/##\s+(.*)/', '*$1*', $waText);
                    $waText = preg_replace('/#\s+(.*)/', '*$1*', $waText);
                    // Convert standard bold **text** to WA bold *text*
                    $waText = preg_replace('/\*\*(.*?)\*\*/', '*$1*', $waText);
                    
                    $waQueue[] = [
                        'phone' => $value,
                        'message' => trim($waText)
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'emails_queued' => $emailQueue,
            'whatsapp_queue' => $waQueue
        ]);
    }
}
