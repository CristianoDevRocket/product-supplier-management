<?php

namespace Database\Seeders;

use App\Enums\EntityStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create test user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create suppliers
        $suppliers = [
            ['name' => 'Tech Solutions Ltda', 'cnpj' => '11.222.333/0001-44', 'email' => 'contato@techsolutions.com.br', 'phone' => '(11) 99999-1111', 'status' => 'active'],
            ['name' => 'Global Parts SA', 'cnpj' => '22.333.444/0001-55', 'email' => 'vendas@globalparts.com.br', 'phone' => '(21) 98888-2222', 'status' => 'active'],
            ['name' => 'Mega Distribuidora', 'cnpj' => '33.444.555/0001-66', 'email' => 'comercial@megadist.com.br', 'phone' => '(31) 97777-3333', 'status' => 'active'],
            ['name' => 'Fornecedor Antigo', 'cnpj' => '44.555.666/0001-77', 'email' => 'antigo@fornecedor.com.br', 'phone' => '(41) 96666-4444', 'status' => 'inactive'],
            ['name' => 'Premium Supply Co', 'cnpj' => '55.666.777/0001-88', 'email' => 'premium@supply.com.br', 'phone' => '(51) 95555-5555', 'status' => 'active'],
        ];

        foreach ($suppliers as $data) {
            Supplier::create($data);
        }

        // Create products
        $products = [
            ['name' => 'Notebook Dell Inspiron', 'description' => 'Notebook 15.6" Intel i7 16GB RAM 512GB SSD', 'internal_code' => 'PROD-001', 'status' => 'active'],
            ['name' => 'Mouse Logitech MX Master', 'description' => 'Mouse sem fio ergonomico', 'internal_code' => 'PROD-002', 'status' => 'active'],
            ['name' => 'Teclado Mecânico Keychron', 'description' => 'Teclado mecanico RGB wireless', 'internal_code' => 'PROD-003', 'status' => 'active'],
            ['name' => 'Monitor Samsung 27"', 'description' => 'Monitor 4K IPS 27 polegadas', 'internal_code' => 'PROD-004', 'status' => 'active'],
            ['name' => 'Headset HyperX Cloud', 'description' => 'Headset gamer com microfone', 'internal_code' => 'PROD-005', 'status' => 'active'],
            ['name' => 'Webcam Logitech C920', 'description' => 'Webcam Full HD 1080p', 'internal_code' => 'PROD-006', 'status' => 'active'],
            ['name' => 'Hub USB-C 7 portas', 'description' => 'Hub USB-C com HDMI, USB 3.0 e leitor de cartao', 'internal_code' => 'PROD-007', 'status' => 'active'],
            ['name' => 'Cabo HDMI 2.1 3m', 'description' => 'Cabo HDMI 2.1 de 3 metros', 'internal_code' => 'PROD-008', 'status' => 'inactive'],
            ['name' => 'SSD NVMe 1TB', 'description' => 'SSD M.2 NVMe PCIe 4.0', 'internal_code' => 'PROD-009', 'status' => 'active'],
            ['name' => 'Cadeira Gamer ThunderX3', 'description' => 'Cadeira gamer ergonomica reclinavel', 'internal_code' => 'PROD-010', 'status' => 'active'],
        ];

        foreach ($products as $data) {
            Product::create($data);
        }

        // Link products to suppliers
        $techSolutions = Supplier::where('cnpj', '11.222.333/0001-44')->first();
        $globalParts = Supplier::where('cnpj', '22.333.444/0001-55')->first();
        $megaDist = Supplier::where('cnpj', '33.444.555/0001-66')->first();
        $premium = Supplier::where('cnpj', '55.666.777/0001-88')->first();

        $techSolutions->products()->attach([1, 2, 3, 4, 5]);
        $globalParts->products()->attach([1, 4, 6, 7, 9]);
        $megaDist->products()->attach([2, 3, 5, 8, 10]);
        $premium->products()->attach([1, 2, 3, 4, 5, 6, 7, 9, 10]);

        // Create orders
        $order1 = Order::create([
            'supplier_id' => $techSolutions->id,
            'order_date' => now()->subDays(10),
            'status' => OrderStatus::Completed,
            'notes' => 'Pedido urgente para o escritorio',
            'total' => 0,
        ]);
        OrderStatusHistory::create(['order_id' => $order1->id, 'old_status' => null, 'new_status' => 'open', 'changed_at' => now()->subDays(10)]);
        OrderStatusHistory::create(['order_id' => $order1->id, 'old_status' => 'open', 'new_status' => 'processing', 'changed_at' => now()->subDays(7)]);
        OrderStatusHistory::create(['order_id' => $order1->id, 'old_status' => 'processing', 'new_status' => 'completed', 'changed_at' => now()->subDays(2)]);

        OrderItem::create(['order_id' => $order1->id, 'product_id' => 1, 'quantity' => 3, 'unit_price' => 4500.00, 'total_price' => 13500.00]);
        OrderItem::create(['order_id' => $order1->id, 'product_id' => 2, 'quantity' => 3, 'unit_price' => 450.00, 'total_price' => 1350.00]);
        $order1->update(['total' => 14850.00]);

        $order2 = Order::create([
            'supplier_id' => $globalParts->id,
            'order_date' => now()->subDays(3),
            'status' => OrderStatus::Open,
            'notes' => 'Reposicao de estoque',
            'total' => 0,
        ]);
        OrderStatusHistory::create(['order_id' => $order2->id, 'old_status' => null, 'new_status' => 'open', 'changed_at' => now()->subDays(3)]);

        OrderItem::create(['order_id' => $order2->id, 'product_id' => 4, 'quantity' => 5, 'unit_price' => 2200.00, 'total_price' => 11000.00]);
        OrderItem::create(['order_id' => $order2->id, 'product_id' => 9, 'quantity' => 10, 'unit_price' => 650.00, 'total_price' => 6500.00]);
        $order2->update(['total' => 17500.00]);

        $order3 = Order::create([
            'supplier_id' => $premium->id,
            'order_date' => now()->subDay(),
            'status' => OrderStatus::Processing,
            'notes' => null,
            'total' => 0,
        ]);
        OrderStatusHistory::create(['order_id' => $order3->id, 'old_status' => null, 'new_status' => 'open', 'changed_at' => now()->subDay()]);
        OrderStatusHistory::create(['order_id' => $order3->id, 'old_status' => 'open', 'new_status' => 'processing', 'changed_at' => now()]);

        OrderItem::create(['order_id' => $order3->id, 'product_id' => 10, 'quantity' => 2, 'unit_price' => 1800.00, 'total_price' => 3600.00]);
        $order3->update(['total' => 3600.00]);

        $order4 = Order::create([
            'supplier_id' => $megaDist->id,
            'order_date' => now()->subDays(15),
            'status' => OrderStatus::Cancelled,
            'notes' => 'Cancelado por falta de estoque do fornecedor',
            'total' => 0,
        ]);
        OrderStatusHistory::create(['order_id' => $order4->id, 'old_status' => null, 'new_status' => 'open', 'changed_at' => now()->subDays(15)]);
        OrderStatusHistory::create(['order_id' => $order4->id, 'old_status' => 'open', 'new_status' => 'cancelled', 'changed_at' => now()->subDays(14)]);
    }
}
