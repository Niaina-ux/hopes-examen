<?php

use App\Models\Code;
use App\Models\Fichier;
use App\Models\GlisserDeposer;
use App\Models\ImageExercice;
use App\Models\MotsCroises;
use App\Models\Qcm;
use App\Models\Pointiller;
use App\Models\Redaction;
use App\Models\Relier;
use App\Models\Text;

// ampio ny Model hafa eto rehefa misy

return [
    'qcm' => [
        'model'      => Qcm::class,
        'order_by'   => 'ordre',
    ],
    'pointiller' => [
        'model'      => Pointiller::class,
        'order_by'   => 'ordre',
    ],
    'relier' => [
        'model'      => Relier::class, 
        'order_by'   => 'ordre',
    ],
    'code' => [
        'model'      => Code::class,
        'order_by'   => 'ordre',
    ],
    'fichier' => [
        'model'      => Fichier::class,
        'order_by'   => 'ordre',
    ],
    'text' => [
        'model'      => Text::class,
        'order_by'   => 'ordre',
    ],
    'redaction' => [
        'model'    => Redaction::class,
        'order_by' => 'ordre',
    ],
    'motscroises' => [
        'model'    => MotsCroises::class,
        'order_by' => 'ordre',
    ],
    'glisserdeposer' => [
        'model'       => GlisserDeposer::class,
        'order_by'    => 'ordre',
    ],
    'image' => [
        'model'       => ImageExercice::class,
        'order_by'    => 'ordre',
    ]
    // Ampio eto isaky ny misy type_exercice vaovao — tsy ilaina manova code Controller intsony
];