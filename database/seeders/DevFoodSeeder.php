<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DevFoodSeeder extends Seeder
{
    private array $catalog;

    private array $categoryMap = [
        'burgers' => 'lanches',
        'pizzas' => 'pizzas',
        'japanese' => 'japonesa',
        'brazilian' => 'brasileira',
        'desserts' => 'sorvete',
        'juices' => 'sucos',
    ];

    public function run(): void
    {
        $this->catalog = require database_path('seeders/data/devfood-catalog.php');

        $this->seedCategories();
        $this->seedBanners();

        $categories = Category::all()->keyBy('slug');

        foreach (['burgers', 'pizzas', 'japanese', 'brazilian'] as $key) {
            $this->seedRestaurantGroup($key, $categories);
        }
    }

    private function seedCategories(): void
    {
        $items = [
            ['name' => 'Sobremesas', 'slug' => 'sorvete', 'icon' => '/categories/sorvete.png', 'sort_order' => 1],
            ['name' => 'Sucos', 'slug' => 'sucos', 'icon' => '/categories/sucos.png', 'sort_order' => 2],
            ['name' => 'Hambúrgueres', 'slug' => 'lanches', 'icon' => '/categories/lanches.png', 'sort_order' => 3],
            ['name' => 'Pizzas', 'slug' => 'pizzas', 'icon' => '/categories/pizzas.png', 'sort_order' => 4],
            ['name' => 'Japonesa', 'slug' => 'japonesa', 'icon' => '/categories/japonesa.png', 'sort_order' => 5],
            ['name' => 'Brasileira', 'slug' => 'brasileira', 'icon' => '/categories/brasileira.png', 'sort_order' => 6],
        ];

        foreach ($items as $item) {
            Category::create($item);
        }
    }

    private function seedBanners(): void
    {
        $banners = [
            [
                'title' => 'Está com fome?',
                'subtitle' => 'Com apenas alguns cliques...',
                'image' => '/images/catalog/ff6196be3a117071.jpg',
                'badge' => 'DevFood',
                'sort_order' => 1,
            ],
            [
                'title' => 'Até 30% em pizzas',
                'subtitle' => 'Promoção especial',
                'image' => '/images/catalog/d8b9af1fa4af0bc5.jpg',
                'badge' => 'Pizza',
                'link' => '/categoria/pizzas/',
                'sort_order' => 2,
            ],
            [
                'title' => 'Lanches a partir de R$ 17,90',
                'subtitle' => 'Peça agora',
                'image' => '/images/catalog/63bfe007023caf86.jpg',
                'badge' => 'Burger',
                'link' => '/categoria/lanches/',
                'sort_order' => 3,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }

    private function seedRestaurantGroup(string $groupKey, $categories): void
    {
        $group = $this->catalog[$groupKey];
        $categorySlug = $this->categoryMap[$groupKey];
        $category = $categories[$categorySlug];
        $description = $this->catalog['description'];

        $labels = [
            'lanches' => 'Hambúrgueres',
            'pizzas' => 'Pizzas',
            'japonesa' => 'Japonesa',
            'brasileira' => 'Brasileira',
        ];

        foreach ($group['restaurants'] as $row) {
            $name = $row[0];
            $slug = $row[1];
            $image = $row[2];
            $fee = $row[3];
            $minutes = $row[4];
            $featured = $row[5] ?? false;

            $restaurant = Restaurant::create([
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'image' => $image,
                'logo' => $image,
                'category_label' => $labels[$categorySlug].' • Especialidade',
                'category_id' => $category->id,
                'rating' => 5.0,
                'reviews_count' => random_int(200, 2500),
                'delivery_time' => "{$minutes} min",
                'delivery_fee' => $fee,
                'min_order' => 15.00,
                'is_free_delivery' => $fee === 0,
                'is_featured' => $featured,
                'is_open' => true,
            ]);

            $sort = 0;
            foreach ($group['products'] as $productRow) {
                $this->createProduct(
                    $restaurant,
                    $category,
                    $productRow,
                    $description,
                    $sort++
                );
            }

            foreach ($this->catalog['desserts']['products'] as $productRow) {
                $this->createProduct(
                    $restaurant,
                    $categories['sorvete'],
                    $productRow,
                    $description,
                    $sort++
                );
            }

            foreach ($this->catalog['juices']['products'] as $productRow) {
                $this->createProduct(
                    $restaurant,
                    $categories['sucos'],
                    $productRow,
                    $description,
                    $sort++
                );
            }
        }
    }

    private function createProduct(
        Restaurant $restaurant,
        Category $category,
        array $row,
        string $description,
        int $sort
    ): void {
        $name = $row[0];
        $image = $row[1];
        $basePrice = $row[2];
        $discount = $row[3];
        $popular = $row[4] ?? false;

        [$finalPrice, $originalPrice] = $this->prices($basePrice, $discount);

        Product::create([
            'restaurant_id' => $restaurant->id,
            'category_id' => $category->id,
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $description,
            'image' => $image,
            'price' => $finalPrice,
            'original_price' => $originalPrice,
            'category' => $category->name,
            'is_promo' => $discount > 0,
            'is_popular' => $popular,
            'sort_order' => $sort,
        ]);
    }

    private function prices(float $basePrice, int $discountPercent): array
    {
        if ($discountPercent <= 0) {
            return [$basePrice, null];
        }

        $final = round($basePrice * (1 - $discountPercent / 100), 2);

        return [$final, $basePrice];
    }
}
