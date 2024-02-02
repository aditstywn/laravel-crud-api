<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = DB::table('products')->when($request->input('name'), function ($query, $name) {
            return $query->where('name', 'like', '%' . $name . '%');
        })->orderBy('id', 'desc')->paginate(5);
        return view('pages.product.index', [
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.product.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'stock' => 'required',
            'price' => 'required',
            'category' => 'required|in:drink,snack,food',
            'image' => 'required|image|mimes:png,jpg,jpeg'
        ]);

        $filename = time() . '.' . $request->image->extension();

        $validate['image'] = $filename;

        // memasukan image ke dalam storage
        $request->image->storeAs('public/products', $filename);

        Product::create($validate);
        return redirect()->route('product.index')->with('success', 'User Successfully Created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('pages.product.edit', [
            'products' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validate = $request->validate([
            'name' => 'required',
            'stock' => 'required',
            'price' => 'required',
            'category' => 'required|in:Drink,Snack,Food',
        ]);
        // Proses pembaruan gambar jika ada gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            Storage::delete('public/products/' . $product->image);

            // Simpan gambar baru
            $filename = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/products', $filename);
            $product->update(['image' => $filename]); // bisa pakai ini juga
            // $validate['image'] = $filename;
        }


        $product->update($validate);

        return redirect()->route('product.index')->with('success', 'Product Successfuly Update');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::delete('public/products/' . $product->image);
        }
        // Storage::delete('public/products/' . $product->image);
        $product->delete();
        return redirect()->route('product.index')->with('success', 'Product Successfuly Delete');
    }
}
