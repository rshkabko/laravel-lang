<?php

namespace Flamix\Lang\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class LangController extends Controller
{
    public function change(): View
    {
        return view('settings');
    }
}
