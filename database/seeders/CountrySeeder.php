<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class CountrySeeder extends Seeder
{
    /**
     * Seed the countries table with a list of country names.
     *
     * This method retrieves a list of country names, generates a unique ULID for each,
     * and inserts them into the 'countries' database table. Each country is stored
     * with an 'id' (ULID) and a 'name'.
     */
    public function run(): void
    {
        $countries = $this->getCountries();

        DB::table('countries')->insert(
            collect($countries)->map(fn ($name) => [
                'id' => $this->getUlid(),
                'name' => $name,
            ])->toArray()
        );
    }

    /**
     * Retrieves a list of country names.
     *
     * @return array An array containing the names of countries in Portuguese.
     */
    private function getCountries(): array
    {
        return [
            'Afeganistão',
            'África do Sul',
            'Albânia',
            'Alemanha',
            'Andorra',
            'Angola',
            'Antígua e Barbuda',
            'Arábia Saudita',
            'Argélia',
            'Argentina',
            'Arménia',
            'Austrália',
            'Áustria',
            'Azerbaijão',
            'Bahamas',
            'Bangladeche',
            'Barbados',
            'Barém',
            'Bélgica',
            'Belize',
            'Benim',
            'Bielorrússia',
            'Bolívia',
            'Bósnia e Herzegovina',
            'Botsuana',
            'Brasil',
            'Brunei',
            'Bulgária',
            'Burquina Faso',
            'Burúndi',
            'Butão',
            'Cabo Verde',
            'Camarões',
            'Camboja',
            'Canadá',
            'Catar',
            'Cazaquistão',
            'Chade',
            'Chile',
            'China',
            'Chipre',
            'Colômbia',
            'Comores',
            'Congo-Brazzaville',
            'Congo-Kinshasa',
            'Coreia do Norte',
            'Coreia do Sul',
            'Costa do Marfim',
            'Costa Rica',
            'Croácia',
            'Cuba',
            'Dinamarca',
            'Dominica',
            'Egito',
            'El Salvador',
            'Emirados Árabes Unidos',
            'Equador',
            'Eritreia',
            'Eslováquia',
            'Eslovénia',
            'Espanha',
            'Estados Unidos',
            'Estónia',
            'Etiópia',
            'Fiji',
            'Filipinas',
            'Finlândia',
            'França',
            'Gabão',
            'Gâmbia',
            'Gana',
            'Geórgia',
            'Granada',
            'Grécia',
            'Guatemala',
            'Guiana',
            'Guiné',
            'Guiné Equatorial',
            'Guiné-Bissau',
            'Haiti',
            'Honduras',
            'Hungria',
            'Iémen',
            'Índia',
            'Indonésia',
            'Irão',
            'Iraque',
            'Irlanda',
            'Islândia',
            'Israel',
            'Itália',
            'Jamaica',
            'Japão',
            'Jordânia',
            'Kiribati',
            'Kosovo',
            'Kuwait',
            'Laos',
            'Lesoto',
            'Letónia',
            'Líbano',
            'Libéria',
            'Líbia',
            'Listenstaine',
            'Lituânia',
            'Luxemburgo',
            'Macedónia do Norte',
            'Madagáscar',
            'Malásia',
            'Malávi',
            'Maldivas',
            'Mali',
            'Malta',
            'Marrocos',
            'Maurícia',
            'Mauritânia',
            'México',
            'Mianmar',
            'Micronésia',
            'Moçambique',
            'Moldávia',
            'Mónaco',
            'Mongólia',
            'Montenegro',
            'Namíbia',
            'Nauru',
            'Nepal',
            'Nicarágua',
            'Níger',
            'Nigéria',
            'Noruega',
            'Nova Zelândia',
            'Omã',
            'Países Baixos',
            'Palau',
            'Panamá',
            'Papua-Nova Guiné',
            'Paquistão',
            'Paraguai',
            'Peru',
            'Polónia',
            'Portugal',
            'Quénia',
            'Quirguistão',
            'Reino Unido',
            'República Centro-Africana',
            'República Checa',
            'República Dominicana',
            'Roménia',
            'Ruanda',
            'Rússia',
            'Salomão',
            'Salvador',
            'Samoa',
            'Santa Lúcia',
            'São Cristóvão e Neves',
            'São Marino',
            'São Tomé e Príncipe',
            'São Vicente e Granadinas',
            'Seicheles',
            'Senegal',
            'Serra Leoa',
            'Sérvia',
            'Singapura',
            'Síria',
            'Somália',
            'Sri Lanca',
            'Sudão',
            'Sudão do Sul',
            'Suécia',
            'Suíça',
            'Suriname',
            'Tailândia',
            'Taiwan',
            'Tajiquistão',
            'Tanzânia',
            'Timor-Leste',
            'Togo',
            'Tonga',
            'Trindade e Tobago',
            'Tunísia',
            'Turquemenistão',
            'Turquia',
            'Tuvalu',
            'Ucrânia',
            'Uganda',
            'Uruguai',
            'Uzbequistão',
            'Vanuatu',
            'Vaticano',
            'Venezuela',
            'Vietname',
            'Zâmbia',
            'Zimbabué',
        ];
    }

    /**
     * Generate a lowercase ULID (Universally Unique Lexicographically Sortable Identifier).
     *
     * This method utilizes the `str()->ulid()` helper to create a ULID and converts it to lowercase.
     *
     * @return string The generated lowercase ULID.
     */
    private function getUlid(): string
    {
        return mb_strtolower(
            (string) str()->ulid()
        );
    }
}
