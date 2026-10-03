<?php

namespace App\Observers;

use App\Actions\Catalog\GetActiveProducts;
use App\Models\Product;

class ProductObserver
{
    public function saved(Product $product): void
    {
        GetActiveProducts::flush();
    }

    public function deleted(Product $product): void
    {
        GetActiveProducts::flush();
    }
}
