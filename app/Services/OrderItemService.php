<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrderItemService
{
    public function addItem(Order $order, array $data): OrderItem
    {
        $product = Product::findOrFail($data['product_id']);

        // Validate product is active
        if (!$product->isActive()) {
            throw new \InvalidArgumentException("O produto '{$product->name}' esta inativo.");
        }

        // Validate product is linked to the order's supplier
        $isLinked = $order->supplier->products()->where('products.id', $product->id)->exists();
        if (!$isLinked) {
            throw new \InvalidArgumentException(
                "O produto '{$product->name}' nao esta vinculado ao fornecedor '{$order->supplier->name}'."
            );
        }

        // Validate order is editable
        if (!$order->isEditable()) {
            throw new \InvalidArgumentException('Este pedido nao pode ser editado.');
        }

        return OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
        ]);
    }

    public function removeItem(Order $order, OrderItem $item): void
    {
        if (!$order->isEditable()) {
            throw new \InvalidArgumentException('Este pedido nao pode ser editado.');
        }

        $item->delete();
    }
}
