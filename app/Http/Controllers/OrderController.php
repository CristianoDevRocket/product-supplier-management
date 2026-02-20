<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderItemRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Supplier;
use App\Services\OrderItemService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly OrderItemService $itemService
    ) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Orders/Index', [
            'orders' => $this->orderService->list($request->all()),
            'filters' => $request->only(['search', 'status', 'supplier_id', 'sort', 'direction']),
            'suppliers' => Supplier::where('status', 'active')->orderBy('name')->get(['id', 'name']),
            'statuses' => collect(OrderStatus::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->label()]),
        ]);
    }

    public function create(): Response
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get(['id', 'name', 'cnpj']);

        return Inertia::render('Orders/Create', [
            'suppliers' => $suppliers,
        ]);
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $supplier = Supplier::findOrFail($request->supplier_id);

        if (!$supplier->isActive()) {
            return redirect()->back()->with('error', 'Fornecedor inativo nao pode receber novos pedidos.');
        }

        $order = $this->orderService->create($request->validated());

        return redirect()->route('orders.show', $order)
            ->with('success', 'Pedido criado com sucesso. Adicione itens ao pedido.');
    }

    public function show(Order $order): Response
    {
        $order->load(['supplier:id,name,cnpj', 'items.product:id,name,internal_code', 'statusHistories']);

        $availableProducts = $order->supplier->products()
            ->where('products.status', 'active')
            ->orderBy('products.name')
            ->get(['products.id', 'products.name', 'products.internal_code']);

        $allowedTransitions = [];
        foreach (OrderStatus::cases() as $status) {
            if ($order->status->canTransitionTo($status)) {
                $allowedTransitions[] = ['value' => $status->value, 'label' => $status->label()];
            }
        }

        return Inertia::render('Orders/Show', [
            'order' => $order,
            'availableProducts' => $availableProducts,
            'allowedTransitions' => $allowedTransitions,
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate(['status' => ['required', 'string']]);

        try {
            $newStatus = OrderStatus::from($request->status);
            $this->orderService->updateStatus($order, $newStatus);
            return redirect()->back()->with('success', "Status atualizado para '{$newStatus->label()}'.");
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function addItem(StoreOrderItemRequest $request, Order $order): RedirectResponse
    {
        try {
            $this->itemService->addItem($order, $request->validated());
            return redirect()->back()->with('success', 'Item adicionado ao pedido.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function removeItem(Order $order, OrderItem $orderItem): RedirectResponse
    {
        try {
            $this->itemService->removeItem($order, $orderItem);
            return redirect()->back()->with('success', 'Item removido do pedido.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
