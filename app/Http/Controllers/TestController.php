<?php

namespace App\Http\Controllers;

use App\Models\Complaint;

class TestController extends Controller
{
    public function showImageUrl()
    {
        // Retrieve a sample complaint (replace `1` with an actual complaint ID)
        $complaint = Complaint::find(1);  // Replace with the ID you want to test

        // Check if the complaint exists and retrieve the media URL
        if ($complaint) {
            $imageUrl = $complaint->getFirstMediaUrl('complaint_images') ? url($complaint->getFirstMediaUrl('complaint_images')) : 'No image available';
        } else {
            $imageUrl = 'Complaint not found';
        }

        // Return a simple view with the image URL or message
        return view('test_image', ['imageUrl' => $imageUrl]);
    }
}
