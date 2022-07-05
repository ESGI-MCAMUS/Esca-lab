<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UtilsController extends AbstractController
{
    // Generate OTP code
    public function generateOTP($n = 6)
    {
        $generator = '1357902468';
        $result = '';

        for ($i = 1; $i <= $n; $i++) {
            $result .= substr($generator, rand() % strlen($generator), 1);
        }
        return $result;
    }
}
