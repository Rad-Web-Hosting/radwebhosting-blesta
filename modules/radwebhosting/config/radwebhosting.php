<?php
// Transfer fields
Configure::set('radwebhosting.transfer_fields',
[
    'domain' =>
    [
        'label' => Language::_('radwebhosting.transfer.domain', true),
        'type' => 'text'
    ],
    'EPPCode' =>
    [
        'label' => Language::_('radwebhosting.transfer.EPPCode', true),
        'type' => 'text'
    ]
]);

// Domain fields
Configure::set('radwebhosting.domain_fields',
[
    'domain' =>
    [
        'label' => Language::_('radwebhosting.domain.domain', true),
        'type' => 'text'
    ],
]);

// Nameserver fields
Configure::set('radwebhosting.nameserver_fields',
[
    'ns1' =>
    [
        'label' => Language::_('radwebhosting.nameserver.ns1', true),
        'type' => 'text'
    ],
    'ns2' =>
    [
        'label' => Language::_('radwebhosting.nameserver.ns2', true),
        'type' => 'text'
    ],
    'ns3' =>
    [
        'label' => Language::_('radwebhosting.nameserver.ns3', true),
        'type' => 'text'
    ],
    'ns4' =>
    [
        'label' => Language::_('radwebhosting.nameserver.ns4', true),
        'type' => 'text'
    ],
    'ns5' =>
    [
        'label' => Language::_('radwebhosting.nameserver.ns5', true),
        'type' => 'text'
    ]
]);

// Whois fields
Configure::set('radwebhosting.whois_fields',
[
    "registrant"    =>
    [
        'firstname' =>
        [
            'label' => Language::_('radwebhosting.whois.RegistrantFirstName', true),
            'type' => 'text'
        ],
        'lastname' =>
        [
            'label' => Language::_('radwebhosting.whois.RegistrantLastName', true),
            'type' => 'text'
        ],
        'fullname' =>
        [
            'label' => Language::_('radwebhosting.whois.RegistrantFullname', true),
            'type' => 'text'
        ],
        'companyname' =>
        [
            'label' => Language::_('radwebhosting.whois.RegistrantCompanyName', true),
            'type' => 'text'
        ],
        'email' =>
        [
            'label' => Language::_('radwebhosting.whois.RegistrantEmail', true),
            'type' => 'text'
        ],
        'address1' =>
        [
            'label' => Language::_('radwebhosting.whois.RegistrantAddress1', true),
            'type' => 'text'
        ],
        'address2' =>
        [
            'label' => Language::_('radwebhosting.whois.RegistrantAddress2', true),
            'type' => 'text'
        ],
        'city' =>
        [
            'label' => Language::_('radwebhosting.whois.RegistrantCity', true),
            'type' => 'text'
        ],
        'state' =>
        [
            'label' => Language::_('radwebhosting.whois.RegistrantState', true),
            'type' => 'text'
        ],
        'zipcode' =>
        [
            'label' => Language::_('radwebhosting.whois.RegistrantZipCode', true),
            'type' => 'text'
        ],
        'country' =>
        [
            'label' => Language::_('radwebhosting.whois.RegistrantCountry', true),
            'type' => 'text'
        ],
        'phone' =>
        [
            'label' => Language::_('radwebhosting.whois.RegistrantPhone', true),
            'type' => 'text'
        ],
    ],

    "tech"       =>
    [
        'firstname' =>
        [
            'label' => Language::_('radwebhosting.whois.TechFirstName', true),
            'type' => 'text'
        ],
        'lastname' =>
        [
            'label' => Language::_('radwebhosting.whois.TechLastName', true),
            'type' => 'text'
        ],
        'fullname' =>
        [
            'label' => Language::_('radwebhosting.whois.TechFullname', true),
            'type' => 'text'
        ],
        'companyname' =>
        [
            'label' => Language::_('radwebhosting.whois.TechCompanyName', true),
            'type' => 'text'
        ],
        'email' =>
        [
            'label' => Language::_('radwebhosting.whois.TechEmail', true),
            'type' => 'text'
        ],
        'address1' =>
        [
            'label' => Language::_('radwebhosting.whois.TechAddress1', true),
            'type' => 'text'
        ],
        'address2' =>
        [
            'label' => Language::_('radwebhosting.whois.TechAddress2', true),
            'type' => 'text'
        ],
        'city' =>
        [
            'label' => Language::_('radwebhosting.whois.TechCity', true),
            'type' => 'text'
        ],
        'state' =>
        [
            'label' => Language::_('radwebhosting.whois.TechState', true),
            'type' => 'text'
        ],
        'zipcode' =>
        [
            'label' => Language::_('radwebhosting.whois.TechZipCode', true),
            'type' => 'text'
        ],
        'country' =>
        [
            'label' => Language::_('radwebhosting.whois.TechCountry', true),
            'type' => 'text'
        ],
        'phone' =>
        [
            'label' => Language::_('radwebhosting.whois.TechPhone', true),
            'type' => 'text'
        ],
    ]
]
);