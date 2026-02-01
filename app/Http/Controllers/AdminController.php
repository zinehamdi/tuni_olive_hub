<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Listing;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Admin Controller - متحكم لوحة الإدارة
 * 
 * Handles all admin panel functionality including user management,
 * listing moderation, and statistics
 * يدير جميع وظائف لوحة الإدارة بما في ذلك إدارة المستخدمين
 * ومراجعة العروض والإحصائيات
 * 
 * @package App\Http\Controllers
 */
class AdminController extends Controller
{
    /**
     * Display the admin dashboard
     * عرض لوحة تحكم المدير
     * 
     * Shows statistics, recent users, and pending listings
     * يعرض الإحصائيات والمستخدمين الجدد والعروض المعلقة
     * 
     * @return \Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException  إذا لم يكن المستخدم مديراً
     */
    public function index()
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        // Get statistics
        $stats = [
            'total_users' => User::count(),
            'total_listings' => Listing::count(),
            'active_listings' => Listing::where('status', 'active')->count(),
            'pending_listings' => Listing::where('status', 'pending')->count(),
            'total_products' => Product::count(),
            
            // Users by role
            'farmers' => User::where('role', 'farmer')->count(),
            'carriers' => User::where('role', 'carrier')->count(),
            'mills' => User::where('role', 'mill')->count(),
            'packers' => User::where('role', 'packer')->count(),
            'normal_users' => User::where('role', 'normal')->count(),
            
            // Recent registrations (last 7 days)
            'new_users_week' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'new_listings_week' => Listing::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        // Get recent users
        $recentUsers = User::with('addresses')
            ->latest()
            ->limit(10)
            ->get();

        // Get recent listings
        $recentListings = Listing::with(['product', 'seller'])
            ->latest()
            ->limit(10)
            ->get();

        // Get pending listings
        $pendingListings = Listing::with(['product', 'seller'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentListings', 'pendingListings'));
    }

    /**
     * Display all users for moderation
     * عرض جميع المستخدمين للمراجعة
     * 
     * Supports filtering by role and searching by name/email/phone
     * يدعم الفلترة حسب الدور والبحث بالاسم/البريد/الهاتف
     * 
     * @param  \Illuminate\Http\Request  $request  طلب يحتوي على معايير البحث والفلترة
     * @return \Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException  إذا لم يكن المستخدم مديراً
     */
    public function users(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $query = User::with('addresses');

        // Filter by role
        if ($request->has('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function editUser(User $user)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        return view('admin.user_edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'show_contact_info' => ['sometimes', 'boolean'],
            'show_address' => ['sometimes', 'boolean'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;
        $user->show_contact_info = $request->boolean('show_contact_info');
        $user->show_address = $request->boolean('show_address');
        $user->save();

        return redirect()->route('admin.users.edit', $user)->with('status', __('User updated successfully'));
    }

    /**
     * Display all listings for moderation
     * عرض جميع العروض للمراجعة
     * 
     * Supports filtering by status and searching by product/seller
     * يدعم الفلترة حسب الحالة والبحث بالمنتج/البائع
     * 
     * @param  \Illuminate\Http\Request  $request  طلب يحتوي على معايير البحث والفلترة
     * @return \Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException  إذا لم يكن المستخدم مديراً
     */
    public function listings(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $query = Listing::with(['product', 'seller']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('variety', 'like', '%' . $request->search . '%');
            })->orWhereHas('seller', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $listings = $query->latest()->paginate(20);

        return view('admin.listings', compact('listings'));
    }

    public function editListing(Listing $listing)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $listing->load(['product', 'seller']);

        return view('admin.listing_edit', [
            'listing' => $listing,
            'product' => $listing->product,
            'seller' => $listing->seller,
        ]);
    }

    public function updateListing(Request $request, Listing $listing)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $listing->load('product');
        $unit = $listing->product->type === 'oil' ? 'liter' : 'kg';

        $data = $request->validate([
            'variety' => ['required', 'string', 'max:64'],
            'quality' => ['nullable', 'string', 'max:64'],
            'is_organic' => ['sometimes', 'boolean'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:8'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', Rule::in(['kg','liter'])],
            'min_order' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', Rule::in(['active','pending','inactive','sold','expired'])],
            'weight_kg' => ['nullable', 'numeric', 'min:0'],
            'volume_liters' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'numeric', 'min:0'],
            'media' => ['nullable', 'string'],
        ]);

        $mediaArray = null;
        if (!empty($data['media'])) {
            $mediaArray = collect(explode(',', $data['media']))
                ->map(fn($v) => trim($v))
                ->filter()
                ->values()
                ->all();
        }

        $product = $listing->product;
        $product->variety = $data['variety'];
        $product->quality = $data['quality'] ?? null;
        $product->is_organic = $request->boolean('is_organic');
        $product->price = $data['price'];
        $product->stock = $data['stock'] ?? ($data['quantity'] ?? $product->stock);
        $product->weight_kg = $data['weight_kg'] ?? null;
        $product->volume_liters = $data['volume_liters'] ?? null;
        $product->save();

        $listing->update([
            'price' => $data['price'],
            'currency' => $data['currency'] ?? $listing->currency,
            'quantity' => $data['quantity'] ?? $listing->quantity,
            // Force unit based on product type to prevent mismatches
            'unit' => $unit,
            'min_order' => $data['min_order'] ?? $listing->min_order,
            'status' => $data['status'],
            'media' => $mediaArray ?? $listing->media,
        ]);

        return redirect()->route('admin.listings')->with('success', __('Listing updated successfully'));
    }

    /**
     * Approve a listing
     * الموافقة على عرض
     * 
     * Changes listing status from pending to active
     * يغير حالة العرض من معلق إلى نشط
     * 
     * @param  \App\Models\Listing  $listing  العرض المراد الموافقة عليه
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException  إذا لم يكن المستخدم مديراً
     */
    public function approveListing(Listing $listing)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $listing->update(['status' => 'active']);

        return redirect()->back()->with('success', __('Listing approved successfully'));
    }

    /**
     * Reject a listing
     * رفض عرض
     * 
     * Changes listing status to inactive
     * يغير حالة العرض إلى غير نشط
     * 
     * @param  \App\Models\Listing  $listing  العرض المراد رفضه
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException  إذا لم يكن المستخدم مديراً
     */
    public function rejectListing(Listing $listing)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $listing->update(['status' => 'inactive']);

        return redirect()->back()->with('success', __('Listing rejected'));
    }

    /**
     * Delete a listing permanently
     * حذف عرض نهائياً
     * 
     * @param  \App\Models\Listing  $listing  العرض المراد حذفه
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException  إذا لم يكن المستخدم مديراً
     */
    public function deleteListing(Listing $listing)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $listing->delete();

        return redirect()->back()->with('success', __('Listing deleted successfully'));
    }

    /**
     * Ban a user and deactivate all their listings
     * حظر مستخدم وإلغاء تفعيل جميع عروضه
     * 
     * Sets email_verified_at to null as a soft ban
     * يضبط email_verified_at إلى null كحظر مؤقت
     * 
     * @param  \App\Models\User  $user  المستخدم المراد حظره
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException  إذا لم يكن المستخدم مديراً
     */
    public function banUser(User $user)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        // Deactivate all user's listings
        Listing::where('seller_id', $user->id)->update(['status' => 'inactive']);

        // You can add a 'banned' field to users table if needed
        // For now, we'll just mark their email as verified = null as a soft ban
        $user->update(['email_verified_at' => null]);

        return redirect()->back()->with('success', __('User banned successfully'));
    }

    /**
     * Delete a user permanently along with their listings
     * حذف مستخدم نهائياً مع جميع عروضه
     * 
     * @param  \App\Models\User  $user  المستخدم المراد حذفه
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException  إذا لم يكن المستخدم مديراً
     */
    public function deleteUser(User $user)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        // Delete user's listings
        Listing::where('seller_id', $user->id)->delete();

        // Delete user
        $user->delete();

        return redirect()->back()->with('success', __('User deleted successfully'));
    }
}
