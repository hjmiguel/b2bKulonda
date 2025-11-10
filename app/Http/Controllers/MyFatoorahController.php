<?php

namespace App\Http\Controllers;

/**
 * Alias para App\Http\Controllers\Payment\MyFatoorahController
 * Criado para resolver problema de referência em rotas legadas
 */
class MyFatoorahController extends \App\Http\Controllers\Payment\MyFatoorahController
{
    // Este é um alias. Toda a lógica está em Payment\MyFatoorahController
}
