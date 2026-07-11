<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Graduate extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $fillable = ['user_id', 'university_id', 'phone', 'major_id', 'graduation_year', 'photo', 'cv_path'];

    public function user() { return $this->belongsTo(User::class); }
    public function major() { return $this->belongsTo(Major::class); }

    /**
     * Path relative to the public disk root (e.g. profile-photos/abc.jpg).
     * Strips accidental "storage/" prefix and normalizes directory separators.
     */
    public static function normalizeRelativePublicPath(?string $stored): ?string
    {
        if (! $stored) {
            return null;
        }

        $path = str_replace('\\', '/', ltrim($stored, '/'));
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        return $path !== '' ? $path : null;
    }

    public function photoRelativePath(): ?string
    {
        return static::normalizeRelativePublicPath($this->photo);
    }

    public function cvRelativePath(): ?string
    {
        return static::normalizeRelativePublicPath($this->cv_path);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        $path = $this->photoRelativePath();
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return route('graduate.profile.photo', [], false);
    }
}
