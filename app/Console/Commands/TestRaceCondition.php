<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TestRaceCondition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:race-condition';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uji fungsional untuk menangani kondisi persaingan penjualan kilat (flash sale).';

    private string $nameUser;
    private string $email;
    private string $password;
    private string $passwordConfirmation;
    private ?string $token;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->nameUser = 'User ' . time();
        $this->email = 'user' . time() . '@example.com';
        $this->password = 'password';
        $this->passwordConfirmation = 'password';
    }

    private function register()
    {
        $response = Http::post(config('app.url') . '/api/auth/register', [
            'name' => $this->nameUser,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->passwordConfirmation,
        ]);

        if ($response->successful()) {
            $this->info("Registrasi berhasil");
            return true;
        } else {
            $this->error("Registrasi gagal: " . var_export($response->json(), true));
            return false;
        }
    }

    private function login()
    {
        $response = Http::post(config('app.url') . '/api/auth/login', [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $this->token = $response->json('data.token');
        if ($this->token) {
            $this->info("Token berhasil didapat: {$this->token}");
            return true;
        } else {
            $this->error("Token gagal didapat: " . var_export($response->json('data.error'), true));
            return false;
        }
    }

    private function logout()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->post(config('app.url') . '/api/auth/logout');

        if ($response->successful()) {
            $this->info("Logout berhasil");
            return true;
        } else {
            $this->error("Logout gagal: " . var_export($response->json(), true));
            return false;
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("1. Melakukan registrasi...");

        if (!$this->register()) {
            return Command::FAILURE;
        }

        $this->info("2. Melakukan login...");

        if (!$this->login()) {
            return Command::FAILURE;
        }

        $this->info("3. Menyiapkan data produk awal...");

        DB::table('order_items')->delete();
        DB::table('orders')->delete();
        DB::table('products')->delete();

        $product = Product::create([
            'name' => 'Sneakers',
            'stock' => 5,
            'price' => 1000000,
        ]);

        $this->info("ID Produk: {$product->id} berhasil dibuat dengan Stok: {$product->stock}");

        $apiUrl = config('app.url') . '/api/orders';
        $totalConcurrentRequests = 10;

        $this->info("2. Mensimulasikan {$totalConcurrentRequests} pesanan bersamaan untuk 1 item...");

        $responses = Http::pool(function ($pool) use ($apiUrl, $totalConcurrentRequests, $product) {
            $requests = [];
            for ($i = 0; $i < $totalConcurrentRequests; $i++) {
                $requests[] = $pool->as("request_{$i}")
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->token,
                    ])
                    ->post($apiUrl, [
                        'items' => [
                            [
                                'product_id' => $product->id,
                                'quantity'   => 1,
                            ]
                        ]
                    ]);
            }
            return $requests;
        });

        $successCount = 0;
        $failedCount = 0;

        foreach ($responses as $response) {
            if ($response->status() === 201) {
                $successCount++;
            } else {
                $this->error("Pesanan Gagal (HTTP 400): {$response->json('message')}");
                $failedCount++;
            }
        }

        $product->refresh();

        $this->newLine();
        $this->line("--- HASIL TEST ---");
        $this->info("Pesanan Berhasil (HTTP 201): {$successCount}");
        $this->error("Pesanan Gagal (HTTP 400): {$failedCount}");
        $this->line("Sisa Stok di DB: {$product->stock}");

        if (!$this->logout()) {
            return Command::FAILURE;
        }

        if ($successCount === 5 && $product->stock === 0) {
            $this->newLine();
            $this->info("LULUS: Race condition berhasil ditangani! Stok tidak menjadi negatif.");
            return Command::SUCCESS;
        }

        $this->newLine();
        $this->error("GAGAL: Terdeteksi overselling atau stok menjadi negatif!");
        return Command::FAILURE;
    }
}
