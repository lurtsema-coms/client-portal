<?php
return [
  'deliverable_statuses' => [
    'PENDING',
    'CLIENT REVIEW',
    'SENT VIA EMAIL',
    'IN-PROGRESS',
    'COMPLETED',
    'POSTED',
    'SCHEDULED',
    'ON-HOLD',
    'REPORTED',
  ],
  'mail_to_address' => env('MAIL_TO_ADDRESS') ? explode(',', env('MAIL_TO_ADDRESS')) : []
];
