<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MovieSeeder extends Seeder
{
    private const DIRECTOR_CHRISTOPHER_NOLAN = 'Christopher Nolan';
    private const GENRE_ACTION = 'Acción';
    private const GENRE_ANIMATION = 'Animación';
    private const GENRE_SCIENCE_FICTION = 'Ciencia Ficción';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = [
            ['title' => 'El Padrino',               'year' => 1972, 'rating' => 9.2, 'director' => 'Francis Ford Coppola',  'duration' => 175, 'genres' => ['Drama']],
            ['title' => 'El Caballero Oscuro',       'year' => 2008, 'rating' => 9.0, 'director' => self::DIRECTOR_CHRISTOPHER_NOLAN, 'duration' => 152, 'genres' => [self::GENRE_ACTION, 'Thriller']],
            ['title' => 'Pulp Fiction',              'year' => 1994, 'rating' => 8.9, 'director' => 'Quentin Tarantino',     'duration' => 154, 'genres' => ['Drama', 'Thriller']],
            ['title' => 'Forrest Gump',              'year' => 1994, 'rating' => 8.8, 'director' => 'Robert Zemeckis',       'duration' => 142, 'genres' => ['Drama', 'Romance']],
            ['title' => 'Origen',                    'year' => 2010, 'rating' => 8.8, 'director' => self::DIRECTOR_CHRISTOPHER_NOLAN, 'duration' => 148, 'genres' => [self::GENRE_ACTION, self::GENRE_SCIENCE_FICTION]],
            ['title' => 'Matrix',                    'year' => 1999, 'rating' => 8.7, 'director' => 'Lana Wachowski',        'duration' => 136, 'genres' => [self::GENRE_ACTION, self::GENRE_SCIENCE_FICTION]],
            ['title' => 'Interstellar',              'year' => 2014, 'rating' => 8.6, 'director' => self::DIRECTOR_CHRISTOPHER_NOLAN, 'duration' => 169, 'genres' => [self::GENRE_SCIENCE_FICTION, 'Drama']],
            ['title' => 'El Señor de los Anillos',   'year' => 2001, 'rating' => 8.8, 'director' => 'Peter Jackson',         'duration' => 178, 'genres' => ['Fantasía', 'Aventura']],
            ['title' => 'El Club de la Pelea',       'year' => 1999, 'rating' => 8.8, 'director' => 'David Fincher',         'duration' => 139, 'genres' => ['Drama', 'Thriller']],
            ['title' => 'Gladiador',                 'year' => 2000, 'rating' => 8.5, 'director' => 'Ridley Scott',          'duration' => 155, 'genres' => [self::GENRE_ACTION, 'Drama']],
            ['title' => 'Titanic',                   'year' => 1997, 'rating' => 7.9, 'director' => 'James Cameron',         'duration' => 194, 'genres' => ['Drama', 'Romance']],
            ['title' => 'Toy Story',                 'year' => 1995, 'rating' => 8.3, 'director' => 'John Lasseter',         'duration' => 81,  'genres' => [self::GENRE_ANIMATION, 'Aventura']],
            ['title' => 'El Rey León',               'year' => 1994, 'rating' => 8.5, 'director' => 'Roger Allers',          'duration' => 88,  'genres' => [self::GENRE_ANIMATION, 'Drama']],
            ['title' => 'Amélie',                    'year' => 2001, 'rating' => 8.3, 'director' => 'Jean-Pierre Jeunet',    'duration' => 122, 'genres' => ['Comedia', 'Romance']],
            ['title' => 'La La Land',                'year' => 2016, 'rating' => 8.0, 'director' => 'Damien Chazelle',       'duration' => 128, 'genres' => ['Musical', 'Romance']],
            ['title' => 'Parásitos',                 'year' => 2019, 'rating' => 8.5, 'director' => 'Bong Joon-ho',          'duration' => 132, 'genres' => ['Drama', 'Thriller']],
            ['title' => 'Avengers: Endgame',         'year' => 2019, 'rating' => 8.4, 'director' => 'Anthony Russo',         'duration' => 181, 'genres' => [self::GENRE_ACTION, 'Aventura']],
            ['title' => 'Joker',                     'year' => 2019, 'rating' => 8.4, 'director' => 'Todd Phillips',         'duration' => 122, 'genres' => ['Drama', 'Thriller']],
            ['title' => 'Coco',                      'year' => 2017, 'rating' => 8.4, 'director' => 'Lee Unkrich',           'duration' => 105, 'genres' => [self::GENRE_ANIMATION, 'Musical']],
            ['title' => 'Spider-Man: Un Nuevo Universo', 'year' => 2018, 'rating' => 8.4, 'director' => 'Bob Persichetti',  'duration' => 117, 'genres' => [self::GENRE_ANIMATION, self::GENRE_ACTION]],
            ['title' => 'Mad Max: Furia en el Camino', 'year' => 2015, 'rating' => 8.1, 'director' => 'George Miller',      'duration' => 120, 'genres' => [self::GENRE_ACTION, 'Aventura']],
            ['title' => 'Her',                       'year' => 2013, 'rating' => 8.0, 'director' => 'Spike Jonze',           'duration' => 126, 'genres' => [self::GENRE_SCIENCE_FICTION, 'Romance']],
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
