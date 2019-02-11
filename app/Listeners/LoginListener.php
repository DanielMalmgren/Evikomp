<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Aacotroneo\Saml2\Events\Saml2LoginEvent;
//use Illuminate\Support\Facades\Auth;
use App\User;

class LoginListener
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
     * @param  object  $event
     * @return void
     */
    public function handle(Saml2LoginEvent $event)
    {
        $messageId = $event->getSaml2Auth()->getLastMessageId();
        //TODO: Logga message-id och förhindra att det återanvänds för att förhindra replay attacks
        //logger(print_r($event->getSaml2User(), true));
        $samluser = $event->getSaml2User();
        $userattr = $samluser->getAttributes();
        $personnr = $userattr["urn:oid:1.3.6.1.4.1.2428.90.1.5"][0];
        $firstname = ucwords(strtolower($userattr["urn:oid:2.5.4.42"][0]));
        $lastname = ucwords(strtolower($userattr["urn:oid:2.5.4.4"][0]));

        logger("SAML Personnr: ".$personnr);
        logger("SAML Förnamn: ".$firstname);
        logger("SAML Efternamn: ".$lastname);

        $user = User::where('personid', $personnr)->first();
        if(empty($user)) {
            logger("Ny användare!");
            $user = new User;
            if(isset($userattr["urn:oid:0.9.2342.19200300.100.1.3"])) {
                $user->email = $userattr["urn:oid:0.9.2342.19200300.100.1.3"][0];
                logger("SAML Mailadress: ".$userattr["urn:oid:0.9.2342.19200300.100.1.3"][0]);
            }
            $user->personid = $personnr;
        }
        $user->name = $firstname." ".$lastname;
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->save();

        //Behövs senare för SLO
        session(['sessionIndex' => $samluser->getSessionIndex()]);
        session(['nameId' => $samluser->getNameId()]);

        \Auth::login($user);
    }
}
