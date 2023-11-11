<?php

namespace App\Http\Controllers;

use App\Events\TicketCreated;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Department;
use App\Models\FrontPage;
use App\Models\Role;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\Type;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class HomeController extends Controller
{

    public function index(){
        return Inertia::render('Landing/Home', [
            'title' => 'Home - I-Helpinc',
            'page' => FrontPage::where('slug', 'home')->first(),
//            'footer' => FrontPage::where('slug', 'footer')->first(),
            'departments' => Department::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'categories' => Category::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'types' => Type::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
        ]);
    }

    public function ticketOpen(){
        $home = FrontPage::where('slug', 'home')->first();
        if(!empty($home) && !empty($home->html)){
            $home = json_decode($home->html, true);
            if(isset($home['sections']) && isset($home['sections'][2]) && isset($home['sections'][2]['login_require_create_ticket']) && $home['sections'][2]['login_require_create_ticket'] && !Auth::check()){
                return Redirect::route('login');
            }
        };
        return Inertia::render('Landing/OpenTicket', [
            'footer' => FrontPage::where('slug', 'footer')->first(),
            'title' => 'Open Ticket - Helpdesk',
            'departments' => Department::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'categories' => Category::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'types' => Type::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
        ]);
    }

    public function ticketPublicStore() {
        $ticket_data = Request::validate([
            'first_name' => ['required', 'max:40'],
            'last_name' => ['required', 'max:40'],
            'subject' => ['required'],
            'department_id' => ['required', Rule::exists('departments', 'id')],
            'category_id' => ['required', Rule::exists('categories', 'id')],
            'type_id' => ['required', Rule::exists('types', 'id')],
            'email' => ['required', 'max:60', 'email'],
            'details' => ['required'],
        ]);

        $user = User::where('email', $ticket_data['email'])->first();

        if(empty($user)){
            $plain_password = $this->genRendomPassword();
            $customerRole = Role::where('slug', 'customer')->first();
            $user = User::create([
                'first_name' => $ticket_data['first_name'],
                'last_name' => $ticket_data['last_name'],
                'email' => $ticket_data['email'],
                'role_id' => $customerRole ? $customerRole->id : null,
                'password' => $plain_password,
            ]);
        }

        $ticket = Ticket::create([
            'subject' => $ticket_data['subject'],
            'details' => $ticket_data['details'],
            'department_id' => $ticket_data['department_id'],
            'category_id' => $ticket_data['category_id'],
            'type_id' => $ticket_data['type_id'],
            'user_id' => $user->id,
        ]);
        $ticket->uid = floor(rand(500,999)*10000) + $ticket->id;
        $ticket->save();

        if(Request::hasFile('files')){
            $files = Request::file('files');
            foreach($files as $file){
                $file_path = $file->store('tickets', ['disk' => 'file_uploads']);
                Attachment::create(['ticket_id' => $ticket->id, 'name' => $file->getClientOriginalName(), 'size' => $file->getSize(), 'path' => $file_path]);
            }
        }

        $variables = [
            'name' => $user->first_name,
            'email' => $user->email,
            'password' => $plain_password??null,
            'login_url' => URL::to('login'),
            'sender_name' => config('mail.from.name', 'support@web.com'),
            'ticket_id' => $ticket->id,
            'uid' => $ticket->uid,
            'subject' => $ticket->subject,
        ];
        event(new TicketCreated($variables));

        return Redirect::route('ticket_open')->with('success', 'El ticket ha sido enviado, recibiremos un mensaje nuestro para realizar un seguimiento de la actualización del ticket. Por favor revise la carpeta de spam y asegúrese de haber recibido nuestro correo. ');
    }

    private function genRendomPassword() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 13; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}
