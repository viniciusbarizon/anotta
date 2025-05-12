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
            collect($countries)->map(fn ($name, $code) => [
                'id' => $this->getUlid(),
                'code' => $code,
                'is_tax_id_numeric' => $this->isTaxIdNumeric(name: $name),
                'name' => $name,
                'tax_id_length' => $this->getTaxIdLength(name: $name),
            ])->toArray()
        );
    }

    /**
     * Retrieves a list of countries with their respective ISO 3166-1 alpha-2 codes
     * and names in Portuguese.
     *
     * @return array An associative array where the keys are ISO 3166-1 alpha-2
     *               country codes and the values are the country names in Portuguese.
     */
    private function getCountries(): array
    {
        return [
            'AF' => 'Afeganistão',
            'ZA' => 'África do Sul',
            'AL' => 'Albânia',
            'DE' => 'Alemanha',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AG' => 'Antígua e Barbuda',
            'SA' => 'Arábia Saudita',
            'DZ' => 'Argélia',
            'AR' => 'Argentina',
            'AM' => 'Arménia',
            'AU' => 'Austrália',
            'AT' => 'Áustria',
            'AZ' => 'Azerbaijão',
            'BS' => 'Bahamas',
            'BD' => 'Bangladeche',
            'BB' => 'Barbados',
            'BH' => 'Barém',
            'BE' => 'Bélgica',
            'BZ' => 'Belize',
            'BJ' => 'Benim',
            'BY' => 'Bielorrússia',
            'BO' => 'Bolívia',
            'BA' => 'Bósnia e Herzegovina',
            'BW' => 'Botsuana',
            'BR' => 'Brasil',
            'BN' => 'Brunei',
            'BG' => 'Bulgária',
            'BF' => 'Burquina Faso',
            'BI' => 'Burúndi',
            'BT' => 'Butão',
            'CV' => 'Cabo Verde',
            'CM' => 'Camarões',
            'KH' => 'Camboja',
            'CA' => 'Canadá',
            'QA' => 'Catar',
            'KZ' => 'Cazaquistão',
            'TD' => 'Chade',
            'CL' => 'Chile',
            'CN' => 'China',
            'CY' => 'Chipre',
            'CO' => 'Colômbia',
            'KM' => 'Comores',
            'CG' => 'Congo-Brazzaville',
            'CD' => 'Congo-Kinshasa',
            'KP' => 'Coreia do Norte',
            'KR' => 'Coreia do Sul',
            'CI' => 'Costa do Marfim',
            'CR' => 'Costa Rica',
            'HR' => 'Croácia',
            'CU' => 'Cuba',
            'DK' => 'Dinamarca',
            'DM' => 'Dominica',
            'EG' => 'Egito',
            'SV' => 'El Salvador',
            'AE' => 'Emirados Árabes Unidos',
            'EC' => 'Equador',
            'ER' => 'Eritreia',
            'SK' => 'Eslováquia',
            'SI' => 'Eslovénia',
            'ES' => 'Espanha',
            'US' => 'Estados Unidos',
            'EE' => 'Estónia',
            'ET' => 'Etiópia',
            'FJ' => 'Fiji',
            'PH' => 'Filipinas',
            'FI' => 'Finlândia',
            'FR' => 'França',
            'GA' => 'Gabão',
            'GM' => 'Gâmbia',
            'GH' => 'Gana',
            'GE' => 'Geórgia',
            'GD' => 'Granada',
            'GR' => 'Grécia',
            'GT' => 'Guatemala',
            'GY' => 'Guiana',
            'GN' => 'Guiné',
            'GQ' => 'Guiné Equatorial',
            'GW' => 'Guiné-Bissau',
            'HT' => 'Haiti',
            'HN' => 'Honduras',
            'HU' => 'Hungria',
            'YE' => 'Iémen',
            'IN' => 'Índia',
            'ID' => 'Indonésia',
            'IR' => 'Irão',
            'IQ' => 'Iraque',
            'IE' => 'Irlanda',
            'IS' => 'Islândia',
            'IL' => 'Israel',
            'IT' => 'Itália',
            'JM' => 'Jamaica',
            'JP' => 'Japão',
            'JO' => 'Jordânia',
            'KI' => 'Kiribati',
            'XK' => 'Kosovo',
            'KW' => 'Kuwait',
            'LA' => 'Laos',
            'LS' => 'Lesoto',
            'LV' => 'Letónia',
            'LB' => 'Líbano',
            'LR' => 'Libéria',
            'LY' => 'Líbia',
            'LI' => 'Listenstaine',
            'LT' => 'Lituânia',
            'LU' => 'Luxemburgo',
            'MK' => 'Macedónia do Norte',
            'MG' => 'Madagáscar',
            'MY' => 'Malásia',
            'MW' => 'Malávi',
            'MV' => 'Maldivas',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MA' => 'Marrocos',
            'MU' => 'Maurícia',
            'MR' => 'Mauritânia',
            'MX' => 'México',
            'MM' => 'Mianmar',
            'FM' => 'Micronésia',
            'MZ' => 'Moçambique',
            'MD' => 'Moldávia',
            'MC' => 'Mónaco',
            'MN' => 'Mongólia',
            'ME' => 'Montenegro',
            'NA' => 'Namíbia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'NI' => 'Nicarágua',
            'NE' => 'Níger',
            'NG' => 'Nigéria',
            'NO' => 'Noruega',
            'NZ' => 'Nova Zelândia',
            'OM' => 'Omã',
            'NL' => 'Países Baixos',
            'PW' => 'Palau',
            'PA' => 'Panamá',
            'PG' => 'Papua-Nova Guiné',
            'PK' => 'Paquistão',
            'PY' => 'Paraguai',
            'PE' => 'Peru',
            'PL' => 'Polónia',
            'PT' => 'Portugal',
            'KE' => 'Quénia',
            'KG' => 'Quirguistão',
            'GB' => 'Reino Unido',
            'CF' => 'República Centro-Africana',
            'CZ' => 'República Checa',
            'DO' => 'República Dominicana',
            'RO' => 'Roménia',
            'RW' => 'Ruanda',
            'RU' => 'Rússia',
            'SB' => 'Salomão',
            'SV' => 'Salvador',
            'WS' => 'Samoa',
            'LC' => 'Santa Lúcia',
            'KN' => 'São Cristóvão e Neves',
            'SM' => 'São Marino',
            'ST' => 'São Tomé e Príncipe',
            'VC' => 'São Vicente e Granadinas',
            'SC' => 'Seicheles',
            'SN' => 'Senegal',
            'SL' => 'Serra Leoa',
            'RS' => 'Sérvia',
            'SG' => 'Singapura',
            'SY' => 'Síria',
            'SO' => 'Somália',
            'LK' => 'Sri Lanca',
            'SD' => 'Sudão',
            'SS' => 'Sudão do Sul',
            'SE' => 'Suécia',
            'CH' => 'Suíça',
            'SR' => 'Suriname',
            'TH' => 'Tailândia',
            'TW' => 'Taiwan',
            'TJ' => 'Tajiquistão',
            'TZ' => 'Tanzânia',
            'TL' => 'Timor-Leste',
            'TG' => 'Togo',
            'TO' => 'Tonga',
            'TT' => 'Trindade e Tobago',
            'TN' => 'Tunísia',
            'TM' => 'Turquemenistão',
            'TR' => 'Turquia',
            'TV' => 'Tuvalu',
            'UA' => 'Ucrânia',
            'UG' => 'Uganda',
            'UY' => 'Uruguai',
            'UZ' => 'Uzbequistão',
            'VU' => 'Vanuatu',
            'VA' => 'Vaticano',
            'VE' => 'Venezuela',
            'VN' => 'Vietname',
            'ZM' => 'Zâmbia',
            'ZW' => 'Zimbabué',
        ];
    }
    
    /**
     * Check if the tax identification number for a given country is numeric.
     *
     * @param  string  $name  The name of the country.
     * @return bool Whether the tax identification number for the country is numeric.
     */
    private function isTaxIdNumeric(string $name): bool
    {
        return in_array($name, ['Brasil', 'Portugal']);
    }

    /**
     * Get the length of the tax identification number for a given country.
     *
     * @param  string  $name  The name of the country.
     * @return int|null The length of the tax identification number if defined, or null if not defined.
     */
    private function getTaxIdLength(string $name): ?int
    {
        if ($name === 'Portugal') {
            return 9;
        }

        if ($name === 'Brasil') {
            return 11;
        }

        return null;
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
