# Admin Dashboard Access Guide

## Quick Access

### Option 1: Use Seeded Admin Account

If you've run the database seeder, an admin account is already created:

**Email:** `admin@olive.local`  
**Password:** `password`

**Access URL:** `http://localhost:8000/admin/dashboard`

---

### Option 2: Make Any Existing User an Admin

Run this command in terminal to make any user an admin:

```bash
php artisan tinker
```

Then run one of these commands:

```php
# Make a user admin by email
$user = User::where('email', 'your-email@example.com')->first();
$user->role = 'admin';
$user->save();

# Or in one line:
User::where('email', 'your-email@example.com')->update(['role' => 'admin']);
```

---

### Option 3: Create a New Admin User

Run this in terminal:

```bash
php artisan tinker
```

Then run:

```php
User::create([
    'name' => 'Super Admin',
    'email' => 'superadmin@example.com',
    'password' => Hash::make('your-password-here'),
    'role' => 'admin',
    'email_verified_at' => now(),
    'phone' => '12345678',
    'locale' => 'ar'
]);
```

---

### Option 4: Run Database Seeder

If you haven't seeded the database yet:

```bash
php artisan db:seed
```

This will create:
- Admin user: `admin@olive.local` / `password`
- Sample users for all roles
- Sample listings and products

---

## Admin Dashboard Features

Once logged in, you can access:

### 📊 **Dashboard** (`/admin/dashboard`)
- View platform statistics (total users, listings, active/pending)
- See weekly growth metrics
- Monitor users by role
- Quick access to manage users and listings
- View and approve pending listings
- See recent users and listings

### 👥 **User Management** (`/admin/users`)
- Filter users by role (Farmer, Carrier, Mill, Packer, etc.)
- Search users by name, email, or phone
- View user profiles
- Ban users (deactivates account and listings)
- Delete users and all their listings
- Pagination for large user lists

### 📝 **Listing Management** (`/admin/listings`)
- Filter listings by status (Active, Pending, Inactive, Sold)
- Search by product variety or seller name
- Approve pending listings
- Reject listings (sets to inactive)
- Delete listings permanently
- Beautiful card-based grid layout

---

## Authorization

- Only users with `role = 'admin'` can access admin routes
- All admin routes are protected with authentication
- Unauthorized access returns 403 Forbidden error

---

## Troubleshooting

### Can't log in?
1. Make sure your email is verified: `email_verified_at` must not be null
2. Check your role: Must be exactly `'admin'`
3. Clear caches: `php artisan cache:clear`

### Admin routes not working?
```bash
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

### Check if admin user exists:
```bash
php artisan tinker --execute="User::where('role', 'admin')->get(['name', 'email', 'role'])"
```

---

## Security Notes

⚠️ **Important:**
- Change default passwords in production
- Use strong passwords for admin accounts
- Limit admin access to trusted users only
- Consider adding 2FA for admin accounts
- Monitor admin actions through logs

---

## Quick Command Reference

```bash
# Check admin users
php artisan tinker --execute="User::where('role', 'admin')->count()"

# Make user admin
php artisan tinker --execute="User::where('email', 'user@example.com')->update(['role' => 'admin'])"

# Remove admin role
php artisan tinker --execute="User::where('email', 'user@example.com')->update(['role' => 'farmer'])"

# Clear all caches
php artisan optimize:clear
```

---

**Created:** October 2025  
**Admin System Version:** 1.0
