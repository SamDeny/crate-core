<?php declare(strict_types=1);

return [
  
    'default'   => 'sendmail',

    'drivers'   => [
        
        'sendmail' => [
            'driver'    => \Crate\Mailer\Mailer::class
        ]

    ]

];
