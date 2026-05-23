<?php
class Product {
    public int    $id;
    public string $image;
    public array  $gallery;       // array of image paths for thumbnails
    public string $title;
    public string $subtitle;      // short tagline e.g. "Premium Puppy Dry Food"
    public string $description;   // long description shown on individual page
    public string $weight;
    public float  $price;
    public string $category;      // e.g. "Food & Treats", "Toys", "Grooming"
    public string $brand;
    public string $pet_type;      // e.g. "Dog", "Cat", "All Pets"
    public string $life_stage;    // e.g. "Puppy", "Adult", "Senior", "All"
    public string $food_form;     // e.g. "Dry Food", "Wet Food", "—"
    public array  $flavors;       // e.g. ['Chicken & Milk', 'Beef & Vegetables']
    public string $storage_type;  // e.g. "Room Temperature"
    public string $origin;        // e.g. "Philippines Supplier"
    public float  $rating;        // e.g. 4.9
    public int    $review_count;

    // Rich content for the individual page (optional — use empty array/string if not applicable)
    public array  $specs;         // key => value pairs for the spec table
    public string $full_description; // HTML-safe longer description paragraphs
    public array  $ingredients;   // list items
    public array  $guaranteed_analysis; // list items
    public array  $feeding_guide; // list items
    public array  $whats_inside;  // list items

    public function __construct(
        int    $id,
        string $image,
        array  $gallery,
        string $title,
        string $subtitle,
        string $description,
        string $full_description,
        string $weight,
        float  $price,
        string $category,
        string $brand,
        string $pet_type,
        string $life_stage,
        string $food_form,
        array  $flavors,
        string $storage_type,
        string $origin,
        float  $rating,
        int    $review_count,
        array  $specs           = [],
        array  $ingredients     = [],
        array  $guaranteed_analysis = [],
        array  $feeding_guide   = [],
        array  $whats_inside    = []
    ) {
        $this->id                  = $id;
        $this->image               = $image;
        $this->gallery             = $gallery ?: [$image];
        $this->title               = $title;
        $this->subtitle            = $subtitle;
        $this->description         = $description;
        $this->full_description    = $full_description;
        $this->weight              = $weight;
        $this->price               = $price;
        $this->category            = $category;
        $this->brand               = $brand;
        $this->pet_type            = $pet_type;
        $this->life_stage          = $life_stage;
        $this->food_form           = $food_form;
        $this->flavors             = $flavors;
        $this->storage_type        = $storage_type;
        $this->origin              = $origin;
        $this->rating              = $rating;
        $this->review_count        = $review_count;
        $this->specs               = $specs;
        $this->ingredients         = $ingredients;
        $this->guaranteed_analysis = $guaranteed_analysis;
        $this->feeding_guide       = $feeding_guide;
        $this->whats_inside        = $whats_inside;
    }

    public function getFormattedPrice(): string {
        return '₱' . number_format($this->price, 2);
    }

    public function getStars(): string {
        $full  = floor($this->rating);
        $half  = ($this->rating - $full) >= 0.5 ? 1 : 0;
        $empty = 5 - $full - $half;
        return str_repeat('★', (int)$full) . str_repeat('½', $half) . str_repeat('☆', (int)$empty);
    }
}

// ── Category helper (for the filter sidebar) ──────────────────
function product_category_count(array $products, string $cat): int {
    return count(array_filter($products, fn($p) => $p->category === $cat));
}

// ─────────────────────────────────────────────────────────────
//  PRODUCT DATA
//  When you move to a DB gyud , replace this array with a query oke?.
//  supply.php?id=1  →  loads product with id 1
// ─────────────────────────────────────────────────────────────
$products = [

    new Product(
        id: 1,
        image: 'Assets/pedigree_puppy.jpg',
        gallery: [
            'Assets/pedigree_puppy.jpg',
            'Assets/pedigree_puppy2.jpg',
            'Assets/pedigree_puppy3.jpg',
        ],
        title:       'Pedigree Adult Complete Nutrition',
        subtitle:    'Complete & Balanced Dry Dog Food for Adult Dogs',
        description: 'A protein-rich daily dry dog food enriched with vitamins, minerals, and omega-6 fatty acids to support healthy muscles, shiny coats, and strong immunity.',
        full_description:
            'Pedigree Adult Complete Nutrition is specially formulated to provide adult dogs with complete daily nutrition for long-term health and vitality.'.
            'Made with quality meat proteins, grains, essential vitamins, and minerals, this crunchy kibble supports lean muscle maintenance, healthy digestion, stronger immunity, and naturally cleaner teeth through chewing action.'.
            'Omega-6 fatty acids and zinc help maintain healthy skin and a shiny coat, making it an excellent everyday meal for active adult dogs. ',
            
        weight:       '2.8kg',
        price:        650.00,
        category:    'Foods',
        brand:       'Pedigree',
        pet_type:    'Dog',
        life_stage:  'Puppy',
        food_form:   'Dry Food',
        flavors:     ['Chicken & Milk', 'Beef & Vegetables'],
        storage_type: 'Room Temperature',
        origin:      'Philippines Supplier',
        rating:      4.9,
        review_count: 128,
        specs: [
            'Category'   => 'Foods and Treats',
            'Brand'      => 'Pedigree',
            'Pet Type'   => 'Dog',
            'Life Stage' => 'Puppy',
            'Food Form'  => 'Dry Food',
            'Flavor'     => 'Chicken & Milk / Beef & Vegetables',
            'Weight'     => '2.8kg',
            'Storage'    => 'Room Temperature',
            'Origin'     => 'Philippines Supplier',
        ],
        ingredients: [
            'Cereals',
            'Poultry meal',
            'Soybean products',
            'Vitamins and minerals',
            'Vegetable oils',
            'Dried vegetables',
        ],
        guaranteed_analysis: [
            'Protein (min) – 22%',
            'Fat (min) – 10%',
            'Fiber (max) – 4%',
            'Moisture (max) – 12%',
        ],
        feeding_guide: [
            'Small Puppies (up to 5kg): 60–120g/day',
            'Medium Puppies (5–15kg): 120–220g/day',
            'Large Puppies (15–30kg): 220–380g/day',
        ],
        whats_inside: [
            'PEDIGREE Puppy Dry Dog Food 2.8kg Bag',
        ]
    ),

    new Product(
        id: 2,
        image: 'Assets/toys.jpg',
        gallery: ['Assets/toys.jpg'],
        title:       'Squeaky Rubber Chew Toy',
        subtitle:    'Durable Dog Chew Toy',
        description: 'Durable rubber chew toy for aggressive chewers. Keeps dogs entertained for hours.',
        full_description:
            'This premium squeaky rubber chew toy is built tough for aggressive chewers. ' .
            'Made from 100% non-toxic, BPA-free natural rubber that is safe for your dog to gnaw on all day. ' .
            'The built-in squeaker provides instant feedback that excites and engages your pup. ' .
            'Great for interactive play and solo entertainment. Helps reduce destructive chewing behavior ' .
            'by redirecting energy to a safe and fun outlet.',
        weight:       '0.2kg',
        price:        180.50,
        category:    'Toys',
        brand:       'FluffPlay',
        pet_type:    'Dog',
        life_stage:  'All',
        food_form:   '—',
        flavors:     [],
        storage_type: 'Room Temperature',
        origin:      'Local Supplier',
        rating:      4.6,
        review_count: 47,
        specs: [
            'Category'   => 'Toys → Chew Toys',
            'Brand'      => 'FluffPlay',
            'Pet Type'   => 'Dog',
            'Life Stage' => 'All Stages',
            'Material'   => 'Natural Rubber (BPA-free)',
            'Weight'     => '0.2kg',
            'Storage'    => 'Room Temperature',
            'Origin'     => 'Local Supplier',
        ],
        ingredients: [],
        guaranteed_analysis: [],
        feeding_guide: [],
        whats_inside: ['1× Squeaky Rubber Chew Toy']
    ),

    new Product(
        id: 3,
        image: 'Assets/grooming.jpg',
        gallery: ['Assets/grooming.jpg'],
        title:       'Soft Grooming Brush',
        subtitle:    'Gentle Pet Grooming Tool',
        description: 'Designed for gentle grooming of your pet\'s fur. Reduces shedding and keeps coats shiny.',
        full_description:
            'This soft-bristle grooming brush is perfect for daily coat maintenance on cats and dogs. ' .
            'The ergonomic handle reduces hand fatigue during long grooming sessions. ' .
            'Fine-tipped bristles gently detangle without pulling or scratching skin. ' .
            'Regular brushing with this tool helps distribute natural oils, keeping coats healthy and lustrous. ' .
            'Suitable for short, medium, and long-haired pets.',
        weight:       '0.1kg',
        price:        95.00,
        category:    'Grooming',
        brand:       'FluffCare',
        pet_type:    'All Pets',
        life_stage:  'All',
        food_form:   '—',
        flavors:     [],
        storage_type: 'Room Temperature',
        origin:      'Local Supplier',
        rating:      4.7,
        review_count: 63,
        specs: [
            'Category'   => 'Grooming → Brushes & Combs',
            'Brand'      => 'FluffCare',
            'Pet Type'   => 'All Pets',
            'Life Stage' => 'All Stages',
            'Material'   => 'Soft Nylon Bristles + ABS Handle',
            'Weight'     => '0.1kg',
            'Storage'    => 'Room Temperature',
            'Origin'     => 'Local Supplier',
        ],
        ingredients: [],
        guaranteed_analysis: [],
        feeding_guide: [],
        whats_inside: ['1× Soft Grooming Brush']
    ),

     new Product(
        id: 4,
        image: 'Assets/grooming.jpg',
        gallery: ['Assets/grooming.jpg'],
        title:       'Whiskas 1+ Complete Nutrition',
        subtitle:    'Crunchy Complete Dry Cat Food for Adult Cats',
        description: 'A delicious and balanced dry cat food with crunchy pockets and essential nutrients that support healthy coats and strong immunity.',
        full_description:
            'Whiskas 1+ Complete Nutrition is designed for adult cats aged one year and above.'.
            'This complete daily diet combines crunchy kibble with flavorful pockets filled with tasty centers cats enjoy.'.
            'Rich in protein, omega-6 fatty acids, zinc, and essential vitamins, it supports healthy skin, shiny coats, digestion, and immune health while satisfying your cat’s natural craving for crunchy textures.',
        weight:       '7kg',
        price:        1650.00,
        category:    'Food',
        brand:       'Whiskas',
        pet_type:    'Cat',
        life_stage:  'Adult',
        food_form:   'Dry Food',
        flavors:      ['Ocean Fish', 'Tuna & Salmon'],
        storage_type: 'Room Temperature',
        origin:      'Imported through Philippine distributors',
        rating:      4.9/5.0,
        review_count: 2011,
        specs: [
            'Category'   => 'Food',
            'Brand'      => 'Whiskas',
            'Pet Type'   => 'Cat',
            'Life Stage' => 'Adult',
            'Weight'     => '7kg',
            'Storage'    => 'Room Temperature',
            'Origin'     => 'Imported through Philippine distributors',
        ],
        ingredients: ['Fish meal', 'Poultry meal', 'Corn gluten', 'Rice', 'Animal fats', 'Vitamins and minerals', 'Omega fatty acids', 'Taurine'],
        guaranteed_analysis: ['Protein (min) – 30%', 'Fat (min) – 12%', 'Fiber (max) – 5%', 'Moisture (max) – 10%'],
        feeding_guide: ['Small Cats (2–4kg): 35–60g/day', 'Medium Cats (4–6kg): 60–85g/day', 'Large Cats (6–8kg): 90–120g/day'],
        whats_inside: ['1× Whiskas 1+ Complete Nutrition']
    ),

    new Product(
        id: 5,
        image: 'Assets/grooming.jpg',
        gallery: ['Assets/grooming.jpg'],
        title:       'Mazuri Timothy-Based Rabbit Diet',
        subtitle:    'High-Fiber Timothy Hay Pellet Food for Rabbits',
        description: 'A premium Timothy hay-based rabbit pellet fortified with probiotics and natural nutrients for healthy digestion and long-term wellness.',
        full_description:
            'Mazuri Timothy-Based Rabbit Diet is formulated to support rabbits at every life stage with high-fiber nutrition and digestive support.'.
            'Made using premium Timothy hay, this uniform pellet helps prevent selective feeding while promoting healthy chewing habits.'.
            'Enhanced with probiotics, flaxseed, and natural vitamin E, it supports digestive balance, coat condition, and overall vitality without artificial additives.',
        weight:       '1.5LB',
        price:        620.00,
        category:    'Food',
        brand:       'Mazuri',
        pet_type:    'Rabbit',
        life_stage:  'Adult',
        food_form:   'Dry Pellet',
        flavors:      ['Timothy Hay Blend', 'Garden Herb Blend'],
        storage_type: 'Room Temperature',
        origin:      'Imported via Philippine suppliers',
        rating:      4.9/5.0,
        review_count: 2011,
        specs: [
            'Category'   => 'Food',
            'Brand'      => 'Mazuri',
            'Pet Type'   => 'Rabbit',
            'Life Stage' => 'Adult',
            'Weight'     => '1.5LB',
            'Storage'    => 'Room Temperature',
            'Origin'     => 'Imported via Philippine suppliers',
        ],
        ingredients: ['Timothy hay', 'Soybean hulls', 'Flaxseed', 'Wheat middlings', 'Probiotics', 'Vitamin E', 'Chelated minerals'],
        guaranteed_analysis: ['Protein (min) – 14%', 'Fat (min) – 2%', 'Fiber (max) – 22%', 'Moisture (max) – 12%'],
        feeding_guide: ['Small Rabbits (2–4kg): 35–60g/day', 'Medium Rabbits (4–6kg): 60–85g/day', 'Large Rabbits (6–8kg): 90–120g/day'],
        whats_inside: ['1× Mazuri Timothy-Based Rabbit Diet 1.5LB Bag']
    ),


];

// ── Build a quick ID-keyed lookup (mirrors $pets pattern) ─────
$product_lookup = [];
foreach ($products as $p) {
    $product_lookup[$p->id] = $p;
}
?>