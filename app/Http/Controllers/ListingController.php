<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;
use Illuminate\Support\Facades\Redirect;

class ListingController extends Controller
{
	public function store(Request $request)
	{
		$data = $request->all();
		// Set status to active by default
		$data['status'] = 'active';
		$listing = Listing::create($data);
		// Redirect to a success page or listing view
		return Redirect::route('listings.create')->with('success', 'تم نشر العرض بنجاح!');
	}
}
