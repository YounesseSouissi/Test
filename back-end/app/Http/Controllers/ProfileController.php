<?php

namespace App\Http\Controllers;

use App\Enums\Gender;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $credantials = $request->validate([
            'firstname' => ['required', 'string', 'max:50'],
            'lastname' => ['required', 'string', 'max:50'],
            'date_of_birth' => ['required', 'date'],
            'gender' => [new Enum(Gender::class)],
            'phone' => ['required', 'regex:/^0[67]\d{8}$/'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
        ]);
        $user->fill($credantials);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        return response()->json([
            'user' => $user,
            'message' => 'inforamtions updated'
        ]);
    }
    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'password updated'
        ]);
    }
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $oldPhotoPath = $request->user()->photo;

        // Store the new photo
        $photo = $request->file('photo')->store('photos', 'public');

        $request->user()->update(['photo' => $photo]);

        if ($oldPhotoPath) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        return response()->json([
            'user' => $request->user(),
            'message' => 'Photo updated'
        ]);
    }
}
