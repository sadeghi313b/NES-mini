<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::with([
            'customer',
            'blueWireApplicator',
            'brownWireApplicator',
            'yellowWireApplicator',
            'mold',
            'createdBy'
        ]);

        // Search filter
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('cable_name', 'like', '%' . $request->search . '%');
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->paginate(15);

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => $request->only(['search', 'status'])
        ]);
    }
    // public function index()
    // {
    //     $products = Product::with([
    //         'customer',
    //         'blueWireApplicator',
    //         'brownWireApplicator',
    //         'yellowWireApplicator',
    //         'mold',
    //         'createdBy'
    //     ])->paginate(15);

    //     return Inertia::render('Products/Index', [
    //         'products' => $products
    //     ]);
    // }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return Inertia::render('Products/Create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(ProductRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = auth()->id();

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load([
            'customer',
            'blueWireApplicator',
            'brownWireApplicator',
            'yellowWireApplicator',
            'doubleWireApplicator',
            'mold',
            'orders',
            'cycleTimes.activity',
            'createdBy'
        ]);

        return Inertia::render('Products/Show', [
            'product' => $product
        ]);
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', [
            'product' => $product
        ]);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $validated = $request->validated();
        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
