<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Supplier;

class ProductSupplierService
{
    public function link(Product $product, Supplier $supplier): void
    {
        $product->suppliers()->syncWithoutDetaching([$supplier->id]);
    }

    public function unlink(Product $product, Supplier $supplier): void
    {
        $product->suppliers()->detach($supplier->id);
    }
}
