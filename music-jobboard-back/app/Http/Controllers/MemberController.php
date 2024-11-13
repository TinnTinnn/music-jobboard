<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Member::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields= $request->validate([
            'name' => 'required|max:255|unique:members,name',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($request->hasFile('profile_picture')) {
            // อัปโหลดไฟล์และเก็บ URL
            try {
                // อัพโหลดและเก็บ Url
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $fields['profile_picture'] = asset('storage/' . $path);
                Log::info('Profile Picture Path', ['profile_picture' => $fields['profile_picture']]);

                Log::info('Fields after profile picture upload', ['data' => $fields]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload profile picture',
                    'error' => $e->getMessage()
                ], 500);
            }
        } else {
            // หากไม่มีการอัปโหลด ให้ตั้งค่าเป้น null
            $fields['profile_picture'] = null;
        }

       $member = Member::create($fields);


       return $member;
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        return $member;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        $fields= $request->validate([
            'name' => 'required|max:255|unique:members,name',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $member->update($fields);

        return $member;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        $member->delete();
        return ['message'=>'Member has been deleted'];
    }
}
