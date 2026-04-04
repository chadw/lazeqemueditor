<?php

namespace App\Http\Controllers;

use App\Models\Mail;
use Illuminate\Http\Request;
use App\Http\Requests\MailRequest;

class MailController extends Controller
{
    public function index(Request $request)
    {
        $mails = Mail::with([
            'character' => function ($q) {
                $q->select('id', 'name');
            }
        ])
            ->orderBy('timestamp', 'desc')
            ->paginate(100);

        return view('mail.index', compact('mails'));
    }

    public function store(MailRequest $request)
    {
        $data = $request->validated();

        Mail::create($data);

        return back()->with('success', 'Mail created.');
    }

    public function update(MailRequest $request, Mail $mail)
    {
        $data = $request->validated();

        $mail->update($data);

        return back()->with('success', 'Mail updated.');
    }

    public function destroy(Mail $mail)
    {
        $mail->delete();

        return back()->with('success', 'Mail deleted.');
    }
}
