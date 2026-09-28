<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\CarrierDocumentsSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class CarrierVerificationController extends Controller
{
    /**
     * Upload carrier verification documents (Carte Grise, Permis, CIN).
     */
    public function upload(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'carrier' && $user->role !== 'admin') {
            abort(403, __('Only carrier accounts can submit verification documents.'));
        }

        $request->validate([
            'carte_grise' => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:10240',
            'permis' => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:10240',
            'cin' => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:10240',
            'vehicle_plate' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ], [
            'carte_grise.required' => __('Veuillez télécharger la carte grise du véhicule (البطاقة الرمادية).'),
            'permis.required' => __('Veuillez télécharger votre permis de conduire (رخصة السياقة).'),
            'cin.required' => __('Veuillez télécharger votre carte d\'identité nationale (بطاقة التعريف الوطنية).'),
            '*.max' => __('La taille maximale autorisée par fichier est de 10 Mo.'),
        ]);

        $folder = 'carrier_verifications/' . $user->id;

        // Store files
        $carteGrisePath = $request->file('carte_grise')->store($folder, 'public');
        $permisPath = $request->file('permis')->store($folder, 'public');
        $cinPath = $request->file('cin')->store($folder, 'public');

        $meta = $user->meta_data ?? [];
        $meta['carrier_verification'] = [
            'carte_grise' => $carteGrisePath,
            'permis' => $permisPath,
            'cin' => $cinPath,
            'vehicle_plate' => $request->input('vehicle_plate'),
            'notes' => $request->input('notes'),
            'status' => 'pending',
            'submitted_at' => now()->toIso8601String(),
        ];

        $user->meta_data = $meta;
        $user->save();

        // Notify admins
        try {
            $admins = User::where('role', 'admin')->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new CarrierDocumentsSubmittedNotification($user));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to notify admins of carrier documents: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', __('تم رفع وثائق التحقق بنجاح! سيقوم فريق إدارة المنصة بفحصها وتفعيل شارة التوثيق في أقرب وقت.'));
    }
}
