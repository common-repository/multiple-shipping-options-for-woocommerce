<?php

namespace MsoLfq;

use MsoUpsFreight\MsoUpsFreight;
use MsoFedexFreight\MsoFedexFreight;

class MsoLfq
{
    static public function mso_init()
    {
        return array_merge(MsoUpsFreight::mso_init(),MsoFedexFreight::mso_init());
    }
}