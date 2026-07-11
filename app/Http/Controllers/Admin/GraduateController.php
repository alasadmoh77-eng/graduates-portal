<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class GraduateController extends Controller
{
    public function show(User $graduate): View
    {
        abort_unless($graduate->role === 'graduate', 404);

        $graduate->load([
            'graduate.major',
            'documentRequests.documentType'
        ]);

        return view('admin.graduates.show', compact('graduate'));
    }
}
