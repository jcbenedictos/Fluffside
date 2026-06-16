<?php
// pets.inc.php — Single source of truth for all pet data.
// When you move to a database, replace this array with a DB query.
// The "VIEW" button on residents.php links to: pet.php?id=SCOUT (or the pet's id key)

$pets = [

    'scout' => [
        'id'          => 'scout',
        'name'        => 'SCOUT',
        'breed'       => 'Golden Retriever',
        'type'        => 'DOG',       // used for filter tag
        'gender'      => 'MALE',      // used for filter tag
        'age'         => '12 weeks old', // used for filter tag
        'age_group'   => 'Young',     // for filter: Young / Adult / Senior
        'image'       => 'Assets/Residents/Dog/Scout1.jpg',
        'gallery'     => [
            'Assets/Residents/Dog/Scout1.jpg',
            'Assets/Residents/Dog/Scout2.jpg',
            'Assets/Residents/Dog/Scout3.jpg',
        ],
        'traits'      => ['Playful', 'Social', 'Affectionate', 'Curious', 'Kid-Friendly'],
        'likes'       => ['Soft Toys', 'Belly Rubs', 'Napping'],
        'dislikes'    => ['Being alone', 'Sudden Loud Noises', 'Rainy Walks'],
        'description' => 'Scout is a bundle of golden sunshine looking for his forever home! At just 12 weeks old, he is in the heart of his “explorer phase”, curious about every leaf, butterfly, and shoelace he encounters. True to his breed, Scout is incredibly social and views every stranger as a potential best friend. He is currently working on his potty training and basic “sit”, though he is easily distracted by the promise of a treat or a head pat. If you are looking for a loyal, wiggly companion who will grow into a devoted family member, Scout is ready to join your pack!',
    ],

    'clover' => [
        'id'          => 'clover',
        'name'        => 'CLOVER',
        'breed'       => 'Lop-eared Rabbit',
        'type'        => 'RABBIT',
        'gender'      => 'FEMALE',
        'age'         => '1 year old',
        'age_group'   => 'Adult',
        'image'       => 'Assets/Residents/Rabbit/Clover1.jpg',
        'gallery'     => [
            'Assets/Residents/Rabbit/Clover1.jpg',
            'Assets/Residents/Rabbit/Clover2.jpg',
            'Assets/Residents/Rabbit/Clover3.png',
        ],
        'traits'      => ['Curious', 'Gentle', 'Affectionate', 'Quiet', 'First-time Owner Friendly'],
        'likes'       => ['Fresh Greens', 'Exploring in the Grass', 'Forehead Rubs'],
        'dislikes'    => ['Being Picked Up Suddenly', 'Loud Noises', 'Slippery Floors'],
        'description' => 'Meet Clover, a gentle soul with the softest fur you’ll ever touch! With her floppy ears and bright, curious eyes, Clover is a bundle of quiet joy. Holland Lops are known for having wonderful, sweet temperaments, making them great indoor companions. Clover is quite the "binkier"—when she’s happy, she’ll do a little mid-air twist to show it! She is litter-box trained and would love a home where she can have some supervised time to hop around and explore. If you’re looking for a calm, quiet, and incredibly cute friend to brighten your days, Clover is waiting for you.',
    ],

    'cheddar' => [
        'id'          => 'cheddar',
        'name'        => 'CHEDDAR',
        'breed'       => 'Syrian Hamster',
        'type'        => 'HAMSTER',
        'gender'      => 'MALE',
        'age'         => '6 months old',
        'age_group'   => 'Young',
        'image'       => 'Assets/Residents/Hamster/Cheddar1.jpg',
        'gallery'     => [
            'Assets/Residents/Hamster/Cheddar1.jpg',
            'Assets/Residents/Hamster/Cheddar2.jpg',
            'Assets/Residents/Hamster/Cheddar3.jpg',
            
        ],
        'traits'      => ['Friendly', 'Curious', 'Gentle', 'Independent', 'First-time Owner Friendly'],
        'likes'       => ['Sunflower Seeds', 'Nighttime Runs', 'Deep Bedding'],
        'dislikes'    => ['Daytime Wake-up Calls', 'Roommates', 'Sudden Bright Lights'],
        'description' => 'Say hello to Cheddar, the tiny explorer! Cheddar is a classic Syrian hamster with a curious and gentle nature. Syrians are known for being the most handleable of the hamster breeds, and Cheddar is already very comfortable interacting with humans. At 6 months old, he is full of energy and loves exploring safe, enclosed play areas. He is a master architect who spends his hours creating elaborate tunnels and cozy nests in his bedding. If you’re looking for a small, low-maintenance friend who is big on personality, Cheddar is ready to roll into your life!',
    ],

    'mochi' => [
        'id'          => 'mochi',
        'name'        => 'MOCHI',
        'breed'       => 'Siamese',
        'type'        => 'CAT',
        'gender'      => 'FEMALE',
        'age'         => '4 months old',
        'age_group'   => 'Young',
        'image'       => 'Assets/Residents/Cat/Mochi1.jpg',
        'gallery'     => [
            'Assets/Residents/Cat/Mochi1.jpg',
            'Assets/Residents/Cat/Mochi2.jpg',
            'Assets/Residents/Cat/Mochi3.jpg',
        ],
        'traits'      => ['Playful', 'Social', 'Friendly', 'Affectionate', 'Curious'],
        'likes'       => ['Warm Laps', 'Soft Blankets'],
        'dislikes'    => ['Closed Doors', 'Empty Bowls'],
        'description' => 'Meet Mochi, a tiny feline with a big personality! With her striking blue eyes and beautiful pointed coat, she is sure to steal hearts instantly. Mochi is a social butterfly who thrives on interaction and “chatting” with her human companions. She is at that perfect age where she is full of curiosity and playfulness, but also highly values her beauty sleep. Siamese-type cats are known for being exceptionally loyal and dog-like in their devotion, and Mochi is already showing those wonderful traits. If you are looking for a clever, talkative, and affectionate companion, Mochi is the purr-fect match for you!',
    ],

    'pearl' => [
        'id'          => 'pearl',
        'name'        => 'PEARL',
        'breed'       => 'Persian',
        'type'        => 'CAT',
        'gender'      => 'FEMALE',
        'age'         => '7 years old',
        'age_group'   => 'Senior',
        'image'       => 'Assets/Residents/Cat/Pearl1.jpg',
        'gallery'     => [
            'Assets/Residents/Cat/Pearl1.jpg',
            'Assets/Residents/Cat/Pearl2.jpg',
            'Assets/Residents/Cat/Pearl3.png',
        ],
        'traits'      => ['Quiet', 'Gentle', 'Independent', 'Affectionate'],
        'likes'       => ['Grooming Session', 'Quiet Corners', 'Being Pampered'],
        'dislikes'    => ['Loud Environment', 'Sticky or Dirty Floors', 'Being Rushed'],
        'description' => 'Pearl is a true royalty of the shelter! Looking at her, it’s clear she is a refined and dignified companion. Persians are famous for their sweet, gentle temperaments, and Pearl embodies this perfectly. She isn’t much for jumping on counters or chasing lasers; she would much rather sit beautifully by your side while you read or work. Because of her long, luxurious fur, she will need a family dedicated to keeping her looking her best with regular grooming. If you are looking for a calm, quiet, and breathtakingly beautiful feline friend, Pearl is ready to grace your home with her presence.',
    ],

    'loki' => [
        'id'          => 'loki',
        'name'        => 'LOKI',
        'breed'       => 'Aspin',
        'type'        => 'DOG',
        'gender'      => 'MALE',
        'age'         => '1 year old',
        'age_group'   => 'Adult',
        'image'       => 'Assets/Residents/Dog/Loki1.png',
        'gallery'     => [
            'Assets/Residents/Dog/Loki1.png',
            'Assets/Residents/Dog/Loki2.png',
            'Assets/Residents/Dog/Loki3.jpg',
        ],
        'traits'      => ['Protective', 'Playful', 'Independent', 'Curious', 'First-time Owner Friendly'],
        'likes'       => ['Outdoor Adventures', 'Personal Space', 'Playful Interaction'],
        'dislikes'    => ['Tight Spaces', 'Being Ignored', 'Loud Noises'],
        'description' => 'Meet the ultimate “Good Boy!”. This charming Aspin, Loki, is the definition of a loyal companion. Aspins are celebrated by their hardy nature and high intelligence, making them very quick learners when it comes to new tricks. At around a year old, he has moved past the tiny puppy stage but still has plenty of “zoomies” left in him. He is medium-sized, perfectly athletic, and has a short, easy-to-groom coat. Whether you’re looking for a hiking buddy or a dedicated protector for your home, this sweet boy is ready to give a lifetime of wagging tails and smiles,',
    ],

    'sunny' => [
        'id'          => 'sunny',
        'name'        => 'SUNNY',
        'breed'       => 'Puspin',
        'type'        => 'CAT',
        'gender'      => 'FEMALE',
        'age'         => '3 years old',
        'age_group'   => 'Adult',
        'image'       => 'Assets/Residents/Cat/Sunny1.png',
        'gallery'     => [
            'Assets/Residents/Cat/Sunny1.png',
            'Assets/Residents/Cat/Sunny2.jpg',
            'Assets/Residents/Cat/Sunny3.jpg',
        ],
        'traits'      => ['Friendly', 'Affectionate', 'Social', 'Gentle', 'First-time Owner Friendly'],
        'likes'       => ['Window Watching', 'Soft Surfaces', 'Chin Scratches'],
        'dislikes'    => ['Cold Floors', 'Being Ignored', 'Empty Food Bowls'],
        'description' => 'Sunny is a vibrant orange Puspin who is ready to bring a burst of light into your home! As you can see, she has a wonderfully expressive face and a relaxed, friendly vibe. Puspins are known for being incredibly smart and adaptable, and Sunny is a perfect example—she gets along well with almost everyone and settles into new environments with ease. At 3 years old, she has the perfect balance of playfulness and "chill" energy. She is a low-maintenance, high-affection companion who just wants a warm lap and a loving family to call her own.',
    ],

    'minty' => [
        'id'          => 'minty',
        'name'        => 'MINTY',
        'breed'       => "Fischer's Lovebird",
        'type'        => 'BIRD',
        'gender'      => 'FEMALE',
        'age'         => '4 months old',
        'age_group'   => 'Young',
        'image'       => 'Assets/Residents/Bird/Minty1.jpg',
        'gallery'     => [
            'Assets/Residents/Bird/Minty1.jpg',
            'Assets/Residents/Bird/Minty2.jpg',
            'Assets/Residents/Bird/Minty3.jpg',
        ],
        'traits'      => ['Playful', 'Curious', 'Social', 'Friendly', 'Affectionate'],
        'likes'       => ['Mirror Toys', 'Gentle Head Scratches', 'Morning Chirping Sessions'],
        'dislikes'    => ['Sudden Darkness', 'Loud Clapping Sounds', 'Dirty Water Bowls'],
        'description' => 'Misty is a tiny bundle of sunshine wrapped in feathers! This adorable budgie is full of playful energy and loves fluttering around her perch while happily chirping little tunes throughout the day. She is naturally curious and enjoys investigating new toys, especially anything shiny or colorful. Though she may act shy at first, Misty warms up quickly with patience and soft voices, and she absolutely loves spending time near her favorite humans. With her bright personality and sweet nature, Misty would make a wonderful companion for someone looking for a cheerful little friend.',
    ],

    'skye' => [
        'id'          => 'skye',
        'name'        => 'SKYE',
        'breed'       => 'Cockatiel',
        'type'        => 'BIRD',
        'gender'      => 'FEMALE',
        'age'         => '1 year old',
        'age_group'   => 'Adult',
        'image'       => 'Assets/Residents/Bird/Skye1.jpg',
        'gallery'     => [
            'Assets/Residents/Bird/Skye1.jpg',
            'Assets/Residents/Bird/Skye2.jpg',
            'Assets/Residents/Bird/Skye3.jpg',
        ],
        'traits'      => ['Social', 'Playful', 'Curious', 'Affectionate', 'Friendly'],
        'likes'       => ['Window Perches', 'Whistling Songs', 'Millet Treats'],
        'dislikes'    => ['Being Alone Too Long', 'Sudden Movements', 'Dark Rooms'],
        'description' => 'Meet Skye, a cheerful little cockatiel with a curious heart and an adorable crest that pops up whenever she gets excited! Skye loves spending her mornings perched by the window, chirping softly while watching the world go by. She is playful, intelligent, and enjoys learning simple whistles and tricks—especially when treats are involved. Cockatiels are known for forming strong bonds with their humans, and Skye absolutely adores attention and companionship. Whether she’s hopping onto your shoulder or softly singing nearby, Skye is sure to fill your home with warmth and happy energy.',
    ],

    'honey' => [
        'id'          => 'honey',
        'name'        => 'HONEY ',
        'breed'       => 'Holland Lop',
        'type'        => 'RABBIT',
        'gender'      => 'FEMALE',
        'age'         => '1 year old',
        'age_group'   => 'Adult',
        'image'       => 'Assets/Residents/Rabbit/Honey1.png',
        'gallery'     => [
            'Assets/Residents/Rabbit/Honey1.png',
            'Assets/Residents/Rabbit/Honey2.png',
            'Assets/Residents/Rabbit/Honey3.jpg',
        ],
        'traits'      => ['Social', 'Curious', 'Playful', 'Friendly', 'Energetic'],
        'likes'       => ['Standing Tall', 'Social Time', 'Soft Toes'],
        'dislikes'    => ['Loud, Sudden Noises', 'Being Picked Up', 'Strong Scents'],
        'description' => "Honey is as sweet as her name suggests! This beautiful cinnamon-colored Holland Lop, is looking for a home where she can be the center of attention. She is a curious and active young rabbit who loves to investigate every corner of her play area. Honey is very social and would likely thrive in a home with a patient family who can give her plenty of floor time to hop and play. She is already working on her litter-box skills and is ready to find a forever home where she can binky to her heart's content.",
    ],

    'pebble' => [
        'id'          => 'pebble',
        'name'        => 'PEBBLE',
        'breed'       => 'Winter White Dwarf Hamster',
        'type'        => 'HAMSTER',
        'gender'      => 'MALE',
        'age'         => '5 months old',
        'age_group'   => 'Young',
        'image'       => 'Assets/Residents/Hamster/Pebble1.jpg',
        'gallery'     => [
            'Assets/Residents/Hamster/Pebble1.jpg',
            'Assets/Residents/Hamster/Pebble2.jpg',
            'Assets/Residents/Hamster/Pebble3.jpg',
        ],
        'traits'      => ['Energetic', 'Curious', 'Playful', 'Independent'],
        'likes'       => ['Window Watching', 'Soft Surfaces', 'Chin Scratches'],
        'dislikes'    => ['Cold Floors', 'Being Ignored', 'Empty Food Bowls'],
        'description' => 'Pebble is a tiny ball of energy! Pebble is a charming Winter White Dwarf hamster with beautiful grey and white markings. Unlike larger hamsters, dwarf breeds are known for being very fast and active, making them a joy to watch as they go about their "business." Pebble is a curious little guy who is slowly learning that human hands often carry tasty treats. He is a master of the wheel and would love a home with plenty of vertical and horizontal space to explore. If you’re looking for a small, lively, and incredibly cute companion, Pebble is ready to meet you!',
    ],

    'biscuit' => [
        'id'          => 'biscuit',
        'name'        => 'BISCUIT',
        'breed'       => 'Netherland Dwarf',
        'type'        => 'RABBIT',
        'gender'      => 'MALE',
        'age'         => '9 years old',
        'age_group'   => 'Senior',
        'image'       => 'Assets/Residents/Rabbit/Biscuit1.jpg',
        'gallery'     => [
            'Assets/Residents/Rabbit/Biscuit1.jpg',
            'Assets/Residents/Rabbit/Biscuit2.jpg',
            'Assets/Residents/Rabbit/Biscuit3.png',
        ],
        'traits'      => ['Energetic', 'Playful', 'Curious', 'Independent'],
        'likes'       => ['Chin Resting', 'Gentle Nuzzles', 'Peeking Around Corners'],
        'dislikes'    => ['Fast Hand Movements', 'Being Overlooked', 'Slippery Floors'],
        'description' => "Don't let his tiny size fool you—Biscuit has a personality that could fill a whole room! Biscuit is a classic Netherland Dwarf with a beautiful tawny coat and a very expressive face. This breed is known for being energetic and 'spunky,'' and Biscuit is no exception. He is a busy little bee who loves to zoom around his play area and perform impressive 'binkies.' Because of his small stature, he would do best in a calm home with older children or adults who can handle him with care. If you are looking for a small, intelligent, and endlessly entertaining companion, Biscuit is ready to hop into your heart!",
    ],

    'cosmo' => [
        'id'          => 'cosmo',
        'name'        => 'COSMO',
        'breed'       => 'Samoyed',
        'type'        => 'DOG',
        'gender'      => 'MALE',
        'age'         => '6 months old',
        'age_group'   => 'Young',
        'image'       => 'Assets/Residents/Dog/Cosmo1.jpg',
        'gallery'     => [
            'Assets/Residents/Dog/Cosmo1.jpg',
            'Assets/Residents/Dog/Cosmo2.jpg',
            'Assets/Residents/Dog/Cosmo3.png',
        ],
        'traits'      => ['Energetic', 'Playful', 'Curious', 'Independent'],
        'likes'       => ['Chin Resting', 'Gentle Nuzzles', 'Peeking Around Corners'],
        'dislikes'    => ['Fast Hand Movements', 'Being Overlooked', 'Slippery Floors'],
        'description' => "Don't let his tiny size fool you—Biscuit has a personality that could fill a whole room! Biscuit is a classic Netherland Dwarf with a beautiful tawny coat and a very expressive face. This breed is known for being energetic and 'spunky,'' and Biscuit is no exception. He is a busy little bee who loves to zoom around his play area and perform impressive 'binkies.' Because of his small stature, he would do best in a calm home with older children or adults who can handle him with care. If you are looking for a small, intelligent, and endlessly entertaining companion, Biscuit is ready to hop into your heart!",
    ],

];


/*<!-- for clean comments -->*/