<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        return Order::query()
            ->with('supplier:id,name')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%")
                      ->orWhereHas('supplier', function ($sq) use ($search) {
                          $sq->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['supplier_id'] ?? null, function ($query, $supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->orderBy($filters['sort'] ?? 'created_at', $filters['direction'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 10)
            ->withQueryString();
    }

    public function create(array $data): Order
    {
        $order = Order::create([
            'supplier_id' => $data['supplier_id'],
            'order_date' => $data['order_date'],
            'notes' => $data['notes'] ?? null,
            'status' => OrderStatus::Open,
            'total' => 0,
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'old_status' => null,
            'new_status' => OrderStatus::Open->value,
            'changed_at' => now(),
        ]);

        return $order;
    }

    public function updateStatus(Order $order, OrderStatus $newStatus): Order
    {
        $oldStatus = $order->status;

        if (!$oldStatus->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException(
                "Transicao de '{$oldStatus->label()}' para '{$newStatus->label()}' nao e permitida."
            );
        }

        $order->update(['status' => $newStatus]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'old_status' => $oldStatus->value,
            'new_status' => $newStatus->value,
            'changed_at' => now(),
        ]);

        return $order->fresh();
    }
}
