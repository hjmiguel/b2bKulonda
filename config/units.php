<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Unidades de Medida - HORECA (Portugal e Angola)
    |--------------------------------------------------------------------------
    |
    | TOP 20 Unidades mais utilizadas no setor HORECA
    | (Hotéis, Restaurantes e Catering)
    |
    */

    'units' => [
        // 1. Quilograma - Mais usada
        [
            'code' => 'kg',
            'name_pt' => 'Quilograma',
            'name_en' => 'Kilogram',
            'symbol' => 'kg',
            'category' => 'weight',
            'priority' => 1
        ],

        // 2. Litro - Líquidos
        [
            'code' => 'L',
            'name_pt' => 'Litro',
            'name_en' => 'Liter',
            'symbol' => 'L',
            'category' => 'volume',
            'priority' => 2
        ],

        // 3. Unidade - Contagem
        [
            'code' => 'un',
            'name_pt' => 'Unidade',
            'name_en' => 'Unit',
            'symbol' => 'un',
            'category' => 'quantity',
            'priority' => 3
        ],

        // 4. Caixa - Embalagens
        [
            'code' => 'cx',
            'name_pt' => 'Caixa',
            'name_en' => 'Box',
            'symbol' => 'cx',
            'category' => 'packaging',
            'priority' => 4
        ],

        // 5. Embalagem/Pacote
        [
            'code' => 'emb',
            'name_pt' => 'Embalagem',
            'name_en' => 'Package',
            'symbol' => 'emb',
            'category' => 'packaging',
            'priority' => 5
        ],

        // 6. Garrafa
        [
            'code' => 'grf',
            'name_pt' => 'Garrafa',
            'name_en' => 'Bottle',
            'symbol' => 'grf',
            'category' => 'packaging',
            'priority' => 6
        ],

        // 7. Dúzia
        [
            'code' => 'dz',
            'name_pt' => 'Dúzia',
            'name_en' => 'Dozen',
            'symbol' => 'dz',
            'category' => 'quantity',
            'priority' => 7
        ],

        // 8. Lata
        [
            'code' => 'lt',
            'name_pt' => 'Lata',
            'name_en' => 'Can/Tin',
            'symbol' => 'lt',
            'category' => 'packaging',
            'priority' => 8
        ],

        // 9. Saco
        [
            'code' => 'sc',
            'name_pt' => 'Saco',
            'name_en' => 'Bag',
            'symbol' => 'sc',
            'category' => 'packaging',
            'priority' => 9
        ],

        // 10. Bandeja
        [
            'code' => 'bdj',
            'name_pt' => 'Bandeja',
            'name_en' => 'Tray',
            'symbol' => 'bdj',
            'category' => 'packaging',
            'priority' => 10
        ],

        // 11. Engradado
        [
            'code' => 'eng',
            'name_pt' => 'Engradado',
            'name_en' => 'Crate',
            'symbol' => 'eng',
            'category' => 'packaging',
            'priority' => 11
        ],

        // 12. Fardo
        [
            'code' => 'frd',
            'name_pt' => 'Fardo',
            'name_en' => 'Bundle',
            'symbol' => 'frd',
            'category' => 'packaging',
            'priority' => 12
        ],

        // 13. Barril
        [
            'code' => 'bbl',
            'name_pt' => 'Barril',
            'name_en' => 'Keg/Barrel',
            'symbol' => 'bbl',
            'category' => 'volume',
            'priority' => 13
        ],

        // 14. Garrafão
        [
            'code' => 'gfr',
            'name_pt' => 'Garrafão',
            'name_en' => 'Jug',
            'symbol' => 'gfr',
            'category' => 'packaging',
            'priority' => 14
        ],

        // 15. Pacote
        [
            'code' => 'pct',
            'name_pt' => 'Pacote',
            'name_en' => 'Pack',
            'symbol' => 'pct',
            'category' => 'packaging',
            'priority' => 15
        ],

        // 16. Rolo
        [
            'code' => 'rl',
            'name_pt' => 'Rolo',
            'name_en' => 'Roll',
            'symbol' => 'rl',
            'category' => 'packaging',
            'priority' => 16
        ],

        // 17. Resma
        [
            'code' => 'rsm',
            'name_pt' => 'Resma',
            'name_en' => 'Ream',
            'symbol' => 'rsm',
            'category' => 'quantity',
            'priority' => 17
        ],

        // 18. Peça
        [
            'code' => 'pç',
            'name_pt' => 'Peça',
            'name_en' => 'Piece',
            'symbol' => 'pç',
            'category' => 'quantity',
            'priority' => 18
        ],

        // 19. Display
        [
            'code' => 'dsp',
            'name_pt' => 'Display',
            'name_en' => 'Display',
            'symbol' => 'dsp',
            'category' => 'packaging',
            'priority' => 19
        ],

        // 20. Bisnaga
        [
            'code' => 'bis',
            'name_pt' => 'Bisnaga',
            'name_en' => 'Tube',
            'symbol' => 'bis',
            'category' => 'packaging',
            'priority' => 20
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Categorias de Unidades
    |--------------------------------------------------------------------------
    */

    'categories' => [
        'weight' => 'Peso',
        'volume' => 'Volume',
        'quantity' => 'Quantidade',
        'packaging' => 'Embalagem',
    ],

];
