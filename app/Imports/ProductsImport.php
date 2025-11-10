<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class ProductsImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    protected $userId;
    protected $shopId;
    protected $importedCount = 0;
    protected $errors = [];
    protected $skippedCount = 0;

    public function __construct($userId, $shopId)
    {
        $this->userId = $userId;
        $this->shopId = $shopId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                // Skip empty rows
                if (empty($row["name"]) && empty($row["nome"])) {
                    $this->skippedCount++;
                    continue;
                }

                $productData = $this->prepareProductData($row);
                $product = Product::create($productData);
                $this->importedCount++;
                
            } catch (\Exception $e) {
                $this->errors[] = "Linha " . ($index + 2) . ": " . $e->getMessage();
            }
        }
    }

    protected function prepareProductData($row)
    {
        // Get product name (support both English and Portuguese headers)
        $name = $row["name"] ?? $row["nome"] ?? "Produto Sem Nome";
        
        // Get description
        $description = $row["description"] ?? $row["descricao"] ?? "";
        
        // Get price
        $price = $row["price"] ?? $row["preco"] ?? 0;
        $price = $this->parsePrice($price);
        
        // Get stock
        $stock = $row["stock"] ?? $row["estoque"] ?? 0;
        $stock = intval($stock);
        
        // Get category
        $categoryId = $row["category_id"] ?? $row["categoria_id"] ?? 1;
        
        // Get brand
        $brandId = $row["brand_id"] ?? $row["marca_id"] ?? null;
        
        // Generate unique slug
        $slug = Str::slug($name) . "-" . time() . rand(100, 999);

        return [
            "name" => $name,
            "added_by" => "seller",
            "user_id" => $this->userId,
            "category_id" => $categoryId,
            "brand_id" => $brandId,
            "photos" => null,
            "thumbnail_img" => null,
            "tags" => $row["tags"] ?? null,
            "description" => $description,
            "unit_price" => $price,
            "purchase_price" => $row["purchase_price"] ?? null,
            "variant_product" => 0,
            "attributes" => "[]",
            "choice_options" => "[]",
            "colors" => "[]",
            "variations" => null,
            "todays_deal" => 0,
            "published" => 1,
            "approved" => 1,
            "stock_visibility_state" => "quantity",
            "cash_on_delivery" => 1,
            "featured" => 0,
            "seller_featured" => 0,
            "current_stock" => $stock,
            "unit" => $row["unit"] ?? $row["unidade"] ?? "Pc",
            "min_qty" => 1,
            "low_stock_quantity" => 1,
            "discount" => $row["discount"] ?? $row["desconto"] ?? 0,
            "discount_type" => "amount",
            "tax" => 0,
            "tax_type" => "amount",
            "shipping_type" => "free",
            "shipping_cost" => 0,
            "num_of_sale" => 0,
            "meta_title" => $name,
            "meta_description" => substr($description, 0, 160),
            "slug" => $slug,
            "rating" => 0,
            "barcode" => $row["barcode"] ?? $row["codigo_barras"] ?? null,
        ];
    }

    protected function parsePrice($price)
    {
        // Convert to string if needed
        $price = strval($price);
        
        // Remove currency symbols and spaces
        $price = preg_replace("/[^0-9.,]/", "", $price);
        
        // Replace comma with dot for decimal
        $price = str_replace(",", ".", $price);
        
        return floatval($price);
    }

    public function chunkSize(): int
    {
        return 50;
    }

    public function batchSize(): int
    {
        return 50;
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    public function getSkippedCount()
    {
        return $this->skippedCount;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
