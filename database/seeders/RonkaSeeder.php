<?php

namespace Database\Seeders;

use App\Models\GalleryItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RonkaSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@ronka.com'],
            [
                'name' => 'Admin RONKA',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'membre@ronka.com'],
            [
                'name' => 'Membre Test',
                'username' => 'membre@ronka.com',
                'password' => Hash::make('membre123'),
                'role' => 'member',
            ]
        );

        User::firstOrCreate(
            ['email' => 'public@ronka.com'],
            [
                'name' => 'Utilisateur Public',
                'password' => Hash::make('public123'),
                'role' => 'public',
            ]
        );

        if (GalleryItem::count() === 0) {
            $items = [
                ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?w=800', 'title' => 'Mariage élégant'],
                ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?w=800', 'title' => 'Réception événementielle'],
                ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800', 'title' => 'Fête privée'],
                ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=800', 'title' => 'Service traiteur'],
                ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?w=800', 'title' => 'Conférence'],
            ];
            foreach ($items as $item) {
                GalleryItem::create($item);
            }
        }
    }
}
