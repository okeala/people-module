<?php

namespace Modules\People\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Modules\Documents\Models\Document;
use Modules\Helpcenter\Models\Address;
use Modules\Helpcenter\Models\Tag;
use Modules\People\Database\Factories\PersonFactory;
use Modules\Workshops\Models\InstructorSpecialty;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Person extends User implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password',
        'role', 'role_description',
        'is_admin', 'is_editor', 'is_author', 'is_contributor', 'is_subscriber',
    ];

    // Relation avec les spécialités
    public function specialties(): HasMany
    {
        return $this->hasMany(InstructorSpecialty::class, 'person_id');
    }

    // Méthodes utilitaires pour les spécialités
    public function hasSpecialty(string $specialty): bool
    {
        return $this->specialties()->where('specialty', $specialty)->exists();
    }

    public function getSpecialty(string $specialty): ?InstructorSpecialty
    {
        return $this->specialties()->where('specialty', $specialty)->first();
    }

    public function addSpecialty(string $specialty, int $experienceYears = 0, int $skillLevel = 1): InstructorSpecialty
    {
        return $this->specialties()->updateOrCreate(
            ['specialty' => $specialty],
            [
                'experience_years' => $experienceYears,
                'skill_level' => $skillLevel
            ]
        );
    }

    public function scopeWithSpecialty($query, string $specialty)
    {
        return $query->whereHas('specialties', function ($q) use ($specialty) {
            $q->where('specialty', $specialty);
        });
    }

    public function scopeWithSpecialtyAndLevel($query, string $specialty, int $minLevel = 3)
    {
        return $query->whereHas('specialties', function ($q) use ($specialty, $minLevel) {
            $q->where('specialty', $specialty)
                ->where('skill_level', '>=', $minLevel);
        });
    }

    // Relations existantes...
    public function articles(): HasMany
    {
        return $this->hasMany(\Modules\Blog\Models\Article::class, 'author_id');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(\Modules\Documents\Models\Document::class, 'person_id');
    }

    public function workshops(): BelongsToMany
    {
        return $this->belongsToMany(\Modules\Workshops\Models\Workshop::class, 'workshop_instructor')->withTimestamps();
    }

    public function address(): HasOne
    {
        return $this->hasOne(Address::class, 'person_id');
    }

    public static function newFactory(): PersonFactory
    {
        return new PersonFactory();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('gallery')
            ->useDisk('public')
            ->acceptsFile(function (Media $media) {
                return in_array($media->mime_type, ['image/jpeg', 'image/png', 'image/webp']);
            });
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(480)
            ->height(320)
            ->sharpen(10)
            ->performOnCollections('cover', 'gallery');
    }
}
