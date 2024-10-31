<?php

namespace MsoSpq;

use MsoUps\MsoUps;
use MsoFedex\MsoFedex;

class MsoSpq
{
    static public function mso_init()
    {
        return array_merge(MsoUps::mso_init(), MsoFedex::mso_init());
    }
}