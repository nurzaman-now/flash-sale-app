<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar semua produk.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $products = Product::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate($perPage, ['*'], 'page', $page);

        return $this->responseSuccessPagination('Mengambil data produk berhasil', $products);
    }

    /**
     * Menyimpan produk baru.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            $product = Product::create($validatedData);
            DB::commit();

            return $this->responseSuccess('Produk berhasil ditambahkan', $product, 201);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Gagal menambahkan produk");
        }
    }

    /**
     * Menampilkan detail produk berdasarkan ID.
     */
    public function show(Product $product): JsonResponse
    {
        return $this->responseSuccess('Mengambil data produk berhasil', $product);
    }

    /**
     * Memperbarui data produk.
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();

            if (empty($validatedData)) {
                return $this->responseError('Tidak ada data yang diupdate', null, 422);
            }
            $product->update($validatedData);
            DB::commit();

            return $this->responseSuccess('Produk berhasil diupdate', $product);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Gagal update produk");
        }
    }

    /**
     * Menghapus produk.
     */
    public function destroy(Product $product): JsonResponse
    {
        DB::beginTransaction();
        try {
            $product->delete();
            DB::commit();

            return $this->responseSuccess('Produk berhasil dihapus');
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Gagal hapus produk");
        }
    }

    /**
     * Mengembalikan data yang di hapus
     */
    public function restore(Product $product)
    {
        DB::beginTransaction();
        try {
            $product->restore();
            DB::commit();

            return $this->responseSuccess('Produk berhasil dikembalikan', $product);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Gagal mengembalikan produk");
        }
    }

    /**
     * Menghapus data produk secara permanen
     */
    public function permanentlyDelete(Product $product)
    {
        DB::beginTransaction();
        try {
            $product->forceDelete();
            DB::commit();
            return $this->responseSuccess('Produk berhasil dihapus permanen');
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Gagal menghapus produk");
        }
    }

    /**
     * Mengembalikan data yang di hapus
     */
    public function trashed()
    {
        $products = Product::onlyTrashed()->get();
        return $this->responseSuccess('Mengambil data produk yang dihapus berhasil', $products);
    }
}
