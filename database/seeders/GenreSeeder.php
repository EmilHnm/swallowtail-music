<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $genres = [
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Pop music",
                'description' => 'A genre of popular music that originated in the West during the 1950s and 1960s. Pop music is eclectic, often borrowing elements from urban, dance, rock, Latin, country, and other styles. Songs are typically short to medium-length with repeated choruses, melodic tunes, and hooks.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Hip hop music",
                'description' => 'Hip hop or rap music formed in the United States in the 1970s and consists of stylized rhythmic music that commonly accompanies rhythmic and rhyming speech ("rapping").'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Rock music",
                'description' => 'A genre of popular music that originated as "rock and roll" in the United States in the 1950s, and developed into a range of different styles in the 1960s and later. Compared to pop music, rock places a higher degree of emphasis on musicianship, live performance, and an ideology of authenticity.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Rhythm and blues",
                'description' => 'A genre of popular African-American music that originated in the 1940s as urbane, rocking, jazz based music with a heavy, insistent beat. Lyrics focus heavily on the themes of triumphs and failures in terms of relationships, freedom, economics, aspirations, and sex.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Soul music",
                'description' => 'A popular music genre that combines elements of African-American gospel music, rhythm and blues and jazz.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Reggae",
                'description' => 'A music genre that originated in Jamaica in the late 1960s, strongly influenced by traditional mento as well as American jazz and rhythm and blues, instantly recognizable from the counterpoint between the bass and drum downbeat, and the offbeat rhythm section.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Country",
                'description' => 'A genre of United States popular music with origins in folk, Blues and Western music, often consisting of ballads and dance tunes with generally simple forms and harmonies accompanied by mostly string instruments such as banjos, electric and acoustic guitars, dobros, and fiddles as well as harmonicas.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Funk",
                'description' => 'A music genre that originated in the 1960s when African American musicians created a rhythmic, danceable new form of music that de-emphasized melody and chord progressions to bring a strong rhythmic groove of a bass line and drum part to the foreground.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Folk music",
                'description' => 'A genre that evolved from traditional music during the 20th century folk revival. One meaning often given is that of old songs with no known composers; another is music that has been transmitted and evolved by a process of oral transmission or performed by custom over a long period of time.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Middle Eastern music",
                'description' => 'Music originating from the vast region from Morocco to Iran, including the Arabic countries of the Middle East and North Africa, the Iraqi traditions of Mesopotamia, Iranian traditions of Persia, the Hebrew music of Israel, Armenian music, the varied traditions of Cypriot music, the music of Turkey, traditional Assyrian music, Berbers of North Africa, and Coptic Christians in Egypt.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Jazz",
                'description' => 'A music genre that originated from African American communities of New Orleans during the late 19th and early 20th centuries in the form of independent traditional and popular musical styles, all linked by the common bonds of African American and European American musical parentage with a performance orientation.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Disco",
                'description' => 'A genre of dance music containing elements of funk, soul, pop, and salsa that achieved popularity during the mid-1970s to the early 1980s.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Classical music",
                'description' => 'Art music produced or rooted in the traditions of Western music, including both liturgical and secular music, over the broad span of time from roughly the 11th century to the present day.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Electronic music",
                'description' => 'A large set of predominantly popular and dance genres in which synthesizers and other electronic instruments are the primary sources of sound.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Music of Latin America",
                'description' => 'Music originating from Latin America which encompasses a wide variety of styles, including son, rumba, salsa, merengue, tango, samba, and bossa nova.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Blues",
                'description' => 'A genre and musical form developed by African Americans in the United States around the end of the 19th century from African-American work songs and European-American folk music. The blues form, ubiquitous in jazz and rock and roll, is characterized by the call-and-response pattern, the blues scale and specific chord progressions, of which the twelve-bar blues is the most common. Blues shuffles or walking bass reinforce the trance-like rhythm and form a repetitive groove effect.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Music for children",
                'description' => 'Music performed for children, often designed to provide an entertaining means of teaching about cultures, good behavior, facts, and skills.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "New-age music",
                'description' => 'A genre of music intended to create artistic inspiration, relaxation, and optimism used by listeners for yoga, massage, meditation, and reading as a method of stress management or to create a peaceful atmosphere. Includes both electronic and acoustic forms.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Vocal music",
                'description' => 'Music performed by one or more singers, with or without instrumental accompaniment, in which singing provides the main focus of the piece.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Music of Africa",
                'description' => 'Music whose style or form clearly indicates African origin or primarily African influence. Given the vastness of the continent, this covers many distinct musical traditions. Sub-Saharan African music frequently relies on percussion instruments including xylophones, drums, and tone-producing instruments such as the mbira or "thumb piano."'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Christian music",
                'description' => 'Music that has been written to express either personal or a communal belief regarding Christian life and faith. Its forms vary widely across the world, according to culture and social context.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Music of Asia",
                'description' => 'Musical styles originating from a large number of Asian countries located in Central, Southern, and East Asia.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Ska",
                'description' => 'A music genre that originated in Jamaica in the late 1950s, combining elements of Caribbean mento and calypso with American jazz and rhythm and blues. It is characterized by a walking bass line accented with rhythms on the off-beat.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Traditional music",
                'description' => 'Musical forms that have origins many generations into the past, commonly without formal notation or description, commonly familiar to people in a given culture.'
            ],
            [
                'genre_id' => 'genre_' . Str::random(10),
                'name' => "Independent music",
                'description' => 'Music produced independent of major commercial record labels, possibly including a do-it-yourself approach to recording and publishing. The term indie is also used to describe music of this style regardless of actual production channel.'
            ],
        ];
        Genre::insert($genres);
    }
}
