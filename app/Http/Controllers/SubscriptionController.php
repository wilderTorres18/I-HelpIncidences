<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

class SubscriptionController extends Controller {
    public function subscribe() {
        $request = Request::validate([
            'email' => ['required', 'email']
        ]);
        Contact::create($request);
        return Redirect::back()->with('success', 'Acabas de suscribirte para recibir las últimas noticias. ¡Gracias!');
    }
}
