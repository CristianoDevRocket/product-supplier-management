<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SupplierController extends Controller
{
    public function __construct(
        private readonly SupplierService $service
    ) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Suppliers/Index', [
            'suppliers' => $this->service->list($request->all()),
            'filters' => $request->only(['search', 'status', 'sort', 'direction']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Suppliers/Create');
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', 'Fornecedor criado com sucesso.');
    }

    public function edit(Supplier $supplier): Response
    {
        return Inertia::render('Suppliers/Edit', [
            'supplier' => $supplier,
        ]);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->service->update($supplier, $request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', 'Fornecedor atualizado com sucesso.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $this->service->delete($supplier);

        return redirect()->route('suppliers.index')
            ->with('success', 'Fornecedor removido com sucesso.');
    }
}
