<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductsImportNoHeader implements ToCollection
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
                if (empty($row[1])) {
                    $this->skippedCount++;
                    continue;
                }

                $productData = $this->prepareProductData($row, $index);
                $product = Product::create($productData);
                $this->importedCount++;
                
            } catch (\Exception $e) {
                $this->errors[] = "Linha " . ($index + 1) . ": " . $e->getMessage();
            }
        }
    }

    protected function prepareProductData($row, $index)
    {
        // Column mapping based on inspection
        // 0: SKU
        // 1: Product Name
        // 4: Description
        // 5: Status
        // 6: Price
        // 8: Stock status
        // 9: Quantity
        // 14: Category path
        
        $sku = $row[0] ?? "";
        $name = $row[1] ?? "Produto Sem Nome";
        $description = $row[4] ?? "";
        $price = $row[6] ?? 0;
        $stock = $row[9] ?? 0;
        $categoryPath = $row[14] ?? "";
        
        // Parse price
        $price = $this->parsePrice($price);
        $stock = intval($stock);
        
        // Get category ID from path
        $categoryId = $this->getCategoryIdFromPath($categoryPath);
        
        // Generate unique slug
        $slug = Str::slug($name) . "-" . time() . rand(100, 999);

        return [
            "name" => $name,
            "added_by" => "seller",
            "user_id" => $this->userId,
            "category_id" => $categoryId,
            "brand_id" => null,
            "photos" => null,
            "thumbnail_img" => null,
            "tags" => null,
            "description" => $description,
            "unit_price" => $price,
            "purchase_price" => null,
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
            "unit" => "Unidade",
            "min_qty" => 1,
            "low_stock_quantity" => 1,
            "discount" => 0,
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
            "barcode" => $sku,
        ];
    }

    protected function getCategoryIdFromPath($categoryPath)
    {
        if (empty($categoryPath)) {
            return 1;
        }
        
        // Extract last category from path (e.g., "Bebidas > Não Alcoólicas > Água" -> "Água")
        $parts = explode(">", $categoryPath);
        $categoryName = trim(end($parts));
        
        // Try to find category by name
        $category = Category::where("name", "LIKE", "%{$categoryName}%")->first();
        
        if ($category) {
            return $category->id;
        }
        
        // Check for "Bebidas" categories
        if (stripos($categoryPath, "Bebidas") !== false) {
            if (stripos($categoryPath, "Não Alcoólicas") !== false || stripos($categoryPath, "Nao Alcoolicas") !== false) {
                return 4; // Bebidas Nao Alcoolicas
            }
            if (stripos($categoryPath, "Alcoólicas") !== false || stripos($categoryPath, "Alcoolicas") !== false) {
                return 3; // Bebidas Alcoolicas
            }
            if (stripos($categoryPath, "Cerveja") !== false) {
                return 2; // Cervejas
            }
            return 1; // Bebidas
        }
        
        return 1; // Default
    }

    protected function parsePrice($price)
    {
        $price = strval($price);
        $price = preg_replace("/[^0-9.,]/", "", $price);
        $price = str_replace(",", ".", $price);
        return floatval($price);
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
