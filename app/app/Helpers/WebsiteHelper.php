<?php

namespace App\Helpers;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WebsiteHelper
{
    public static function extractWebsiteMetadata($url)
    {
        try {
            $response = Http::timeout(10)->get($url);
            $html = $response->body();

            $doc = new DOMDocument();
            @$doc->loadHTML($html);
            $xpath = new DOMXPath($doc);

            // Ekstraksi judul
            $title = self::extractTitle($xpath, $html);

            // Ekstraksi gambar
            $image = self::extractImage($xpath);

            // Ekstraksi deskripsi
            $description = self::extractDescription($xpath, $doc);

            return [
                'title' => $title,
                'image' => $image,
                'description' => $description,
            ];
        } catch (\Exception $e) {
            return [
                'title' => 'Untitled',
                'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRKREcZ7HHA4nPfRJwXkrv0-i11G3uaxIGZVA&s',
                'description' => 'No Description',
            ];
        }
    }

    protected static function extractDescription($xpath, $doc)
    {
        // Coba dapatkan deskripsi dari meta tag dengan name "description"
        $metaDescription = $xpath->query('//meta[@name="description"]')->item(0);
        if ($metaDescription) {
            return trim($metaDescription->getAttribute('content'));
        }

        // Coba dapatkan deskripsi dari meta tag dengan property "og:description"
        $ogDescription = $xpath->query('//meta[@property="og:description"]')->item(0);
        if ($ogDescription) {
            return trim($ogDescription->getAttribute('content'));
        }

        // Coba dapatkan deskripsi dari meta tag dengan name "twitter:description"
        $twitterDescription = $xpath->query('//meta[@name="twitter:description"]')->item(0);
        if ($twitterDescription) {
            return trim($twitterDescription->getAttribute('content'));
        }

        // Jika tidak ada meta description, coba ambil dari paragraf pertama
        $firstParagraph = $xpath->query('//p')->item(0);
        if ($firstParagraph) {
            return Str::limit(trim($firstParagraph->nodeValue), 200);
        }

        // Jika masih tidak ada, ambil 200 karakter pertama dari body
        $body = $doc->getElementsByTagName('body')->item(0);
        if ($body) {
            return Str::limit(trim($body->textContent), 200);
        }

        // Jika semua metode gagal, kembalikan null
        return 'No Description';
    }

    protected static function extractTitle($xpath, $html)
    {
        // Metode 1: Menggunakan regular expression untuk tag <title>
        if (preg_match('/<title>(.*?)<\/title>/i', $html, $matches)) {
            return trim($matches[1]);
        }

        // Metode 2: Menggunakan parsing DOM
        $title = $xpath->query('//title')->item(0);
        if ($title) {
            return trim($title->nodeValue);
        }

        // Coba dapatkan judul dari tag meta dengan property "og:title"
        $ogTitle = $xpath->query('//meta[@property="og:title"]')->item(0);
        if ($ogTitle) {
            return trim($ogTitle->getAttribute('content'));
        }

        // Coba dapatkan judul dari tag meta dengan name "twitter:title"
        $twitterTitle = $xpath->query('//meta[@name="twitter:title"]')->item(0);
        if ($twitterTitle) {
            return trim($twitterTitle->getAttribute('content'));
        }

        // Coba dapatkan judul dari h1 pertama
        $h1 = $xpath->query('//h1')->item(0);
        if ($h1) {
            return trim($h1->nodeValue);
        }

        // Jika semua metode gagal, kembalikan 'Untitled'
        return 'Untitled';
    }

    protected static function extractImage($xpath)
    {
        // Coba dapatkan gambar dari tag meta dengan property "og:image"
        $ogImage = $xpath->query('//meta[@property="og:image"]')->item(0);
        if ($ogImage) {
            return $ogImage->getAttribute('content');
        }

        // Coba dapatkan gambar dari tag meta dengan name "twitter:image"
        $twitterImage = $xpath->query('//meta[@name="twitter:image"]')->item(0);
        if ($twitterImage) {
            return $twitterImage->getAttribute('content');
        }

        // Coba dapatkan gambar pertama dengan class 'featured-image' atau 'post-thumbnail'
        $featuredImage = $xpath->query('//img[contains(@class, "featured-image") or contains(@class, "post-thumbnail")]')->item(0);
        if ($featuredImage) {
            return $featuredImage->getAttribute('src');
        }

        // Jika tidak ada yang cocok, ambil gambar pertama dari halaman
        $firstImage = $xpath->query('//img')->item(0);
        if ($firstImage) {
            return $firstImage->getAttribute('src');
        }

        // Jika tidak ada gambar ditemukan, kembalikan null
        return 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRKREcZ7HHA4nPfRJwXkrv0-i11G3uaxIGZVA&s';
    }
}
