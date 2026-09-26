<?php

namespace App\Http\Controllers;

use App\Models\ClickLog;
use App\Models\LinkButton;
use App\Models\Page;
use Illuminate\Http\Request;

class PageViewController extends Controller
{
    public function index()
    {
        $page = Page::where('slug', 'home')->where('is_active', true)->with('activeButtons')->first()
            ?? Page::where('is_active', true)->orderBy('sort_order')->with('activeButtons')->firstOrFail();

        return view('frontend.page', compact('page'));
    }

    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->with('activeButtons')->firstOrFail();

        return view('frontend.page', compact('page'));
    }

    /** Redirect tombol + hitung klik (untuk analitik dashboard). */
    public function click(int $id, Request $request)
    {
        $button = LinkButton::with('page')->findOrFail($id);
        $button->increment('click_count');
        try {
            ClickLog::create([
                'link_button_id' => $button->id,
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'clicked_at' => now(),
            ]);
        } catch (\Throwable $e) {
        }

        $url = $button->url;
        // URL internal slug (mis. /admin-contact) tetap jalan
        return redirect()->away($url);
    }
}
