<?php

namespace MsoSpq;

use MsoDhl\MsoDhl;
use MsoUps\MsoUps;
use MsoFedex\MsoFedex;
use MsoUsps\MsoUsps;

class MsoSpq
{
    static public function mso_init()
    {
        return array_merge(MsoUps::mso_init(), MsoFedex::mso_init(), MsoUsps::mso_init(), MsoDhl::mso_init());
    }
}