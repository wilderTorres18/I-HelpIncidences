<?php

namespace App\Listeners;

use App\Events\TicketCreated;
use App\Mail\SendMailFromHtml;
use App\Mail\TicketCreatedMessage;
use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Models\Ticket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class TicketCreatedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\TicketCreated  $event
     * @return void
     */
    public function handle(TicketCreated $event) {
        $data = $event->data;
        $ticket = Ticket::where('id', $data['ticket_id'])->with(['user','ticketType'])->first();
        $notifications = app('App\HelpDesk')->getSettingsEmailNotifications();
        $ticket_slug = !empty($data['password']) ? 'create_ticket_new_customer' : 'create_ticket_dashboard';
        if(!empty($ticket) && $notifications[$ticket_slug]){
            $template = EmailTemplate::where('slug', $ticket_slug)->first();
            if(!empty($template)){
                $template = $template->html;
                $variables = [
                    'name' => $ticket->user->first_name,
                    'email' => $ticket->user->email,
                    'password' => $data['password'] ?? '',
                    'url' => config('app.url').'/dashboard/tickets/'.$ticket->uid,
                    'sender_name' => 'Manager',
                    'ticket_id' => $ticket->id,
                    'uid' => $ticket->uid,
                    'subject' => $ticket->subject,
                    'type' => $ticket->ticketType ? $ticket->ticketType->name: '',
                ];
                if (preg_match_all("/{(.*?)}/", $template, $m)) {
                    foreach ($m[1] as $i => $varname) {
                        $template = str_replace($m[0][$i], sprintf($variables[$m[1][$i]], $varname), $template);
                    }
                }
                $messageData = ['html' => $template, 'subject' => '[Ticket#'.$ticket->uid.'] - '.$ticket->subject];
                if(config('queue.enable')){
                    Mail::to($ticket->user->email)->queue(new SendMailFromHtml($messageData));
                }else{
                    Mail::to($ticket->user->email)->send(new SendMailFromHtml($messageData));
                }
            }
        }
    }
}
