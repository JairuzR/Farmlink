<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Show all pending farmers
    public function pendingFarmers()
    {
        $farmers = User::where('role', 'farmer')
                       ->where('is_approved', false)
                       ->latest()
                       ->get();

        return view('admin.pending-farmers', compact('farmers'));
    }

    // Approve a farmer
    public function approveFarmer(User $user)
    {
        $user->update(['is_approved' => true]);

        // Optional: notify the farmer by email
        // Mail::to($user)->send(new FarmerApproved($user));

        return back()->with('success', "Farmer {$user->name} has been approved.");
    }

    // Reject a farmer (deletes their account)
    public function rejectFarmer(User $user)
    {
        $name = $user->name;
        $user->delete();

        return back()->with('success', "Farmer {$name} has been rejected and removed.");
    }
}