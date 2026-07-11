<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\BulkSubscriberEmail;

class SubscriberController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role', 'all');
        $type = $request->query('type', 'all');

        $subscribersQuery = null;
        $usersQuery = null;
        $usersWithPhoneQuery = null;

        $search = $request->query('search');

        // 1. Guest newsletter subscribers
        if (($role === 'all' || $role === 'subscriber') && ($type === 'all' || $type === 'email')) {
            $subscribersQuery = DB::table('subscribers')
                ->select(
                    'id',
                    'type',
                    'contact_value',
                    'ip_address',
                    'created_at',
                    DB::raw("'subscriber' as source"),
                    DB::raw("'subscriber' as role")
                );
            if ($type === 'email') {
                $subscribersQuery->where('type', 'email');
            }
            if ($search) {
                $subscribersQuery->where('contact_value', 'like', "%{$search}%");
            }
        }

        // 2. Registered users
        if ($role !== 'subscriber') {
            if ($role === 'has_listings') {
                // only users who have created a listing
                if ($type === 'all' || $type === 'email') {
                    $usersQuery = DB::table('users')
                        ->join('listings', 'users.id', '=', 'listings.seller_id')
                        ->select(
                            'users.id',
                            DB::raw("'email' as type"),
                            'users.email as contact_value',
                            DB::raw("NULL as ip_address"),
                            'users.created_at',
                            DB::raw("'user' as source"),
                            DB::raw("'has_listings' as role")
                        )
                        ->distinct();
                    
                    if ($search) {
                        $usersQuery->where(function($q) use ($search) {
                            $q->where('users.email', 'like', "%{$search}%")
                              ->orWhere('users.name', 'like', "%{$search}%");
                        });
                    }
                }

                if ($type === 'all' || $type === 'whatsapp') {
                    $usersWithPhoneQuery = DB::table('users')
                        ->join('listings', 'users.id', '=', 'listings.seller_id')
                        ->whereNotNull('users.phone')
                        ->where('users.phone', '!=', '')
                        ->select(
                            'users.id',
                            DB::raw("'whatsapp' as type"),
                            'users.phone as contact_value',
                            DB::raw("NULL as ip_address"),
                            'users.created_at',
                            DB::raw("'user' as source"),
                            DB::raw("'has_listings' as role")
                        )
                        ->distinct();

                    if ($search) {
                        $usersWithPhoneQuery->where(function($q) use ($search) {
                            $q->where('users.phone', 'like', "%{$search}%")
                              ->orWhere('users.name', 'like', "%{$search}%");
                        });
                    }
                }
            } else {
                // normal role filtering
                if ($type === 'all' || $type === 'email') {
                    $usersQuery = DB::table('users')
                        ->select(
                            'id',
                            DB::raw("'email' as type"),
                            'email as contact_value',
                            DB::raw("NULL as ip_address"),
                            'created_at',
                            DB::raw("'user' as source"),
                            'role'
                        );
                    if ($role !== 'all') {
                        $usersQuery->where('role', $role);
                    }
                    if ($search) {
                        $usersQuery->where(function($q) use ($search) {
                            $q->where('email', 'like', "%{$search}%")
                              ->orWhere('name', 'like', "%{$search}%");
                        });
                    }
                }

                if ($type === 'all' || $type === 'whatsapp') {
                    $usersWithPhoneQuery = DB::table('users')
                        ->whereNotNull('phone')
                        ->where('phone', '!=', '')
                        ->select(
                            'id',
                            DB::raw("'whatsapp' as type"),
                            'phone as contact_value',
                            DB::raw("NULL as ip_address"),
                            'created_at',
                            DB::raw("'user' as source"),
                            'role'
                        );
                    if ($role !== 'all') {
                        $usersWithPhoneQuery->where('role', $role);
                    }
                    if ($search) {
                        $usersWithPhoneQuery->where(function($q) use ($search) {
                            $q->where('phone', 'like', "%{$search}%")
                              ->orWhere('name', 'like', "%{$search}%");
                        });
                    }
                }
            }
        }

        // Combine queries
        $query = null;
        if ($subscribersQuery) {
            $query = $subscribersQuery;
        }

        if ($usersQuery) {
            $query = $query ? $query->union($usersQuery) : $usersQuery;
        }

        if ($usersWithPhoneQuery) {
            $query = $query ? $query->union($usersWithPhoneQuery) : $usersWithPhoneQuery;
        }

        // Count total contacts matching the filter across all pages
        $totalContacts = 0;
        if ($query) {
            $totalContacts = DB::query()->fromSub($query, 'union_table')->count();
            $contacts = $query->orderBy('created_at', 'desc')->paginate(50)->withQueryString();
        } else {
            $contacts = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 50);
        }

        return view('admin.subscribers.index', compact('contacts', 'role', 'type', 'totalContacts'));
    }

    public function bulkMessage(Request $request)
    {
        $request->validate([
            'message_body' => 'required|string',
        ]);

        $subject = $request->input('subject', 'Zintoop Update');
        $body = $request->input('message_body');
        $scope = $request->input('recipient_scope', 'selected');
        $role = $request->input('role_filter', 'all');
        $type = $request->input('type_filter', 'all');

        $contacts = [];

        if ($scope === 'all_filtered') {
            // Retrieve all contacts dynamically matching filter parameters
            $subscribersQuery = null;
            $usersQuery = null;
            $usersWithPhoneQuery = null;

            if (($role === 'all' || $role === 'subscriber') && ($type === 'all' || $type === 'email')) {
                $subscribersQuery = DB::table('subscribers')
                    ->select('type', 'contact_value', DB::raw("'subscriber' as source"));
                if ($type === 'email') {
                    $subscribersQuery->where('type', 'email');
                }
            }

            if ($role !== 'subscriber') {
                if ($role === 'has_listings') {
                    if ($type === 'all' || $type === 'email') {
                        $usersQuery = DB::table('users')
                            ->join('listings', 'users.id', '=', 'listings.seller_id')
                            ->select(DB::raw("'email' as type"), 'users.email as contact_value', DB::raw("'user' as source"))
                            ->distinct();
                    }

                    if ($type === 'all' || $type === 'whatsapp') {
                        $usersWithPhoneQuery = DB::table('users')
                            ->join('listings', 'users.id', '=', 'listings.seller_id')
                            ->whereNotNull('users.phone')
                            ->where('users.phone', '!=', '')
                            ->select(DB::raw("'whatsapp' as type"), 'users.phone as contact_value', DB::raw("'user' as source"))
                            ->distinct();
                    }
                } else {
                    if ($type === 'all' || $type === 'email') {
                        $usersQuery = DB::table('users')
                            ->select(DB::raw("'email' as type"), 'email as contact_value', DB::raw("'user' as source"));
                        if ($role !== 'all') {
                            $usersQuery->where('role', $role);
                        }
                    }

                    if ($type === 'all' || $type === 'whatsapp') {
                        $usersWithPhoneQuery = DB::table('users')
                            ->whereNotNull('phone')
                            ->where('phone', '!=', '')
                            ->select(DB::raw("'whatsapp' as type"), 'phone as contact_value', DB::raw("'user' as source"));
                        if ($role !== 'all') {
                            $usersWithPhoneQuery->where('role', $role);
                        }
                    }
                }
            }

            $query = null;
            if ($subscribersQuery) {
                $query = $subscribersQuery;
            }
            if ($usersQuery) {
                $query = $query ? $query->union($usersQuery) : $usersQuery;
            }
            if ($usersWithPhoneQuery) {
                $query = $query ? $query->union($usersWithPhoneQuery) : $usersWithPhoneQuery;
            }

            if ($query) {
                $dbContacts = $query->get();
                foreach ($dbContacts as $c) {
                    $contacts[] = "{$c->source}:{$c->type}:{$c->contact_value}";
                }
            }
        } else {
            $request->validate([
                'contacts' => 'required|array',
            ]);
            $contacts = $request->contacts;
        }

        $emailQueue = 0;
        $waQueue = [];

        foreach ($contacts as $contactString) {
            $parts = explode(':', $contactString, 3);
            if (count($parts) === 3) {
                $type = $parts[1];
                $value = $parts[2];
                
                if ($type === 'email') {
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        // Standard bulk mailer sending
                        Mail::to($value)->send(new BulkSubscriberEmail($subject, $body));
                        $emailQueue++;
                    }
                } elseif ($type === 'whatsapp') {
                    $waText = $body;
                    
                    // Convert simple HTML tags to WhatsApp formatting before stripping the rest
                    $waText = str_replace(['<strong>', '</strong>', '<b>', '</b>'], '*', $waText);
                    $waText = str_replace(['<em>', '</em>', '<i>', '</i>'], '_', $waText);
                    $waText = str_replace(['<p>', '</p>', '<br>', '<br/>', '<br />'], "\n", $waText);
                    $waText = strip_tags($waText);
                    $waText = html_entity_decode($waText);
                    
                    // Simple text cleanups for markdown formatting compat
                    $waText = preg_replace('/!\[(.*?)\]\((.*?)\)/', '$1: $2', $waText);
                    $waText = preg_replace('/\[(.*?)\]\((.*?)\)/', '$1: $2', $waText);
                    $waText = preg_replace('/###\s+(.*)/', '*$1*', $waText);
                    $waText = preg_replace('/##\s+(.*)/', '*$1*', $waText);
                    $waText = preg_replace('/#\s+(.*)/', '*$1*', $waText);
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

    public function uploadEmailImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // 10MB max
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('uploads/emails', 'public');
            $url = Storage::disk('public')->url($path);
            
            return response()->json(['url' => $url]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
