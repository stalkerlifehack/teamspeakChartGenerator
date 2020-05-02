<?php
$cfg = [

  /*

    Server connection

  */
  'conn' => [

    # IP
    'ip_address' => 'X',

    # voice port
    'voice_port' => 9987,

    # query port
    'query_port' => 10011,

    # query passwd
    'query_passwd' => 'X',

    # query login
    'query_login' => 'serveradmin',

    # bot name (The bot must have in his name phrase 'qBot')
    'bot_name' => 'qBot @ chartGenerator',

    # Channel id where the bot will connect
    'bot_channel_id' => 1,
  ],

  'settings' => [

    'groups' => [

      # serverGroup => channelID
      7 => 6,
      8 => 6
    ],

    # Path where the bot will save charts
    'path' => '/Users/dani/Desktop',

    # Url to charts
    'url' => 'http://query.jutuby.net',

    # Channel description
    'desc' => '[center][chart][/center]'
  ],


  'background' => [

    # Background colour
    'hex_colour' => "#153147",
  ],

  'chart_lines' => [

    # Lines colour on the chart
    'colour' => '#ffffff'
  ],

  'scale' => [

    # Scale lines colour
    'lines_colour' => '#4f4f4f',

    # Scale text colour
    'text_colour' => '#ffffff',

    # Font
    'font' => 'fonts/Helvetica.ttc'

  ],

  'text' => [

    # Days of the week
    'days' => [
      'size' => 12,
      'colour' => '#ffffff',
      'font' => 'fonts/Helvetica.ttc',
      'days' => [
        1 => 'Mon.',
        2 => 'Tue.',
        3 => 'Wed.',
        4 => 'Thu.',
        5 => 'Fri.',
        6 => 'Sat.',
        7 => 'Sun.',
      ],
    ],


    'online' => [
      'size' => 10,
      'colour' => '#ffffff',
      'text' => 'All Online',
      'font' => 'fonts/Helvetica.ttc'
    ],

    'title' => [
      'size' => 12,
      'colour' => '#ffffff',
      'text' => 'Chart of activity',
      'font' => 'fonts/Helvetica.ttc'
    ],
  ]
];
 ?>
