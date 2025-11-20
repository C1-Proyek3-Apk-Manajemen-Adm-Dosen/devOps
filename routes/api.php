use App\Http\Controllers\DosenPddiktiController;
use Illuminate\Support\Facades\Route;

// Group route yang memerlukan autentikasi
Route::middleware('auth:sanctum')->group(function () {
// Endpoint yang mengambil biodata dosen berdasarkan user yang sedang login
// Tidak perlu parameter NIDN/Nama di URL, karena sudah ada di session/token
Route::get('/profil/pddikti', [DosenPddiktiController::class, 'getBiodataDosenLogin']);
});