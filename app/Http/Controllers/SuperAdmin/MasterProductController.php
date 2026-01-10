<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CreditFacility;
use App\Models\CreditFacilityTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MasterProductController extends Controller
{
    public function index(Request $request)
    {
        // 1. Mulai Query
        $query = CreditFacility::withCount('tiers')->latest();

        // 2. Cek apakah ada input 'search'
        $query->when($request->search, function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                // Cari berdasarkan Kode ATAU Nama (Case Insensitive di MySQL/MariaDB)
                $sub->where('kode', 'like', '%' . $request->search . '%')
                    ->orWhere('nama', 'like', '%' . $request->search . '%');
            });
        });

        // 3. Paginate (Penting: withQueryString() agar pagination tidak mereset pencarian)
        $products = $query->paginate(10)->withQueryString();

        return view('superadmin.master.products.index', compact('products'));
    }

    public function create()
    {
        return view('superadmin.master.products.create');
    }

    public function store(Request $request)
    {
        // 1. SANITASI DATA (Ubah Format Indo -> DB)
        $tiers = $request->tiers ?? [];

        foreach ($tiers as $key => $val) {
            // Helper Function Kecil untuk Bersihin Duit
            // Contoh: "1.500.000,50" -> "1500000.50"
            $cleanMoney = function ($value) {
                if ($value === null || $value === '') return null;
                // Buang titik ribuan
                $noDot = str_replace('.', '', $value);
                // Ganti koma desimal jadi titik
                return str_replace(',', '.', $noDot);
            };

            $tiers[$key]['min_plafond'] = $cleanMoney($val['min_plafond']);
            $tiers[$key]['max_plafond'] = $cleanMoney($val['max_plafond']);
        }

        // Merge data bersih kembali ke request
        $request->merge(['tiers' => $tiers]);

        // 2. VALIDASI (Logic sama seperti sebelumnya)
        $request->validate([
            'kode' => ['required', Rule::unique('credit_facilities')->whereNull('deleted_at')],
            'nama' => 'required|string|max:255',
            'max_jangka_waktu' => 'required|integer|min:1',
            'tiers' => 'required|array|min:1',

            // Numeric validation sekarang aman karena format sudah 1500000.50
            'tiers.*.min_plafond' => 'required|numeric|min:0',

            'tiers.*.max_plafond' => [
                'nullable',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $min = $request->input("tiers.$index.min_plafond");

                    if (!is_null($value) && $value <= $min) {
                        $fail('Max Plafond harus lebih besar dari Min Plafond.');
                    }
                }
            ],
            'tiers.*.bunga' => 'required|numeric|min:0',
        ], [
            // Parameter 2: Custom Messages (Pesan error khusus)
            'tiers.*.max_plafond.numeric' => 'Format angka salah.',
        ], [
            // Parameter 3: Custom Attributes (Ganti nama tiers.0.xxx jadi enak dibaca)
            'tiers.*.min_plafond' => 'Min Plafond',
            'tiers.*.max_plafond' => 'Max Plafond',
            'tiers.*.bunga' => 'Suku Bunga',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Simpan Produk
                $product = CreditFacility::create([
                    'kode' => $request->kode,
                    'nama' => $request->nama,
                    'deskripsi' => $request->deskripsi,
                    'max_jangka_waktu' => $request->max_jangka_waktu,
                    'aktif' => $request->has('aktif') ? 1 : 0,
                ]);

                // 2. Simpan Tiers
                foreach ($request->tiers as $tier) {
                    $product->tiers()->create($tier);
                }
            });

            return redirect()->route('app.products.index')
                ->with('success', 'Produk Kredit berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $product = CreditFacility::with('tiers')->findOrFail($id);
        return view('superadmin.master.products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $product = CreditFacility::findOrFail($id);

        // 1. SANITASI DATA (Ubah Format Indo -> DB)
        $tiers = $request->tiers ?? [];

        foreach ($tiers as $key => $val) {
            $cleanMoney = function ($value) {
                // "0" tidak boleh dianggap null
                if ($value === null || $value === '') return null;

                $noDot = str_replace('.', '', $value);
                return str_replace(',', '.', $noDot);
            };

            $tiers[$key]['min_plafond'] = $cleanMoney($val['min_plafond']);
            $tiers[$key]['max_plafond'] = $cleanMoney($val['max_plafond']);
        }

        $request->merge(['tiers' => $tiers]);

        // 2. VALIDASI
        $request->validate([
            // Cek Unik: Ignore ID saat ini, dan Abaikan yang soft deleted
            'kode' => [
                'required',
                Rule::unique('credit_facilities')->ignore($id)->whereNull('deleted_at')
            ],
            'nama' => 'required|string|max:255',
            'max_jangka_waktu' => 'required|integer|min:1',
            'tiers' => 'required|array|min:1',

            'tiers.*.min_plafond' => 'required|numeric|min:0',
            'tiers.*.max_plafond' => [
                'nullable',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $min = $request->input("tiers.$index.min_plafond");
                    // Validasi: Kalau Max diisi, harus > Min
                    if (!is_null($value) && $value <= $min) {
                        $fail('Max Plafond harus lebih besar dari Min Plafond.');
                    }
                }
            ],
            'tiers.*.bunga' => 'required|numeric|min:0',
        ], [
            'tiers.*.max_plafond.numeric' => 'Format angka salah.',
        ], [
            'tiers.*.min_plafond' => 'Min Plafond',
            'tiers.*.max_plafond' => 'Max Plafond',
            'tiers.*.bunga' => 'Suku Bunga',
        ]);

        try {
            DB::transaction(function () use ($request, $product, $tiers) { // Pakai $tiers yang sudah bersih
                // A. Update Produk Utama
                $product->update([
                    'kode' => $request->kode,
                    'nama' => $request->nama,
                    'deskripsi' => $request->deskripsi,
                    'max_jangka_waktu' => $request->max_jangka_waktu,
                    'aktif' => $request->has('aktif') ? 1 : 0,
                ]);

                // B. Update Tiers (Hapus Lama -> Buat Baru)
                // Ini strategi paling aman untuk one-to-many dinamis
                $product->tiers()->delete();

                foreach ($tiers as $tier) {
                    $product->tiers()->create($tier);
                }
            });

            return redirect()->route('app.products.index')
                ->with('success', 'Produk Kredit berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $product = CreditFacility::findOrFail($id);

        // Laravel otomatis mengisi kolom deleted_at (Soft Delete)
        // Data tidak hilang dari SQL, tapi hilang dari query Eloquent biasa
        $product->delete();

        return redirect()->route('app.products.index')
            ->with('success', 'Produk berhasil dihapus (diarsipkan).');
    }
}
