<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BulkLinkProductSuppliers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly array $productIds,
        public readonly array $supplierIds
    ) {}

    public function handle(): void
    {
        foreach ($this->productIds as $productId) {
            $product = Product::find($productId);
            if ($product) {
                $product->suppliers()->syncWithoutDetaching($this->supplierIds);
            }
        }
    }
}
