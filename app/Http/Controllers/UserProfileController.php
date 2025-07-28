<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function showByHandle(string $handle)
    {
        $user = User::where('handle', $handle)->firstOrFail();
        return $this->show($user);
    }

    public function showById(User $user)
    {
        return $this->show($user);
    }

    public function showByUsername(User $user)
    {
        return $this->show($user);
    }

    protected function show(User $user)
    {
        $breadcrumbs = [
            ['label' => __('strings.user_profile')],
            ['label' => $user->display_name],
        ];

        return view('profile', [
            'user' => $user,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}