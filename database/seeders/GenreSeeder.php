<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $genres = [
            ['name' => 'Acción',         'description' => 'Películas con secuencias de acción, persecuciones y combates.'],
            ['name' => 'Aventura',        'description' => 'Historias de exploración, viajes y búsqueda de tesoros.'],
            ['name' => 'Comedia',         'description' => 'Películas diseñadas para provocar risa y entretenimiento.'],
            ['name' => 'Drama',           'description' => 'Historias serias que exploran la condición humana.'],
            ['name' => 'Terror',          'description' => 'Películas diseñadas para provocar miedo o suspenso.'],
            ['name' => 'Ciencia Ficción', 'description' => 'Exploración de futuros posibles, tecnología avanzada y vida extraterrestre.'],
            ['name' => 'Fantasía',        'description' => 'Mundos imaginarios con magia, criaturas míticas y héroes épicos.'],
            ['name' => 'Thriller',        'description' => 'Narrativas de tensión, suspenso y giros inesperados.'],
            ['name' => 'Romance',         'description' => 'Historias centradas en el amor y las relaciones sentimentales.'],
            ['name' => 'Animación',       'description' => 'Películas creadas mediante técnicas de animación para todas las edades.'],
            ['name' => 'Documental',      'description' => 'Registros audiovisuales de hechos o temas reales.'],
            ['name' => 'Musical',         'description' => 'Películas donde las canciones y el baile son parte central de la trama.'],
        ];

        foreach ($genres as $genre) {
            Genre::firstOrCreate([
                'slug' => Str::slug($genre['name'])
            ], array_merge($genre, [
                'is_active' => true
                ])
            );
        }
    }
}
