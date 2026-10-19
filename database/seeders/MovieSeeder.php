<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = [
            ['title' => 'El Padrino',               'year' => 1972, 'rating' => 9.2, 'director' => 'Francis Ford Coppola',  'duration' => 175, 'genres' => ['Drama']],
            ['title' => 'El Caballero Oscuro',       'year' => 2008, 'rating' => 9.0, 'director' => 'Christopher Nolan',     'duration' => 152, 'genres' => ['Acción', 'Thriller']],
            ['title' => 'Pulp Fiction',              'year' => 1994, 'rating' => 8.9, 'director' => 'Quentin Tarantino',     'duration' => 154, 'genres' => ['Drama', 'Thriller']],
            ['title' => 'Forrest Gump',              'year' => 1994, 'rating' => 8.8, 'director' => 'Robert Zemeckis',       'duration' => 142, 'genres' => ['Drama', 'Romance']],
            ['title' => 'Origen',                    'year' => 2010, 'rating' => 8.8, 'director' => 'Christopher Nolan',     'duration' => 148, 'genres' => ['Acción', 'Ciencia Ficción']],
            ['title' => 'Matrix',                    'year' => 1999, 'rating' => 8.7, 'director' => 'Lana Wachowski',        'duration' => 136, 'genres' => ['Acción', 'Ciencia Ficción']],
            ['title' => 'Interstellar',              'year' => 2014, 'rating' => 8.6, 'director' => 'Christopher Nolan',     'duration' => 169, 'genres' => ['Ciencia Ficción', 'Drama']],
            ['title' => 'El Señor de los Anillos',   'year' => 2001, 'rating' => 8.8, 'director' => 'Peter Jackson',         'duration' => 178, 'genres' => ['Fantasía', 'Aventura']],
            ['title' => 'El Club de la Pelea',       'year' => 1999, 'rating' => 8.8, 'director' => 'David Fincher',         'duration' => 139, 'genres' => ['Drama', 'Thriller']],
            ['title' => 'Gladiador',                 'year' => 2000, 'rating' => 8.5, 'director' => 'Ridley Scott',          'duration' => 155, 'genres' => ['Acción', 'Drama']],
            ['title' => 'Titanic',                   'year' => 1997, 'rating' => 7.9, 'director' => 'James Cameron',         'duration' => 194, 'genres' => ['Drama', 'Romance']],
            ['title' => 'Toy Story',                 'year' => 1995, 'rating' => 8.3, 'director' => 'John Lasseter',         'duration' => 81,  'genres' => ['Animación', 'Aventura']],
            ['title' => 'El Rey León',               'year' => 1994, 'rating' => 8.5, 'director' => 'Roger Allers',          'duration' => 88,  'genres' => ['Animación', 'Drama']],
            ['title' => 'Amélie',                    'year' => 2001, 'rating' => 8.3, 'director' => 'Jean-Pierre Jeunet',    'duration' => 122, 'genres' => ['Comedia', 'Romance']],
            ['title' => 'La La Land',                'year' => 2016, 'rating' => 8.0, 'director' => 'Damien Chazelle',       'duration' => 128, 'genres' => ['Musical', 'Romance']],
            ['title' => 'Parásitos',                 'year' => 2019, 'rating' => 8.5, 'director' => 'Bong Joon-ho',          'duration' => 132, 'genres' => ['Drama', 'Thriller']],
            ['title' => 'Avengers: Endgame',         'year' => 2019, 'rating' => 8.4, 'director' => 'Anthony Russo',         'duration' => 181, 'genres' => ['Acción', 'Aventura']],
            ['title' => 'Joker',                     'year' => 2019, 'rating' => 8.4, 'director' => 'Todd Phillips',         'duration' => 122, 'genres' => ['Drama', 'Thriller']],
            ['title' => 'Coco',                      'year' => 2017, 'rating' => 8.4, 'director' => 'Lee Unkrich',           'duration' => 105, 'genres' => ['Animación', 'Musical']],
            ['title' => 'Spider-Man: Un Nuevo Universo', 'year' => 2018, 'rating' => 8.4, 'director' => 'Bob Persichetti',  'duration' => 117, 'genres' => ['Animación', 'Acción']],
            ['title' => 'Mad Max: Furia en el Camino', 'year' => 2015, 'rating' => 8.1, 'director' => 'George Miller',      'duration' => 120, 'genres' => ['Acción', 'Aventura']],
            ['title' => 'Her',                       'year' => 2013, 'rating' => 8.0, 'director' => 'Spike Jonze',           'duration' => 126, 'genres' => ['Ciencia Ficción', 'Romance']],
            ['title' => 'Whiplash',                  'year' => 2014, 'rating' => 8.5, 'director' => 'Damien Chazelle',       'duration' => 106, 'genres' => ['Drama', 'Musical']],
            ['title' => 'Get Out',                   'year' => 2017, 'rating' => 7.7, 'director' => 'Jordan Peele',          'duration' => 104, 'genres' => ['Terror', 'Thriller']],
            ['title' => 'Hereditary',                'year' => 2018, 'rating' => 7.3, 'director' => 'Ari Aster',             'duration' => 127, 'genres' => ['Terror', 'Drama']],
        ];

        foreach ($movies as $movieData) {
            $genres = $movieData['genres'];
            unset($movieData['genres']);

            $movie = Movie::firstOrCreate(
                ['title' => $movieData['title']],
                $movieData + ['slug' => Str::slug($movieData['title'])]
            );
            $genreIds = Genre::whereIn('name', $genres)->pluck('id');

            $movie->genres()->sync($genreIds);
        }
    }
}
