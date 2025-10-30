# Sistema de Unidades de Medida - HORECA

## Visão Geral

O sistema agora possui **20 unidades de medida** específicas para o setor HORECA (Hotéis, Restaurantes e Catering), configuradas para Portugal e Angola.

## Arquivos Criados

### 1. Configuração
**Localização:** `config/units.php`

Contém todas as 20 unidades com:
- Código da unidade (kg, L, un, etc.)
- Nome em Português e Inglês
- Símbolo
- Categoria (peso, volume, quantidade, embalagem)
- Prioridade de exibição

### 2. Helper
**Localização:** `app/Helpers/UnitHelper.php`

Classe utilitária para facilitar o acesso às unidades.

### 3. Traduções
- `resources/lang/pt/units.php` - Português
- `resources/lang/en/units.php` - Inglês

---

## Como Usar

### 1. Obter Unidades para Dropdown/Select

```php
use App\Helpers\UnitHelper;

// No Controller
public function create()
{
    $units = UnitHelper::getUnitsForDropdown();
    return view('products.create', compact('units'));
}
```

```blade
{{-- Na View (Blade) --}}
<select name=unit class=form-control>
    <option value=>{{ __('units.select_unit') }}</option>
    @foreach($units as $code => $name)
        <option value={{ $code }}>{{ $name }}</option>
    @endforeach
</select>
```

### 2. Exibir Nome da Unidade

```php
use App\Helpers\UnitHelper;

// Obter nome em português
$unitName = UnitHelper::getUnitName('kg'); // Retorna: Quilograma

// Obter nome em inglês
$unitName = UnitHelper::getUnitName('kg', 'en'); // Retorna: Kilogram
```

```blade
{{-- Na View --}}
<p>Unidade: {{ UnitHelper::getUnitName($product->unit) }}</p>
```

### 3. Formatar Valor com Unidade

```php
use App\Helpers\UnitHelper;

// Formatar
$formatted = UnitHelper::formatValue(5, 'kg'); // Retorna: 5 kg
$formatted = UnitHelper::formatValue(12, 'dz'); // Retorna: 12 dz
```

```blade
{{-- Na View --}}
<p>Quantidade: {{ UnitHelper::formatValue($product->quantity, $product->unit) }}</p>
```

### 4. Obter Todas as Unidades

```php
use App\Helpers\UnitHelper;

$allUnits = UnitHelper::getAllUnits();
// Retorna array completo com todas as informações
```

### 5. Obter Unidades por Categoria

```php
use App\Helpers\UnitHelper;

$weightUnits = UnitHelper::getUnitsByCategory('weight');
$volumeUnits = UnitHelper::getUnitsByCategory('volume');
$quantityUnits = UnitHelper::getUnitsByCategory('quantity');
$packagingUnits = UnitHelper::getUnitsByCategory('packaging');
```

---

## TOP 20 Unidades Disponíveis

### Peso
1. **kg** - Quilograma / Kilogram

### Volume
2. **L** - Litro / Liter
3. **bbl** - Barril / Keg/Barrel

### Quantidade
4. **un** - Unidade / Unit
5. **dz** - Dúzia / Dozen
6. **rsm** - Resma / Ream
7. **pç** - Peça / Piece

### Embalagem
8. **cx** - Caixa / Box
9. **emb** - Embalagem / Package
10. **grf** - Garrafa / Bottle
11. **lt** - Lata / Can/Tin
12. **sc** - Saco / Bag
13. **bdj** - Bandeja / Tray
14. **eng** - Engradado / Crate
15. **frd** - Fardo / Bundle
16. **gfr** - Garrafão / Jug
17. **pct** - Pacote / Pack
18. **rl** - Rolo / Roll
19. **dsp** - Display / Display
20. **bis** - Bisnaga / Tube

---

## Exemplo Completo: Formulário de Produto

```blade
{{-- resources/views/products/create.blade.php --}}

<div class=form-group>
    <label for=unit>{{ __('units.unit') }}</label>
    <select name=unit id=unit class=form-control required>
        <option value=>{{ __('units.select_unit') }}</option>
        @foreach(App\Helpers\UnitHelper::getUnitsForDropdown() as $code => $name)
            <option value={{ $code }} {{ old('unit') == $code ? 'selected' : '' }}>
                {{ $name }} ({{ $code }})
            </option>
        @endforeach
    </select>
</div>
```

---

## Exemplo: Exibir Produto com Unidade

```blade
{{-- resources/views/products/show.blade.php --}}

<div class=product-info>
    <h3>{{ $product->name }}</h3>
    
    <p>
        <strong>Preço:</strong> 
        {{ $product->unit_price }} Kz / {{ App\Helpers\UnitHelper::getUnitSymbol($product->unit) }}
    </p>
    
    <p>
        <strong>Quantidade mínima:</strong>
        {{ App\Helpers\UnitHelper::formatValue($product->min_qty, $product->unit) }}
    </p>
    
    <p>
        <strong>Unidade de venda:</strong>
        {{ App\Helpers\UnitHelper::getUnitName($product->unit) }}
    </p>
</div>
```

---

## Métodos Disponíveis no UnitHelper

| Método | Descrição | Exemplo |
|--------|-----------|---------|
| `getAllUnits()` | Retorna todas as unidades | `UnitHelper::getAllUnits()` |
| `getUnitsForDropdown()` | Retorna array [code => name] para select | `UnitHelper::getUnitsForDropdown()` |
| `getUnitsByCategory($cat)` | Retorna unidades de uma categoria | `UnitHelper::getUnitsByCategory('weight')` |
| `getUnitByCode($code)` | Retorna dados completos de uma unidade | `UnitHelper::getUnitByCode('kg')` |
| `getUnitName($code, $lang)` | Retorna nome da unidade | `UnitHelper::getUnitName('kg', 'pt')` |
| `getUnitSymbol($code)` | Retorna símbolo da unidade | `UnitHelper::getUnitSymbol('kg')` |
| `formatValue($value, $code)` | Formata valor com unidade | `UnitHelper::formatValue(5, 'kg')` |

---

## Categorias de Unidades

- **weight** - Peso (kg)
- **volume** - Volume (L, bbl, gfr)
- **quantity** - Quantidade (un, dz, rsm, pç)
- **packaging** - Embalagem (cx, emb, grf, lt, sc, bdj, eng, frd, pct, rl, dsp, bis)

---

## Notas Importantes

1. **Campo na tabela:** O campo `unit` na tabela `products` é `varchar(20)`
2. **Validação:** Sempre valide se o código da unidade existe
3. **Tradução:** Use `__('units.kg')` para traduções automáticas
4. **Extensão:** Para adicionar mais unidades, edite `config/units.php`

---

## Suporte

Para dúvidas ou adicionar mais unidades, consulte:
- Arquivo de configuração: `config/units.php`
- Helper: `app/Helpers/UnitHelper.php`
- Traduções: `resources/lang/{pt,en}/units.php`
