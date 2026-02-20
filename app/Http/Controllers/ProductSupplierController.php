<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkLinkRequest;
use App\Jobs\BulkLinkProductSuppliers;
use App\Jobs\BulkUnlinkProductSuppliers;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\ProductSupplierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductSupplierController extends Controller
{
    public function __construct(
        private readonly ProductSupplierService $service
    ) {}

    public function index(Request $request): Response
    {
        $supplierId = $request->get('supplier_id');

        $suppliers = Supplier::orderBy('name')->get(['id', 'name', 'cnpj', 'status']);

        $selectedSupplier = $supplierId ? Supplier::find($supplierId) : null;

        $linkedProducts = collect();
        $availableProducts = Product::where('status', 'active')->orderBy('name')->get(['id', 'name', 'internal_code', 'status']);

        if ($selectedSupplier) {
            $linkedProducts = $selectedSupplier->products()->orderBy('name')->get(['products.id', 'products.name', 'products.internal_code', 'products.status']);
            $linkedIds = $linkedProducts->pluck('id')->toArray();
            $availableProducts = Product::where('status', 'active')
                ->whereNotIn('id', $linkedIds)
                ->orderBy('name')
                ->get(['id', 'name', 'internal_code', 'status']);
        }

        return Inertia::render('ProductSuppliers/Index', [
            'suppliers' => $suppliers,
            'selectedSupplierId' => $supplierId ? (int) $supplierId : null,
            'linkedProducts' => $linkedProducts,
            'availableProducts' => $availableProducts,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
        ]);

        $product = Product::findOrFail($request->product_id);
        $supplier = Supplier::findOrFail($request->supplier_id);

        $this->service->link($product, $supplier);

        return redirect()->route('product-suppliers.index', ['supplier_id' => $supplier->id])
            ->with('success', "Produto '{$product->name}' vinculado com sucesso.");
    }

    public function destroy(Product $product, Supplier $supplier): RedirectResponse
    {
        $this->service->unlink($product, $supplier);

        return redirect()->route('product-suppliers.index', ['supplier_id' => $supplier->id])
            ->with('success', "Produto '{$product->name}' desvinculado com sucesso.");
    }

    public function bulkLink(BulkLinkRequest $request): RedirectResponse
    {
        BulkLinkProductSuppliers::dispatch(
            $request->validated()['product_ids'],
            $request->validated()['supplier_ids']
        );

        return redirect()->route('product-suppliers.index', ['supplier_id' => $request->supplier_ids[0] ?? null])
            ->with('info', 'Vinculacao em massa enviada para processamento.');
    }

    public function bulkUnlink(BulkLinkRequest $request): RedirectResponse
    {
        BulkUnlinkProductSuppliers::dispatch(
            $request->validated()['product_ids'],
            $request->validated()['supplier_ids']
        );

        return redirect()->route('product-suppliers.index', ['supplier_id' => $request->supplier_ids[0] ?? null])
            ->with('info', 'Desvinculacao em massa enviada para processamento.');
    }
}
