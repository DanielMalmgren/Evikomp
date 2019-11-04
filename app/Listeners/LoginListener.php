<?php

namespace App\Listeners;

use Aacotroneo\Saml2\Events\Saml2LoginEvent;
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
        //$messageId = $event->getSaml2Auth()->getLastMessageId();
        //TODO: Logga message-id och förhindra att det återanvänds för att förhindra replay attacks
        logger(print_r($event->getSaml2User(), true));
        $samluser = $event->getSaml2User();
        $userattr = $samluser->getAttributes();
        $personnr = $userattr["urn:oid:1.3.6.1.4.1.2428.90.1.5"][0];
        $firstname = mb_convert_case($userattr["urn:oid:2.5.4.42"][0], MB_CASE_TITLE, "UTF-8");
        $lastname = mb_convert_case($userattr["urn:oid:2.5.4.4"][0], MB_CASE_TITLE, "UTF-8");
        if(isset($userattr["urn:oid:2.5.4.10"])) {
            session(['authnissuer' => $userattr["urn:oid:2.5.4.10"][0]]);
        } else {
            session(['authnissuer' => "Verisec Freja eID AB"]); //TODO: Remove this! It is a HIGHLY temporary because our IdP provider doesn't always sent this attribute
        }

        logger("SAML Personnr: ".$personnr);
        logger("SAML Förnamn: ".$firstname);
        logger("SAML Efternamn: ".$lastname);
        logger("SAML Utfärdare: ".session('authnissuer'));

        $user = User::where('personid', $personnr)->first();
        if(empty($user)) {
            logger("Ny användare!");
            $user = new User();
            if(isset($userattr["urn:oid:0.9.2342.19200300.100.1.3"])) {
                $user->email = $userattr["urn:oid:0.9.2342.19200300.100.1.3"][0];
                logger("SAML Mailadress: ".$userattr["urn:oid:0.9.2342.19200300.100.1.3"][0]);
            }
            $user->personid = $personnr;
            if(str_word_count_utf8($firstname) === 1) {
                $user->firstname = $firstname;
            } else {
                $user->firstname = str_word_count_utf8($firstname, 1)[0];
            }

            $user->name = $firstname." ".$lastname;
        }

        $user->saml_firstname = $firstname;
        $user->lastname = $lastname;
        $user->save();

        //Behövs senare för SLO
        session(['sessionIndex' => $samluser->getSessionIndex()]);
        session(['nameId' => $samluser->getNameId()]);

        \Auth::login($user);
    }
}
