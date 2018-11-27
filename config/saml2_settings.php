<?php

//This is variable is an example - Just make sure that the urls in the 'idp' config are ok.
$idp_host = env('SAML2_IDP_HOST', 'http://localhost:8000/simplesaml');

return $settings = array(

    /**
     * If 'useRoutes' is set to true, the package defines five new routes:
     *
     *    Method | URI                      | Name
     *    -------|--------------------------|------------------
     *    POST   | {routesPrefix}/acs       | saml_acs
     *    GET    | {routesPrefix}/login     | saml_login
     *    GET    | {routesPrefix}/logout    | saml_logout
     *    GET    | {routesPrefix}/metadata  | saml_metadata
     *    GET    | {routesPrefix}/sls       | saml_sls
     */
    'useRoutes' => true,

    'routesPrefix' => '/saml2',

    /**
     * which middleware group to use for the saml routes
     * Laravel 5.2 will need a group which includes StartSession
     */
    'routesMiddleware' => ['saml'],

    /**
     * Indicates how the parameters will be
     * retrieved from the sls request for signature validation
     */
    'retrieveParametersFromServer' => false,

    /**
     * Where to redirect after logout
     */
    'logoutRoute' => '/',

    /**
     * Where to redirect after login if no other option was provided
     */
    'loginRoute' => '/',


    /**
     * Where to redirect after login if no other option was provided
     */
    'errorRoute' => '/',




    /*****
     * One Login Settings
     */



    // If 'strict' is True, then the PHP Toolkit will reject unsigned
    // or unencrypted messages if it expects them signed or encrypted
    // Also will reject the messages if not strictly follow the SAML
    // standard: Destination, NameId, Conditions ... are validated too.
    'strict' => true, //@todo: make this depend on laravel config

    // Enable debug mode (to print errors)
    'debug' => env('APP_DEBUG', false),

    // If 'proxyVars' is True, then the Saml lib will trust proxy headers
    // e.g X-Forwarded-Proto / HTTP_X_FORWARDED_PROTO. This is useful if
    // your application is running behind a load balancer which terminates
    // SSL.
    'proxyVars' => false,

    // Service Provider Data that we are deploying
    'sp' => array(

        // Specifies constraints on the name identifier to be used to
        // represent the requested subject.
        // Take a look on lib/Saml2/Constants.php to see the NameIdFormat supported
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',

        // Usually x509cert and privateKey of the SP are provided by files placed at
        // the certs folder. But we can also provide them with the following parameters
        'x509cert' => env('SAML2_SP_x509',''),
        'privateKey' => env('SAML2_SP_PRIVATEKEY',''),

        // Identifier (URI) of the SP entity.
        // Leave blank to use the 'saml_metadata' route.
        'entityId' => env('SAML2_SP_ENTITYID',''),

        // Specifies info about where and how the <AuthnResponse> message MUST be
        // returned to the requester, in this case our SP.
        'assertionConsumerService' => array(
            // URL Location where the <Response> from the IdP will be returned,
            // using HTTP-POST binding.
            // Leave blank to use the 'saml_acs' route
            'url' => '',
        ),
        // Specifies info about where and how the <Logout Response> message MUST be
        // returned to the requester, in this case our SP.
        // Remove this part to not include any URL Location in the metadata.
        'singleLogoutService' => array(
            // URL Location where the <Response> from the IdP will be returned,
            // using HTTP-Redirect binding.
            // Leave blank to use the 'saml_sls' route
            'url' => '',
        ),
    ),

    // Identity Provider Data that we want connect with our SP
    'idp' => array(
        // Identifier of the IdP entity  (must be a URI)
        'entityId' => env('SAML2_IDP_ENTITYID', $idp_host . '/saml2/idp/metadata.php'),
        // SSO endpoint info of the IdP. (Authentication Request protocol)
        'singleSignOnService' => array(
            // URL Target of the IdP where the SP will send the Authentication Request Message,
            // using HTTP-Redirect binding.
            'url' => $idp_host . '/wa/auth/saml/',
        ),
        // SLO endpoint info of the IdP.
        'singleLogoutService' => array(
            // URL Location of the IdP where the SP will send the SLO Request,
            // using HTTP-Redirect binding.
            'url' => $idp_host . '/wa/logout',
        ),
        // Public x509 certificate of the IdP
        'x509cert' => env('SAML2_IDP_x509', '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'),
        /*
         *  Instead of use the whole x509cert you can use a fingerprint
         *  (openssl x509 -noout -fingerprint -in "idp.crt" to generate it)
         */
        // 'certFingerprint' => '',
    ),



    /***
     *
     *  OneLogin advanced settings
     *
     *
     */
    // Security settings
    'security' => array(

        /** signatures and encryptions offered */

        // Indicates that the nameID of the <samlp:logoutRequest> sent by this SP
        // will be encrypted.
        'nameIdEncrypted' => false,

        // Indicates whether the <samlp:AuthnRequest> messages sent by this SP
        // will be signed.              [The Metadata of the SP will offer this info]
        'authnRequestsSigned' => false,

        // Indicates whether the <samlp:logoutRequest> messages sent by this SP
        // will be signed.
        'logoutRequestSigned' => false,

        // Indicates whether the <samlp:logoutResponse> messages sent by this SP
        // will be signed.
        'logoutResponseSigned' => false,

        /* Sign the Metadata
         False || True (use sp certs) || array (
                                                    keyFileName => 'metadata.key',
                                                    certFileName => 'metadata.crt'
                                                )
        */
        'signMetadata' => false,


        /** signatures and encryptions required **/

        // Indicates a requirement for the <samlp:Response>, <samlp:LogoutRequest> and
        // <samlp:LogoutResponse> elements received by this SP to be signed.
        'wantMessagesSigned' => false,

        // Indicates a requirement for the <saml:Assertion> elements received by
        // this SP to be signed.        [The Metadata of the SP will offer this info]
        'wantAssertionsSigned' => false,

        // Indicates a requirement for the NameID received by
        // this SP to be encrypted.
        'wantNameIdEncrypted' => false,

        // Authentication context.
        // Set to false and no AuthContext will be sent in the AuthNRequest,
        // Set true or don't present thi parameter and you will get an AuthContext 'exact' 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport'
        // Set an array with the possible auth context values: array ('urn:oasis:names:tc:SAML:2.0:ac:classes:Password', 'urn:oasis:names:tc:SAML:2.0:ac:classes:X509'),
        'requestedAuthnContext' => true,
    ),

    // Contact information template, it is recommended to suply a technical and support contacts
    'contactPerson' => array(
        'technical' => array(
            'givenName' => 'DanielMalmgren',
            'emailAddress' => 'daniel.malmgren@itsam.se'
        ),
        'support' => array(
            'givenName' => 'Servicedesk',
            'emailAddress' => 'servicedesk@itsam.se'
        ),
    ),

    // Organization information template, the info in en_US lang is recomended, add more if required
    'organization' => array(
        'en-US' => array(
            'name' => 'Kommunalförbundet ITSAM',
            'displayname' => 'ITSAM',
            'url' => 'https://www.itsam.se'
        ),
    ),

/* Interoperable SAML 2.0 Web Browser SSO Profile [saml2int]   http://saml2int.org/profile/current

   'authnRequestsSigned' => false,    // SP SHOULD NOT sign the <samlp:AuthnRequest>,
                                      // MUST NOT assume that the IdP validates the sign
   'wantAssertionsSigned' => true,
   'wantAssertionsEncrypted' => true, // MUST be enabled if SSL/HTTPs is disabled
   'wantNameIdEncrypted' => false,
*/

);
