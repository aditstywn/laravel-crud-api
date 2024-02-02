<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::orderBy('id', 'desc')->get();
        return response()->json([
            'success' => true,
            'message' => 'Get List Data Berhasil',
            'data' => $products,
        ]);
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
            'image' => 'required|image|mimes:png,jpg,jpeg',
        ]);

        $filename = time() . '.' . $request->image->extension();

        $validate['image'] = $filename;

        // memasukan image ke dalam storage
        $request->image->storeAs('public/products', $filename);

        $product = Product::create($validate);

        if ($product) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil Create Product',
                'data' => $product,
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal Create Product',
            ], 409);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Get Data Berhasil',
            'data' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product tidak ditemukan',
            ], 404);
        }

        $validate = $request->validate([
            'name' => 'required',
            'stock' => 'required',
            'price' => 'required',
            'category' => 'required|in:drink,snack,food',
            'image' => 'required|image|mimes:png,jpg,jpeg',
        ]);


        // Jika ada file gambar yang diunggah, proses gambar baru
        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->image->extension();
            $validate['image'] = $filename;

            // memasukan image ke dalam storage
            $request->image->storeAs('public/products', $filename);

            // Hapus gambar lama jika ada
            if ($product->image) {
                Storage::delete('public/products/' . $product->image);
            }
        }

        $product->update($validate);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Update Product',
            'data' => $product,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product tidak ditemukan',
            ], 404);
        }

        // Hapus gambar dari storage jika ada
        if ($product->image) {
            Storage::delete('public/products/' . $product->image);
        }

        // Hapus produk dari database
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Hapus Product',
        ]);
    }


    public function edit(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product tidak ditemukan',
            ], 404);
        }


        $validate = $request->validate([
            'name' => 'required',
            'stock' => 'required',
            'price' => 'required',
            'category' => 'required|in:drink,snack,food',
            'image' => 'required|image|mimes:png,jpg,jpeg',
        ]);


        // Jika ada file gambar yang diunggah, proses gambar baru
        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->image->extension();
            $validate['image'] = $filename;

            // memasukan image ke dalam storage
            $request->image->storeAs('public/products', $filename);

            // Hapus gambar lama jika ada
            if ($product->image) {
                Storage::delete('public/products/' . $product->image);
            }
        }


        $product->update($validate);



        return response()->json([
            'success' => true,
            'message' => 'Berhasil Update Product',
            'data' => $product,
        ]);
    }
}
